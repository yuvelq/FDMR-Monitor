#!/usr/bin/env python

###############################################################################
#   Copyright (C) 2022 Christian Quiroz, OA4DOA <adm@dmr-peru.pe>
#
#   This program is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 3 of the License, or
#   (at your option) any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program; if not, write to the Free Software Foundation,
#   Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
###############################################################################

import logging
from json import loads as jloads

from twisted.enterprise import adbapi
from twisted.internet.defer import inlineCallbacks, returnValue

__author__     = 'Christian Quiroz, OA4DOA'
__copyright__  = 'Copyright (c) 2022 Christian Quiroz, OA4DOA'
__license__    = 'GNU GPLv3'
__maintainer__ = 'Christian Quiroz, OA4DOA'
__email__      = 'adm@dmr-peru.pe'

logger = logging.getLogger("fdmr-mon")


# Seconds to a friendly time format
def sec_time(_time):
    _time = int(_time)
    seconds = _time % 60
    minutes = int(_time/60) % 60
    hours = int(_time/60/60) % 24
    if hours:
        return f'{hours}h {minutes}m'
    elif minutes:
        return f'{minutes}m {seconds}s'
    else:
        return f'{seconds}s'


class MoniDB:
    def __init__(self, db_name):
        self.db = adbapi.ConnectionPool("sqlite3", db_name, check_same_thread=False)

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
                            date_time TEXT NOT NULL,
                            qso_time DECIMAL(3,2),
                            qso_type VARCHAR(20) NOT NULL,
                            system VARCHAR(50) NOT NULL,
                            tg_num INT NOT NULL,
                            dmr_id INT NOT NULL)''')

                txn.execute('''CREATE TABLE IF NOT EXISTS tg_count (
                            date TEXT NOT NULL,
                            tg_num INT PRIMARY KEY NOT NULL,
                            qso_count INT NOT NULL,
                            qso_time DECIMAL(4,2) NOT NULL)''')

                txn.execute('''CREATE TABLE IF NOT EXISTS user_count (
                            date TEXT NOT NULL,
                            tg_num INT NOT NULL,
                            dmr_id INT NOT NULL,
                            qso_time DECIMAL(4,2) NOT NULL,
                            UNIQUE(tg_num, dmr_id))''')

            yield self.db.runInteraction(create_tbl)
            logger.info("Tables created successfully.")

        except Exception as err:
            logger.error(f"create_tables: {err}.")

    @inlineCallbacks
    def populate_tbl(self, table, lst_data, wipe_tbl, _file):
        try:
            def populate(txn, wipe_tbl):
                if table == "talkgroup_ids":
                    stm = "INSERT OR IGNORE INTO talkgroup_ids VALUES (?, ?)"
                    w_stm = "DELETE FROM talkgroup_ids"
                elif table == "subscriber_ids":
                    stm = stm = "INSERT OR IGNORE INTO subscriber_ids VALUES (?, ?, ?)"
                    w_stm = "DELETE FROM subscriber_ids"
                elif table == "peer_ids":
                    stm = "INSERT OR IGNORE INTO peer_ids VALUES (?, ?)"
                    w_stm = "DELETE FROM peer_ids"

                if wipe_tbl:
                    txn.execute(w_stm)

                result = txn.executemany(stm, lst_data).rowcount
                if result > 0:
                    logger.info(f"{result} entries added to: {table} table from: {_file}")

            yield self.db.runInteraction(populate, wipe_tbl)

        except Exception as err:
            logger.error(f"populate_tbl: {err}.")

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
            logger.error(f"table_count: {err}.")

    @inlineCallbacks
    def ins_lstheard(self, qso_time, qso_type, system, tg_num, dmr_id):
        try:
            yield self.db.runOperation(
                "INSERT OR REPLACE INTO last_heard VALUES (datetime('now', 'localtime'), ?, ?, ?, ?, ?)",
                (qso_time, qso_type, system, tg_num, dmr_id))

        except Exception as err:
            logger.error(f"ins_lstheard: {err}.")

    @inlineCallbacks
    def ins_lstheard_log(self, qso_time, qso_type, system, tg_num, dmr_id):
        try:
            yield self.db.runOperation(
                '''INSERT INTO lstheard_log (date_time, qso_time, qso_type, system, tg_num, dmr_id)
                VALUES(datetime('now', 'localtime'), ?, ?, ?, ?, ?)''',
                (qso_time, qso_type, system, tg_num, dmr_id))

        except Exception as err:
            logger.error(f"ins_lstheard_log: {err}.")

    @inlineCallbacks
    def slct_2dict(self, _id, _table):
        try:
            if _table == "subscriber_ids":
                stm = "SELECT * FROM subscriber_ids WHERE id = ?"
            elif _table == "talkgroup_ids":
                stm = "SELECT * FROM talkgroup_ids WHERE id = ?"

            result = yield self.db.runQuery(stm, (_id,))
            if result:
                returnValue(result[0])
            else:
                returnValue(None)

        except Exception as err:
            logger.error(f"slct_2dict: {err}.")

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
            tmp_lst = []
            if result:
                for row in result:
                    if row[7]:
                        r_lst = list(row)
                        r_lst[7] = jloads(row[7])
                        tmp_lst.append(tuple(r_lst))
                    else:
                        tmp_lst.append(row)
            returnValue(tmp_lst)
            
        except Exception as err:
            logger.error(f"slct_2render: {err}.")

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
            logger.error(f"clean_tables: {err}.")

    @inlineCallbacks
    def ins_tgcount(self, _tg_num, _dmr_id, _qso_time):
        try:
            def db_actn(txn):
                txn.execute('''INSERT INTO tg_count VALUES (date('now', 'localtime'), ?, 1, ?)
                            ON CONFLICT (tg_num) DO UPDATE SET qso_time = qso_time + ?,
                            qso_count = qso_count + 1''', (_tg_num, _qso_time, _qso_time))

                txn.execute('''INSERT INTO user_count VALUES(date('now', 'localtime'), ?, ?, ?)
                            ON CONFLICT (tg_num, dmr_id) DO UPDATE SET qso_time = qso_time + ?''',
                            (_tg_num, _dmr_id, _qso_time, _qso_time))

            yield self.db.runInteraction(db_actn)

        except Exception as err:
            logger.error(f"ins_tgcount: {err}.")

    @inlineCallbacks
    def slct_tgcount(self, _row_num):
        try:
            rows = yield self.db.runQuery(
                '''SELECT tg_num, ifnull(callsign, ''), qso_count, qso_time FROM tg_count
                LEFT JOIN talkgroup_ids ON talkgroup_ids.id = tg_count.tg_num ORDER BY qso_time
                DESC LIMIT ?''', (_row_num,))
            if rows:
                res_lst = []
                for tg_num, name, qso_c, qso_time in rows:
                    res = yield self.db.runQuery(
                        '''SELECT ifnull(callsign, "N0CALL") FROM user_count 
                        LEFT JOIN subscriber_ids ON subscriber_ids.id = user_count.dmr_id
                        WHERE tg_num = ? ORDER BY qso_time DESC LIMIT 4''', (tg_num,))

                    res_lst.append((tg_num, name, qso_c, sec_time(qso_time), tuple([ite[0] for ite in res])))
                returnValue(res_lst)
            else:
                returnValue(None)

        except Exception as err:
            logger.error(f"slct_tgcount: {err}.")

    @inlineCallbacks
    def clean_tgcount(self):
        try:
            yield self.db.runOperation(
                "DELETE FROM tg_count WHERE date IS NOT date('now', 'localtime')")
            yield self.db.runOperation(
                "DELETE FROM user_count WHERE date IS NOT date('now', 'localtime')")

            logger.info("TG Count tables cleaned successfully")

        except Exception as err:
            logger.error(f"clean_tgcount: {err}.")


if __name__ == '__main__':
    from twisted.internet import reactor

    logging.basicConfig(level=logging.DEBUG,
                        format='%(asctime)s %(levelname)s %(message)s',
                        datefmt='%Y-%m-%d %H:%M:%S')

    # Create an instance of MoniDB
    test_db = MoniDB("mon.db")

    # Create tables
    test_db.create_tables()

    reactor.callLater(5, reactor.stop)
    reactor.run()
