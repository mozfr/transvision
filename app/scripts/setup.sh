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

# Store current directory path to be able to call this script from anywhere
DIR=$(dirname "$0")
# Convert .ini file in bash variables
eval $(cat $DIR/../config/config.ini | $DIR/ini_to_bash.py)

# Generate sources (gaia versions, supported locales)
echogreen "Generate list of locales and supported Gaia versions"
php $DIR/generate_sources $config

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

function createSymlinks() {
    branches=( trunk aurora beta release )

    case "$1" in
        "mozilla" | "comm" )
        # Restructure en-US mozilla-* and comm-*
        for dir in $(cat "$config/list_rep_$1-central.txt")
        do
            for branch in "${branches[@]}"
            do
                if [ "$branch" == "trunk" ]
                then
                    # Possible values: mozilla-central, comm-central
                    local repo_name="$1-central"
                else
                    # Possible values: mozilla-aurora, comm-aurora, mozilla-beta, etc.
                    local repo_name="$1-$branch"
                fi

                path="$local_hg/${branch^^}_EN-US/COMMUN/$dir"
                if [ ! -L "$path/en-US" ]
                then
                    echored "Missing symlink for ${branch^^}_EN-US/COMMUN/$dir"
                    mkdir -p "$path"
                    ln -s "$local_hg/${branch^^}_EN-US/$repo_name/$dir" "$path"
                fi
            done
        done
        ;;

        "chatzilla" | "venkman")
        # Restructure chatzilla and venkman
        for branch in "${branches[@]}"
        do
            if [ "$1" == "chatzilla" ]
            then
                # Source repo is called "chatzilla", l10n folder is "irc"
                local dir="extensions/irc/locales/en-US"
            else
                local dir="extensions/$1/locales/en-US"
            fi
            repo_name="$1/locales/en-US"
            path="$local_hg/${branch^^}_EN-US/COMMUN/$dir"
            if [ ! -L "$path/en-US" ]
            then
                echored "Missing symlink for ${branch^^}_EN-US/COMMUN/$dir"
                mkdir -p "$path"
                # Since these products have only one repo, we always create
                # symlinks to TRUNK in order to check out source code only once
                ln -s "$local_hg/TRUNK_EN-US/$repo_name" "$path"
            fi
        done
        ;;
    esac
}

function checkoutSilme() {
    # Check out SILME library to a specific version (0.8.0)
    if [ ! -d $libraries/silme/.hg ]
    then
        echogreen "Checking out the SILME library into $libraries"
        cd $libraries
        hg clone http://hg.mozilla.org/l10n/silme -u silme-0.8.0
        cd $install
    fi
}

# Make sure we have hg repos in the directories, if not check them out
function initDesktopL10nRepo() {
    if [ "$1" == "central" ]
    then
        local repo_folder="trunk_l10n"
        local repo_path="http://hg.mozilla.org/l10n-central"
        local locale_list="trunk_locales"
    else
        local repo_folder="${1}_l10n"
        local repo_path="http://hg.mozilla.org/releases/l10n/mozilla-$1"
        local locale_list="${1}_locales"
    fi

    # If repo_folder="trunk_l10n", ${!repo_folder} is equal to $trunk_l10n
    cd ${!repo_folder}

    for locale in $(cat ${!locale_list})
        do
            if [ ! -d $locale ]
            then
                mkdir $locale
            fi

            if [ ! -d $locale/.hg ]
            then
                echogreen "Checking out the following repo:"
                echogreen $1/$locale/
                hg clone $repo_path/$locale $locale
            fi

            if [ ! -d $root/TMX/$1/$locale ]
            then
                # ${1^^} = uppercase($1)
                echogreen "Creating this locale cache for ${1^^}:"
                echogreen $locale
                mkdir -p $root/TMX/$1/$locale
            fi
    done
}

function initDesktopSourceRepo() {
    if [ "$1" == "central" ]
    then
        if [ ! -d $trunk_source/comm-central/.hg ]
        then
            echogreen "Checking out the following repo:"
            echogreen $trunk_source/comm-central/
            cd $trunk_source
            hg clone http://hg.mozilla.org/comm-central/
        fi

        if [ ! -d $trunk_source/mozilla-central/.hg ]
        then
            echogreen "Checking out the following repo:"
            echogreen $trunk_source/mozilla-central/
            cd $trunk_source
            hg clone http://hg.mozilla.org/mozilla-central/
        fi

        # Checkout chatzilla and venkman only on trunk, since they don't
        # have branches. Can add other products to array nobranch_products,
        # as long as their code is located in http://hg.mozilla.org/PRODUCT
        local nobranch_products=( chatzilla venkman )
        for product in "${nobranch_products[@]}"
        do
            if [ ! -d $trunk_source/$product/.hg ]
            then
                echogreen "Checking out the following repo:"
                echogreen $trunk_source/$product
                cd $trunk_source
                hg clone http://hg.mozilla.org/$product/
            fi
        done
    else
        local target="$1_source"
        # If target="aurora_source", ${!target} is equal to $aurora_source
        cd ${!target}
        if [ ! -d ${!target}/comm-$1/.hg ]
        then
            echogreen "Checking out the following repo:"
            echogreen ${!target}/comm-$1/
            hg clone http://hg.mozilla.org/releases/comm-$1/
        fi

        if [ ! -d ${!target}/mozilla-$1/.hg ]
        then
            echogreen "Checking out the following repo:"
            echogreen ${!target}/mozilla-$1/
            hg clone http://hg.mozilla.org/releases/mozilla-$1/
        fi
    fi

    if [ ! -d $root/TMX/$1/en-US ]
    then
        echogreen "Creating this locale cache for $1:"
        echogreen en-US
        mkdir -p $root/TMX/$1/en-US
    fi
}

function initGaiaRepo () {
    # $1 = version, could be "gaia" or a version number with underscores (e.g. 1_3, 1_4 etc)
    if [ "$1" == "gaia" ]
    then
        local locale_list="gaia_locales"
        local repo_name="gaia"
        local repo_pretty_name="Gaia"
        local repo_path="http://hg.mozilla.org/gaia-l10n"
    else
        local locale_list="gaia_locales_$1"
        local repo_name="gaia_$1"
        # If version is "1_4", repo_pretty_name will be "Gaia 1.4"
        local repo_pretty_name="Gaia ${1/_/.}"
        local repo_path="http://hg.mozilla.org/releases/gaia-l10n/v$1"
    fi

    echogreen "$repo_pretty_name initialization"
    # Initialize repo only if folder exists
    # If repo_name="gaia", ${!repo_name} is equal to $gaia
    if [ ! -d ${!repo_name} ]
    then
        echored "$repo_pretty_name folder does not exist"
    else
        cd ${!repo_name}
        for locale in $(cat ${!locale_list})
            do
                if [ ! -d $locale/.hg ]
                then
                    echogreen "Checking out the following repo:"
                    echogreen $locale
                    hg clone $repo_path/$locale
                fi

                if [ ! -d $root/TMX/$repo_name/$locale ]
                then
                    echogreen "Creating this locale cache for $repo_pretty_name:"
                    echogreen $locale
                    mkdir -p $root/TMX/$repo_name/$locale
                fi
        done
    fi
}

checkoutSilme

initDesktopSourceRepo "central"
initDesktopSourceRepo "release"
initDesktopSourceRepo "beta"
initDesktopSourceRepo "aurora"

initDesktopL10nRepo "central"
initDesktopL10nRepo "release"
initDesktopL10nRepo "beta"
initDesktopL10nRepo "aurora"

# Create symlinks (or recreate if missing)
createSymlinks "mozilla"
createSymlinks "comm"
createSymlinks "chatzilla"
createSymlinks "venkman"

# Set up all Gaia versions
for gaia_version in $(cat ${gaia_versions})
do
    initGaiaRepo ${gaia_version}
done

# Check out svn repos
echogreen "mozilla.org repo being checked out from subversion"
cd $mozilla_org
if [ ! -d $mozilla_org/.svn ]
then
    echogreen "Checking out mozilla.org repo"
    svn co https://svn.mozilla.org/projects/mozilla.com/trunk/locales/ .
fi

# We now deal with L20n test repo as a specific case
echogreen "L20n test repo initialization"
cd $l20n_test
if [ ! -d $l20n_test/l20ntestdata/.git ]
then
    echogreen "Checking out the following repo:"
    git clone https://github.com/pascalchevrel/l20ntestdata.git
fi

for locale in $(cat $l20n_test_locales)
    do
        if [ ! -d $root/TMX/l20n_test/$locale ]
        then
            echogreen "Creating this locale cache for L20n test:"
            echogreen $locale
            mkdir -p $root/TMX/l20n_test/$locale
        fi
done

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
stats_files=( $install/web/stats_locales.json
              $install/web/stats_requests.json )

for stats_file in "${stats_files[@]}"
do
    if [ ! -f $stats_file ]
    then
        echogreen "Add $stats_file file"
        echo '{}' > $stats_file
    fi
done
