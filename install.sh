#! /bin/bash

# Install the required support programs
apt install python3 python3-pip python3-dev \
libffi-dev libssl-dev cargo sed default-libmysqlclient-dev build-essential -y
pip3 install -r requirements.txt
# Copy config file 
if [ ! -e fdmr-mon.cfg  ]; then
  if [ -e fdmr-mon_SAMPLE.cfg  ]; then
    cp fdmr-mon_SAMPLE.cfg fdmr-mon.cfg
    echo 'Config file copied successfully.'
  else
    echo 'fdmr-mon_SAMPLE.cfg not found.'
  fi
else
  echo 'Config file alredy exists.'
fi

if [ ! -z $1 ] && [ $1 == '-i' ]; then
  # Parse database information
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
        echo -n  "${stm} " ; read input
        if [ -z $input ]; then
          echo 'invalid value, try again.'
          if [ $i -eq 3 ]; then
            echo 'Invalid Value, Bye'
            exit
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
