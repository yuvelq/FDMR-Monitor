#!/usr/bin/env python3
###############################################################################
#   Copyright (C) 2016-2019  Cortney T. Buffington, N0MJS <n0mjs@me.com>
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
#
#   Python 3 port by Steve Miller, KC1AWV <smiller@kc1awv.net>
#   HBMonitor v2 (2021) Version by Waldek SP2ONG
#
###############################################################################
#
#  FDMR-Monitor (2021-22) Version by Christian Quiroz OA4DOA <adm@dmr-peru.pe>
#
###############################################################################

# Standard modules
import logging
import sys
from collections import deque
from csv import DictReader as csv_dict_reader
from json import load as jload
from os.path import getmtime
from pathlib import Path
from pickle import  loads
from time import time, strftime, localtime
from datetime import date

# Twisted modules
from twisted.internet.protocol import ReconnectingClientFactory
from twisted.protocols.basic import NetstringReceiver
from twisted.internet import reactor, task
from twisted.internet.threads import deferToThread
from twisted.internet.defer import inlineCallbacks
# Autobahn provides websocket service under Twisted
from autobahn.twisted.websocket import WebSocketServerProtocol, WebSocketServerFactory
# Web templating environment
from jinja2 import Environment, PackageLoader, select_autoescape
# Utilities from K0USY Group sister project
from dmr_utils3.utils import int_id, try_download, bytes_4

# Local modules and config variables
from mon_db import MoniDB
from config import mk_config
from log import create_logger
from const import OPCODE

__version__ = '1.0.0'

# SP2ONG - Increase the value if HBlink link break occurs
NetstringReceiver.MAX_LENGTH = 5000000


# Global Variables:
CONFIG = {}
# Number of rows showed on the lastheard log page
LASTHEARD_LOG_ROWS = 70
CTABLE = {
    "MASTERS": {},
    "PEERS": {},
    "OPENBRIDGES": {},
    "SETUP": {}
    }
BTABLE = {
    "BRIDGES": {},
    "SETUP": {}
    }
BRIDGES = {}
BRIDGES_RX = ""
CONFIG_RX = ""
LOGBUF = deque(100*[""], 100)

GROUPS = {
    "all_clients": {},
    "main": {},
    "bridge": {},
    "lnksys": {},
    "opb": {},
    "statictg":{},
    "log":{},
    "lsthrd_log":{},
    "tgcount":{}
    }

peer_ids = {}
subscriber_ids = {}
talkgroup_ids = {}
not_in_db = []
# Last modified local files
lcl_lstmod = {"peer_ids": None, "subscriber_ids": None, "talkgroup_ids": None}
# Store active queries
act_query = []

TGC_DATE = None

# create empty systems list
sys_dict = {"lst_clean": 0}

# CONSTANTS
SUB_FIELDS   = ("id", "callsign", "fname", "surname", "city", "state", "country")
PEER_FIELDS  = ("id", "call_sign", "city", "state")
TGID_FIELDS  = ("id", "callsign")


# LONG VERSION - MAKES A FULL DICTIONARY OF INFORMATION BASED ON TYPE OF ALIAS FILE
# BASED ON DOWNLOADS FROM RADIOID.NET
# moved from dmr_utils3
def fill_table(_path, _file, _table, wipe_tbl=True):
    temp_lst = []
    try:
        with Path(_path, _file).open("r", encoding="utf8") as _handle:
            if _file.split(".")[1] == "csv":
                if _table == "subscriber_ids":
                    fields = SUB_FIELDS
                elif _table == "peer_ids":
                    fields = PEER_FIELDS
                elif _table == "talkgroup_ids":
                    fields = TGID_FIELDS
                records = csv_dict_reader(
                    _handle, fieldnames=fields, restkey="OTHER", dialect="excel", delimiter=",")

            else:
                records = jload(_handle)
                if "count" in [*records]:
                    records.pop("count")
                records = records[[*records][0]]

            if _table == "peer_ids":
                for record in records:
                    try:
                        temp_lst.append((int(record["id"]), record["callsign"]))

                    except:
                        pass

            elif _table == "subscriber_ids":
                for record in records:
                    # Try to craete a string name regardless of existing data
                    if "surname" in record and "fname"in record:
                        _name = str(record["fname"])
                    elif "fname" in record:
                        _name = str(record["fname"])
                    elif "surname" in record:
                        _name = str(record["surname"])
                    else:
                        _name = "NO NAME"

                    try:
                        temp_lst.append((int(record["id"]), record["callsign"], _name))

                    except:
                        pass

            elif _table == "talkgroup_ids":
                for record in records:
                    try:
                        temp_lst.append((int(record["id"]), record["callsign"]))

                    except:
                        pass

        if temp_lst:
            db_conn.populate_tbl(_table, temp_lst, wipe_tbl, _file)

    except Exception as err:
        logger.error("fill_table error: {err}, {type_}", err=err, type_=type(err))


@inlineCallbacks
# Update DB tables
def update_table(_path, _file, _url, _stale, _table):
    try:
        global not_in_db
        count = yield db_conn.table_count(_table)
        result = yield deferToThread(try_download, _path, _file, _url, _stale)
        if "successfully" in result or count <= 2:
            fill_table(_path, _file, _table)
            not_in_db = []
            lcl_lstmod[_table] = None
            update_local(_table)
        else:
            logger.info('result: {res}', res=result)

    except Exception as err:
        logger.error("update_table: {err}, {type_}", err=err, type_=type(err))


def update_local(_table=None):
    updt_files = []
    if _table == "peer_ids" or not _table and CONF["FILES"]["LCL_PEER"]:
        updt_files.append((CONF["FILES"]["LCL_PEER"], "peer_ids"))
    if _table == "subscriber_ids" or not _table and CONF["FILES"]["LCL_SUBS"]:
        updt_files.append((CONF["FILES"]["LCL_SUBS"], "subscriber_ids"))
    if _table == "talkgroup_ids" or not _table and CONF["FILES"]["LCL_TGID"]:
        updt_files.append((CONF["FILES"]["LCL_TGID"], "talkgroup_ids"))

    for file, tbl in updt_files:
        p2f = Path(CONF["FILES"]["PATH"], file)
        if not p2f.exists() or getmtime(p2f) == lcl_lstmod[tbl]:
            continue
        fill_table(CONF["FILES"]["PATH"], file, tbl, wipe_tbl=False)
        lcl_lstmod[tbl] = getmtime(p2f)


# THESE ARE THE SAME THING FOR LEGACY PURPOSES
# moved from dmr_urils3
def get_alias(_id, _dict, *args):
    if type(_id) == bytes:
        _id = int_id(_id)
    if _id in _dict:
        if args:
            retValue = []
            for _item in args:
                try:
                    retValue.append(_dict[_id][_item])
                except TypeError:
                    return _dict[_id]
            return retValue
        else:
            return _dict[_id]
    return _id


# Alias string processor
def alias_string(_id, _dict):
    alias = get_alias(_id, _dict, "CALLSIGN", "CITY", "STATE")
    if type(alias) == list:
        for x,item in enumerate(alias):
            if item == None:
                alias.pop(x)
        return ", ".join(alias)
    else:
        return alias


def alias_short(_id, _dict):
    alias = get_alias(_id, _dict, "CALLSIGN", "NAME")
    if type(alias) == list:
        for x,item in enumerate(alias):
            if item == None:
                alias.pop(x)
        return ", ".join(alias)
    else:
        return str(alias)


def alias_call(_id, _dict):
    alias = get_alias(_id, _dict, "CALLSIGN")
    if type(alias) == list:
        for x, item in enumerate(alias):
            if item == None:
                alias.pop(x)
        return ", ".join(alias)
    else:
        return str(alias)


def alias_tgid(_id, _dict):
    alias = get_alias(_id, _dict, "NAME")
    if type(alias) == list:
        return str(alias[0])
    else:
        return str(" ")


# Return friendly elapsed time from time in seconds.
def time_str(_time, param):
    now = int(time())
    if param == "since":
        _time = now - int(_time)
    elif param == "to":
        _time = int(_time) - now
    seconds = _time % 60
    minutes = int(_time/60) % 60
    hours = int(_time/60/60) % 24
    days = int(_time/60/60/24)
    if days:
        return f"{days}d {hours}h"
    elif hours:
        return f"{hours}h {minutes}m"
    elif minutes:
        return f"{minutes}m {seconds}s"
    else:
        return f"{seconds}s"


@inlineCallbacks
def db2dict(_id, _table):
    if _table == "subscriber_ids":
        _dict = subscriber_ids
    elif _table == "talkgroup_ids":
        _dict = talkgroup_ids

    if _id in _dict or _id in not_in_db or _id in act_query:
        return None
    act_query.append(_id)

    result = yield db_conn.slct_2dict(_id, _table)
    if result:
        if _table == "subscriber_ids":
            subscriber_ids[result[0]] = {"CALLSIGN": result[1], "NAME": result[2]}

        elif _table == "talkgroup_ids":
            talkgroup_ids[result[0]] = {"NAME": result[1]}
    else:
        not_in_db.append(_id)
    act_query.remove(_id)


def error_hdl(failure):
    # Called when loop execution failed.
    if reactor.running:
        logger.error("Loop error: {err}, stopping the reactor.", err=failure.getBriefTraceback())
        reactor.stop()
    else:
        sys.exit(failure)


##################################################
# Cleaning entries in tables - Timeout (5 min)
#
def cleanTE():
    timeout = time()
    for system in CTABLE["MASTERS"]:
        for peer in CTABLE["MASTERS"][system]["PEERS"]:
            for timeS in (1, 2):
                if CTABLE["MASTERS"][system]["PEERS"][peer][timeS]["TS"]:
                    ts = CTABLE["MASTERS"][system]["PEERS"][peer][timeS]["TIMEOUT"]
                    td = ts - timeout if ts > timeout else timeout - ts
                    td = int(round(abs((td)) / 60))
                    if td > 3:
                        CTABLE["MASTERS"][system]["PEERS"][peer][timeS].update({
                            "TS": False,
                            "TYPE": "",
                            "SUB": "",
                            "SRC": "",
                            "DEST": ""
                            })

    for system in CTABLE["PEERS"]:
        for timeS in (1, 2):
            if CTABLE["PEERS"][system][timeS]["TS"]:
                ts = CTABLE["PEERS"][system][timeS]["TIMEOUT"]
                td = ts - timeout if ts > timeout else timeout - ts
                td = int(round(abs((td)) / 60))
                if td > 3:
                    CTABLE["PEERS"][system][timeS].update({
                        "TS": False,
                        "TYPE": "",
                        "SUB": "",
                        "SRC": "",
                        "DEST": ""
                        })

    for system in CTABLE["OPENBRIDGES"]:
        for streamId in list(CTABLE["OPENBRIDGES"][system]["STREAMS"]):
            ts = CTABLE["OPENBRIDGES"][system]["STREAMS"][streamId][3]
            td = ts - timeout if ts > timeout else timeout - ts
            td = int(round(abs((td)) / 60))
            if td > 3:
                del CTABLE["OPENBRIDGES"][system]["STREAMS"][streamId]


def add_hb_peer(_peer_conf, _ctable_loc, _peer):
    _ctable_loc[int_id(_peer)] = {}
    _ctable_peer = _ctable_loc[int_id(_peer)]

    # if the Frequency is 000.xxx assume it's not an RF peer, otherwise format the text fields
    # (9 char, but we are just software)  see https://wiki.brandmeister.network/index.php/Homebrew/example/php2

    if (_peer_conf["TX_FREQ"].strip().isdigit() and _peer_conf["RX_FREQ"].strip().isdigit() and
        isinstance(_peer_conf["TX_FREQ"], bytes) and isinstance(_peer_conf["RX_FREQ"], bytes)):

        if (_peer_conf["TX_FREQ"][:3] == b"000" or _peer_conf["TX_FREQ"][:1] == b"0" or
            _peer_conf["RX_FREQ"][:3] == b"000" or _peer_conf["RX_FREQ"][:1] == b"0"):
            _ctable_peer["TX_FREQ"] = "N/A"
            _ctable_peer["RX_FREQ"] = "N/A"
        else:
            _ctable_peer["TX_FREQ"] = ''.join(
                (_peer_conf["TX_FREQ"][:3].decode(), ".", _peer_conf["TX_FREQ"][3:7].decode(), " MHz")
                )
            _ctable_peer["RX_FREQ"] = ''.join(
                (_peer_conf["RX_FREQ"][:3].decode(), ".", _peer_conf["RX_FREQ"][3:7].decode(), " MHz")
                )
    else:
        _ctable_peer["TX_FREQ"] = "N/A"
        _ctable_peer["RX_FREQ"] = "N/A"

    # timeslots are kinda complicated too. 0 = none, 1 or 2 mean that one slot,
    # 3 is both, and anything else it considered DMO Slots
    # (0, 1=1, 2=2, 1&2=3 Duplex, 4=Simplex)
    # see https://wiki.brandmeister.network/index.php/Homebrew/example/php2
    if _peer_conf["SLOTS"] == b"0":
        _ctable_peer["SLOTS"] = "NONE"
    elif _peer_conf["SLOTS"] == b"1" or _peer_conf["SLOTS"] == b"2":
        _ctable_peer["SLOTS"] = _peer_conf["SLOTS"].decode()
    elif _peer_conf["SLOTS"] == b"3":
        _ctable_peer["SLOTS"] = "Duplex"
    else:
        _ctable_peer["SLOTS"] = "Simplex"

    # Simple translation items
    if isinstance(_peer_conf["PACKAGE_ID"], bytes):
        _ctable_peer["PACKAGE_ID"] = _peer_conf["PACKAGE_ID"].decode()
    else:
        _ctable_peer["PACKAGE_ID"] = _peer_conf["PACKAGE_ID"]

    if isinstance(_peer_conf["SOFTWARE_ID"], bytes):
        _ctable_peer["SOFTWARE_ID"] = _peer_conf["SOFTWARE_ID"].decode()
    else:
        _ctable_peer["SOFTWARE_ID"] = _peer_conf["SOFTWARE_ID"]

    if isinstance(_peer_conf["LOCATION"], bytes):
        _ctable_peer["LOCATION"] = _peer_conf["LOCATION"].decode().strip()
    else:
        _ctable_peer["LOCATION"] = _peer_conf["LOCATION"]

    if isinstance(_peer_conf["DESCRIPTION"], bytes):
        _ctable_peer["DESCRIPTION"] = _peer_conf["DESCRIPTION"].decode().strip()
    else:
        _ctable_peer["DESCRIPTION"] = _peer_conf["DESCRIPTION"]

    if isinstance(_peer_conf["URL"], bytes):
        _ctable_peer["URL"] = _peer_conf["URL"].decode().strip()
    else:
        _ctable_peer["URL"] = _peer_conf["URL"]

    if isinstance(_peer_conf["CALLSIGN"], bytes):
        _ctable_peer["CALLSIGN"] = _peer_conf["CALLSIGN"].decode().strip()
    else:
        _ctable_peer["CALLSIGN"] = _peer_conf["CALLSIGN"]

    if isinstance(_peer_conf["COLORCODE"], bytes):
        _ctable_peer["COLORCODE"] = _peer_conf["COLORCODE"].decode().strip()
    else:
        _ctable_peer["COLORCODE"] = _peer_conf["COLORCODE"]

    _ctable_peer["CONNECTION"] = _peer_conf["CONNECTION"]
    _ctable_peer["CONNECTED"] = time_str(_peer_conf["CONNECTED"], "since")

    _ctable_peer["IP"] = _peer_conf["IP"]
    _ctable_peer["PORT"] = _peer_conf["PORT"]

    # SLOT 1&2 - for real-time montior: make the structure for later use
    for ts in (1, 2):
        _ctable_peer[ts]= {
            "TS": "",
            "TYPE": "",
            "SUB": "",
            "SRC": "",
            "DEST": "",
            }

###############################################################################
#
# Build the HBlink connections table
#
def build_hblink_table(_config, _stats_table):
    for _hbp, _hbp_data in list(_config.items()):
        if _hbp_data["ENABLED"] == True:
            # Process Master Systems
            if _hbp_data["MODE"] == "MASTER":
                _stats_table["MASTERS"][_hbp] = {}
                if _hbp_data["REPEAT"]:
                    _stats_table["MASTERS"][_hbp]["REPEAT"] = "repeat"
                else:
                    _stats_table["MASTERS"][_hbp]["REPEAT"] = "isolate"
                _stats_table["MASTERS"][_hbp]["PEERS"] = {}
                for _peer in _hbp_data["PEERS"]:
                    add_hb_peer(_hbp_data["PEERS"][_peer], _stats_table["MASTERS"][_hbp]["PEERS"], _peer)

            # Process Peer Systems
            elif _hbp_data["MODE"] in ("XLXPEER", "PEER") and CONF["GLOBAL"]["HB_INC"]:
                _stats_table["PEERS"][_hbp] = {"MODE": _hbp_data["MODE"]}

                if isinstance(_hbp_data["LOCATION"], bytes):
                    _stats_table["PEERS"][_hbp]["LOCATION"] = _hbp_data["LOCATION"].decode().strip()
                else:
                    _stats_table["PEERS"][_hbp]["LOCATION"] = _hbp_data["LOCATION"]

                if isinstance(_hbp_data["DESCRIPTION"], bytes):
                    _stats_table["PEERS"][_hbp]["DESCRIPTION"] = _hbp_data["DESCRIPTION"].decode().strip()
                else:
                    _stats_table["PEERS"][_hbp]["DESCRIPTION"] = _hbp_data["DESCRIPTION"]

                if isinstance(_hbp_data["URL"], bytes):
                    _stats_table["PEERS"][_hbp]["URL"] = _hbp_data["DESCRIPTION"].decode().strip()
                else:
                    _stats_table["PEERS"][_hbp]["URL"] = _hbp_data["DESCRIPTION"]

                if isinstance(_hbp_data["CALLSIGN"], bytes):
                    _stats_table["PEERS"][_hbp]["CALLSIGN"] = _hbp_data["CALLSIGN"].decode().strip()
                else:
                    _stats_table["PEERS"][_hbp]["CALLSIGN"] = _hbp_data["CALLSIGN"]

                _stats_table["PEERS"][_hbp].update({
                    "RADIO_ID": int_id(_hbp_data["RADIO_ID"]),
                    "MASTER_IP": _hbp_data["MASTER_IP"],
                    "MASTER_PORT": _hbp_data["MASTER_PORT"],
                    "STATS": {}
                    })

                if _stats_table["PEERS"][_hbp]["MODE"] == "XLXPEER":
                    _stats_table["PEERS"][_hbp]["STATS"]["CONNECTION"] = _hbp_data["XLXSTATS"]["CONNECTION"]

                    if _hbp_data["XLXSTATS"]["CONNECTION"] == "YES":
                        _stats_table["PEERS"][_hbp]["STATS"].update({
                            "CONNECTED": time_str(_hbp_data["XLXSTATS"]["CONNECTED"],"since"),
                            "PINGS_SENT": _hbp_data["XLXSTATS"]["PINGS_SENT"],
                            "PINGS_ACKD": _hbp_data["XLXSTATS"]["PINGS_ACKD"]
                            })
                    else:
                        _stats_table["PEERS"][_hbp]["STATS"].update({
                            "CONNECTED": "--   --",
                            "PINGS_SENT": 0,
                            "PINGS_ACKD": 0
                            })
                else:
                    _stats_table["PEERS"][_hbp]["STATS"]["CONNECTION"] = _hbp_data["STATS"]["CONNECTION"]
                    if _hbp_data["STATS"]["CONNECTION"] == "YES":
                        _stats_table["PEERS"][_hbp]["STATS"].update({
                            "CONNECTED": time_str(_hbp_data["STATS"]["CONNECTED"], "since"),
                            "PINGS_SENT": _hbp_data["STATS"]["PINGS_SENT"],
                            "PINGS_ACKD": _hbp_data["STATS"]["PINGS_ACKD"]
                            })
                    else:
                        _stats_table["PEERS"][_hbp]["STATS"].update({
                            "CONNECTED": "--   --",
                            "PINGS_SENT": 0,
                            "PINGS_ACKD": 0
                            })

                if _hbp_data["SLOTS"] == b"0":
                    _stats_table["PEERS"][_hbp]["SLOTS"] = "NONE"

                elif _hbp_data["SLOTS"] in (b"1", b"2"):
                    _stats_table["PEERS"][_hbp]["SLOTS"] = _hbp_data["SLOTS"].decode()

                elif _hbp_data["SLOTS"] == b"3":
                    _stats_table["PEERS"][_hbp]["SLOTS"] = "1&2"

                else:
                    _stats_table["PEERS"][_hbp]["SLOTS"] = "DMO"

                # SLOT 1&2 - for real-time montior: make the structure for later use
                for ts in range(1,3):
                    _stats_table["PEERS"][_hbp][ts]= {
                        "COLOR": "",
                        "BGCOLOR": "",
                        "TS": "",
                        "TYPE": "",
                        "SUB": "",
                        "SRC": "",
                        "DEST": ""
                        }

            # Process OpenBridge systems
            elif _hbp_data["MODE"] == "OPENBRIDGE":
                _stats_table["OPENBRIDGES"][_hbp] = {
                    "NETWORK_ID": int_id(_hbp_data["NETWORK_ID"]),
                    "TARGET_IP": _hbp_data["TARGET_IP"],
                    "TARGET_PORT": _hbp_data["TARGET_PORT"],
                    "STREAMS": {}
                    }


def update_hblink_table(_config, _stats_table):
    # Is there a system in HBlink's config monitor doesn't know about?
    for _hbp in _config:
        if _config[_hbp]["MODE"] == "MASTER":
            for _peer in _config[_hbp]["PEERS"]:
                if int_id(_peer) not in _stats_table["MASTERS"][_hbp]["PEERS"] and _config[_hbp]["PEERS"][_peer]["CONNECTION"] == "YES":
                    logger.info("Adding peer to CTABLE that has registerred: {peer}", peer=int_id(_peer))
                    add_hb_peer(_config[_hbp]["PEERS"][_peer], _stats_table["MASTERS"][_hbp]["PEERS"], _peer)

    # Is there a system in monitor that's been removed from HBlink's config?
    for _hbp in _stats_table["MASTERS"]:
        remove_list = []
        if _config[_hbp]["MODE"] == "MASTER":
            for _peer in _stats_table["MASTERS"][_hbp]["PEERS"]:
                if bytes_4(_peer) not in _config[_hbp]["PEERS"]:
                    remove_list.append(_peer)

            for _peer in remove_list:
                logger.info("Deleting stats peer not in hblink config: {peer}", peer=_peer)
                del (_stats_table["MASTERS"][_hbp]["PEERS"][_peer])
    # Update connection time
    for _hbp in _stats_table["MASTERS"]:
        for _peer in _stats_table["MASTERS"][_hbp]["PEERS"]:
            if bytes_4(_peer) in _config[_hbp]["PEERS"]:
                _stats_table["MASTERS"][_hbp]["PEERS"][_peer]["CONNECTED"] = time_str(
                    _config[_hbp]["PEERS"][bytes_4(_peer)]["CONNECTED"],"since")

    for _hbp in _stats_table["PEERS"]:
        if _stats_table["PEERS"][_hbp]["MODE"] == "XLXPEER":
            if _config[_hbp]["XLXSTATS"]["CONNECTION"] == "YES":
                _stats_table["PEERS"][_hbp]["STATS"].update({
                    "CONNECTED": time_str(_config[_hbp]["XLXSTATS"]["CONNECTED"],"since"),
                    "CONNECTION": _config[_hbp]["XLXSTATS"]["CONNECTION"],
                    "PINGS_SENT": _config[_hbp]["XLXSTATS"]["PINGS_SENT"],
                    "PINGS_ACKD": _config[_hbp]["XLXSTATS"]["PINGS_ACKD"]
                    })
            else:
                _stats_table["PEERS"][_hbp]["STATS"].upadte({
                    "CONNECTED": "--   --",
                    "CONNECTION": _config[_hbp]["XLXSTATS"]["CONNECTION"],
                    "PINGS_SENT": 0,
                    "PINGS_ACKD": 0
                    })
        else:
            if _config[_hbp]["STATS"]["CONNECTION"] == "YES":
                _stats_table["PEERS"][_hbp]["STATS"].update({
                    "CONNECTED": time_str(_config[_hbp]["STATS"]["CONNECTED"],"since"),
                    "CONNECTION": _config[_hbp]["STATS"]["CONNECTION"],
                    "PINGS_SENT": _config[_hbp]["STATS"]["PINGS_SENT"],
                    "PINGS_ACKD": _config[_hbp]["STATS"]["PINGS_ACKD"]
                    })
            else:
                _stats_table["PEERS"][_hbp]["STATS"].update({
                    "CONNECTED": "--   --",
                    "CONNECTION": _config[_hbp]["STATS"]["CONNECTION"],
                    "PINGS_SENT": 0,
                    "PINGS_ACKD": 0
                    })
    cleanTE()
    build_stats()


######################################################################
#
# CONFBRIDGE TABLE FUNCTIONS
#
def build_bridge_table(_bridges):
    _stats_table = {}
    _now = time()
    _cnow = strftime("%Y-%m-%d %H:%M:%S", localtime(_now))

    for _bridge, _bridge_data in list(_bridges.items()):
        _stats_table[_bridge] = {}

        for system in _bridges[_bridge]:
            _stats_table[_bridge][system["SYSTEM"]] = {
                "TS": system["TS"],
                "TGID": int_id(system["TGID"])
                }

            if system["TO_TYPE"] == "ON" or system["TO_TYPE"] == "OFF":
                if system["TIMER"] - _now > 0:
                    _stats_table[_bridge][system["SYSTEM"]]["EXP_TIME"] = int(system["TIMER"] - _now)
                else:
                    _stats_table[_bridge][system["SYSTEM"]]["EXP_TIME"] = "Expired"
                if system["TO_TYPE"] == "ON":
                    _stats_table[_bridge][system["SYSTEM"]]["TO_ACTION"] = "Disconnect"
                else:
                    _stats_table[_bridge][system["SYSTEM"]]["TO_ACTION"] = "Connect"
            else:
                _stats_table[_bridge][system["SYSTEM"]]["EXP_TIME"] = "N/A"
                _stats_table[_bridge][system["SYSTEM"]]["TO_ACTION"] = "None"

            if system["ACTIVE"] == True:
                _stats_table[_bridge][system["SYSTEM"]]["ACTIVE"] = "Connected"

            elif system["ACTIVE"] == False:
                _stats_table[_bridge][system["SYSTEM"]]["ACTIVE"] = "Disconnected"

            for i in range(len(system["ON"])):
                system["ON"][i] = str(int_id(system["ON"][i]))

            _stats_table[_bridge][system["SYSTEM"]]["TRIG_ON"] = ", ".join(system["ON"])

            for i in range(len(system["OFF"])):
                system["OFF"][i] = str(int_id(system["OFF"][i]))

            _stats_table[_bridge][system["SYSTEM"]]["TRIG_OFF"] = ", ".join(system["OFF"])
    return _stats_table


######################################################################
#
# BUILD HBlink AND CONFBRIDGE TABLES FROM CONFIG/BRIDGES DICTS
#          THIS CURRENTLY IS A TIMED CALL
#
build_time = 0
build_deferred = None
def build_stats():
    global build_time, build_deferred
    if time() - build_time < 0.5:
        if not build_deferred or build_deferred.called or build_deferred.cancelled:
            build_deferred = reactor.callLater(0.5, build_stats)
        else:
            build_deferred.reset(0.5)
        return
    else:
        if build_deferred and not build_deferred.called and not build_deferred.cancelled:
            build_deferred.cancel()

    if CONFIG:
        if GROUPS["main"]:
            render_fromdb("last_heard", CONF["GLOBAL"]["LH_ROWS"])
        if GROUPS["lnksys"]:
            lnksys = "c" + ctemplate.render(_table=CTABLE, emaster=CONF["GLOBAL"]["EMPTY_MASTERS"])
            dashboard_server.broadcast(lnksys, "lnksys")
        if GROUPS["opb"]:
            opb = "o" + otemplate.render(_table=CTABLE)
            dashboard_server.broadcast(opb, "opb")
        if GROUPS["statictg"]:
            statictg = "s" + stemplate.render(_table=CTABLE, emaster=CONF["GLOBAL"]["EMPTY_MASTERS"])
            dashboard_server.broadcast(statictg, "statictg")
        if GROUPS["lsthrd_log"]:
            render_fromdb("lstheard_log", LASTHEARD_LOG_ROWS)

    if BRIDGES and CONF["GLOBAL"]["BRDG_INC"]:
        if GROUPS["bridge"]:
            bridges = "b" + btemplate.render(_table=BTABLE, dbridges=CONF["GLOBAL"]["BRDG_INC"])
            dashboard_server.broadcast(bridges, "bridge")
    build_time = time()


@inlineCallbacks
def render_fromdb(_tbl, _row_num, _snd=False):
    try:
        if _tbl in ("last_heard", "lstheard_log"):
            result = yield db_conn.slct_2render(_tbl, _row_num)
        elif _tbl == "tgcount":
            result = yield db_conn.slct_tgcount(_row_num)
        if result:
            if not _snd:
                if _tbl == "last_heard":
                    main = "i" + itemplate.render(_table=CTABLE, lastheard=result)
                    dashboard_server.broadcast(main, "main")

                elif _tbl == "lstheard_log":
                    lsth_log = "h" + htemplate.render(_table=result)
                    dashboard_server.broadcast(lsth_log, "lsthrd_log")

                elif _tbl == "tgcount" and GROUPS["tgcount"]:
                    tgcount = "t" + ttemplate.render(_table=result)
                    dashboard_server.broadcast(tgcount, "tgcount")

            else:
                if _tbl == "last_heard":
                    _snd.sendMessage(
                        ("i" + itemplate.render(_table=CTABLE, lastheard=result)).encode())

                elif _tbl == "lstheard_log":
                    _snd.sendMessage(("h" + htemplate.render(_table=result)).encode())

                elif _tbl == "tgcount":
                    _snd.sendMessage(("t" + ttemplate.render(_table=result)).encode())

    except Exception as err:
        logger.error("render_fromdb: {err}, {type_}", err=err, type_=type(err))


def build_tgstats():
    if CONFIG and CTABLE:
        CTABLE["SERVER"] ={"TS1":[],"TS2":[]}
        tmp_dict = {}
        srv_info = 0
        # make a list with systems in use
        for system in CTABLE["MASTERS"]:
            if not CTABLE["MASTERS"][system]["PEERS"]:
                continue
            for peer in CTABLE["MASTERS"][system]["PEERS"]:
                if system not in tmp_dict:
                    tmp_dict[system] = {'peers': [peer], 1: [], 2: []} 
                else:
                    tmp_dict[system]['peers'].append(peer)

        # Get the static TG of the server
        for system in CONFIG:
            if system not in tmp_dict:
                continue
            if not srv_info and "_default_options" in CONFIG[system]:
                CTABLE["SERVER"]["SINGLE_MODE"] = CONFIG[system]["SINGLE_MODE"]
                for item in CONFIG[system]["_default_options"].split(";")[:2]:
                    if len(item) > 11 and item.startswith("TS1_STATIC="):
                        CTABLE["SERVER"]["TS1"] = item[11:].split(",")
                    if len(item) > 11 and item.startswith("TS2_STATIC="):
                        CTABLE["SERVER"]["TS2"] = item[11:].split(",")
                srv_info = 1

            for peer in CTABLE["MASTERS"][system]["PEERS"]:
                CTABLE["MASTERS"][system]["PEERS"][peer]["SINGLE_TS1"] = {"TGID": "", "TO": ""}
                CTABLE["MASTERS"][system]["PEERS"][peer]["SINGLE_TS2"] = {"TGID": "", "TO": ""}
                if isinstance(CONFIG[system]["TS1_STATIC"], bool):
                    CTABLE["MASTERS"][system]["PEERS"][peer]["TS1_STATIC"] = []
                else:
                    split_tgs = CONFIG[system]["TS1_STATIC"].split(",")
                    CTABLE["MASTERS"][system]["PEERS"][peer]["TS1_STATIC"] = split_tgs
                    tmp_dict[system][1].extend(split_tgs) 

                if isinstance(CONFIG[system]["TS2_STATIC"], bool):
                    CTABLE["MASTERS"][system]["PEERS"][peer]["TS2_STATIC"] = []
                else:
                    split_tgs = CONFIG[system]["TS2_STATIC"].split(",")
                    CTABLE["MASTERS"][system]["PEERS"][peer]["TS2_STATIC"] = split_tgs
                    tmp_dict[system][2].extend(split_tgs) 

    # Find Single TG
    if CTABLE and BRIDGES and tmp_dict:
        for bridge in BRIDGES:
            for system in BRIDGES[bridge]:
                if not system["ACTIVE"] or system["SYSTEM"][:3] == "OBP" or system["TO_TYPE"] == "OFF":
                    continue

                if system["SYSTEM"] in tmp_dict and str(int_id(system["TGID"])) not in tmp_dict[system["SYSTEM"]][system["TS"]]:
                    for peer in CTABLE["MASTERS"][system["SYSTEM"]]["PEERS"]:
                        CTABLE["MASTERS"][system["SYSTEM"]]["PEERS"][peer]["SINGLE_TS"+str(system["TS"])] = {
                            "TGID": int_id(system["TGID"]), "TO": ''
                            }


def timeout_clients():
    now = time()
    try:
        for group in dashboard_server.clients:
            for client in dashboard_server.clients[group]:
                if dashboard_server.clients[group][client] + CONF["WS"]["CLT_TO"] < now:
                    logger.info("TIMEOUT: disconnecting client {client_}", client_=dashboard_server.clients[client])
                    try:
                        dashboard.sendClose(client)
                    except Exception as err:
                        logger.error("Exception caught parsing client timeout {err}", err=err)
    except:
        logger.info("CLIENT TIMEOUT: List does not exist, skipping. If this message persists, contact the developer")


def rts_update(p):
    callType = p[0]
    action = p[1]
    trx = p[2]
    system = p[3]
    streamId = p[4]
    sourcePeer = int(p[5])
    sourceSub = int(p[6])
    timeSlot = int(p[7])
    destination = int(p[8])
    timeout = time()
    if system in CTABLE["MASTERS"]:
        for peer in CTABLE["MASTERS"][system]["PEERS"]:
            tgt_peer = CTABLE["MASTERS"][system]["PEERS"][peer][timeSlot]
            if sourcePeer == peer:
                crxstatus = "RX"
            else:
                crxstatus = "TX"
            if action == "START":
                tgt_peer.update({
                    "TIMEOUT": timeout,
                    "TS": True,
                    "TYPE": callType,
                    "SUB": f"{alias_short(sourceSub, subscriber_ids)} ({sourceSub})",
                    "CALL": f"{alias_call(sourceSub, subscriber_ids)}",
                    "SRC": peer,
                    "DEST": f"TG {destination}&nbsp;&nbsp;&nbsp;&nbsp;{alias_tgid(destination,talkgroup_ids)}",
                    "TG": f"TG&nbsp;{destination}",
                    "TRX": crxstatus
                    })

            if action == "END":
                tgt_peer.update({
                    "TS": False,
                    "TYPE": "",
                    "SUB": "",
                    "CALL": "",
                    "SRC": "",
                    "DEST": "",
                    "TG": "",
                    "TRX": ""
                    })

    if system in CTABLE["OPENBRIDGES"]:
        if action == "START":
            CTABLE["OPENBRIDGES"][system]["STREAMS"][streamId] = (
                trx, alias_call(sourceSub, subscriber_ids),f"{destination}",timeout
                )

        if action == "END":
            if streamId in CTABLE["OPENBRIDGES"][system]["STREAMS"]:
                del CTABLE["OPENBRIDGES"][system]["STREAMS"][streamId]

    if system in CTABLE["PEERS"]:
        tgt_system =  CTABLE["PEERS"][system][timeSlot]
        if trx == "RX":
            prxstatus = "RX"
        else:
            prxstatus = "TX"

        if action == "START":
            tgt_system.update({
                "TIMEOUT": timeout,
                "TS": True,
                "SUB": f"{alias_short(sourceSub, subscriber_ids)} ({sourceSub})",
                "CALL": f"{alias_call(sourceSub, subscriber_ids)}",
                "SRC": sourcePeer,
                "DEST": f"TG {destination}&nbsp;&nbsp;&nbsp;&nbsp;{alias_tgid(destination,talkgroup_ids)}",
                "TG": f"TG&nbsp;{destination}",
                "TRX": prxstatus
                })

        if action == "END":
            tgt_system.update({
                "TS": False,
                "TYPE": "",
                "SUB": "",
                "CALL": "",
                "SRC": "",
                "DEST": "",
                "TG": "",
                "TRX": ""
                })

    build_stats()


######################################################################
#
# PROCESS INCOMING MESSAGES AND TAKE THE CORRECT ACTION DEPENING ON
#    THE OPCODE
#
def process_message(_bmessage):
    global CTABLE, CONFIG, BRIDGES, CONFIG_RX, BRIDGES_RX
    _message = _bmessage.decode("utf-8", "ignore")
    opcode = _message[:1]
    _now = strftime("%Y-%m-%d %H:%M:%S %Z", localtime(time()))

    if opcode == OPCODE["CONFIG_SND"]:
        logger.debug("got CONFIG_SND opcode")
        CONFIG = load_dictionary(_bmessage)
        CONFIG_RX = strftime("%Y-%m-%d %H:%M:%S", localtime(time()))
        if CTABLE["MASTERS"]:
            update_hblink_table(CONFIG, CTABLE)
        else:
            build_hblink_table(CONFIG, CTABLE)

    elif opcode == OPCODE["BRIDGE_SND"]:
        logger.debug("got BRIDGE_SND opcode")
        BRIDGES = load_dictionary(_bmessage)
        BRIDGES_RX = strftime("%Y-%m-%d %H:%M:%S", localtime(time()))
        if CONF["GLOBAL"]["BRDG_INC"]:
            BTABLE["BRIDGES"] = build_bridge_table(BRIDGES)
        build_tgstats()

    elif opcode == OPCODE["LINK_EVENT"]:
        logger.info("LINK_EVENT Received: {msg}", msg=_message[1:])

    elif opcode == OPCODE["BRDG_EVENT"]:
        logger.debug("BRIDGE EVENT: {msg}", msg=_message[1:])
        p = _message[1:].split(",")
        # Import data from DB
        db2dict(int(p[6]), "subscriber_ids")
        db2dict(int(p[8]), "talkgroup_ids")
        if p[0] == "GROUP VOICE":
            rts_update(p)
            if p[2] == "TX" or p[5] in CONF["OPB_FLTR"]["OPB_FILTER"]:
                return None

            if p[1] == "END" and p[4] in sys_dict and sys_dict[p[4]]["sys"] == p[3]:
                del sys_dict[p[4]]
                logger.info("BRIDGE EVENT: {msg}", msg=_message[1:])
                log_message = (
                    f"{_now[10:19]} {p[0][6:]:5.5s} {p[1]:5.5s} SYS: {p[3]:10.10s} SRC_ID: {p[5]:5.5s} "
                    f"TS: {p[7]} TGID: {p[8]:7.7s} {alias_tgid(int(p[8]), talkgroup_ids):17.17s} "
                    f"SUB: {p[6]:9.9s}; {alias_short(int(p[6]), subscriber_ids):18.18s} "
                    f"Time: {int(float(p[9]))}s")

                if CONF["GLOBAL"]["TGC_INC"] and int(float(p[9])) > 5:
                    # Get data for TG Count
                    db_conn.ins_tgcount(p[8], p[6], p[9])

                if CONF["GLOBAL"]["LH_INC"]:
                    # Insert voice qso into lstheard_log DB table
                    db_conn.ins_lstheard_log(p[9], p[0], p[3], p[8], p[6])
                    # use >= 0 instead of > 2 if you want to record all activities
                    if int(float(p[9])) > 2:
                        # Insert voice qso into lstheard DB table
                        db_conn.ins_lstheard(p[9], p[0], p[3], p[8], p[6])

                # Removing obsolete entries from the sys_dict (3 sec)
                if time() - sys_dict["lst_clean"] >= 3:
                    sys_dict["lst_clean"] = time()
                    for k, v in list(sys_dict.items()):
                        if k == "lst_clean":
                            continue
                        if time() - v["timeST"] >= 3:
                            del sys_dict[k]

            elif p[1] == "START":
                logger.info("BRIDGE EVENT: {msg}", msg=_message[1:])
                log_message = (
                    f"{_now[10:19]} {p[0][6:]:5.5s} {p[1]:5.5s} SYS: {p[3]:10.10s} SRC_ID: {p[5]:5.5s} "
                    f"TS: {p[7]} TGID: {p[8]:7.7s} {alias_tgid(int(p[8]), talkgroup_ids):17.17s} "
                    f"SUB: {p[6]:9.9s}; {alias_short(int(p[6]), subscriber_ids):18.18s}")

                sys_dict[p[4]] = {"sys": p[3], "timeST": time()}

            elif p[1] == "END":
                log_message = (
                    f"{_now[10:19]} {p[0][6:]:5.5s} {p[1]:5.5s} SYS: {p[3]:10.10s} SRC_ID: {p[5]:5.5s} "
                    f"TS: {p[7]} TGID: {p[8]:7.7s} {alias_tgid(int(p[8]), talkgroup_ids):17.17s} "
                    f"SUB: {p[6]:9.9s}; {alias_short(int(p[6]), subscriber_ids):18.18s} "
                    f"Time: {int(float(p[9]))}s")

            elif p[1] == "END WITHOUT MATCHING START":
                log_message = (
                    f"{_now[10:19]} {p[0][6:]:5.5s} {p[1]:5.5s} on SYSTEM: {p[3]:10.10s} SRC_ID: {p[5]:5.5s} "
                    f"TS: {p[7]} TGID: {p[8]:7.7s} {alias_tgid(int(p[8]), talkgroup_ids):17.17s} "
                    f"SUB: { p[6]:9.9s}; {alias_short(int(p[6]), subscriber_ids):18.18s}")

            else:
                log_message = f"{_now[10:19]} Unknown GROUP VOICE log message."

            dashboard_server.broadcast("l" + log_message, "log")
            LOGBUF.append(log_message)

        elif p[0] == "UNIT DATA HEADER" and p[2] != "TX" and p[5] not in CONF["OPB_FLTR"]["OPB_FILTER"]:
            logger.info("BRIDGE EVENT: {msg}", msg=_message[1:])
            # Insert data qso into lstheard DB table
            db_conn.ins_lstheard(None, p[0], p[3], p[8], p[6])
            # Insert data qso into lstheard_log DB table
            db_conn.ins_lstheard_log(None, p[0], p[3], p[8], p[6])

        else:
            logger.warn("Unknown log message: {msg}", msg=_message)

    elif opcode == OPCODE["SERVER_MSG"]:
        logger.debug("SERVER MSG: {msg}", msg=_message)

    else:
        logger.warn("got unknown opcode: {code}, message: {msg}", code=repr(opcode), msg=_message)


def load_dictionary(_message):
    data = _message[1:]
    logger.debug("Successfully decoded dictionary")
    return loads(data)


######################################################################
#
# COMMUNICATION WITH THE HBlink INSTANCE
#

class report(NetstringReceiver):
    def __init__(self):
        pass

    def connectionMade(self):
        pass

    def connectionLost(self, reason):
        pass

    def stringReceived(self, data):
        process_message(data)


class reportClientFactory(ReconnectingClientFactory):
    def __init__(self):
        logger.info("reportClient object for connecting to HBlink.py created at: {s}", s=self)

    def startedConnecting(self, connector):
        logger.info("Initiating Connection to Server.")
        if "dashboard_server" in locals() or "dashboard_server" in globals():
            dashboard_server.broadcast("q" + "Connection to HBlink Established", "all_clients")

    def buildProtocol(self, addr):
        logger.info("Connected, resetting connection delay")
        self.resetDelay()
        return report()

    def clientConnectionLost(self, connector, reason):
        CTABLE["MASTERS"].clear()
        CTABLE["PEERS"].clear()
        CTABLE["OPENBRIDGES"].clear()
        BTABLE["BRIDGES"].clear()
        logger.info("Lost connection.  Reason: {r}", r=reason)
        ReconnectingClientFactory.clientConnectionLost(self, connector, reason)
        dashboard_server.broadcast("q" + "Connection to HBlink Lost", "all_clients")

    def clientConnectionFailed(self, connector, reason):
        logger.info("Connection failed. Reason: {r}", r=reason)
        ReconnectingClientFactory.clientConnectionFailed(self, connector, reason)

######################################################################
#
# WEBSOCKET COMMUNICATION WITH THE DASHBOARD CLIENT
#

class dashboard(WebSocketServerProtocol):

    def onConnect(self, request):
        logger.info("Client connecting: {client}", client=request.peer)

    def onOpen(self):
        logger.info("WebSocket connection open.")

    def onMessage(self, payload, isBinary):
        if isBinary:
            logger.info("Binary message received: {len_} bytes", len_=len(payload))
        else:
            msg = payload.decode().split(",")
            logger.info("Text message received: {pay}", pay=payload)
            if msg[0] != "conf":
                return
            for group in msg[1:]:
                if group not in GROUPS:
                    continue
                self.factory.register(self, group)
                if group == "bridge":
                    if BRIDGES and CONF["GLOBAL"]["BRDG_INC"]:
                        self.sendMessage(
                            ("b" + btemplate.render(
                                _table=BTABLE,dbridges=CONF["GLOBAL"]["BRDG_INC"])).encode())
                elif group == "lnksys":
                    self.sendMessage(
                        ("c" + ctemplate.render(
                            _table=CTABLE,emaster=CONF["GLOBAL"]["EMPTY_MASTERS"])).encode())
                elif group == "opb":
                    self.sendMessage(
                        ("o" + otemplate.render(_table=CTABLE)).encode())
                elif group == "main":
                    render_fromdb("last_heard", CONF["GLOBAL"]["LH_ROWS"], self)
                elif group == "statictg":
                    self.sendMessage(
                        ("s" + stemplate.render(
                            _table=CTABLE, emaster=CONF["GLOBAL"]["EMPTY_MASTERS"])).encode())
                elif group == "lsthrd_log":
                    render_fromdb("lstheard_log", LASTHEARD_LOG_ROWS, self)
                elif group == "tgcount" and CONF["GLOBAL"]["TGC_INC"]:
                    render_fromdb("tgcount", CONF["GLOBAL"]["TGC_ROWS"], self)
                elif group == "log":
                    for _message in LOGBUF:
                        if _message:
                            _bmessage = ("l" + _message).encode()
                            self.sendMessage(_bmessage)

    def onClose(self, wasClean, code, reason):
        self.factory.unregister(self)
        logger.info("WebSocket connection closed: {r}", r=reason)


class dashboardFactory(WebSocketServerFactory):
    def __init__(self, url):
        WebSocketServerFactory.__init__(self, url)
        self.clients = GROUPS

    def register(self, client, group):
        if client not in self.clients[group]:
            self.clients[group][client] = time()
            logger.info("registered client {client} to group {_group}", client=client.peer, _group=group)
        if client not in self.clients["all_clients"]:
            self.clients["all_clients"][client] = time()

    def unregister(self, client):
        logger.info("unregistered client {client}", client=client.peer)
        for group in self.clients:
            if client in self.clients[group]:
                del self.clients[group][client]

    def broadcast(self, msg, group):
        logger.debug("broadcasting message to: {cients}", clients=self.clients[group])
        for client in self.clients[group]:
            client.sendMessage(msg.encode("utf8"))
            logger.debug("message sent to {client}", client=client.peer)


@inlineCallbacks
# Show the number of entries in the DB tables
def count_db_entries():
    try:
        for tbl in ("peer_ids", "talkgroup_ids", "subscriber_ids"):
            result = yield db_conn.table_count(tbl)
            if result:
                logger.info("{t} entries: {r}", t=tbl, r=result)

    except Exception as err:
        logger.error("count_db_entries: {err}, {type_}", err=err, type_=type(err))


def clean_tgcount():
    global TGC_DATE
    today = date.today()
    if not TGC_DATE or today != TGC_DATE:
        TGC_DATE = today
        db_conn.clean_tgcount()


def files_update():
    # Download, update files and tables
    UPDT_FILES = (
    (CONF["FILES"]["PEER"], CONF["FILES"]["PEER_URL"], "peer_ids"),
    (CONF["FILES"]["SUBS"], CONF["FILES"]["SUBS_URL"], "subscriber_ids"),
    (CONF["FILES"]["TGID"], CONF["FILES"]["TGID_URL"], "talkgroup_ids")
    )
    for file, url, tbl in UPDT_FILES:
        update_table(CONF["FILES"]["PATH"], file, url, CONF["FILES"]["RELOAD_TIME"], tbl)


def cleaning_loop():
    if CONF["GLOBAL"]["TGC_INC"]:
        clean_tgcount()
    tbls = (("last_heard", CONF["GLOBAL"]["LH_ROWS"]),
            ("lstheard_log", LASTHEARD_LOG_ROWS))
    for _table, _row_num in tbls:
        db_conn.clean_table(_table, _row_num)


#######################################################################
if __name__ == "__main__":
    # Make config
    CONF = mk_config("fdmr-mon.cfg")

    # Create logger
    log_conf = {
        'LOG_FILE': CONF["LOG"]["PATH"] + CONF["LOG"]["LOG_FILE"],
        'LOG_LEVEL': CONF["LOG"]["LOG_LEVEL"],
        'LOG_HANDLERS': 'console,file'
        }

    logger, log_level = create_logger(log_conf)

    logger.info("monitor.py starting up")
    logger.info("\n\n\tCopyright (c) 2016-2022\n\tThe Regents of the K0USY Group. All rights "
                "reserved.\n\n\tPython 3 port:\n\t2019 Steve Miller, KC1AWV <smiller@kc1awv.net>"
                "\n\n\tFDMR-Monitor OA4DOA 2022\n\n")
    
    CTABLE["SETUP"]["LASTHEARD"] = CONF["GLOBAL"]["LH_INC"]

    # Create an instance of MoniDB
    db_conn = MoniDB(CONF["DB"]["SERVER"], CONF["DB"]["USER"], CONF["DB"]["PASSWD"],
                     CONF["DB"]["NAME"], CONF["DB"]["PORT"])
    # Test database connection
    db_conn.test_db(reactor)

    # Jinja2 Stuff
    env = Environment(
        loader=PackageLoader("monitor", "templates"),
        autoescape=select_autoescape(["html", "xml"])
        )

    # define tables template
    itemplate = env.get_template("main_table.html")
    ctemplate = env.get_template("lnksys_table.html")
    otemplate = env.get_template("opb_table.html")
    btemplate = env.get_template("bridge_table.html")
    stemplate = env.get_template("statictg_table.html")
    htemplate = env.get_template("lasthrd_log.html")
    ttemplate = env.get_template("tgcount_table.html")

    # Start update loop
    update_stats = task.LoopingCall(build_stats)
    update_stats.start((CONF["WS"]["FREQ"])).addErrback(error_hdl)

    # Start the timeout loop
    if CONF["WS"]["CLT_TO"]:
        timeout = task.LoopingCall(timeout_clients)
        timeout.start(10).addErrback(error_hdl)

    # TG Count loop
    if CONF["GLOBAL"]["TGC_INC"]:
        tgc = task.LoopingCall(render_fromdb, "tgcount", CONF["GLOBAL"]["TGC_ROWS"])
        tgc.start(60).addErrback(error_hdl)

    # files update loop
    file_loop = task.LoopingCall(files_update)
    file_loop.start(1800).addErrback(error_hdl)

    # Clean DB tables loop
    cdb_loop = task.LoopingCall(cleaning_loop)
    cdb_loop.start(900, now=False).addErrback(error_hdl)

    # Update local files at start
    reactor.callLater(3, update_local)
    # Show number of entries in DB tables
    reactor.callLater(5, count_db_entries)

    # Connect to HBlink
    reactor.connectTCP(
        CONF["FDMR_CXN"]["FD_IP"], CONF["FDMR_CXN"]["FD_PORT"], reportClientFactory())

    # HBmonitor does not require the use of SSL as no "sensitive data" is sent to it but if you want to use SSL:
    # create websocket server to push content to clients via SSL https://
    # the web server apache2 should be configured with a signed certificate for example Letsencrypt
    # we need install pyOpenSSL required by twisted: pip3 install pyOpenSSL
    # and add load ssl module in line number 43: from twisted.internet import reactor, task, ssl
    #
    # put certificate https://letsencrypt.org/ used in apache server
    # certificate = ssl.DefaultOpenSSLContextFactory("/etc/letsencrypt/live/hbmon.dmrserver.org/privkey.pem",
    # "/etc/letsencrypt/live/hbmon.dmrserver.org/cert.pem")
    # dashboard_server = dashboardFactory("wss://*:9000")
    # dashboard_server.protocol = dashboard
    # reactor.listenSSL(9000, dashboard_server,certificate)

    logger.info('Starting webserver on port {port} with SSL = {ssl_}', port=CONF["WS"]["WS_PORT"], ssl_=CONF["WS"]["USE_SSL"])

    if CONF["WS"]["USE_SSL"]:
        from twisted.internet import ssl
        certificate = ssl.DefaultOpenSSLContextFactory(CONF["WS"]["P2F_PKEY"], CONF["WS"]["P2F_CERT"])
        dashboard_server = dashboardFactory(f'wss://*:{CONF["WS"]["WS_PORT"]}')
        dashboard_server.protocol = dashboard
        reactor.listenSSL(CONF["WS"]["WS_PORT"], dashboard_server, certificate)

    else:
        dashboard_server = dashboardFactory(f'ws://*:{CONF["WS"]["WS_PORT"]}')
        dashboard_server.protocol = dashboard
        reactor.listenTCP(CONF["WS"]["WS_PORT"], dashboard_server)

    reactor.run()
