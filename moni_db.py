from ast import Num
from twisted.enterprise import adbapi
from twisted.internet.defer import inlineCallbacks, returnValue
import logging
from json import loads as jloads


logger = logging.getLogger("hbmon")

class MoniDB:
    def __init__(self):
        self.db = adbapi.ConnectionPool("sqlite3", "monit.db", check_same_thread=False)

    @inlineCallbacks
    def create_tables(self):
        try:
            def create_tbl(txn):
                txn.execute('''CREATE TABLE IF NOT EXISTS talkgroup_ids (
                                        id INT PRIMARY KEY UNIQUE NOT NULL, 
                                        callsign VARCHAR(255) NOT NULL)''')

                txn.execute('''CREATE TABLE IF NOT EXISTS subscriber_ids (
                                        id INT PRIMARY KEY UNIQUE NOT NULL,
                                        callsign VARCHAR(255) NOT NULL,
                                        name VARCHAR(255) NOT NULL)''')

                txn.execute('''CREATE TABLE IF NOT EXISTS peer_ids (
                                        id INT PRIMARY KEY UNIQUE NOT NULL, 
                                        callsign VARCHAR(255) NOT NULL)''')

                txn.execute('''CREATE TABLE IF NOT EXISTS last_heard (
                                        date_time TEXT NOT NULL,
                                        qso_time DECIMAL(3,2),
                                        qso_type VARCHAR(20) NOT NULL,
                                        system VARCHAR(50) NOT NULL,
                                        tg_num INT NOT NULL,
                                        dmr_id INT PRIMARY KEY UNIQUE NOT NULL)''')

                txn.execute('''CREATE TABLE IF NOT EXISTS lstheard_log (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                                        date_time TEXT NOT NULL ,
                                        qso_time DECIMAL(3,2),
                                        qso_type VARCHAR(20) NOT NULL,
                                        system VARCHAR(50) NOT NULL,
                                        tg_num INT NOT NULL,
                                        dmr_id INT NOT NULL)''')

            yield self.db.runInteraction(create_tbl)
            print("Tables created successfully.")

        except Exception as err:
            logger.error(f"create_tables: {err}, {type(err)}")

    @inlineCallbacks
    def populate_tbl(self, table, lst_data, wipe_tbl, _file):
        try:
            def populate(txn, wipe_tbl):
                if table == "talkgroup_ids":
                    stm = "INSERT OR IGNORE INTO talkgroup_ids VALUES (?, ?)"
                elif table == "subscriber_ids":
                    stm = stm = "INSERT OR IGNORE INTO subscriber_ids VALUES (?, ?, ?)"
                elif table == "peer_ids":
                    stm = "INSERT OR IGNORE INTO peer_ids VALUES (?, ?)"
 
                if wipe_tbl:
                    txn.execute(f"DELETE FROM {table}")
                result = txn.executemany(stm, lst_data).rowcount
                if result > 0:
                    logger.info(f"{result} entries added to: {table} table from: {_file}")

            yield self.db.runInteraction(populate, wipe_tbl)

        except Exception as err:
            logger.error(f"populate: {err}, {type(err)}")

    @inlineCallbacks
    def table_count(self, _table):
        try:
            if _table == "talkgroup_ids":
                stm = "SELECT count(*) FROM talkgroup_ids"
            elif _table == "subscriber_ids":
                stm = "SELECT count(*) FROM subscriber_ids"
            elif _table == "peer_ids":
                stm = "SELECT count(*) FROM peer_ids"

            result = yield self.db.runQuery(stm)
            if result:
                returnValue(result[0][0])
            else:
                returnValue(None)

        except Exception as err:
            logger.error(f"table_count: {err}, {type(err)}")

    @inlineCallbacks
    def ins_lstheard(self, qso_time, qso_type, system, tg_num, dmr_id):
        try:
            yield self.db.runOperation("INSERT OR REPLACE INTO last_heard VALUES(datetime('now', 'localtime'), ?, ?, ?, ?, ?)", 
                                       (qso_time, qso_type, system, tg_num, dmr_id))

        except Exception as err:
            logger.error(f"ins_lstheard: {err}, {type(err)}")

    @inlineCallbacks
    def ins_lstheard_log(self, qso_time, qso_type, system, tg_num, dmr_id):
        try:
            yield self.db.runOperation('''INSERT INTO lstheard_log(date_time, qso_time, qso_type, system, tg_num, dmr_id)
                                        VALUES(datetime('now', 'localtime'), ?, ?, ?, ?, ?)''',
                                       (qso_time, qso_type, system, tg_num, dmr_id))

        except Exception as err:
            logger.error(f"ins_lstheard_log: {err}, {type(err)}")

    @inlineCallbacks
    def slct_2dict(self, _id, _table):
        try:
            if _table == "subscriber_ids":
                stm = "SELECT * FROM subscriber_ids WHERE id = ?"
            if _table == "talkgroup_ids":
                stm = "SELECT * FROM talkgroup_ids WHERE id = ?"

            result = yield self.db.runQuery(stm, (_id,))
            if result:
                returnValue(result[0])
            else:
                returnValue(None)

        except Exception as err:
            logger.error(f"slct_2dict: {err}, {type(err)}")

    @inlineCallbacks
    def slct_2render(self, _table, _row_num):
        try:
            if _table == "last_heard":
                stm = '''SELECT date_time, qso_time, qso_type, system, tg_num,
                        (SELECT callsign FROM talkgroup_ids WHERE id = tg_num), dmr_id,
                        (SELECT json_array(callsign, name) FROM subscriber_ids WHERE id = dmr_id)
                        FROM last_heard ORDER BY date_time DESC LIMIT ?'''
                
            elif _table == "lstheard_log":
                stm = '''SELECT date_time, qso_time, qso_type, system, tg_num,
                        (SELECT callsign FROM talkgroup_ids WHERE id = tg_num), dmr_id,
                        (SELECT json_array(callsign, name) FROM subscriber_ids WHERE id = dmr_id)
                        FROM lstheard_log ORDER BY date_time DESC LIMIT ?'''

            result = yield self.db.runQuery(stm, (_row_num,))
            if result:                
                tmp_lst = []
                for row in result:
                    if row[7]:
                        r_lst = list(row)
                        r_lst[7] = jloads(row[7])
                        tmp_lst.append(tuple(r_lst))
                    else:
                        tmp_lst.append(row)
                returnValue(tmp_lst)
            else:
                returnValue(None)

        except Exception as err:
            logger.error(f"slct_2render: {err}, {type(err)}")

    @inlineCallbacks
    def clean_table(self, _table, _row_num):
        try:
            if _table == "last_heard":
                stm = '''DELETE FROM last_heard WHERE dmr_id NOT IN 
                        (SELECT dmr_id FROM last_heard ORDER BY date_time DESC LIMIT ?)'''
            elif _table == "lstheard_log":
                stm = '''DELETE FROM lstheard_log WHERE id NOT IN
                        (SELECT id FROM lstheard_log ORDER BY date_time DESC LIMIT ?)'''

            yield self.db.runOperation(stm, (int(_row_num * 1.25),))
            logger.info(f"{_table} DB table cleaned successfully.")

        except Exception as err:
            logger.error(f"clean_tables: {err}, {type(err)}")


if __name__ == '__main__':
    from twisted.internet import reactor

    test_db = MoniDB()
    # Create tables in db
    test_db.create_tables()

    reactor.callLater(5, reactor.stop)
    reactor.run()