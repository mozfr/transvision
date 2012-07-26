#!/bin/bash

# Variables
# mozfr location:
# root=/
# local_hg=/data/HG
root=~/transvision
local_hg=$root/data/hg
glossaire=$root/glossaire
checkrepo=false

# List of locations of our local hg repos
release_l10n=$local_hg/RELEASE_L10N
beta_l10n=$local_hg/BETA_L10N
aurora_l10n=$local_hg/AURORA_L10N
trunk_l10n=$local_hg/TRUNK_L10N

release_source=$local_hg/RELEASE_EN-US
beta_source=$local_hg/BETA_EN-US
aurora_source=$local_hg/AURORA_EN-US
trunk_source=$local_hg/TRUNK_EN-US


# List of locales per branch
trunk_locales=$root/trunk.txt
aurora_locales=$root/aurora.txt
beta_locales=$root/beta.txt
release_locales=$root/release.txt

# Make sure that we have the file structure
mkdir -p $release_l10n
mkdir -p $beta_l10n
mkdir -p $aurora_l10n
mkdir -p $trunk_l10n
mkdir -p $glossaire

# Make sure we have all the glossaire files in the Transvision repo
cp tmxmaker.py $glossaire
cp glossaire.sh $glossaire

# Make sure we have all the list of locales in the Transvision repo
cp trunk.txt $root
cp aurora.txt $root
cp beta.txt $root
cp release.txt $root
cp list_rep_mozilla-central.txt $root
cp list_rep_comm-central.txt $root

# Restructure en-US
for dir in `cat $root/list_rep_mozilla-central.txt`
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

for dir in `cat $root/list_rep_comm-central.txt`
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
fi

# Make sure we have hg repos in the directories, if not check them out
initL10nRepo() {
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

    if [ $1 = trunk ]
    then
        cd $trunk_l10n
    fi

    echo $1
    echo $aurora_l10n
    pwd

    for i in `cat $root/$1.txt`
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

initSourceRepo() {

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

    if [ $1 = trunk ]
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

initSourceRepo trunk
initSourceRepo release
initSourceRepo beta
initSourceRepo aurora

initL10nRepo trunk
initL10nRepo release
initL10nRepo beta
initL10nRepo aurora
