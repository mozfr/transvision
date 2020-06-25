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
eval $(cat $script_path/../config/config.ini | $script_path/ini_to_bash.py)

# Check if we have sources
echogreen "Checking if Transvision sources are available..."
if ! $(ls $config/sources/*.txt &> /dev/null)
then
    echored "CRITICAL ERROR: no sources available, aborting."
    echored "Check the value for l10nwebservice in your config.ini and run setup.sh"
    exit
fi

# Create all bash variables
source $script_path/bash_variables.sh

function updateLocale() {
    # Update this locale's repository
    # $1: Path to l10n repository
    # $2: Locale code
    # $3: Repository name

    # Assign input variables to variables with meaningful names
    l10n_path="$1"
    locale="$2"
    repository_name="$3"

    cd $l10n_path/$locale
    # Check if there are incoming changesets
    hg incoming -r default --bundle incoming.hg 2>&1 >/dev/null
    incoming_changesets=$?
    if [ $incoming_changesets -eq 0 ]
    then
        # Update with incoming changesets and remove the bundle
        echogreen "Updating $repository_name"
        hg pull --update incoming.hg
        rm incoming.hg

        # Return 1: we need to create the cache for this locale
        return 1
    else
        echogreen "There are no changes to pull for $repository_name"

        # Return 0: no need to create the cache
        return 0
    fi
}

function updateGeckoStringsChannelRepo() {
    # Update specified repository. Parameters:
    # $1: Channel name used in folders and TMX
    # $2: Channel name used in variable names

    function buildCache() {
        # Build the cache
        # $1: Locale code
        echogreen "Create cache for $repo_name/$1"
        nice -20 python $install/app/scripts/tmx/tmx_products.py $repo_folder/$1/ $1 en-US $repo_name
        if [ "$1" = "en-US" ]
        then
            # Append strings for other sources
            # Chatzilla
            nice -20 python $install/app/scripts/tmx/tmx_products.py $sources_path/chatzilla/locales/en-US en-US en-US $repo_name append extensions/irc
        else
            nice -20 python $install/app/scripts/tmx/tmx_products.py $repo_folder/$1/extensions/irc $1 en-US $repo_name append extensions/irc
        fi
    }

    local repo_name="gecko_strings"
    local repo_folder="$gecko_strings_path"
    local locale_list="gecko_strings_locales"

    updated_english=false

    # Store md5 of the existing en-US cache before updating the repositories
    cache_file="${root}TMX/en-US/cache_en-US_${repo_name}.php"
    if [ -f $cache_file ]
    then
        existing_md5=($(md5sum $cache_file))
    else
        existing_md5=0
    fi

    # Update en-US, create TMX for en-US and check the updated md5
    hg --cwd $repo_folder/en-US pull --update -r default
    buildCache en-US
    updated_md5=($(md5sum $cache_file))
    if [ $existing_md5 != $updated_md5 ]
    then
        echo "English strings have been updated."
        updated_english=true
    fi

    if [ "$all_locales" = true ]
    then
        for locale in $(cat ${!locale_list})
        do
            if [ $locale != "en-US" ]
            then
                if [ -d $repo_folder/$locale ]
                then
                    updated_locale=0
                    if [ "$checkrepo" = true ]
                    then
                        updateLocale $repo_folder $locale $repo_name/$locale
                        updated_locale=$?
                    fi

                    # Check if we have a cache file for this locale. If it's a brand
                    # new locale, we'll have the folder and no updates, but we
                    # still need to create the cache.
                    cache_file="${root}TMX/${locale}/cache_${locale}_${repo_name}.php"
                    if [ ! -f $cache_file ]
                    then
                        echored "Cache doesn't exist for ${repo_name}/${locale}"
                        updated_locale=1
                    else
                        php -l $cache_file 2>&1 1>/dev/null
                        if [ $? -ne 0 ]
                        then
                            # There are PHP errors, force the rebuild
                            echored "PHP errors in $cache_file. Forcing rebuild."
                            updated_locale=1
                        fi
                    fi

                    if [ "$forceTMX" = true -o "$updated_english" = true -o "$updated_locale" -eq 1 ]
                    then
                        buildCache $locale
                    fi
                else
                    echored "Folder $repo_folder/$locale does not exist. Run setup.sh to fix the issue."
                fi
            fi
        done
    else
        if [ -d $repo_folder/$locale_code ]
        then
            updated_locale=0
            if [ "$checkrepo" = true ]
            then
                updateLocale $repo_folder $locale_code $repo_name/$locale_code
                updated_locale=$?
            fi

            cache_file="${root}TMX/${locale_code}/cache_${locale_code}_${repo_name}.php"
            if [ ! -f $cache_file ]
            then
                echored "Cache doesn't exist for ${repo_name}/${locale_code}"
                updated_locale=1
            else
                php -l $cache_file 2>&1 1>/dev/null
                if [ $? -ne 0 ]
                then
                    # There are PHP errors, force the rebuild
                    echored "PHP errors in $cache_file. Forcing rebuild."
                    updated_locale=1
                fi
            fi

            if [ "$forceTMX" = true -o "$updated_english" = true -o "$updated_locale" -eq 1 ]
            then
                buildCache $locale_code
            fi
        else
            echored "Folder $repo_folder/$locale_code does not exist."
        fi
    fi
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
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $android_l10n/l10n.toml en-US android_l10n
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
    nice -20 $install/app/scripts/tmx/tmx_projectconfig.py $mozilla_org/l10n-pontoon.toml en mozilla_org
}

echogreen "Activating virtualenv..."
source $install/python-venv/bin/activate || exit 1

updateGeckoStringsChannelRepo
updateMozOrg
updateOtherProduct firefox_ios "Firefox for iOS" tmx_xliff
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
