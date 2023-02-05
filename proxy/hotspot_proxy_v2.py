#!/usr/bin/env python3
###############################################################################
# Copyright (C) 2020 Simon Adlem, G7RZU <g7rzu@gb7fr.org.uk>
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
from hashlib import pbkdf2_hmac
import random
import ipaddress
import os
from datetime import datetime
from time import time

from twisted.internet.protocol import DatagramProtocol
from twisted.internet.defer import inlineCallbacks
from twisted.internet import reactor, task
from setproctitle import setproctitle
from dmr_utils3.utils import int_id

from proxy_db import ProxyDB

# Does anybody read this stuff? There's a PEP somewhere that says I should do this.
__author__     = 'Simon Adlem - G7RZU'
__verion__     = '1.0.0'
__copyright__  = 'Copyright (c) Simon Adlem, G7RZU 2020,2021,2022'
__credits__    = 'Jon Lee, G4TSN; Norman Williams, M6NBP; Christian, OA4DOA'
__license__    = 'GNU GPLv3'
__maintainer__ = 'Simon Adlem G7RZU'
__email__      = 'simon@gb7fr.org.uk'


def IsIPv4Address(ip):
    try:
        ipaddress.IPv4Address(ip)
        return True
    except ValueError as errorCode:
        pass
        return False


def IsIPv6Address(ip):
    try:
        ipaddress.IPv6Address(ip)
        return True
    except ValueError as errorCode:
        pass


class Proxy(DatagramProtocol):

    def __init__(self, Master, ListenPort, connTrack, peerTrack, blackList, IPBlackList, Timeout,
                 Debug, ClientInfo, DestportStart, DestPortEnd, db_proxy, selfservice):
        self.master = Master
        self.connTrack = connTrack
        self.peerTrack = peerTrack
        self.timeout = Timeout
        self.debug = Debug
        self.clientinfo = ClientInfo
        self.blackList = blackList
        self.IPBlackList = IPBlackList
        self.destPortStart = DestportStart
        self.destPortEnd = DestPortEnd
        self.numPorts = DestPortEnd - DestportStart
        self.db_proxy = db_proxy
        self.selfserv = selfservice

    def reaper(self,_peer_id):
        if self.debug:
            print('dead', _peer_id)
        if self.clientinfo and _peer_id != b'\xff\xff\xff\xff':
            print(f"{datetime.now().replace(microsecond=0)} Client: ID:{str(int_id(_peer_id)).rjust(9)} "
                  f"IP:{self.peerTrack[_peer_id]['shost'].rjust(15)} Port:{self.peerTrack[_peer_id]['sport']} Removed.")
        self.transport.write(b'RPTCL'+_peer_id, (self.master,self.peerTrack[_peer_id]['dport']))
        self.connTrack[self.peerTrack[_peer_id]['dport']] = False
        if self.selfserv:
            self.db_proxy.updt_tbl('log_out', _peer_id)
        del self.peerTrack[_peer_id]

    def datagramReceived(self, data, addr):
        # HomeBrew Protocol Commands
        DMRD    = b'DMRD'
        DMRA    = b'DMRA'
        MSTCL   = b'MSTCL'
        MSTNAK  = b'MSTNAK'
        MSTPONG = b'MSTPONG'
        MSTN    = b'MSTN'
        MSTP    = b'MSTP'
        MSTC    = b'MSTC'
        RPTL    = b'RPTL'
        RPTPING = b'RPTPING'
        RPTCL   = b'RPTCL'
        RPTL    = b'RPTL'
        RPTACK  = b'RPTACK'
        RPTK    = b'RPTK'
        RPTC    = b'RPTC'
        RPTP    = b'RPTP'
        RPTA    = b'RPTA'
        RPTO    = b'RPTO'

        #Proxy control commands
        PRBL    = b'PRBL'

        _peer_id = False
        host,port = addr
        nowtime = time()
        Debug = self.debug

        if host in self.IPBlackList:
            return

        #If the packet comes from the master
        if host == self.master:
            _command = data[:4]

            if _command == PRBL:
                _peer_id = data[4:8]
                _bltime = data[8:].decode('UTF-8')
                _bltime = float(_bltime)
                try:
                    self.IPBlackList[self.peerTrack[_peer_id]['shost']] = _bltime
                except KeyError:
                    return
                if self.clientinfo:
                    print('Add to blacklist: host {}. Expire time {}').format(self.peerTrack[_peer_id]['shost'],_bltime)
                return

            if _command == DMRD:
                _peer_id = data[11:15]
            elif  _command == RPTA:
                    if data[6:10] in self.peerTrack:
                        _peer_id = data[6:10]
                    else:
                        _peer_id = self.connTrack[port]
            elif _command == MSTN:
                    _peer_id = data[6:10]
            elif _command == MSTP:
                    _peer_id = data[7:11]
            elif _command == MSTC:
                    _peer_id = data[5:9]

            if self.debug:
                print(data)
            if _peer_id in self.peerTrack:
                self.transport.write(data,(self.peerTrack[_peer_id]['shost'],self.peerTrack[_peer_id]['sport']))
                # Remove the client after send a MSTN or MSTC packet
                if _command in (MSTN, MSTC):
                    # Give time to the client for a reply to prevent port reassignment
                    self.peerTrack[_peer_id]['timer'].reset(15)
            return

        else:
            _command = data[:4]

            if _command == DMRD:                # DMRData -- encapsulated DMR data frame
                _peer_id = data[11:15]
            elif _command == DMRA:              # DMRAlias -- Talker Alias information
                _peer_id = data[4:8]
            elif _command == RPTL:              # RPTLogin -- a repeater wants to login
                _peer_id = data[4:8]
            elif _command == RPTK:              # Repeater has answered our login challenge
                _peer_id = data[4:8]
            elif _command == RPTC:              # Repeater is sending it's configuraiton OR disconnecting
                if data[:5] == RPTCL:           # Disconnect command
                    _peer_id = data[5:9]
                else:
                    _peer_id = data[4:8]        # Configure Command
                    if self.selfserv and _peer_id in self.peerTrack:
                        mode = data[97:98].decode()
                        callsign = data[8:16].rstrip().decode()
                        self.db_proxy.ins_conf(int_id(_peer_id), _peer_id, callsign, addr[0], mode)
                        # Self Service options will be send 10 sec. after login
                        self.peerTrack[_peer_id]['opt_timer'] = reactor.callLater(10, self.login_opt, _peer_id)

            elif _command == RPTO:              # options
                _peer_id = data[4:8]
                if self.selfserv and _peer_id in self.peerTrack:
                    # Store Self Service password in database
                    if data[8:].upper().startswith(b'PASS='):
                        _psswd = data[13:]
                        if len(_psswd) >= 6:
                            dk = pbkdf2_hmac('sha256', _psswd, b'FreeDMR', 2000).hex()
                            self.db_proxy.updt_tbl('psswd', _peer_id, psswd=dk)
                            self.transport.write (b''.join([RPTACK, _peer_id]), addr)
                            print(f'Password stored for: {int_id(_peer_id)}')
                            return
                    self.db_proxy.updt_tbl('opt_rcvd', _peer_id)
                    # Options send by peer overrides Self Service options
                    if self.peerTrack[_peer_id]['opt_timer'].active():
                        self.peerTrack[_peer_id]['opt_timer'].cancel()
                        print(f'Options received from: {int_id(_peer_id)}')

            elif _command == RPTP:              # RPTPing -- peer is pinging us
                _peer_id = data[7:11]
            else:
                return
            
            if _peer_id in self.peerTrack:
                _dport = self.peerTrack[_peer_id]['dport']
                self.peerTrack[_peer_id]['sport'] = port
                self.peerTrack[_peer_id]['shost'] = host
                self.transport.write(data, (self.master,_dport))
                self.peerTrack[_peer_id]['timer'].reset(self.timeout)
                if self.debug:
                    print(data)
                return

            else:
                if int_id(_peer_id) in self.blackList:
                    return
                # Make a list with the available ports
                _ports_avail = [port for port in self.connTrack if not self.connTrack[port]]
                if _ports_avail:
                    _dport = random.choice(_ports_avail)
                else:
                    return
                self.connTrack[_dport] = _peer_id
                self.peerTrack[_peer_id] = {}
                self.peerTrack[_peer_id]['dport'] = _dport
                self.peerTrack[_peer_id]['sport'] = port
                self.peerTrack[_peer_id]['shost'] = host
                self.peerTrack[_peer_id]['timer'] = reactor.callLater(self.timeout,self.reaper,_peer_id)
                self.transport.write(data, (self.master,_dport))
                pripacket = b''.join([b'PRIN',host.encode('UTF-8'),b':',str(port).encode('UTF-8')])
                #Send IP and Port info to server
                self.transport.write(pripacket, (self.master,_dport))

                if self.clientinfo and _peer_id != b'\xff\xff\xff\xff':
                    print(f'{datetime.now().replace(microsecond=0)} New client: ID:{str(int_id(_peer_id)).rjust(9)} '
                          f'IP:{host.rjust(15)} Port:{port}, assigned to port:{_dport}.')
                if self.debug:
                    print(data)
                return

    @inlineCallbacks
    def login_opt(self, _peer_id):
        try:
            res = yield db_proxy.slct_opt(_peer_id)
            options = res[0][0]
            if options:
                bytes_pkt = b''.join((b'RPTO', _peer_id, options.encode()))
                self.transport.write(bytes_pkt, (self.master, self.peerTrack[_peer_id]['dport']))
                print(f'Options sent at login for: {int_id(_peer_id)}, opt: {options}')

        except Exception as err:
            print(f'login_opt error: {err}')

    @inlineCallbacks
    def send_opts(self):
        try:
            results = yield db_proxy.slct_db()
            for item in results:
                _peer_id, options = item
                if _peer_id not in self.peerTrack or not options:
                    continue
                self.db_proxy.updt_tbl('rst_mod', _peer_id)
                bytes_pkt = b''.join((b'RPTO', _peer_id, options.encode()))
                self.transport.write(bytes_pkt, (self.master, self.peerTrack[_peer_id]['dport']))
                print(f'Options update sent for: {int_id(_peer_id)}')

        except Exception as err:
            print(f'send_opts error: {err}')

    def lst_seen(self):
        # Update last seen
        dmrid_list = [(ite,) for ite in self.peerTrack]
        if dmrid_list:
            self.db_proxy.updt_lstseen(dmrid_list)


if __name__ == '__main__':
    
    import signal
    import configparser
    import argparse
    import sys
    import json

    #Set process title early
    setproctitle(__file__)

    # Change the current directory to the location of the application
    os.chdir(os.path.dirname(os.path.realpath(sys.argv[0])))

    # CLI argument parser - handles picking up the config file from the command line, and sending a "help" message
    parser = argparse.ArgumentParser()
    parser.add_argument('-c', '--config', action='store', dest='CONFIG_FILE', help='/full/path/to/config.file (usually freedmr.cfg)')
    cli_args = parser.parse_args()


    # Ensure we have a path for the config file, if one wasn't specified, then use the execution directory
    if not cli_args.CONFIG_FILE:
        cli_args.CONFIG_FILE = os.path.dirname(os.path.abspath(__file__))+'/freedmr.cfg'

    _config_file = cli_args.CONFIG_FILE

    config = configparser.ConfigParser()

    if not config.read(_config_file):
        print('Configuration file \''+_config_file+'\' is not a valid configuration file!')

    try:

        Master = config.get('PROXY','Master')
        ListenPort = config.getint('PROXY','ListenPort')
        ListenIP = config.get('PROXY','ListenIP')
        DestportStart = config.getint('PROXY','DestportStart')
        DestPortEnd = config.getint('PROXY','DestPortEnd')
        Timeout = config.getint('PROXY','Timeout')
        Stats = config.getboolean('PROXY','Stats')
        Debug = config.getboolean('PROXY','Debug')
        ClientInfo = config.getboolean('PROXY','ClientInfo')
        BlackList = json.loads(config.get('PROXY','BlackList'))
        IPBlackList = json.loads(config.get('PROXY','IPBlackList'))
        # Self Service
        use_selfservice = config.getboolean('SELF SERVICE', 'use_selfservice')
        db_server = config.get('SELF SERVICE', 'server')
        db_username = config.get('SELF SERVICE', 'username')
        db_password = config.get('SELF SERVICE', 'password')
        db_name = config.get('SELF SERVICE', 'db_name')
        db_port = config.getint('SELF SERVICE', 'port')

    except configparser.Error as err:
        print('Error processing configuration file -- {}'.format(err))

        print('Using default config')
#*** CONFIG HERE ***

        Master = "127.0.0.1"
        ListenPort = 62031
        #'' = all IPv4, '::' = all IPv4 and IPv6 (Dual Stack)
        ListenIP = ''
        DestportStart = 54000
        DestPortEnd = 54100
        Timeout = 30
        Stats = False
        Debug = False
        ClientInfo = False
        BlackList = [1234567]
        #e.g. {10.0.0.1: 0, 10.0.0.2: 0}
        IPBlackList = {}

        # Self Service database configuration
        use_selfservice = True
        db_server = 'localhost'
        db_username = 'root'
        db_password = ''
        db_name = 'test'
        db_port = 3306

#*******************        
    
    CONNTRACK = {}
    PEERTRACK = {}
    
    # Set up the signal handler
    def sig_handler(_signal, _frame):
        print('(GLOBAL) SHUTDOWN: PROXY IS TERMINATING WITH SIGNAL {}'.format(str(_signal)))
        reactor.stop()

    # Set signal handers so that we can gracefully exit if need be
    for sig in [signal.SIGINT, signal.SIGTERM]:
        signal.signal(sig, sig_handler)
        
    #readState()
    
    #If IPv6 is enabled by enivornment variable...
    if ListenIP == '' and 'FDPROXY_IPV6' in os.environ and bool(os.environ['FDPROXY_IPV6']):
        ListenIP = '::'
        
    #Override static config from Environment
    if 'FDPROXY_STATS' in os.environ:
        Stats = bool(os.environ['FDPROXY_STATS'])
    #if 'FDPROXY_DEBUG' in os.environ:
    #    Debug = bool(os.environ['FDPROXY_DEBUG'])
    if 'FDPROXY_CLIENTINFO' in os.environ:
        ClientInfo = bool(os.environ['FDPROXY_CLIENTINFO'])
    if 'FDPROXY_LISTENPORT' in os.environ:
        ListenPort = os.environ['FDPROXY_LISTENPORT']

    for port in range(DestportStart,DestPortEnd+1,1):
        CONNTRACK[port] = False

    #If we are listening IPv6 and Master is an IPv4 IPv4Address
    #IPv6ify the address.
    if ListenIP == '::' and IsIPv4Address(Master):
        Master = '::ffff:' + Master

    if use_selfservice:
        # Create an instance of db_proxy and them pass it to the proxy
        db_proxy = ProxyDB(db_server, db_username, db_password, db_name, db_port)
        db_proxy.test_db(reactor)
    else:
        db_proxy = None

    srv_proxy = Proxy(Master, ListenPort, CONNTRACK, PEERTRACK, BlackList, IPBlackList, Timeout,
                      Debug, ClientInfo, DestportStart, DestPortEnd, db_proxy, use_selfservice)

    reactor.listenUDP(ListenPort, srv_proxy, interface=ListenIP)

    def loopingErrHandle(failure):
        print('(GLOBAL) STOPPING REACTOR TO AVOID MEMORY LEAK: Unhandled error innowtimed loop.\n {}'.format(failure))
        reactor.stop()

    if use_selfservice:
        # Options loop
        opts_loop = task.LoopingCall(srv_proxy.send_opts)
        opts_loop.start(10).addErrback(loopingErrHandle)

        # Clean table every hour
        cl_tbl = task.LoopingCall(db_proxy.clean_tbl)
        cl_tbl.start(3600).addErrback(loopingErrHandle)

        # Update last seen loop
        ls_loop = task.LoopingCall(srv_proxy.lst_seen)
        ls_loop.start(120).addErrback(loopingErrHandle)

    def stats():
        count = 0
        nowtime = time()
        for port in CONNTRACK:
            if CONNTRACK[port]:
                count = count+1

        totalPorts = DestPortEnd - DestportStart
        freePorts = totalPorts - count

        print("{} ports out of {} in use ({} free)".format(count,totalPorts,freePorts))

    def blackListTrimmer():
        _timenow = time()
        _dellist = []
        for entry in IPBlackList:
            deletetime = IPBlackList[entry]
            if deletetime and deletetime < _timenow:
                _dellist.append(entry)

        for delete in _dellist:
            IPBlackList.pop(delete)
            if ClientInfo:
                print('Remove dynamic blacklist entry for {}').format(delete)


    if Stats == True:
        stats_task = task.LoopingCall(stats)
        statsa = stats_task.start(30)
        statsa.addErrback(loopingErrHandle)

    blacklist_task = task.LoopingCall(blackListTrimmer)
    blacklista = blacklist_task.start(15)
    blacklista.addErrback(loopingErrHandle)

    reactor.run()
