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

# Get server configuration variables
source ./iniparser.sh

# Make sure that we have the file structure
mkdir -p $release_l10n
mkdir -p $beta_l10n
mkdir -p $aurora_l10n
mkdir -p $trunk_l10n
mkdir -p $gaia
mkdir -p $gaia_1_1
mkdir -p $gaia_1_2
mkdir -p $gaia_1_3
mkdir -p $l20n_test
mkdir -p $libraries

# Restructure en-US
for dir in $(cat $install/list_rep_mozilla-central.txt)
do
    path=$local_hg/RELEASE_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/RELEASE_EN-US/COMMUN/$dir
        ln -s  $local_hg/RELEASE_EN-US/mozilla-release/$dir $path
    fi

    path=$local_hg/BETA_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/BETA_EN-US/COMMUN/$dir
        ln -s  $local_hg/BETA_EN-US/mozilla-beta/$dir $path
    fi

    path=$local_hg/AURORA_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/AURORA_EN-US/COMMUN/$dir
        ln -s  $local_hg/AURORA_EN-US/mozilla-aurora/$dir $path
    fi

    path=$local_hg/TRUNK_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/TRUNK_EN-US/COMMUN/$dir
        ln -s  $local_hg/TRUNK_EN-US/mozilla-central/$dir $path
    fi
done

for dir in $(cat $install/list_rep_comm-central.txt)
do
    path=$local_hg/RELEASE_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/RELEASE_EN-US/COMMUN/$dir
        ln -s  $local_hg/RELEASE_EN-US/comm-release/$dir $path
    fi

    path=$local_hg/BETA_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/BETA_EN-US/COMMUN/$dir
        ln -s  $local_hg/BETA_EN-US/comm-beta/$dir $path
    fi

    path=$local_hg/AURORA_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/AURORA_EN-US/COMMUN/$dir
        ln -s  $local_hg/AURORA_EN-US/comm-aurora/$dir $path
    fi

    path=$local_hg/TRUNK_EN-US/COMMUN/$dir
    if [ ! -L $path/en-US ]
    then
        mkdir -p $local_hg/TRUNK_EN-US/COMMUN/$dir
        ln -s  $local_hg/TRUNK_EN-US/comm-central/$dir $path
    fi
done

# Check out SILME library to a specific version (0.8.0)
if [ ! -d $libraries/silme/.hg ]
then
    echogreen "Checking out the SILME library into $libraries"
    cd $libraries
    hg clone http://hg.mozilla.org/l10n/silme -u silme-0.8.0
    cd $install
fi

# Make sure we have hg repos in the directories, if not check them out
function initDesktopL10nRepo() {
    if [ "$1" == "central" ]
    then
        local repo_folder="trunk_l10n"
        local repo_path="http://hg.mozilla.org/l10n-central"
    else
        local repo_folder="${1}_l10n"
        local repo_path="http://hg.mozilla.org/releases/l10n/mozilla-$1"
    fi

    # If repo_folder="trunk_l10n", ${!repo_folder} is equal to $trunk_l10n
    cd ${!repo_folder}

    for locale in $(cat $install/$1.txt)
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
                echogreen "Creating this locale TMX for ${1^^}:"
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
            cd $trunk_source;
            hg clone http://hg.mozilla.org/comm-central/
        fi

        if [ ! -d $trunk_source/mozilla-central/.hg ]
        then
            echogreen "Checking out the following repo:"
            echogreen $trunk_source/mozilla-central/
            cd $trunk_source;
            hg clone http://hg.mozilla.org/mozilla-central/
        fi
    else
        local target="$1_source"
        # If target="aurora_source", ${!target} is equal to $aurora_source
        cd ${!target};
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
        echogreen "Creating this locale TMX for $1:"
        echogreen en-US
        mkdir -p $root/TMX/$1/en-US
    fi
}

function initGaiaRepo () {
    # $1 = version, could be "gaia" or a version number with underscores (e.g. 1_1, 1_2, etc)
    if [ "$1" == "gaia" ]
    then
        local locale_list="gaia_locales"
        local repo_name="gaia"
        local repo_pretty_name="Gaia"
        local repo_path="http://hg.mozilla.org/gaia-l10n"
    else
        local locale_list="gaia_locales_$1"
        local repo_name="gaia_$1"
        # If version is "1_1", repo_pretty_name will be "Gaia 1.1"
        local repo_pretty_name="Gaia ${1/_/.}"
        local repo_path="http://hg.mozilla.org/releases/gaia-l10n/v$1"
    fi

    echogreen "$repo_pretty_name initialization"
    # If repo_name="gaia", ${!repo_name} is equal to $gaia
    cd ${!repo_name}
    for locale in $(cat $install/$repo_name.txt)
        do
            if [ ! -d $locale/.hg ]
            then
                echogreen "Checking out the following repo:"
                echogreen $locale
                hg clone $repo_path/$locale
            fi

            if [ ! -d $root/TMX/$repo_name/$locale ]
            then
                echogreen "Creating this locale TMX for $repo_pretty_name:"
                echogreen $locale
                mkdir -p $root/TMX/$repo_name/$locale
            fi
    done
}

initDesktopSourceRepo "central"
initDesktopSourceRepo "release"
initDesktopSourceRepo "beta"
initDesktopSourceRepo "aurora"

initDesktopL10nRepo "central"
initDesktopL10nRepo "release"
initDesktopL10nRepo "beta"
initDesktopL10nRepo "aurora"

initGaiaRepo "gaia"
initGaiaRepo "1_1"
initGaiaRepo "1_2"
initGaiaRepo "1_3"

# We now deal with L20n test repo as a specific case
echogreen "L20n test repo initialization"
cd $l20n_test
if [ ! -d $l20n_test/l20ntestdata/.git ]
then
    echogreen "Checking out the following repo:"
    git clone https://github.com/pascalchevrel/l20ntestdata.git
fi

for locale in $(cat $install/l20n_test.txt)
    do
        if [ ! -d $root/TMX/l20n_test/$locale ]
        then
            echogreen "Creating this locale TMX for L20n test:"
            echogreen $locale
            mkdir -p $root/TMX/l20n_test/$locale
        fi
done

# Add .htaccess to TMX folder. Folder should already exists, but check in
# advance to be sure. I overwrite an existing .htaccess if already present.
echogreen "Add .htaccess to TMX folder"
if [ ! -d $root/TMX ]
    then
        echogreen "Creating TMX folder"
        mkdir -p $root/TMX
fi
echogreen -n "AddType application/octet-stream .tmx" > $root/TMX/.htaccess

# At this point I'm sure TMX exists, adding a symlink inside $install/web
if [ ! -L $install/web/TMX ]
then
    echogreen "Add symlink to $root/TMX inside $install/web"
    ln -s $root/TMX $install/web/TMX
fi

echogreen "Add log files"
touch $install/transvision.log
touch $install/web/stats.json
