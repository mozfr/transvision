#! /usr/bin/env bash

# Syntax:
# - without parameters: update all locales
# - one parameter (locale code): update only the requested locale

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

function echo_manual() {
    echo "ERROR: too many or incorrect arguments. "
    echo "Run 'glossaire.sh' without parameters to update all locales"
    echo "---"
    echo "To update only one locale, add the locale code as first parameter"
    echo "(e.g. 'glossaire.sh fr' to update only French)."
    echo "---"
    echo "To update all locales, and avoid creating a data snapshot at the end, add 'no-snapshot'"
    echo "(e.g. 'glossaire.sh no-snapshot' to update all locales without creating a data snapshot)."
    echo "---"
    echo "To update only one locale, and avoid creating a data snapshot at the end, add locale code and 'no-snapshot'"
    echo "(e.g. 'glossaire.sh fr no-snapshot' to update only French without creating a data snapshot)."

}

all_locales=true
create_snapshot=true

if [ $# -eq 1 ]
then
    # I have one parameter, it could be 'no-snapshot' or a locale code
    if [ "$1" == "no-snapshot" ]
    then
        create_snapshot=false
    else
        all_locales=false
        locale_code=$1
    fi
fi

if [ $# -eq 2 ]
then
    # I have two parameters, I expect the first to be a locale code, the
    # second to be 'no-snapshot'
    all_locales=false
    locale_code=$1
    if [ "$2" != "no-snapshot" ]
    then
        echo_manual
        exit 1
    fi
    create_snapshot=false
fi

if [ $# -gt 2 ]
then
    # Too many parameters, warn and exit
    echo_manual
    exit 1
fi

# Get server configuration variables
APP_FOLDER=$(dirname $PWD)
export PATH=$PATH:$APP_FOLDER/app/inc
export PATH=$PATH:$APP_FOLDER/

# Store current directory path to be able to call this script from anywhere
DIR=$(dirname "$0")
# Convert .ini file in bash variables
eval $(cat $DIR/../config/config.ini | $DIR/ini_to_bash.py)

# Check if we have sources
echogreen "Checking if Transvision sources are available..."
if ! $(ls $config/sources/*.txt &> /dev/null)
then
    echored "CRITICAL ERROR: no sources available, aborting."
    echored "Check the value for l10nwebservice in your config.ini and run setup.sh"
    exit
fi

# Create all bash variables
source $DIR/bash_variables.sh

# Decide if must update hg repositories and create TMX
checkrepo=true
createTMX=true

function updateStandardRepo() {
    # Update specified repository. Parameters:
    # $1 = channel name used in folders and TMX
    # $2 = channel name used in variable names

    local repo_name="$1"                # e.g. release, beta, aurora, central
    local comm_repo="comm-$1"           # e.g. comm-release, etc.
    local mozilla_repo="mozilla-$1"     # e.g. mozilla-release, etc.
    local repo_source="${2}_source"     # e.g. release_source, beta_source, aurora_source, trunk_source
    local repo_l10n="${2}_l10n"         # e.g. release_l10n, etc.
    local locale_list="${2}_locales"    # e.g. release_locales, etc.

    if $checkrepo
    then
        cd ${!repo_source}              # value of variable called repo, e.g. value of $release_source
        echogreen "Update $comm_repo"
        cd $comm_repo
        hg pull -r default
        hg update -C
        echogreen "Update $mozilla_repo"
        cd ../$mozilla_repo
        hg pull -r default
        hg update -C

        if $all_locales
        then
            cd ${!repo_l10n}            # value of variable called repo_l10n, e.g. value of $release_l10n
            for locale in $(cat ${!locale_list})
            do
                if [ -d ${!repo_l10n}/$locale ]
                then
                    echogreen "Update $repo_name/$locale"
                    cd $locale
                    hg pull -r default
                    hg update -C
                    cd ..
                else
                    echored "Folder ${!repo_l10n}/$locale does not exist. Run setup.sh to fix the issue."
                fi
            done
        else
            if [ -d ${!repo_l10n}/$locale_code ]
            then
                echogreen "Update $repo_name/$locale_code"
                cd ${!repo_l10n}/$locale_code
                hg pull -r default
                hg update -C
                cd ..
            else
                echored "Folder ${!repo_l10n}/$locale_code does not exist."
            fi
        fi
    fi

    cd $install
    if $createTMX
    then
        find -L ${!repo_source}/COMMUN/ -type l | while read -r file; do echored "$file is orphaned";  unlink $file; done
        if $all_locales
        then
            for locale in $(cat ${!locale_list})
            do
                echogreen "Create ${repo_name^^} cache for $locale"
                nice -20 python app/scripts/tmxmaker.py ${!repo_l10n}/$locale/ ${!repo_source}/COMMUN/ $locale en-US $repo_name
            done
        else
            if [ -d ${!repo_l10n}/$locale_code ]
            then
                echogreen "Create ${repo_name^^} cache for $locale_code"
                nice -20 python app/scripts/tmxmaker.py ${!repo_l10n}/$locale_code/ ${!repo_source}/COMMUN/ $locale_code en-US $repo_name
            else
                echored "Folder ${!repo_l10n}/$locale_code does not exist."
            fi
        fi

        echogreen "Create ${repo_name^^} cache for en-US"
        nice -20 python app/scripts/tmxmaker.py ${!repo_source}/COMMUN/ ${!repo_source}/COMMUN/ en-US en-US $repo_name
    fi
}


function updateNoBranchRepo() {
    if $checkrepo
    then
        # These repos exist only on trunk
        cd $trunk_source/$1
        echogreen "Update $1"
        hg pull -r default
        hg update -C
    fi
}


function updateGaiaRepo() {
    # Update specified Gaia repository. Parameters:
    # $1 = version, could be "trunk" or a version (e.g. 1_3, 1_4, etc)

    if [ "$1" == "gaia" ]
    then
        local locale_list="gaia_locales"
        local repo_name="gaia"
    else
        local locale_list="gaia_locales_$1"
        local repo_name="gaia_$1"
    fi

    if $checkrepo
    then
        if $all_locales
        then
            cd ${!repo_name}
            for locale in $(cat ${!locale_list})
            do
                if [ -d ${!repo_name}/$locale ]
                then
                    echogreen "Update $repo_name/$locale"
                    cd $locale
                    hg pull -r default
                    hg update -C
                    cd ..
                else
                    echored "Folder ${!repo_name}/$locale does not exist. Run setup.sh to fix the issue."
                fi
            done
        else
            if [ -d ${!repo_name}/$locale_code ]
            then
                echogreen "Update $repo_name/$locale_code"
                cd ${!repo_name}/$locale_code
                hg pull -r default
                hg update -C
                cd ..
            else
                echored "Folder ${!repo_name}/$locale_code does not exist."
            fi
        fi
    fi

    cd $install
    if $createTMX
    then
        if $all_locales
        then
            for locale in $(cat ${!locale_list})
            do
                echogreen "Create ${repo_name^^} cache for $locale"
                nice -20 python app/scripts/tmxmaker.py ${!repo_name}/$locale/ ${!repo_name}/en-US/ $locale en-US $repo_name
            done
        else
            if [ -d ${!repo_name}/$locale_code ]
            then
                echogreen "Create ${repo_name^^} cache for $locale_code"
                nice -20 python app/scripts/tmxmaker.py ${!repo_name}/$locale_code/ ${!repo_name}/en-US/ $locale_code en-US $repo_name
            else
                echored "Folder ${!repo_name}/$locale_code does not exist."
            fi
        fi

        echogreen "Create ${repo_name^^} cache for en-US"
        nice -20 python app/scripts/tmxmaker.py ${!repo_name}/en-US/ ${!repo_name}/en-US/ en-US en-US $repo_name
    fi
}

function updateFromSVN() {
    if $checkrepo
    then
        cd $mozilla_org
        echogreen "Update subversion repositories"
        svn up
    fi
    if $createTMX
    then
        echogreen "Extract strings on svn"
        cd $install
        nice -20 php app/inc/mozorg.php
    fi
}

# Update repos without branches first (their TMX is created in updateStandardRepo)
updateNoBranchRepo "chatzilla"
updateNoBranchRepo "venkman"

updateStandardRepo "release" "release"
updateStandardRepo "beta" "beta"
updateStandardRepo "aurora" "aurora"
updateStandardRepo "central" "trunk"

for gaia_version in $(cat ${gaia_versions})
do
    updateGaiaRepo ${gaia_version}
done

# mozilla.org has its own extraction script, mozorg.php
updateFromSVN

# Generate productization data
cd $install
echogreen "Extracting p12n data"
nice -20 python app/scripts/p12n_extract.py

# Update L20N test repo
if $checkrepo
then
    cd $l20n_test/l20ntestdata
    git pull origin master
fi

cd $install
if $createTMX
then
    if $all_locales
    then
        for locale in $(cat $l20n_test_locales)
        do
            echogreen "Create L20N test repo cache for $locale"
            nice -20 python app/scripts/tmxmaker.py $l20n_test/l20ntestdata/$locale/ $l20n_test/l20ntestdata/en-US/ $locale en-US l20n_test
        done
    else
        if [ -d $l20n_test/l20ntestdata/$locale_code ]
        then
            echogreen "Create L20N test repo cache for $locale_code"
            nice -20 python app/scripts/tmxmaker.py $l20n_test/l20ntestdata/$locale_code/ $l20n_test/l20ntestdata/en-US/ $locale_code en-US l20n_test
        else
            echored "Folder $l20n_test/$locale_code does not exist."
        fi
    fi

    echogreen "Create L20N test repo cache for en-US"
    nice -20 python app/scripts/tmxmaker.py $l20n_test/l20ntestdata/en-US/ $l20n_test/l20ntestdata/en-US/ en-US en-US l20n_test
fi

# Create a file to get the timestamp of the last string extraction for caching
echogreen "Creating extraction timestamp for cache system"
touch cache/lastdataupdate.txt

echogreen "Deleting all the old cached files"
rm -f cache/*.cache

echogreen "Deleting custom TMX files"
rm -f web/download/*.tmx

# Create a snapshot of all extracted data for download
if $create_snapshot
then
    cd $root
    echogreen "Creating a snapshot of extracted strings in web/data.tar.gz"
    tar --exclude='*.tmx' -zcf datatemp.tar.gz TMX

    echogreen "Snapshot created in the web root for download"
    mv datatemp.tar.gz $install/web/data.tar.gz
fi
