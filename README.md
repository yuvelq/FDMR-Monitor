**FDMR Monitor is a "web dashboard" for FreeDMR servers by OA4DOA.**

***This version has been forked from HBMonitor V2 by SP2ONG 2019-2022***

**This branch includes the Self Service page for configuring options through a web interface**  

If you are looking for the version that doesn't include Self Service please click [here.](https://github.com/yuvelq/FDMR-Monitor)

Some of the changes in FDMR Monitor:
- Select options through Self Service web interface.
- TG Count page added.
- Config file added.
- It's integrated with MariaDB database.
- Improved usage of memory and CPU.
- Broadcasting groups has been added to save server's resources.
- JavaScript code was added to support broadcasting groups.
- The code has been updated to HTML5.
- Static and single TG's page added.
- Data QSO's now are showed in the dashboard.

FDMR Monitor has been tested on Debian v10 and v11

This version of FDMR Monitor requires a LAMP (Linux, Apache, MySQL, PHP) installation there are
some good tutorials on the internet that will help you with the installation.

Self Service has been tested with:
- Debian v10, v11
- Apache 2.4.53 (Debian)
- MariaDB 15.1 Distrib 10.5.15-MariaDB
- PHP 7.4.28

The install.sh file has been improved, now it will guide you throug the installation process, please 
be prepeard with the next information to make the installation easier:
- The location of your web server root folder:
  If you are using Apache2 web server it usually is:
  - /var/www/html/

- The location of FreeDMR folder:
  The script will create a new branch named Self_Service and copy the content of the proxy file into 
  it, the location usually is:
  - /opt/FreeDMR

- Database information:
  This information is neccesary for the database connection, you will need to provide the next 
  items:
  - Database username
  - Database password
  - Database name


  cd /opt
  sudo git clone https://github.com/yuvelq/Self_Service.git  
  cd FDMR-Monitor  
  sudo chmod +x install.sh  
  sudo ./install.sh
  sudo cp fdmr-mon_SAMPLE.cfg fdmr-mon.cfg
  - Edit fdmr-mon.cfg and adjust it to your server configuration:
    sudo nano fdmr-mon.cfg

  - Give readig permission to the config file:
    sudo chmod 644 fdmr-mon.cfg

  Copy the contents of the /opt/FDMR-Monitor/html directory to 
  the web server directory.
  - This example will work for Apache server:
    sudo cp /opt/FDMR-Monitor/html/* /var/www/html/ -r

  With this configuration you server will be available at:
    http://yourserverhost.org/

  Now you can configure the theme color and name for your Dashboard from the config.cfg file also
  you can define the height of the Server Activity 
  window: 45 1 row, 60 2 rows, 80 3 rows:
  HEIGHT_ACTIVITY = 45

  Now if you set TGCOUNT_INC to True the button will be added automatically
  If you want to modify or add any other button you will find a buttons.php file in the root of your
  web server.
  
  You can replace the logo with an image of your preference in the img/ directory img/logo.png
  
  - This will rotate the log file:
    sudo cp utils/logrotate/fdmr_mon /etc/logrotate.d/

  - Add the systemd file:
    sudo cp utils/systemd/fdmr_mon.service /lib/systemd/system/

  - Enable the monitor to start automatically after reboot:
    sudo systemctl enable fdmr_mon

  - You can start, stop, or restart with the next commands:
    sudo systemctl start fdmr_mon
    sudo systemctl stop fdmr_mon
    sudo systemctl restart fdmr_mon

  forward TCP port 9000 and web server port in firewall
      
  I recommend that you do not use the BRIDGE_INC = True option to display bridge information 
  (if you have multiple bridges displaying this information will increase the CPU load, 
  try to use BRIDGES_INC = False in config.py) 
  
  If for any reason you want to reset the database to the original values:
  sudo systemctl stop fdmr_mon
  sudo rm mon.db
  sudo python3 mon_db.py
  sudo systemct start fdmr_mon


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
