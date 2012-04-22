#!/bin/bash


# RELEASE

cd /data/HG/RELEASE_EN-US/
cd comm-release
hg pull -r tip
hg update -c
cd ../mozilla-release
hg pull -r tip
hg update -c

cd /data/HG/RELEASE_L10N/
for i in `cat locales_liste.txt`
do
cd $i
hg pull -r tip
hg update -c
cd ..
done

cd /data/HG/glossaire/

for i in `cat /data/HG/RELEASE_L10N/locales_liste.txt`
do
echo $i
nice -20 python tmxmaker.py /data/HG/RELEASE_L10N/$i/ /data/HG/RELEASE_EN-US/COMMUN/ $i en-US release
done

# BETA
cd /data/HG/BETA_EN-US/
cd comm-beta
hg pull -r tip
hg update -c
cd ../mozilla-beta
hg pull -r tip
hg update -c

cd /data/HG/BETA_L10N/
for i in `cat locales_liste.txt`
do
cd $i
hg pull -r tip
hg update -c
cd ..
done

cd /data/HG/glossaire/

for i in `cat /data/HG/BETA_L10N/locales_liste.txt`
do
echo $i
nice -20 python tmxmaker.py /data/HG/BETA_L10N/$i/ /data/HG/BETA_EN-US/COMMUN/ $i en-US beta
done

# TRUNK
cd /data/HG/TRUNK_EN-US/
cd comm-central
hg pull -r tip
hg update -c
cd ../mozilla-central
hg pull -r tip
hg update -c

cd /data/HG/TRUNK_L10N/
for i in `cat locales_liste.txt`
do
cd $i
hg pull -r tip
hg update -c
cd ..
done

cd /data/HG/glossaire/
for i in `cat /data/HG/TRUNK_L10N/locales_liste.txt`
do
echo $i
nice -20 python tmxmaker.py /data/HG/TRUNK_L10N/$i/ /data/HG/TRUNK_EN-US/COMMUN/ $i en-US trunk
done


# AURORA
cd /data/HG/AURORA_EN-US/
cd comm-aurora
hg pull -r tip
hg update -c
cd ../mozilla-aurora
hg pull -r tip
hg update -c


cd /data/HG/AURORA_L10N/
for i in `cat locales_liste.txt`
do
cd $i
hg pull -r tip
hg update -c
cd ..
done

cd /data/HG/glossaire/
for i in `cat /data/HG/AURORA_L10N/locales_liste.txt`
do
echo $i
nice -20 python tmxmaker.py /data/HG/AURORA_L10N/$i/ /data/HG/AURORA_EN-US/COMMUN/ $i en-US aurora
done


