#! /bin/bash

# Install the required support programs
apt install python3 python3-pip python3-dev libffi-dev libssl-dev cargo sed \
default-libmysqlclient-dev build-essential -y
pip3 install -r requirements.txt
# fdmr-mon folder
mon_path=$(pwd)/
# Copy config file 
if [ ! -e fdmr-mon.cfg  ]; then
  if [ -e fdmr-mon_SAMPLE.cfg  ]; then
    cp fdmr-mon_SAMPLE.cfg fdmr-mon.cfg
    echo 'Config file copied successfully.'
  else
    echo 'fdmr-mon_SAMPLE.cfg not found, exiting.'
    exit 1
  fi
else
  echo 'Config file alredy exists.'
fi
# Create a new branch and copy proxy files
read -p 'Do you want to create a new FreeDMR branch and copy the Proxy files into it? [Y/n]: ' branch
branch=${branch:-y}
if [ "${branch,,}" == 'y' ]; then
  valid=''
  for i in {1..3}; do
    read -p 'Insert the path to FreeDMR folder e.g. /opt/FreeDMR/: ' p2fdmr
    if [ -z "$p2fdmr" ] | [ ! -d "$p2fdmr" ]; then
      echo "Path to FreeDMR folder: '${p2fdmr}' not found, try again."
      if [ $i -eq 3 ]; then
        echo 'To many errors, skipping this.'
      fi
    else
      valid=yes
      break $i
    fi
  done
  if [ ! -z $valid ]; then
    cd $p2fdmr
    if [ "$(git branch --list Self_Service)" ]; then
      git checkout Self_Service
      cp ${mon_path}proxy/* $p2fdmr -r
      echo 'Self_Service branch already exists, proxy files copied successfully'
    else
      git checkout master
      echo 'Creating new branch: Self_Service'
      git checkout -b Self_Service
      cp ${mon_path}proxy/* $p2fdmr -r
      echo "Proxy files copied into $p2fdmr successfully."
    fi
  fi
fi
cd $mon_path
# Copy html files to web root folder
read -p 'Do you want to copy html files to the web server root folder? [Y/n]: ' web
web=${web:-y}
if [ "${web,,}" == 'y' ]; then
  valid=''
  echo 'Insert the path to the web server root folder e.g. /var/www/html/'
  for i in {1..3}; do
    read -p 'Insert path: ' p2web
    if [ -z "$p2web" ] | [ ! -d "$p2web" ]; then
      echo "Path to web server root folder: '${p2web}' not found, try again."
      if [ $i -eq 3 ]; then
        echo 'To many errors, skipping this.'
      fi
    else
      valid=yes
      break $i
    fi
  done
  if [ ! -z $valid ]; then
    read -p "This will delete the content of ${p2web} do you want to continue [y/N]: " dele
    dele=${dele:-n}
    if [ "${dele,,}" == 'y' ]; then
      rm ${p2web}/* -r
      cp html/* $p2web -r
      echo "html files copied successfully into: ${p2web}"
      if [ $(systemctl show -p ActiveState --value apache2) == active ]; then
        chown www-data:www-data $p2web -R
      elif [ $(systemctl show -p ActiveState --value nginx) == active ]; then
        chown nginx:nginx $p2web -R
      fi
    fi
  fi
fi

# install log rotate file
if [ -d "/etc/logrotate.d/" ]; then
  cp utils/logrotate/fdmr_mon /etc/logrotate.d/
  echo 'fdmr_mon copied into /etc/logrotate.d/'
else
  echo '/etc/logrotate.d/ folder not found, you will need to copy fdmr_mon into your logrotate.d folder.'
  sleep 3
fi
# Copy systemd file
if [ -d "/etc/systemd/system/" ]; then
  if [ ! -e "/etc/systemd/system/fdmr_mon.service" ]; then
    cp utils/systemd/fdmr_mon.service /etc/systemd/system/
    echo 'fdmr_mon.service copied to /etc/systemd/system/'
    systemctl daemon-reload
    systemctl enable fdmr_mon.service
    echo 'fdmr_mon.service has been enabled to start after reboot'
    systemctl start fdmr_mon.service
    if [ $(systemctl show -p ActiveState --value fdmr_mon) == active ]; then
      echo 'fdmr_mon.service started successfully.'
    else
      echo 'An error was found when trying to start fdmr_mon.service.'
      echo "Run 'systemctl status fdmr_mon' for more details."
      sleep 3
    fi
  else
    echo 'fdmr_mon.service already exists in /etc/systemd/system/'
    echo "you can edit it by runnig 'nano /etc/systemd/system/fdmr_mon.service'"
    sleep 3
  fi
else
  echo '/etc/systemd/system/ folder not found, you will need to copy fdmr_mon.service into your systemd folder.'
  sleep 3
fi

# Configure database
read -p 'Do you want to configure Self Service database and create tables? [Y/n]: ' db_conf
db_conf=${db_conf:-y}
if [ "${db_conf,,}" == 'y' ]; then
  db_info () {
    if [ $1 == 'DB_USERNAME' ]; then
      stm='Insert database username:'
      local loop=1
    elif [  $1 == 'DB_SERVER' ]; then
      stm='Insert database server host e.g. localhost:'
      local loop=1
    elif [ $1 ==  'DB_NAME' ]; then
      stm='Insert database name:'
      local loop=1
    elif [ $1 == 'DB_PASSWORD' ]; then
      read -p 'Insert database password, for no password leave it blank: ' input
      local loop=0
    else
      echo "invalid value: $1"
      return
    fi
    if [ $loop -eq 1 ]; then
      for i in {1..3}; do
        echo -n "${stm} " ; read input
        if [ -z $input ]; then
          echo 'invalid value, try again.'
          if [ $i -eq 3 ]; then
            echo 'Invalid Value, exiting'
            exit 1
          fi
        else
          break $i
        fi
      done
    fi
    sed -i "s/\(^$1 *= *\).*/\1$input/" fdmr-mon.cfg
  }
  # Define the keys of the options to modify
  db_items=('DB_NAME' 'DB_SERVER' 'DB_USERNAME' 'DB_PASSWORD')
  for i in ${!db_items[@]}; do
    db_info ${db_items[$i]}
  done
  #Create and update database tables
  python3 mon_db.py --create
  python3 mon_db.py --update
fi
echo 'Installation script finished, bye.'
