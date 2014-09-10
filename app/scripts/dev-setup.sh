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

# Store current directory path to be able to call the script from anywhere
DIR=$(dirname "$0")

# Check that we have a config.ini file
if [ ! -f $DIR/../config/config.ini ]
then
    echored "ERROR: There is no app/config/config.ini file, please create it based on app/config/config.ini-dev before launching the dev-setup.sh script"
    exit 1
fi

# Get server configuration variables
source $DIR/iniparser.sh

# Check that $install variable points to a git repo
if [ ! -d $install/.git ]
then
    echored "ERROR: The 'install' variable in your config.ini file is probably wrong as there is no git repository at the location you provided."
    exit 1
fi

# Check that we have PHP installed on this machine
if ! command -v php >/dev/null 2>&1
then
    echored "ERROR: PHP is not installed on your machine, PHP >=5.4 is required to run Transvision."
    echo "If you are on Debian/Ubuntu you can install it with 'sudo apt-get install php5'."
    exit 1
fi

# Check if we have a web/TMX folder in dev mode, if not download a data snapshot
if $dev
then
    if [ ! -d $root/TMX ]
    then
        cd $root
        echogreen "Downloading a snapshot of data from Transvision Web site"
        if ! command -v curl >/dev/null 2>&1
        then
            wget http://transvision.mozfr.org/data.tar.gz -O - | tar -xz
        else
            curl http://transvision.mozfr.org/data.tar.gz | tar -xz
        fi
        echogreen "Data is now extracted in your web/TMX/ folder"
    fi
fi



cd $install

# Install Composer if not installed
if ! command -v composer >/dev/null 2>&1
then
    echogreen "Installing Composer (PHP dependency manager)"
    php -r "readfile('https://getcomposer.org/installer');" | php
fi

# Install PHP dependencies if not done yet
if [ ! -d vendor ]
then
    echogreen "Installing PHP dependencies with Composer"
    php composer.phar install
fi

# Create json files used for stats
stats_file1=web/stats_locales.json
stats_file2=web/stats_requests.json

if [ ! -f $stats_file1 ]
then
    echogreen "Add $stats_file1 file"
    echo '{}' > $stats_file1
fi

if [ ! -f $stats_file2 ]
then
    echogreen "Add $stats_file2 file"
    echo '{}' > $stats_file2
fi

echogreen "Launching PHP development server (php -S localhost:8082 -t web/ app/inc/router.php)"
php -S localhost:8082 -t web/ app/inc/router.php
