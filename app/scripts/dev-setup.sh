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

# IP used to run PHP internal web server
ip_server="localhost"

if [ $# -eq 1 ]
then
    # I have exactly one parameter
    if [ $1 == "-remote" ]
    then
        echogreen "PHP server will listen to 0.0.0.0"
        ip_server="0.0.0.0"
    else
        echored "Unknown parameter ${1}."
        echo "Usage: dev-setup.sh (PHP web server will be listening to localhost)"
        echo "Usage: dev-setup.sh -remote (PHP web server will be listening to 0.0.0.0 and accessible from the outside)"
        echo "Additional parameter will be ignored."
    fi
fi

# Store current directory path to be able to call this script from anywhere
DIR=$(dirname "$0")

# Store transvision install path to use that to generate a config.ini file automatically
TRANSVISIONDIR="$(cd "${DIR}/../../";pwd)"

# Check that we have a config.ini file
if [ ! -f $DIR/../config/config.ini ]
then
    echogreen "WARNING: There is no app/config/config.ini file. Creating one based based on app/config/config.ini-dev template."
    function render_template() {
      eval "echo \"$(cat $1)\""
    }
    render_template $DIR/../config/config.ini-dev > $DIR/../config/config.ini
fi

# Convert config.ini to bash variables
eval $(cat $DIR/../config/config.ini | $DIR/ini_to_bash.py)

# If there are no .txt files in /sources, try to retrieve them online
if ! $(ls $config/sources/*.txt &> /dev/null)
then
    echogreen "Checking if Transvision sources are available..."
    echogreen "Generate list of locales and supported Gaia versions"
    php $DIR/generate_sources $config
    # Check if we actually have sources at this point
    if ! $(ls $config/sources/*.txt &> /dev/null)
    then
        echored "CRITICAL ERROR: no sources available, aborting."
        echored "Check the value for l10nwebservice in your config.ini"
        exit
    fi
fi

# Create all bash variables
source $DIR/bash_variables.sh

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
    if [ ! -d vendor ]
    then
        echogreen "Installing PHP dependencies with Composer (locally installed)"
        php composer.phar install
    fi
else
    if [ ! -d vendor ]
    then
        echogreen "Installing PHP dependencies with Composer (globally installed)"
        composer install
    fi
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
php -S ${ip_server}:8082 -t web/ app/inc/router.php
