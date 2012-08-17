#!/bin/bash


# get server configuration variables
source ./iniparser.sh

# update hg repositories or not
checkrepo=false

# List of locales per branch
trunk_locales=$root/trunk.txt
aurora_locales=$root/aurora.txt
beta_locales=$root/beta.txt
release_locales=$root/release.txt

# List of locations of our local hg repos
release_l10n=$local_hg/RELEASE_L10N
beta_l10n=$local_hg/BETA_L10N
aurora_l10n=$local_hg/AURORA_L10N
trunk_l10n=$local_hg/TRUNK_L10N

release_source=$local_hg/RELEASE_EN-US
beta_source=$local_hg/BETA_EN-US
aurora_source=$local_hg/AURORA_EN-US
trunk_source=$local_hg/TRUNK_EN-US



# Update RELEASE
if [ "$checkrepo" = true ]
then
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
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
    done
fi

for i in `cat $release_locales`
do
    echo "Create RELEASE TMX for $i"
    nice -20 python tmxmaker.py $local_hg/RELEASE_L10N/$i/ $local_hg/RELEASE_EN-US/COMMUN/ $i en-US release
done

# Update BETA
if [ "$checkrepo" = true ]
then
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
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
    done
fi


for i in `cat $beta_locales`
do
    echo "Create BETA TMX for $i"
    nice -20 python tmxmaker.py $local_hg/BETA_L10N/$i/ $local_hg/BETA_EN-US/COMMUN/ $i en-US beta
done

# Update TRUNK
if [ "$checkrepo" = true ]
then
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
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
    done
fi

for i in `cat $trunk_locales`
do
    echo "Create TRUNK TMX for $i"
    nice -20 python tmxmaker.py $local_hg/TRUNK_L10N/$i/ $local_hg/TRUNK_EN-US/COMMUN/ $i en-US central
done


# Update AURORA
if [ "$checkrepo" = true ]
then
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
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
    done
fi

for i in `cat $aurora_locales`
do
    echo "Create AURORA TMX for $i"
    nice -20 python tmxmaker.py $local_hg/AURORA_L10N/$i/ $local_hg/AURORA_EN-US/COMMUN/ $i en-US aurora
done
