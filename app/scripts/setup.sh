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
        virtualenv python-venv || exit 1
    fi

    # Install or update dependencies
    echo "Installing dependencies in virtualenv"
    source python-venv/bin/activate || exit 1
    pip install -r requirements.txt --upgrade
    deactivate
}

function initGeckoStringsRepo() {
    local repo_folder="gecko_strings_path"
    local repo_path="https://hg.mozilla.org/l10n-central"
    local locale_list="gecko_strings_locales"

    # If repo_folder="gecko_strings_path", ${!repo_folder} is equal to $gecko_strings_path
    cd ${!repo_folder}

    # Checkout all locales, including en-US
    for locale in $(cat ${!locale_list})
        do
            if [ ! -d $locale ]
            then
                mkdir $locale
            fi

            if [ ! -d $locale/.hg ]
            then
                echogreen "Checking out the following repo:"
                echogreen "$repo_path/$locale/"
                if [ "$locale" = "en-US" ]
                then
                    hg clone https://hg.mozilla.org/l10n/gecko-strings/ $locale
                else
                    hg clone $repo_path/$locale $locale
                fi
            fi

            if [ ! -d $root/TMX/$locale ]
            then
                echogreen "Creating folder cache for: $locale"
                mkdir -p $root/TMX/$locale
            fi
    done
}

function initOtherSources() {
    # Can add other products to array products, as long
    # as their code is located in https://hg.mozilla.org/PRODUCT
    local products=( chatzilla )
    for product in "${products[@]}"
    do
        if [ ! -d $sources_path/$product/.hg ]
        then
            echogreen "Checking out the following repo:"
            echogreen $sources_path/$product
            cd $sources_path
            hg clone https://hg.mozilla.org/$product/
        fi
    done
}

# Store current directory path to be able to call this script from anywhere
DIR=$(dirname "$0")
# Convert .ini file in bash variables
eval $(cat $DIR/../config/config.ini | $DIR/ini_to_bash.py)

# Generate sources (supported locales and repositories)
echogreen "Generate list of locales and supported repositories"
$DIR/generate_sources $config

# Check if we have sources
echogreen "Checking if Transvision sources are available..."
if ! $(ls $config/sources/*.txt &> /dev/null)
then
    echored "CRITICAL ERROR: no sources available, aborting."
    echored "Check the value for l10nwebservice in your config.ini"
    exit
fi

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
initOtherSources

# Check out GitHub repos
cd $mozilla_org
if [ ! -d $mozilla_org/.git ]
then
    echogreen "Checking out mozilla.org (Fluent) repo"
    git clone https://github.com/mozilla-l10n/www-l10n .
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
    echogreen "Checking out Focus for android-l10n repo"
    git clone https://github.com/mozilla-l10n/android-l10n .
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
