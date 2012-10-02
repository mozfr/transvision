#!/bin/bash


# get server configuration variables
export PATH=$PATH:$HOME/transvision/web/inc
export PATH=$PATH:$HOME/transvision/web/inc
source iniparser.sh

# update hg repositories or not
checkrepo=false

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

cd $install

echo "Create RELEASE TMX for en-US"
nice -20 python tmxmaker.py $local_hg/RELEASE_L10N/en-US/ $local_hg/RELEASE_EN-US/COMMUN/ en-US en-US release

# exit

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

cd $install
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

cd $install
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

cd $install
for i in `cat $aurora_locales`
do
    echo "Create AURORA TMX for $i"
    nice -20 python tmxmaker.py $local_hg/AURORA_L10N/$i/ $local_hg/AURORA_EN-US/COMMUN/ $i en-US aurora
done

# Update GAIA
if [ "$checkrepo" = false ]
then
    cd $gaia
    for i in `cat $gaia_locales`
    do
        cd $i
        hg pull -r tip
        hg update -c
        cd ..
    done
fi

cd $install
for i in `cat $gaia_locales`
do
    echo "Create GAIA TMX for $i"
    nice -20 python tmxmaker.py $local_hg/GAIA/$i/ $local_hg/GAIA/en-US/ $i en-US gaia
done

