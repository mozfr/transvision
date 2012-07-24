#!/bin/bash


# Variables

# mozfr location:
# root=/
# local_hg=/data/HG

root=~/transvision
local_hg=~/transvision/data/hg

# list of locales per branch
trunk_locales=$root/trunk.txt
aurora_locales=$root/aurora.txt
beta_locales=$root/beta.txt
release_locales=$root/release.txt

# list of locations of our local hg repos
release_l10n=$local_hg/RELEASE_L10N
beta_l10n=$local_hg/BETA_L10N
aurora_l10n=$local_hg/AURORA_L10N
trunk_l10n=$local_hg/TRUNK_L10N

release_source=$local_hg/RELEASE_EN-US
beta_source=$local_hg/BETA_EN-US
aurora_source=$local_hg/AURORA_EN-US
trunk_source=$local_hg/TRUNK_EN-US


# Make sure that we have the file structure

mkdir -p $release_l10n
mkdir -p $beta_l10n
mkdir -p $aurora_l10n
mkdir -p $trunk_l10n
mkdir -p $local_hg/RELEASE_EN-US/COMMUN
mkdir -p $local_hg/BETA_EN-US/COMMUN
mkdir -p $local_hg/AURORA_EN-US/COMMUN
mkdir -p $local_hg/TRUNK_EN-US/COMMUN
mkdir -p $local_hg/glossaire

# Make sure we have hg repos in the above directories, if not check them out

cd $release_source;

if [ ! -d $release_source/comm-release/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/comm-release/
    hg clone http://hg.mozilla.org/releases/comm-release/
fi

if [ ! -d $release_source/mozilla-release/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/mozilla-release/
    hg clone http://hg.mozilla.org/releases/mozilla-release/
fi

if [ ! -d $release_source/comm-beta/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/comm-beta/
    hg clone http://hg.mozilla.org/releases/comm-beta/
fi

if [ ! -d $release_source/mozilla-beta/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/mozilla-beta/
    hg clone http://hg.mozilla.org/releases/mozilla-beta/
fi

if [ ! -d $release_source/comm-aurora/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/comm-aurora/
    hg clone http://hg.mozilla.org/releases/comm-aurora/
fi

if [ ! -d $release_source/mozilla-aurora/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/mozilla-aurora/
    hg clone http://hg.mozilla.org/releases/mozilla-aurora/
fi

if [ ! -d $release_source/comm-central/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/comm-central/
    hg clone http://hg.mozilla.org/comm-central/
fi

if [ ! -d $release_source/mozilla-central/.hg ]
then
    echo "checking out the following repo:"
    echo $release_source/mozilla-central/
    hg clone http://hg.mozilla.org/mozilla-central/
fi


# RELEASE

cd $release_source
cd comm-release
hg pull -r tip
hg update -c
cd ../mozilla-release
hg pull -r tip
hg update -c

cd $release_l10n
for i in `cat $release_locales`
    do
        if [ ! -d $i/.hg ]
        then
            echo "checking out the following repo:"
            echo $release_l10n/$i/
            hg clone http://hg.mozilla.org/releases/l10n/mozilla-release/$i
        fi
    cd $i
    hg pull -r tip
    hg update -c
    cd ..
done

cd $local_hg/glossaire/

for i in `cat $release_locales`
do
    echo $i
    nice -20 python tmxmaker.py $local_hg/RELEASE_L10N/$i/ $local_hg/RELEASE_EN-US/COMMUN/ $i en-US release
done

# BETA
cd $beta_source
cd comm-beta
hg pull -r tip
hg update -c
cd ../mozilla-beta
hg pull -r tip
hg update -c

cd $beta_l10n
for i in `cat $beta_locales`
    do
        if [ ! -d $i/.hg ]
        then
            echo "checking out the following repo:"
            echo $beta_l10n/$i/
            hg clone http://hg.mozilla.org/releases/l10n/mozilla-beta/$i
        fi
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
done

cd $local_hg/glossaire/

for i in `cat $beta_locales`
do
    echo $i
    nice -20 python tmxmaker.py $local_hg/BETA_L10N/$i/ $local_hg/BETA_EN-US/COMMUN/ $i en-US beta
done

# TRUNK
cd $trunk_source
cd comm-central
hg pull -r tip
hg update -c
cd ../mozilla-central
hg pull -r tip
hg update -c

cd $trunk_l10n
for i in `cat $trunk_locales`
    do
        if [ ! -d $i/.hg ]
        then
            echo "checking out the following repo:"
            echo $trunk_l10n/$i/
            hg clone http://hg.mozilla.org/l10n-central/$i
        fi
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
done

cd $local_hg/glossaire/
for i in `cat $trunk_locales`
do
    echo $i
    nice -20 python tmxmaker.py $local_hg/TRUNK_L10N/$i/ $local_hg/TRUNK_EN-US/COMMUN/ $i en-US trunk
done


# AURORA
cd $aurora_source
cd comm-aurora
hg pull -r tip
hg update -c
cd ../mozilla-aurora
hg pull -r tip
hg update -c


cd $local_hg/AURORA_L10N/
for i in `cat $aurora_locales`
    do
        if [ ! -d $i/.hg ]
        then
            echo "checking out the following repo:"
            echo $aurora_l10n/$i/
            hg clone http://hg.mozilla.org/releases/l10n/mozilla-aurora/$i
        fi
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
done

cd $local_hg/glossaire/
for i in `cat $aurora_locales`
do
    echo $i
    nice -20 python tmxmaker.py $local_hg/AURORA_L10N/$i/ $local_hg/AURORA_EN-US/COMMUN/ $i en-US aurora
done


