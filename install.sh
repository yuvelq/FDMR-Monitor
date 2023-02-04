#! /bin/bash

# Install the required support programs
apt install python3 python3-pip python3-dev libffi-dev libssl-dev cargo sed -y
pip3 install -r requirements.txt
