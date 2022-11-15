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
eval $($DIR/ini_to_bash.py $DIR/../config/config.ini)

# Create all bash variables
source $DIR/bash_variables.sh

# If there are no .txt files in /sources, try to retrieve them online
if ! $(ls $config/sources/*.txt &> /dev/null)
then
    # Generate sources (supported locales and repositories)
    # 1. Clone or update mozilla-l10n-query in the libraries folder
    if [ ! -d ${libraries}/mozilla-l10n-query ]
    then
        git clone https://github.com/mozilla-l10n/mozilla-l10n-query ${libraries}/mozilla-l10n-query
    else
        git -C ${libraries}/mozilla-l10n-query pull --quiet
    fi
    # 2. Install or update Composer
    if [ ! -d ${libraries}/mozilla-l10n-query/composer.phar ]
    then
        $DIR/install_composer.sh "${libraries}/mozilla-l10n-query"
    else
        ${libraries}/mozilla-l10n-query/composer.phar --self-udpate
    fi
    # 3. Install or update Composer dependencies
    cwd=$(pwd)
    cd "${libraries}/mozilla-l10n-query"
    if [ ! -f composer.lock ]
    then
        php composer.phar install
    else
        php composer.phar update
    fi
    cd ${cwd}
    # 4. Run the PHP server locally on port 8088 (in background)
    nohup php -S localhost:8088 -t "${libraries}/mozilla-l10n-query/web" >/dev/null 2>&1 &
    PHP_SERVER_PID=$!
    echogreen "PHP server running on port 8088 (PID: ${PHP_SERVER_PID})"
    sleep 1
    # 5. Generate sources
    echogreen "Generate list of locales and supported repositories"
    $DIR/generate_sources $config http://localhost:8088
    # 6. Kill the PHP server
    echogreen "Stopping PHP server"
    kill -9 $PHP_SERVER_PID

    # Check if we have sources
    echogreen "Checking if Transvision sources are available..."
    if ! $(ls $config/sources/*.txt &> /dev/null)
    then
        echored "CRITICAL ERROR: no sources available, aborting."
        exit
    fi
fi

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
            wget https://transvision.mozfr.org/data.tar.gz -O - | tar -xz
        else
            curl https://transvision.mozfr.org/data.tar.gz | tar -xz
        fi
        echogreen "Data is now extracted in your web/TMX/ folder"
    fi
fi

cd $install

# Install Composer if not installed
if ! command -v composer >/dev/null 2>&1
then
    echogreen "Installing Composer (PHP dependency manager)"
    $DIR/install_composer.sh .
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
stats_file1=cache/stats_locales.json
stats_file2=cache/stats_requests.json

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

# Add Reference repository as upstream remote
if ! $(git remote | grep upstream &> /dev/null)
then
    origin=$(git config --get remote.origin.url)
    remote_https='https://github.com/mozfr/transvision.git'
    remote_git='git@github.com:mozfr/transvision.git'

    if [ $origin == $remote_https ] || [ $origin == $remote_git ]
    then
        echored "Your local clone is from the reference repository, you should clone your own fork if you intend to contribute code."
    else
        echogreen "$remote_git added as upstream remote"
        git remote add upstream $remote_git
        git fetch upstream
    fi
fi
