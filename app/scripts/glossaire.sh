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

all_locales=true

if [ $# -eq 1 ]
then
    # I have exactly one parameter, it should be the locale code
    all_locales=false
    locale_code=$1
fi

if [ $# -gt 1 ]
then
    # Too many parameters, warn and exit
    echo "ERROR: too many arguments. Run 'glossaire.sh' without parameters to"
    echo "update all locales, or add the locale code as the only parameter "
    echo "(e.g. 'glossaire.sh fr' to update only French)."
    exit 1
fi

# Get server configuration variables
APP_FOLDER=$(dirname $PWD)
export PATH=$PATH:$APP_FOLDER/app/inc
export PATH=$PATH:$APP_FOLDER/

# We need to store the current directory value for the CRON job
DIR=$(dirname "$0")
source $DIR/iniparser.sh

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
                echogreen "Update $repo_name/$locale"
                cd $locale
                hg pull -r default
                hg update -C
                cd ..
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
                echogreen "Update $repo_name/$locale"
                cd $locale
                hg pull -r default
                hg update -C
                cd ..
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

updateGaiaRepo "gaia"
updateGaiaRepo "1_2"
updateGaiaRepo "1_3"
updateGaiaRepo "1_4"
updateGaiaRepo "2_0"

# mozilla.org has its own extraction script, mozorg.php
updateFromSVN

# Generate cache of bugzilla components if it doesn't exist or it's older than 7 days
cd $install
if [ -f cache/bugzilla_components.json ]
then
    # File exist, check the date
    if [ $(find cache/bugzilla_components.json -mtime +6) ]
    then
        echored "Generating cache/bugzilla_components.json (file older than a week)"
        nice -20 python app/scripts/bugzilla_query.py
    else
        echogreen "No need to generate Bugzilla components cache"
    fi
else
    # File does not exist
    echored "Generating cache/bugzilla_components.json (file missing)"
    nice -20 python app/scripts/bugzilla_query.py
fi

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
if $all_locales
then
    cd $root
    echogreen "Creating a snapshot of extracted strings in web/data.tar.gz"
    tar --exclude='*.tmx' -zcf datatemp.tar.gz TMX

    echogreen "Snapshot created in the web root for download"
    mv datatemp.tar.gz $install/web/data.tar.gz
fi
