#! /bin/bash

# Install the required support programs
apt install python3 python3-dev default-libmysqlclient-dev build-essential python3-pip -y
pip3 install -r requirements.txt
