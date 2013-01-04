#!/bin/bash

# get server configuration variables
source ./iniparser.sh

# Make sure that we have the file structure
mkdir -p $release_l10n
mkdir -p $beta_l10n
mkdir -p $aurora_l10n
mkdir -p $trunk_l10n
mkdir -p $gaia
mkdir -p $glossaire

# Restructure en-US
for dir in `cat $install/list_rep_mozilla-central.txt`
do
    mkdir -p $local_hg/RELEASE_EN-US/COMMUN/$dir
    ln -s  $local_hg/RELEASE_EN-US/mozilla-release/$dir $local_hg/RELEASE_EN-US/COMMUN/$dir

    mkdir -p $local_hg/BETA_EN-US/COMMUN/$dir
    ln -s  $local_hg/BETA_EN-US/mozilla-beta/$dir $local_hg/BETA_EN-US/COMMUN/$dir

    mkdir -p $local_hg/AURORA_EN-US/COMMUN/$dir
    ln -s  $local_hg/AURORA_EN-US/mozilla-aurora/$dir $local_hg/AURORA_EN-US/COMMUN/$dir

    mkdir -p $local_hg/TRUNK_EN-US/COMMUN/$dir
    ln -s  $local_hg/TRUNK_EN-US/mozilla-central/$dir $local_hg/TRUNK_EN-US/COMMUN/$dir
done

for dir in `cat $install/list_rep_comm-central.txt`
do
    mkdir -p $local_hg/RELEASE_EN-US/COMMUN/$dir
    ln -s  $local_hg/RELEASE_EN-US/comm-release/$dir $local_hg/RELEASE_EN-US/COMMUN/$dir

    mkdir -p $local_hg/BETA_EN-US/COMMUN/$dir
    ln -s  $local_hg/BETA_EN-US/comm-beta/$dir $local_hg/BETA_EN-US/COMMUN/$dir

    mkdir -p $local_hg/AURORA_EN-US/COMMUN/$dir
    ln -s  $local_hg/AURORA_EN-US/comm-aurora/$dir $local_hg/AURORA_EN-US/COMMUN/$dir

    mkdir -p $local_hg/TRUNK_EN-US/COMMUN/$dir
    ln -s  $local_hg/TRUNK_EN-US/comm-central/$dir $local_hg/TRUNK_EN-US/COMMUN/$dir
done

# Check out the SILME library and set it to the latest released version
if [ ! -d $glossaire/silme/.hg ]
then
    echo "Checking out the SILME library into $glossaire"
    cd $glossaire
    hg clone http://hg.mozilla.org/l10n/silme
    cd silme
    hg update -C silme-0.8
    cd $install
fi

# Make sure we have hg repos in the directories, if not check them out
initDesktopL10nRepo() {
    if [ $1 = aurora ]
    then
        cd $aurora_l10n
    fi

    if [ $1 = beta ]
    then
        cd $beta_l10n
    fi

    if [ $1 = release ]
    then
        cd $release_l10n
    fi

    if [ $1 = central ]
    then
        cd $trunk_l10n
    fi

    echo $1
    echo $aurora_l10n
    pwd

    for i in `cat $install/$1.txt`
        do
            if [ ! -d $i/.hg ]
            then
                echo "Checking out the following repo:"
                echo $1/$i/
                if [ $1 = trunk ]
                then
                    hg clone http://hg.mozilla.org/l10n-central/$i
                else
                    hg clone http://hg.mozilla.org/releases/l10n/mozilla-$1/$i
                fi
            fi

            if [ ! -d $3/$i ]
            then
                echo "Creating this locale TMX for $1:"
                echo $i
                mkdir -p $root/TMX/$1/$i
            fi
    done
}

initDesktopSourceRepo() {

    if [ $1 = aurora ]
    then
        target=$aurora_source
    fi

    if [ $1 = beta ]
    then
        target=$beta_source
    fi

    if [ $1 = release ]
    then
        target=$release_source
    fi

    if [ $1 = central ]
    then

        if [ ! -d $trunk_source/comm-central/.hg ]
        then
            echo "Checking out the following repo:"
            echo $trunk_source/comm-central/
            cd $trunk_source;
            hg clone http://hg.mozilla.org/comm-central/
        fi

        if [ ! -d $trunk_source/mozilla-central/.hg ]
        then
            echo "Checking out the following repo:"
            echo $trunk_source/mozilla-central/
            cd $trunk_source;
            hg clone http://hg.mozilla.org/mozilla-central/
        fi
    else
        echo $target
        cd $target;
        if [ ! -d $target/comm-$1/.hg ]
        then
            echo "Checking out the following repo:"
            echo $target/comm-$1/
            hg clone http://hg.mozilla.org/releases/comm-$1/
        fi

        if [ ! -d $target/mozilla-$1/.hg ]
        then
            echo "Checking out the following repo:"
            echo $target/mozilla-$1/
            hg clone http://hg.mozilla.org/releases/mozilla-$1/
        fi
    fi
}

initDesktopSourceRepo central
initDesktopSourceRepo release
initDesktopSourceRepo beta
initDesktopSourceRepo aurora

initDesktopL10nRepo central
initDesktopL10nRepo release
initDesktopL10nRepo beta
initDesktopL10nRepo aurora

# We now deal with Gaia as a specific case
echo "Gaia initialization"
cd $gaia
for i in `cat $install/gaia.txt`
    do
        if [ ! -d $i/.hg ]
        then
            echo "Checking out the following repo:"
            echo $i
            hg clone http://hg.mozilla.org/gaia-l10n/$i
        fi

        if [ ! -d $root/TMX/gaia/$i ]
        then
            echo "Creating this locale TMX for Gaia:"
            echo $i
            mkdir -p $root/TMX/gaia/$i
        fi
done

echo "add a log file"
touch $config_path/transvision.log
chown www-data:www-data $config_path/transvision.log
