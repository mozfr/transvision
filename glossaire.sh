#!/bin/bash

# Syntax:
# - without parameters: update all locales
# - one parameter (locale code): update only the requested locale

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
export PATH=$PATH:$PWD/web/inc
export PATH=$PATH:$PWD/
source iniparser.sh

# Decide if must update hg repositories and create TMX
checkrepo=true
createTMX=true

# Update RELEASE
if $checkrepo
then
    cd $release_source
    cd comm-release
    hg pull -r tip
    hg update -c
    cd ../mozilla-release
    hg pull -r tip
    hg update -c

    if $all_locales
    then
        cd $release_l10n
        for i in `cat $release_locales`
        do
            cd $i
            hg pull -r tip
            hg update -c
            cd ..
        done
    else
        if [ -d $release_l10n/$locale_code ]
        then
            cd $release_l10n/$locale_code
            hg pull -r tip
            hg update -c
            cd ..
        else
            echo "Folder $release_l10n/$locale_code does not exist."
        fi
    fi
fi

cd $install
if $createTMX
then
    find -L $release_source/COMMUN/ -type l | while read -r file; do echo $file is orphaned;  unlink $file; done
    if $all_locales
    then
        for i in `cat $release_locales`
        do
            echo "Create RELEASE TMX for $i"
            nice -20 python tmxmaker.py $release_l10n/$i/ $release_source/COMMUN/ $i en-US release
        done
    else
        if [ -d $release_l10n/$locale_code ]
        then
            echo "Create RELEASE TMX for $locale_code"
            nice -20 python tmxmaker.py $release_l10n/$locale_code/ $release_source/COMMUN/ $locale_code en-US release
        else
            echo "Folder $release_l10n/$locale_code does not exist."
        fi
    fi

    echo "Create RELEASE TMX for en-US"
    nice -20 python tmxmaker.py $release_source/COMMUN/ $release_source/COMMUN/ en-US en-US release
fi


# Update BETA
if $checkrepo
then
    cd $beta_source
    cd comm-beta
    hg pull -r tip
    hg update -c
    cd ../mozilla-beta
    hg pull -r tip
    hg update -c

    if $all_locales
    then
        cd $beta_l10n
        for i in `cat $beta_locales`
        do
            cd $i
            hg pull -r tip
            hg update -c
            cd ..
        done
    else
        if [ -d $beta_l10n/$locale_code ]
        then
            cd $beta_l10n/$locale_code
            hg pull -r tip
            hg update -c
            cd ..
        else
            echo "Folder $beta_l10n/$locale_code does not exist."
        fi
    fi
fi

cd $install
if $createTMX
then
    find -L $beta_source/COMMUN/ -type l | while read -r file; do echo $file is orphaned;  unlink $file; done
    if $all_locales
    then
        for i in `cat $beta_locales`
        do
            echo "Create BETA TMX for $i"
            nice -20 python tmxmaker.py $beta_l10n/$i/ $beta_source/COMMUN/ $i en-US beta
        done
    else
        if [ -d $beta_l10n/$locale_code ]
        then
            echo "Create BETA TMX for $locale_code"
            nice -20 python tmxmaker.py $beta_l10n/$locale_code/ $beta_source/COMMUN/ $locale_code en-US beta
        else
            echo "Folder $beta_l10n/$locale_code does not exist."
        fi
    fi

    echo "Create BETA TMX for en-US"
    nice -20 python tmxmaker.py $beta_source/COMMUN/ $beta_source/COMMUN/ en-US en-US beta
fi


# Update TRUNK
if $checkrepo
then
    cd $trunk_source
    cd comm-central
    hg pull -r tip
    hg update -c
    cd ../mozilla-central
    hg pull -r tip
    hg update -c

    if $all_locales
    then
        cd $trunk_l10n
        for i in `cat $trunk_locales`
        do
            cd $i
            hg pull -r tip
            hg update -c
            cd ..
        done
    else
        if [ -d $trunk_l10n/$locale_code ]
        then
            cd $trunk_l10n/$locale_code
            hg pull -r tip
            hg update -c
            cd ..
        else
            echo "Folder $trunk_l10n/$locale_code does not exist."
        fi
    fi
fi

cd $install
if $createTMX
then
    find -L $trunk_source/COMMUN/ -type l | while read -r file; do echo $file is orphaned;  unlink $file; done
    if $all_locales
    then
        for i in `cat $trunk_locales`
        do
            echo "Create TRUNK TMX for $i"
            nice -20 python tmxmaker.py $trunk_l10n/$i/ $trunk_source/COMMUN/ $i en-US central
        done
    else
        if [ -d $trunk_l10n/$locale_code ]
        then
            echo "Create TRUNK TMX for $locale_code"
            nice -20 python tmxmaker.py $trunk_l10n/$locale_code/ $trunk_source/COMMUN/ $locale_code en-US central
        else
            echo "Folder $trunk_l10n/$locale_code does not exist."
        fi
    fi

    echo "Create TRUNK TMX for en-US"
    nice -20 python tmxmaker.py $trunk_source/COMMUN/ $trunk_source/COMMUN/ en-US en-US central
fi


# Update aurora
if $checkrepo
then
    cd $aurora_source
    cd comm-aurora
    hg pull -r tip
    hg update -c
    cd ../mozilla-aurora
    hg pull -r tip
    hg update -c

    if $all_locales
    then
        cd $aurora_l10n
        for i in `cat $aurora_locales`
        do
            cd $i
            hg pull -r tip
            hg update -c
            cd ..
        done
    else
        if [ -d $aurora_l10n/$locale_code ]
        then
            cd $aurora_l10n/$locale_code
            hg pull -r tip
            hg update -c
            cd ..
        else
            echo "Folder $aurora_l10n/$locale_code does not exist."
        fi
    fi
fi

cd $install
if $createTMX
then
    find -L $aurora_source/COMMUN/ -type l | while read -r file; do echo $file is orphaned;  unlink $file; done
    if $all_locales
    then
        for i in `cat $aurora_locales`
        do
            echo "Create AURORA TMX for $i"
            nice -20 python tmxmaker.py $aurora_l10n/$i/ $aurora_source/COMMUN/ $i en-US aurora
        done
    else
        if [ -d $aurora_l10n/$locale_code ]
        then
            echo "Create AURORA TMX for $locale_code"
            nice -20 python tmxmaker.py $aurora_l10n/$locale_code/ $aurora_source/COMMUN/ $locale_code en-US aurora
        else
            echo "Folder $aurora_l10n/$locale_code does not exist."
        fi
    fi

    echo "Create AURORA TMX for en-US"
    nice -20 python tmxmaker.py $aurora_source/COMMUN/ $aurora_source/COMMUN/ en-US en-US aurora
fi


# Update GAIA
if $checkrepo
then
    if $all_locales
    then
        cd $gaia
        for i in `cat $gaia_locales`
        do
            cd $i
            hg pull -r tip
            hg update -c
            cd ..
        done
    else
        if [ -d $gaia/$locale_code ]
        then
            cd $gaia/$locale_code
            hg pull -r tip
            hg update -c
            cd ..
        else
            echo "Folder $gaia/$locale_code does not exist."
        fi
    fi
fi

cd $install
if $createTMX
then
    if $all_locales
    then
        for i in `cat $gaia_locales`
        do
            echo "Create GAIA TMX for $i"
            nice -20 python tmxmaker.py $gaia/$i/ $gaia/en-US/ $i en-US gaia
        done
    else
        if [ -d $gaia/$locale_code ]
        then
            echo "Create GAIA TMX for $locale_code"
            nice -20 python tmxmaker.py $gaia/$locale_code/ $gaia/en-US/ $locale_code en-US gaia
        else
            echo "Folder $gaia/$locale_code does not exist."
        fi
    fi

    echo "Create GAIA TMX for en-US"
    nice -20 python tmxmaker.py $gaia/en-US/ $gaia/en-US/ en-US en-US gaia
fi


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
        for i in `cat $l20n_test_locales`
        do
            echo "Create L20N test repo TMX for $i"
            nice -20 python tmxmaker.py $l20n_test/l20ntestdata/$i/ $l20n_test/l20ntestdata/en-US/ $i en-US l20n_test
        done
    else
        if [ -d $l20n_test/l20ntestdata/$locale_code ]
        then
            echo "Create L20N test repo TMX for $locale_code"
            nice -20 python tmxmaker.py $l20n_test/l20ntestdata/$locale_code/ $l20n_test/l20ntestdata/en-US/ $locale_code en-US l20n_test
        else
            echo "Folder $l20n_test/$locale_code does not exist."
        fi
    fi

    echo "Create L20N test repo TMX for en-US"
    nice -20 python tmxmaker.py $l20n_test/l20ntestdata/en-US/ $l20n_test/l20ntestdata/en-US/ en-US en-US l20n_test
fi
