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

function setupExternalLibraries() {
    # Check out or update compare-locales library
    version="RELEASE_2_1"
    if [ ! -d $libraries/compare-locales/.hg ]
    then
        echogreen "Checking out compare-locales in $libraries"
        cd $libraries
        hg clone https://hg.mozilla.org/l10n/compare-locales -u $version
        cd $install
    else
        echogreen "Updating compare-locales in $libraries"
        cd $libraries/compare-locales
        hg pull -r default --update
        hg update $version
        cd $install
    fi

    # Check out or update python-fluent library
    version="0.4.2"
    if [ ! -d $libraries/python-fluent/.git ]
    then
        echogreen "Checking out the python-fluent library in $libraries"
        cd $libraries
        git clone https://github.com/projectfluent/python-fluent
        cd $install
    else
        echogreen "Updating the python-fluent library in $libraries"
        cd $libraries/python-fluent
        git checkout master
        git pull
        git checkout -q $version
        cd $install
    fi

    # Check out or update external p12n-extract library
    if [ ! -d $libraries/p12n/.git ]
    then
        echogreen "Checking out the p12n-extract library in $libraries"
        cd $libraries
        git clone https://github.com/flodolo/p12n-extract/ p12n
        #TODO: remove this
        cd p12n;git checkout x-channel;cd ..
        cd $install
    else
        echogreen "Updating the p12n-extract library in $libraries"
        cd $libraries/p12n
        #TODO: remove this
        git checkout x-channel
        git pull
        cd $install
    fi
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
                echogreen $repo_folder/$locale/
                if [ "$locale" = "en-US" ]
                then
                    #TODO: update URL
                    hg clone https://hg.mozilla.org/users/axel_mozilla.com/gecko-strings-quarantine $locale
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
else
    cd "${install}"
    CURRENT_TIP=$(git rev-parse HEAD)
    LATEST_TAG=$(git describe --abbrev=0 --tags | xargs -I {} git rev-list -n 1 {})
    if [ "${CURRENT_TIP}" = "${LATEST_TAG}" ]
    then
        DEV_VERSION=""
    fi
fi
echo "${CURRENT_TIP:0:7}${DEV_VERSION}" > "${install}/cache/version.txt"

setupExternalLibraries
initGeckoStringsRepo
initOtherSources

# Check out GitHub repos
echogreen "mozilla.org repo being checked out from GitHub"
cd $mozilla_org
if [ ! -d $mozilla_org/.git ]
then
    echogreen "Checking out mozilla.org repo"
    git clone https://github.com/mozilla-l10n/www.mozilla.org .
fi

echogreen "Firefox for iOS repo being checked out from GitHub"
cd $firefox_ios
if [ ! -d $firefox_ios/.git ]
then
    echogreen "Checking out Firefox for iOS repo"
    git clone https://github.com/mozilla-l10n/firefoxios-l10n .
fi

echogreen "Focus for iOS repo being checked out from GitHub"
cd $focus_ios
if [ ! -d $focus_ios/.git ]
then
    echogreen "Checking out Focus for iOS repo"
    git clone https://github.com/mozilla-l10n/focusios-l10n .
fi

echogreen "Focus Android iOS repo being checked out from GitHub"
cd $focus_android
if [ ! -d $focus_android/.git ]
then
    echogreen "Checking out Focus for Android repo"
    git clone https://github.com/mozilla-l10n/focus-android-l10n .
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
