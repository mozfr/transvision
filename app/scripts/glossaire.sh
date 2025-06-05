#! /usr/bin/env bash

# Syntax:
# - locale code: update only the requested locale
# - 'no-snapshot': avoid creating a data snapshot

function interrupt_code()
# This code runs if user hits control-c
{
  echored "\n*** Operation interrupted ***\n"
  exit $?
}

# Trap keyboard interrupt (control-c)
trap interrupt_code SIGINT

# Pretty printing functions
standard_color=$(tput sgr0)
green=$(tput setaf 2; tput bold)
red=$(tput setaf 1)

function echored() {
    echo -e "$red$*$standard_color"
}

function echogreen() {
    echo -e "$green$*$standard_color"
}

function echo_manual() {
    echo "Run 'glossaire.sh' without parameters to update all locales."
    echo "Run 'glossaire.sh help' to display this manual."
    echo "---"
    echo "To update only one locale, add the locale code as first parameter"
    echo "(e.g. 'glossaire.sh fr' to update only French)."
    echo "---"
    echo "To update all locales, and avoid creating a data snapshot at the end, add 'no-snapshot'"
    echo "(e.g. 'glossaire.sh no-snapshot' to update all locales without creating a data snapshot)."
    echo "---"
    echo "To update only one locale, and avoid creating a data snapshot at the end, add locale code and 'no-snapshot'"
    echo "(e.g. 'glossaire.sh fr no-snapshot' to update only French without creating a data snapshot)."
    echo "---"
    echo "To avoid pulling changes from repositories, add 'no-update'"
    echo "(e.g. 'glossaire.sh fr no-update' to update only French without updating repositories)."
    echo "---"
    echo "To force TMX creation, add 'force-tmx'"
    echo "(e.g. 'glossaire.sh force-tmx' to update all locales and forcing TMX creation)."
    echo "(e.g. 'glossaire.sh fr no-snapshot force-tmx' to update only French without creating a data snapshot and forcing TMX creation)."
}

all_locales=true
create_snapshot=true
forceTMX=false
checkrepo=true

while [[ $# -gt 0 ]]
do
    case $1 in
        force-tmx)
            forceTMX=true
        ;;
        help)
            echo_manual
            exit
        ;;
        no-snapshot)
            create_snapshot=false
        ;;
        no-update)
            checkrepo=false
        ;;
        *)
            all_locales=false
            locale_code=$1
        ;;
    esac
    shift
done

echo "Request summary:"
echo "* Create data snapshot: ${create_snapshot}"
echo "* Force TMX creation: ${forceTMX}"
echo "* Update repositories: ${checkrepo}"
if [ "$all_locales" = true ]
then
    echo "* Elaborate all locales: ${all_locales}"
else
    echo "* Elaborate locale: ${locale_code}"
fi

# Get configuration variables from config/config.ini
app_folder=$(dirname $PWD)
export PATH=$PATH:$app_folder/app/inc
export PATH=$PATH:$app_folder/

# Store the relative path to the script
script_path=$(dirname "$0")

# Convert .ini file in bash variables
eval $($script_path/ini_to_bash.py $script_path/../config/config.ini)

# Check if we have sources
echogreen "Checking if Transvision sources are available..."
if ! $(ls $config/sources/*.txt &> /dev/null)
then
    echored "CRITICAL ERROR: no sources available, aborting."
    echored "Run setup.sh first."
    exit
fi

# Create all bash variables
source $script_path/bash_variables.sh

function updateMultirepo() {
    function buildCache() {
        # Build the cache
        # $1: Path containing locale folder
        # $2: Locale code
        local path="$1"
        local locale="$2"
        echogreen "Create cache for $path/$locale"
        mkdir -p "${root}TMX/${locale}/"
        nice -20 python $install/app/scripts/tmx/tmx_products.py --path $path/$locale/ --locale $locale --ref en-US --repo $repo_name
    }

    local repo_name="$1"
    local var_repo_path="$1_path"
    local var_locales_list="$1_locales"
    local repo_folder=${!var_repo_path}
    local locale_list=${!var_locales_list}

    # Update en-US, create TMX for en-US
    git -C $repo_folder/en-US pull
    buildCache $repo_folder en-US

    # Pull l10n repository if necessary
    if [ "$checkrepo" = true ]
    then
        git -C $repo_folder/l10n pull
    fi

    if [ "$all_locales" = true ]
    then
        locales=$(cat $locale_list)
    else
        locales=($locale_code)
    fi

    for locale in $locales
    do
        if [ $locale != "en-US" ]
        then
            if [ -d $repo_folder/l10n/$locale ]
            then
                buildCache $repo_folder/l10n $locale
            else
                echored "Folder $repo_folder/l10n/$locale does not exist. Run setup.sh to fix the issue."
            fi
        fi
    done
}

function updateSeamonkey() {
    function buildCache() {
        # Build the cache
        # $1: Locale code
        echogreen "Create cache for $repo_name/$1"
        mkdir -p "${root}TMX/${locale}/"
        nice -20 python $install/app/scripts/tmx/tmx_products.py --path $repo_folder/$1/ --locale $1 --ref en-US --repo $repo_name
    }

    local repo_name="seamonkey"
    local repo_folder="$seamonkey_path"
    local locale_list="seamonkey_locales"

    if [ "$checkrepo" = true ]
    then
        # Pull repo
        echogreen "Update seamonkey repository"
        git -C $repo_folder pull
    fi

    # Build cache for en-US first, then other locales
    buildCache en-US
    for locale in $(cat ${!locale_list})
    do
        buildCache $locale
    done
}

function updateOtherProduct() {
    # $1: product code
    # $2: product name
    # $3: extraction script
    if [ "$checkrepo" = true ]
    then
        # If $1 = "firefox_ios", ${!1} is equal to $firefox_ios
        cd ${!1}
        echogreen "Update GitHub repository"
        git pull
    fi
    echogreen "Extract strings for $2"
    cd $install
    nice -20 $install/app/scripts/tmx/$3 $1
}

function updateAndroidl10n() {
    if [ "$checkrepo" = true ]
    then
        cd $android_l10n
        echogreen "Update GitHub repository"
        git pull
    fi
    echogreen "Extract strings for android-l10n"
    cd $install
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $android_l10n/firefox.toml --ref en-US --repo android_l10n
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $android_l10n/mozilla-mobile/focus-android/l10n.toml --ref en-US --repo android_l10n --append --prefix mozilla-mobile/focus-android
}

function updateMozOrg() {
    if [ "$checkrepo" = true ]
    then
        cd $mozilla_org
        echogreen "Update GitHub repository"
        git pull
    fi
    echogreen "Extract strings for mozilla.org (Fluent)"
    cd $install
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $mozilla_org/l10n-pontoon.toml --ref en --repo mozilla_org
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $mozilla_org/l10n-vendor.toml --ref en --repo mozilla_org
}

function updateFirefoxCom() {
    if [ "$checkrepo" = true ]
    then
        cd $firefox_com
        echogreen "Update GitHub repository"
        git pull
    fi
    echogreen "Extract strings for firefox.com (Fluent)"
    cd $install
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $firefox_com/l10n-pontoon.toml --ref en --repo firefox_com
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $firefox_com/l10n-vendor.toml --ref en --repo firefox_com
}

echogreen "Activating virtualenv..."
source $install/python-venv/bin/activate || exit 1

updateMultirepo gecko_strings
updateMultirepo thunderbird
updateSeamonkey
updateMozOrg
updateFirefoxCom
updateOtherProduct firefox_ios "Firefox for iOS" tmx_xliff
updateOtherProduct vpn_client "Mozilla VPN Client" tmx_xliff
updateAndroidl10n

# Create a file to get the timestamp of the last string extraction for caching
echogreen "Creating extraction timestamp for cache system"
touch cache/lastdataupdate.txt

echogreen "Deleting all the old cached files"
rm -f cache/*.cache

echogreen "Deleting custom TMX files"
rm -f web/download/*.tmx

# Create a snapshot of all extracted data for download
if [ "$create_snapshot" = true ]
then
    cd $root
    echogreen "Creating a snapshot of extracted strings in web/data.tar.gz"
    tar --exclude="*.tmx" -zcf datatemp.tar.gz TMX

    echogreen "Snapshot created in the web root for download"
    mv datatemp.tar.gz $install/web/data.tar.gz
fi
