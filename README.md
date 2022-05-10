**FDMR Monitor is a "web dashboard" for FreeDMR servers by OA4DOA.**

***This version has been forked from HBMonitor V2 by SP2ONG 2019-2022***

Some of the changes in FDMR Monitor:
- Improved usage of memory and CPU.
- Broadcasting groups has been added to save server's resources.
- JavaScript code to support broadcasting groups.
- The code has been updated to HTML5.
- Page that shows static and single TG.
- Data qso are showed in the dashboard.


FDMR Monitor has been tested on Debian v9, v10 and v11

This version of FDMR Monitor requires a web server like apache2, lighttpd and 
php 7.x running on the server.

    cd /opt
    sudo git clone https://github.com/yuvelq/FDMR-Monitor
    cd FDMR-Monitor
    sudo chmod +x install.sh
    sudo ./install.sh
    sudo cp config-SAMPLE.py config.py
    - Edit config.py and adjust it to your server configuration:
        sudo nano config.py

    Copy the contents of the /opt/FDMR-Monitor/html directory to 
    the web server directory.
    - This example works for Apache server:
        sudo cp /opt/FDMR-Monitor/html/* /var/www/html/ -r

    With this configuration you server will be available at:
        http://yourserverhost.org/

    You will find a configuration file inside 
    html/include/ in the root of the web server, called config.php in this file you can  
    set the color theme and name for your Dashboard.
    
    Also you can define the height of the Server Activity 
    window: 45px; 1 row, 60px 2 rows, 80px 3 rows:
    define("HEIGHT_ACTIVITY","45px");

    In the same directory you will find a buttons.html file where you can add new buttons.
    
    The logo image you can replace with file image in html directory  img/logo.png
    sudo cp utils/logrotate/fdmr_monitor /etc/logrotate.d/
    sudo cp utils/fdmr_mon.service /lib/systemd/system/
    sudo systemctl enable fdmr_mon
    sudo systemctl start fdmr_mon
    sudo systemctl status fdmr_mon
    forward TCP port 9000 and web server port in firewall
        
    I recommend that you do not use the BRIDGE_INC = True option to display bridge information 
    (if you have multiple bridges displaying this information will increase the CPU load, 
    try to use BRIDGES_INC = False in config.py) 
    

---

**HBMonv2 by SP2ONG**

HBMonitor v2 for DMR Server based on HBlink/FreeDMR https://github.com/sp2ong/HBMonv2 

---

**hbmonitor3 by KC1AWV**

Python 3 implementation of N0MJS HBmonitor for HBlink https://github.com/kc1awv/hbmonitor3 

---

Copyright (C) 2013-2018  Cortney T. Buffington, N0MJS <n0mjs@me.com>

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 
02110-1301  USA

---
