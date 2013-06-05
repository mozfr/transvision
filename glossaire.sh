#!/bin/bash


# get server configuration variables
export PATH=$PATH:$PWD/web/inc
export PATH=$PATH:$PWD/
source iniparser.sh

# update hg repositories or not
checkrepo=true
createTMX=true

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
if [ "$createTMX" = true ]
then
    for i in `cat $release_locales`
    do
        echo "Create RELEASE TMX for $i"
        nice -20 python tmxmaker.py $local_hg/RELEASE_L10N/$i/ $local_hg/RELEASE_EN-US/COMMUN/ $i en-US release
    done

    #~ echo "Create RELEASE TMX for fr"
    #~ nice -20 python tmxmaker.py $local_hg/RELEASE_L10N/fr/ $local_hg/RELEASE_EN-US/COMMUN/ fr en-US release
    #~ echo "Create RELEASE TMX for es-ES"
    #~ nice -20 python tmxmaker.py $local_hg/RELEASE_L10N/es-ES/ $local_hg/RELEASE_EN-US/COMMUN/ es-ES en-US release
    echo "Create RELEASE TMX for en-US"
    nice -20 python tmxmaker.py $local_hg/RELEASE_EN-US/COMMUN/ $local_hg/RELEASE_EN-US/COMMUN/ en-US en-US release
fi
#~ exit

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
if [ "$createTMX" = true ]
then
    for i in `cat $beta_locales`
    do
        echo "Create BETA TMX for $i"
        nice -20 python tmxmaker.py $local_hg/BETA_L10N/$i/ $local_hg/BETA_EN-US/COMMUN/ $i en-US beta
    done
    echo "Create BETA TMX for en-US"
    nice -20 python tmxmaker.py $local_hg/BETA_EN-US/COMMUN/ $local_hg/BETA_EN-US/COMMUN/ en-US en-US beta
fi

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
if [ "$createTMX" = true ]
then
    for i in `cat $trunk_locales`
    do
        echo "Create TRUNK TMX for $i"
        nice -20 python tmxmaker.py $local_hg/TRUNK_L10N/$i/ $local_hg/TRUNK_EN-US/COMMUN/ $i en-US central
    done
    echo "Create TRUNK TMX for en-US"
    nice -20 python tmxmaker.py $local_hg/TRUNK_EN-US/COMMUN/ $local_hg/TRUNK_EN-US/COMMUN/ en-US en-US central
fi

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

if [ "$createTMX" = true ]
then
    for i in `cat $aurora_locales`
    do
        echo "Create AURORA TMX for $i"
        nice -20 python tmxmaker.py $local_hg/AURORA_L10N/$i/ $local_hg/AURORA_EN-US/COMMUN/ $i en-US aurora
    done
    echo "Create AURORA TMX for en-US"
    nice -20 python tmxmaker.py $local_hg/AURORA_EN-US/COMMUN/ $local_hg/AURORA_EN-US/COMMUN/ en-US en-US aurora
fi


# Update GAIA
if [ "$checkrepo" = true ]
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
if [ "$createTMX" = true ]
then
    for i in `cat $gaia_locales`
    do
        echo "Create GAIA TMX for $i"
        nice -20 python tmxmaker.py $local_hg/GAIA/$i/ $local_hg/GAIA/en-US/ $i en-US gaia
    done
    echo "Create GAIA TMX for en-US"
    nice -20 python tmxmaker.py $local_hg/GAIA/en-US/ $local_hg/GAIA/en-US/ en-US en-US gaia
fi
