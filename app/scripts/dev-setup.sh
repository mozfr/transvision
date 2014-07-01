#! /usr/bin/env bash

function interrupt_code()
# This code runs if user hits control-c
{
  echored "\n*** Operation interrupted ***\n"
  exit $?
}

# Trap keyboard interrupt (control-c)
trap interrupt_code SIGINT

# Pretty printing functions
NORMAL=$(tput sgr0)
GREEN=$(tput setaf 2; tput bold)
RED=$(tput setaf 1)

function echored() {
    echo -e "$RED$*$NORMAL"
}

function echogreen() {
    echo -e "$GREEN$*$NORMAL"
}

# Get server configuration variables
DIR=$(dirname "$0")
source $DIR/iniparser.sh

cd $root
echogreen "Downloading a snapshot of data from Transvision Web site"
wget http://transvision.mozfr.org/data.tar.gz -O - | tar -xz
echogreen "Data is now extracted in your web/TMX/ folder"

cd $install
echogreen "Installing PHP dependencies with Composer"
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install

echogreen "Add stats.json file"
touch web/stats.json

echogreen "Launching PHP development server (php -S localhost:8082 -t web/ app/inc/router.php)"
cd $install
php -S localhost:8082 -t web/ app/inc/router.php
