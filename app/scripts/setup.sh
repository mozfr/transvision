#! /usr/bin/env bash

function interrupt_code()
# This code runs if user hits control-c
{
  echored "\n*** Setup interrupted ***\n"
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

function setupVirtualEnv() {
    # Create virtualenv folder if missing
    cd $install
    if [ ! -d python-venv ]
    then
        echo "Setting up new virtualenv..."
        virtualenv --python=/usr/bin/python3 python-venv || exit 1
    fi

    # Install or update dependencies
    echo "Installing dependencies in virtualenv"
    source python-venv/bin/activate || exit 1
    pip install --upgrade pip
    pip install -r requirements.txt --upgrade --quiet
    deactivate
}

function initGeckoStringsRepo() {
    local repo_folder="gecko_strings_path"

    # If repo_folder="gecko_strings_path", ${!repo_folder} is equal to $gecko_strings_path
    cd ${!repo_folder}

    # Clone source repository as en-US
    if [ ! -d "en-US" ];
    then
        echogreen "Checking out firefox-l10n-source"
        git clone https://github.com/mozilla-l10n/firefox-l10n-source en-US
    fi

    # Clone l10n monorepo as l10n
    if [ ! -d "l10n" ];
    then
        echogreen "Checking out firefox-l10n-source"
        git clone https://github.com/mozilla-l10n/firefox-l10n l10n
    fi
}

function initThunderbirdRepo() {
    local repo_folder="thunderbird_path"

    # If repo_folder="thunderbird_path", ${!repo_folder} is equal to $thunderbird_path
    cd ${!repo_folder}

    # Clone source repository as en-US
    if [ ! -d "en-US" ];
    then
        echogreen "Checking out thunderbird-l10n-source"
        git clone https://github.com/thunderbird/thunderbird-l10n-source en-US
    fi

    # Clone l10n monorepo as l10n
    if [ ! -d "l10n" ];
    then
        echogreen "Checking out thunderbird-l10n"
        git clone https://github.com/thunderbird/thunderbird-l10n l10n
    fi
}

function initSeamonkeyRepo() {
    local repo_folder="seamonkey_path"

    if [ ! -d ${!repo_folder}/.git ]
    then
        echogreen "Checking out seamonkey-central-l10n repo."
        cd ${local_git}
        git clone https://gitlab.com/seamonkey-project/seamonkey-central-l10n seamonkey
    fi
}

# Store current directory path to be able to call this script from anywhere
DIR=$(dirname "$0")
# Convert .ini file in bash variables
eval $($DIR/ini_to_bash.py $DIR/../config/config.ini)

# Create all bash variables
source $DIR/bash_variables.sh

# Make sure that we have the file structure ($folders is defined in bash_variables.sh)
echogreen "Checking folders..."
for folder in "${folders[@]}"
do
    if [ ! -d $folder ]
    then
        echogreen "Creating folder: $folder"
        mkdir -p "$folder"
    fi
done

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

# Store version information in cache
DEV_VERSION="dev"
if [ ! -d ${install}/.git ]
then
    CURRENT_TIP="unknown"
    LATEST_TAG_NAME="unknown"
else
    cd "${install}"
    CURRENT_TIP=$(git rev-parse HEAD)
    LATEST_TAG=$(git describe --abbrev=0 --tags | xargs -I {} git rev-list -n 1 {})
    LATEST_TAG_NAME=$(git describe --abbrev=0 --tags)
    if [ "${CURRENT_TIP}" = "${LATEST_TAG}" ]
    then
        DEV_VERSION=""
    fi
fi
echo "${CURRENT_TIP:0:7}${DEV_VERSION}" | tr -d '\n' > "${install}/cache/version.txt"
echo "${LATEST_TAG_NAME}" | tr -d '\n' > "${install}/cache/tag.txt"

setupVirtualEnv
initGeckoStringsRepo
initThunderbirdRepo
initSeamonkeyRepo

# Check out GitHub repos
cd $mozilla_org
if [ ! -d $mozilla_org/.git ]
then
    echogreen "Checking out mozilla.org (Fluent) repo"
    git clone https://github.com/mozilla-l10n/www-l10n .
fi

cd $firefox_com
if [ ! -d $firefox_com/.git ]
then
    echogreen "Checking out firefox.com (Fluent) repo"
    git clone https://github.com/mozilla-l10n/www-firefox-l10n .
fi

cd $firefox_ios
if [ ! -d $firefox_ios/.git ]
then
    echogreen "Checking out Firefox for iOS repo"
    git clone https://github.com/mozilla-l10n/firefoxios-l10n .
fi

cd $android_l10n
if [ ! -d $android_l10n/.git ]
then
    echogreen "Checking out android-l10n repo"
    git clone https://github.com/mozilla-l10n/android-l10n .
fi

cd $vpn_client
if [ ! -d $vpn_client/.git ]
then
    echogreen "Checking out VPN Client repo"
    git clone https://github.com/mozilla-l10n/mozilla-vpn-client-l10n .
fi

# Add .htaccess to download folder. Folder should already exists, but check in
# advance to be sure. I overwrite an existing .htaccess if already present.
echogreen "Add .htaccess to download folder"
if [ ! -d $install/web/download ]
then
    echogreen "Creating download folder"
    mkdir -p $install/web/download
fi
echo "AddType application/octet-stream .tmx" > $install/web/download/.htaccess

# Create json files used for stats
stats_files=( $install/cache/stats_locales.json
              $install/cache/stats_requests.json )

for stats_file in "${stats_files[@]}"
do
    if [ ! -f $stats_file ]
    then
        echogreen "Add $stats_file file"
        echo '{}' > $stats_file
    fi
done
