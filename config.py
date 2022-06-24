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
import sys
from configparser import ConfigParser
from pathlib import Path


__author__ = 'Christian Quiroz, OA4DOA'
__copyright__ = 'Copyright (c) 2022 Christian Quiroz, OA4DOA'
__license__ = 'GNU GPLv3'
__maintainer__ = 'Christian Quiroz, OA4DOA'
__email__ = 'adm@dmr-peru.pe'

logger = logging.getLogger("fdmr-mon")


def mk_config(cfg_file):

    CONF = {
        "GLOBAL": {},
        "FDMR_CXN": {},
        "OPB_FLTR": {},
        "FILES": {},
        "LOG": {},
        "WS": {}
        }
    
    default_values = {
        "LOCAL_SUB_FILE": "",
        "LOCAL_PEER_FILE": "",
        "LOCAL_TGID_FILE": "",
        "PORT": 3306
        }

    try:
        conf = ConfigParser(default_values)

        if not conf.read(cfg_file):
            sys.exit(f"Configuration file {cfg_file} is not a valid file.")

        for section in conf:
            if section == "GLOBAL":
                CONF["GLOBAL"] = {
                    "HB_INC": conf.getboolean(section, "HOMEBREW_INC"),
                    "LH_INC": conf.getboolean(section, "LASTHEARD_INC"),
                    "LH_ROWS": conf.getint(section, "LASTHEARD_ROWS"),
                    "BRDG_INC": conf.getboolean(section, "BRIDGES_INC"),
                    "EMPTY_MASTERS": conf.getboolean(section, "EMPTY_MASTERS"),
                    "TGC_INC": conf.getboolean(section, "TGCOUNT_INC"),
                    "TGC_ROWS": conf.getint(section, "TGCOUNT_ROWS")
                    }
            elif section == "FDMR CONNECTION":
                CONF["FDMR_CXN"] = {
                    "FD_IP": conf.get(section, "FDMR_IP"),
                    "FD_PORT": conf.getint(section, "FDMR_PORT"),
                    }
            elif section == "OPB FILTER":
                CONF["OPB_FLTR"] = {
                    "OPB_FILTER": conf.get(section, "OPB_FILTER").replace(" ", "").split(",")}
            elif section == "FILES":
                _path = conf[section]["FILES_PATH"]
                if not _path.endswith("/"):
                    _path = conf[section]["FILES_PATH"] = f"{_path}/"
                CONF["FILES"] = {
                    "PATH": conf.get(section, "FILES_PATH"),
                    "SUBS": conf.get(section, "SUBSCRIBER_FILE"),
                    "PEER": conf.get(section, "PEER_FILE"),
                    "TGID": conf.get(section, "TGID_FILE"),
                    "LCL_SUBS": conf.get(section, "LOCAL_SUB_FILE"),
                    "LCL_PEER": conf.get(section, "LOCAL_PEER_FILE"),
                    "LCL_TGID": conf.get(section, "LOCAL_TGID_FILE"),
                    "RELOAD_TIME": conf.getint(section, "RELOAD_TIME") * 86400,
                    "PEER_URL": conf.get(section, "PEER_URL"),
                    "SUBS_URL": conf.get(section, "SUBSCRIBER_URL"),
                    "TGID_URL": conf.get(section, "TGID_URL")
                    }
            elif section == "LOGGER":
                CONF["LOG"] = {
                    "PATH": conf.get(section, "LOG_PATH"),
                    "LOG_FILE": conf.get(section, "LOG_FILE"),
                    "LOG_LEVEL": conf.get(section, "LOG_LEVEL"),
                    "P2F_LOG": Path(conf[section]["LOG_PATH"], conf[section]["LOG_FILE"])
                    }
            elif section == "WEBSOCKET SERVER":
                CONF["WS"] = {
                    "WS_PORT": conf.getint(section, "WEBSOCKET_PORT"),
                    "USE_SSL": conf.getboolean(section, "USE_SSL"),
                    "SSL_PATH": conf.get(section, "SSL_PATH"),
                    "SSL_CERT": conf.get(section, "SSL_CERTIFICATE"),
                    "P2F_CERT": Path(conf["WEBSOCKET SERVER"]["SSL_PATH"],
                                     conf["WEBSOCKET SERVER"]["SSL_CERTIFICATE"]),
                    "SSL_PKEY": conf.get(section, "SSL_PRIVATEKEY"),
                    "P2F_PKEY": Path(conf["WEBSOCKET SERVER"]["SSL_PATH"],
                                     conf["WEBSOCKET SERVER"]["SSL_PRIVATEKEY"]),
                    "FREQ": conf.getint(section, "FREQUENCY"),
                    "CLT_TO": conf.getint(section, "CLIENT_TIMEOUT")
                    }
            elif section == "SELF SERVICE":
                CONF["DB"] = {
                    "SERVER": conf.get(section, "DB_SERVER"),
                    "USER": conf.get(section, "DB_USERNAME"),
                    "PASSWD": conf.get(section, "DB_PASSWORD"),
                    "NAME": conf.get(section, "DB_NAME"),
                    "PORT": conf.getint(section, "DB_PORT")
                    }
            elif section == "DEFAULT":
                pass

            else:
                logger.warning(f"Unrecognized section in config file: {section}.")

        return CONF

    except Exception as err:
        sys.exit(f"We found an error when parsing config file:\n{err}")
                

if __name__ == '__main__':

    logging.basicConfig(
        level=logging.DEBUG,
        format='%(asctime)s %(levelname)s %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
        )

    print(mk_config("fdmr-mon_SAMPLE.cfg"))
