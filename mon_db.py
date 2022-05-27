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
from sys import exit as sys_exit

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
    def __init__(self, host, user, psswd, db_name):
        self.db = adbapi.ConnectionPool("MySQLdb", host, user, psswd, db_name)

    @inlineCallbacks
    def test_db(self, _reactor):
        try:
            res = yield self.db.runQuery("select 1")
            if res:
                logger.info("Database connection test: OK")

        except Exception as err:
            if _reactor.running:
                logger.error(f"Database connection error: {err}, stopping the reactor.")
                _reactor.stop()
            else:
                sys_exit(f"Database connection error: {err}, exiting.")

    @inlineCallbacks
    def create_tables(self):
        try:
            def create_tbl(txn):
                txn.execute(''' CREATE TABLE IF NOT EXISTS Clients(
                            int_id INT UNIQUE PRIMARY KEY NOT NULL,
                            dmr_id TINYBLOB NOT NULL,
                            callsign VARCHAR(10) NOT NULL,
                            host VARCHAR(15),
                            options VARCHAR(250),
                            opt_rcvd TINYINT(1) DEFAULT False NOT NULL,
                            mode TINYINT(1) DEFAULT 4 NOT NULL,
                            logged_in TINYINT(1) DEFAULT False NOT NULL,
                            modified TINYINT(1) DEFAULT False NOT NULL,
                            psswd BLOB(256),
                            last_seen INT NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS talkgroup_ids (
                            id INT PRIMARY KEY UNIQUE NOT NULL, 
                            callsign VARCHAR(255) NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS subscriber_ids (
                            id INT PRIMARY KEY UNIQUE NOT NULL,
                            callsign VARCHAR(255) NOT NULL,
                            name VARCHAR(255) NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS peer_ids (
                            id INT PRIMARY KEY UNIQUE NOT NULL,
                            callsign VARCHAR(255) NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS last_heard (
                            date_time DATETIME NOT NULL,
                            qso_time DECIMAL(5,2),
                            qso_type VARCHAR(20) NOT NULL,
                            system VARCHAR(50) NOT NULL,
                            tg_num INT NOT NULL,
                            dmr_id INT PRIMARY KEY UNIQUE NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS lstheard_log (
                            date_time DATETIME NOT NULL,
                            qso_time DECIMAL(5,2),
                            qso_type VARCHAR(20) NOT NULL,
                            system VARCHAR(50) NOT NULL,
                            tg_num INT NOT NULL,
                            dmr_id INT NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS tg_count (
                            date DATETIME NOT NULL,
                            tg_num INT PRIMARY KEY NOT NULL,
                            qso_count INT NOT NULL,
                            qso_time DECIMAL(5,2) NOT NULL) CHARSET=utf8mb4''')

                txn.execute('''CREATE TABLE IF NOT EXISTS user_count (
                            date DATETIME NOT NULL,
                            tg_num INT NOT NULL,
                            dmr_id INT NOT NULL,
                            qso_time DECIMAL(5,2) NOT NULL,
                            UNIQUE(tg_num, dmr_id)) CHARSET=utf8mb4''')

            yield self.db.runInteraction(create_tbl)
            logger.info("Tables created successfully.")

        except Exception as err:
            logger.error(f"create_tables: {err}.")

    @inlineCallbacks
    def populate_tbl(self, table, lst_data, wipe_tbl, _file):
        try:
            def populate(txn, wipe_tbl):
                if table == "talkgroup_ids":
                    stm = "INSERT IGNORE INTO talkgroup_ids VALUES (%s, %s)"
                    w_stm = "TRUNCATE TABLE talkgroup_ids"
                elif table == "subscriber_ids":
                    stm = stm = "INSERT IGNORE INTO subscriber_ids VALUES (%s, %s, %s)"
                    w_stm = "TRUNCATE TABLE subscriber_ids"
                elif table == "peer_ids":
                    stm = "INSERT IGNORE INTO peer_ids VALUES (%s, %s)"
                    w_stm = "TRUNCATE TABLE peer_ids"

                if wipe_tbl:
                    txn.execute(w_stm)

                txn.executemany(stm, lst_data)
                
                if txn.rowcount > 0:
                    logger.info(f"{txn.rowcount} entries added to: {table} table from: {_file}")

            yield self.db.runInteraction(populate, wipe_tbl)

        except Exception as err:
            logger.error(f"populate_tbl({_file}): {err}.")

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
                "REPLACE INTO last_heard VALUES (now(), %s, %s, %s, %s, %s)",
                (qso_time, qso_type, system, tg_num, dmr_id))

        except Exception as err:
            logger.error(f"ins_lstheard: {err}.")

    @inlineCallbacks
    def ins_lstheard_log(self, qso_time, qso_type, system, tg_num, dmr_id):
        try:
            yield self.db.runOperation(
                '''INSERT INTO lstheard_log (date_time, qso_time, qso_type, system, tg_num, dmr_id)
                VALUES(now(), %s, %s, %s, %s, %s)''',
                (qso_time, qso_type, system, tg_num, dmr_id))

        except Exception as err:
            logger.error(f"ins_lstheard_log: {err}.")

    @inlineCallbacks
    def slct_2dict(self, _id, _table):
        try:
            if _table == "subscriber_ids":
                stm = "SELECT * FROM subscriber_ids WHERE id = %s"
            elif _table == "talkgroup_ids":
                stm = "SELECT * FROM talkgroup_ids WHERE id = %s"

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
                stm = '''SELECT CONVERT(date_time, CHAR), qso_time, qso_type, system, tg_num,
                    (SELECT callsign FROM talkgroup_ids WHERE id = tg_num), dmr_id,
                    (SELECT json_array(callsign, name) FROM subscriber_ids WHERE id = dmr_id)
                    FROM last_heard ORDER BY date_time DESC LIMIT %s'''

            elif _table == "lstheard_log":
                stm = '''SELECT CONVERT(date_time, CHAR), qso_time, qso_type, system, tg_num,
                    (SELECT callsign FROM talkgroup_ids WHERE id = tg_num), dmr_id,
                    (SELECT json_array(callsign, name) FROM subscriber_ids WHERE id = dmr_id)
                    FROM lstheard_log ORDER BY date_time DESC LIMIT %s'''

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
                stm = '''DELETE FROM last_heard WHERE date_time <= (SELECT date_time FROM (
                    SELECT date_time FROM last_heard ORDER BY date_time DESC LIMIT 1 OFFSET %s)
                    foo )'''

            elif _table == "lstheard_log":
                stm = '''DELETE FROM lstheard_log WHERE date_time <= (SELECT date_time FROM (
                    SELECT date_time FROM lstheard_log ORDER BY date_time DESC LIMIT 1 OFFSET %s)
                    foo )'''

            yield self.db.runOperation(stm, (int(_row_num * 1.25),))
            logger.info(f"{_table} DB table cleaned successfully.")

        except Exception as err:
            logger.error(f"clean_tables: {err}.")

    @inlineCallbacks
    def ins_tgcount(self, _tg_num, _dmr_id, _qso_time):
        try:
            def db_actn(txn):
                txn.execute('''INSERT INTO tg_count VALUES (CURDATE(), %s, 1, %s)
                            ON DUPLICATE KEY UPDATE qso_time = qso_time + %s,
                            qso_count = qso_count + 1''', (_tg_num, _qso_time, _qso_time))

                txn.execute('''INSERT INTO user_count VALUES(CURDATE(), %s, %s, %s)
                            ON DUPLICATE KEY UPDATE qso_time = qso_time + %s''',
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
                DESC LIMIT %s''', (_row_num,))
            if rows:
                res_lst = []
                for tg_num, name, qso_c, qso_time in rows:
                    res = yield self.db.runQuery(
                        '''SELECT ifnull(callsign, "N0CALL") FROM user_count 
                        LEFT JOIN subscriber_ids ON subscriber_ids.id = user_count.dmr_id
                        WHERE tg_num = %s ORDER BY qso_time DESC LIMIT 4''', (tg_num,))

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
                "DELETE FROM tg_count WHERE date != CURDATE()")
            yield self.db.runOperation(
                "DELETE FROM user_count WHERE date != CURDATE()")

            logger.info("TG Count tables cleaned successfully")

        except Exception as err:
            logger.error(f"clean_tgcount: {err}.")


if __name__ == '__main__':
    from argparse import ArgumentParser

    from twisted.internet import reactor

    from config import mk_config
    

    logging.basicConfig(level=logging.DEBUG,
                        format='%(asctime)s %(levelname)s %(message)s',
                        datefmt='%Y-%m-%d %H:%M:%S')

    parser = ArgumentParser()
    parser.add_argument("--create",
                        action="store_true", dest="create_tbl",
                        help="Create FDMR Monitor database tables")
    args = parser.parse_args(["--create"])

    if args.create_tbl:
        CONF = mk_config("fdmr-mon.cfg")
        if "DB" not in CONF:
            sys_exit("Not SELF SERVICE stanza on config file")
        # Create an instance of MoniDB
        _db = MoniDB(CONF["DB"]["SERVER"], CONF["DB"]["USER"],
                     CONF["DB"]["PASSWD"], CONF["DB"]["NAME"])
        _db.test_db(reactor)
        # Create tables in db
        reactor.callLater(1, _db.create_tables)
        reactor.callLater(6, reactor.stop)
        reactor.run()
