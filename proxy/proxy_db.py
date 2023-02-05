#!/usr/bin/env python
#
###############################################################################
#   Copyright (C) 2021-2022 Christian Quiroz, OA4DOA <adm@dmr-peru.pe>
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
import sys

from twisted.enterprise import adbapi
from twisted.internet.defer import inlineCallbacks


__author__     = 'Christian Quiroz, OA4DOA'
__version__    = '1.0.0'
__copyright__  = 'Copyright (c) 2021-2022 Christian Quiroz, OA4DOA'
__license__    = 'GNU GPLv3'
__maintainer__ = 'Christian Quiroz, OA4DOA'
__email__      = 'adm@dmr-peru.pe'


class ProxyDB:
    def __init__(self, host, user, psswd, db_name, port):
        self.db_name = db_name
        self.dbpool = adbapi.ConnectionPool("MySQLdb", host, user, psswd, db_name,
                                            port=port, charset="utf8mb4")

    @inlineCallbacks
    def make_clients_tbl(self):
        try:
            yield self.dbpool.runOperation(
                ''' CREATE TABLE IF NOT EXISTS Clients(
                int_id INT UNIQUE PRIMARY KEY NOT NULL,
                dmr_id TINYBLOB NOT NULL,
                callsign VARCHAR(10) NOT NULL,
                host VARCHAR(15),
                options VARCHAR(100),
                opt_rcvd TINYINT(1) DEFAULT False NOT NULL,
                mode TINYINT(1) DEFAULT 4 NOT NULL,
                logged_in TINYINT(1) DEFAULT False NOT NULL,
                modified TINYINT(1) DEFAULT False NOT NULL,
                psswd BLOB(256),
                last_seen INT NOT NULL) CHARSET=utf8mb4''')

        except Exception as err:
            print(f"make_clientss_tbl error: {err}")

    @inlineCallbacks
    def test_db(self, _reactor):
        try:
            res = yield self.dbpool.runQuery("SELECT 1")
            if res:
                self.updt_tbl("start")
                print("Database connection test: OK")

        except Exception as err:
            if _reactor.running:
                print(f"Database connection error: {err}, stopping the reactor.")
                _reactor.stop()
            else:
                sys.exit(f"Database connection error: {err}, exiting.")

    @inlineCallbacks
    def ins_conf(self, int_id, dmr_id, callsign, host, mode):
        try:
            yield self.dbpool.runOperation(
                '''INSERT IGNORE INTO Clients (
                int_id, dmr_id, callsign, host, mode, logged_in, last_seen, psswd)
                VALUES (%s, %s, %s, %s, %s, True, UNIX_TIMESTAMP(), NULL) ON DUPLICATE KEY UPDATE
                callsign = %s, host = %s, mode = %s, logged_in = True, opt_rcvd = False,
                last_seen = UNIX_TIMESTAMP(), psswd = NULL''',
                (int_id, dmr_id, callsign, host, mode, callsign, host, mode))

        except Exception as err:
            print(f"ins_conf error: {err}")

    @inlineCallbacks
    def clean_tbl(self):
        try:
            yield self.dbpool.runOperation(
                "DELETE FROM Clients WHERE last_seen < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))")

        except Exception as err:
            print(f"clean_tbl error: {err}")

    def slct_db(self):
        return self.dbpool.runQuery(
            "SELECT dmr_id, options FROM Clients WHERE modified = True and logged_in = True")

    def slct_opt(self, _peer_id):
        return self.dbpool.runQuery("SELECT options FROM Clients WHERE dmr_id = %s", (_peer_id,))

    @inlineCallbacks
    def updt_tbl(self, actn, dmr_id=None, psswd=None):
        try:
            if actn == "start":
                yield self.dbpool.runOperation("UPDATE Clients SET logged_in=False, opt_rcvd=False")
            elif actn == "opt_rcvd":
                yield self.dbpool.runOperation(
                    "UPDATE Clients SET opt_rcvd = True, options = NULL WHERE dmr_id = %s",
                    (dmr_id,))
            elif actn == "last_seen":
                yield self.dbpool.runOperation(
                    "UPDATE Clients SET last_seen = UNIX_TIMESTAMP() WHERE dmr_id = %s and logged_in = True",
                    (dmr_id,))
            elif actn == "log_out":
                yield self.dbpool.runOperation(
                    "UPDATE Clients SET logged_in = False, modified = False WHERE dmr_id = %s",
                    (dmr_id,))
            elif actn == "rst_mod":
                yield self.dbpool.runOperation(
                    "UPDATE Clients SET modified = False WHERE dmr_id = %s", (dmr_id,))
            elif actn == "psswd":
                yield self.dbpool.runOperation(
                    "UPDATE Clients SET psswd = %s WHERE dmr_id = %s", (psswd, dmr_id))

        except Exception as err:
            print(f"updt_tbl error: {err}")

    @inlineCallbacks
    def updt_lstseen(self, dmrid_list):
        try:
            def db_actn(txn):
                txn.executemany(
                    "UPDATE Clients SET last_seen = UNIX_TIMESTAMP() WHERE dmr_id = %s", dmrid_list)
            yield self.dbpool.runInteraction(db_actn)

        except Exception as err:
            print(f"updt_lstseen error: {err}")


if __name__ == "__main__":
    db_test = ProxyDB('localhost', 'root', '', 'test', 3306)
    print(db_test)
    