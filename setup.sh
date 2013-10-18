#!/bin/bash

# get server configuration variables
source ./iniparser.sh

# Make sure that we have the file structure
mkdir -p $release_l10n
mkdir -p $beta_l10n
mkdir -p $aurora_l10n
mkdir -p $trunk_l10n
mkdir -p $gaia
mkdir -p $gaia_1_1
mkdir -p $gaia_1_2
mkdir -p $l20n_test
mkdir -p $libraries

# Restructure en-US
for dir in `cat $install/list_rep_mozilla-central.txt`
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

for dir in `cat $install/list_rep_comm-central.txt`
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

# Check out the SILME library and set it to the latest released version
if [ ! -d $libraries/silme/.hg ]
then
    echo "Checking out the SILME library into $libraries"
    cd $libraries
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

    for i in `cat $install/$1.txt`
        do
            if [ ! -d $i ]
            then
                mkdir $i
            fi

            if [ ! -d $i/.hg ]
            then
                echo "Checking out the following repo:"
                echo $1/$i/
                if [ $1 = central ]
                then
                    hg clone http://hg.mozilla.org/l10n-central/$i $i
                else
                    hg clone http://hg.mozilla.org/releases/l10n/mozilla-$1/$i $i
                fi
            fi

            if [ ! -d $root/TMX/$1/$i ]
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

    if [ ! -d $root/TMX/$1/en-US ]
    then
        echo "Creating this locale TMX for $1:"
        echo en-US
        mkdir -p $root/TMX/$1/en-US
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

# Gaia 1.1
echo "Gaia 1.1 initialization"
cd $gaia_1_1
for i in `cat $install/gaia_1_1.txt`
    do
        if [ ! -d $i/.hg ]
        then
            echo "Checking out the following repo:"
            echo $i
            hg clone http://hg.mozilla.org/releases/gaia-l10n/v1_1/$i
        fi

        if [ ! -d $root/TMX/gaia_1_1/$i ]
        then
            echo "Creating this locale TMX for Gaia:"
            echo $i
            mkdir -p $root/TMX/gaia_1_1/$i
        fi
done

# Gaia 1.2
echo "Gaia 1.2 initialization"
cd $gaia_1_2
for i in `cat $install/gaia_1_2.txt`
    do
        if [ ! -d $i/.hg ]
        then
            echo "Checking out the following repo:"
            echo $i
            hg clone http://hg.mozilla.org/releases/gaia-l10n/v1_2/$i
        fi

        if [ ! -d $root/TMX/gaia_1_2/$i ]
        then
            echo "Creating this locale TMX for Gaia:"
            echo $i
            mkdir -p $root/TMX/gaia_1_2/$i
        fi
done

# We now deal with L20n test repo as a specific case
echo "L20n test repo initialization"
cd $l20n_test
if [ ! -d $l20n_test/l20ntestdata/.git ]
then
    echo "Checking out the following repo:"
    echo $i
    git clone https://github.com/pascalchevrel/l20ntestdata.git
fi

for i in `cat $install/l20n_test.txt`
    do
        if [ ! -d $root/TMX/l20n_test/$i ]
        then
            echo "Creating this locale TMX for L20n test:"
            echo $i
            mkdir -p $root/TMX/l20n_test/$i
        fi
done

# Add .htaccess to TMX folder. Folder should already exists, but check in
# advance to be sure. I overwrite an existing .htaccess if already present.
echo "add .htaccess to TMX folder"
if [ ! -d $root/TMX ]
    then
        echo "Creating TMX folder"
        mkdir -p $root/TMX
fi
echo -n "AddType application/octet-stream .tmx" > $root/TMX/.htaccess

# At this point I'm sure TMX exists, adding a symlink inside $install/web
if [ ! -L $install/web/TMX ]
then
    echo "add symlink to $root/TMX inside $install/web"
    ln -s $root/TMX $install/web/TMX
fi

echo "add log files"
touch $config_path/transvision.log
touch $install/web/stats.json
