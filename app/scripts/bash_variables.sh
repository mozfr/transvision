#! /usr/bin/env bash

# Set variables used by bash scripts

# List of folders setup.sh needs to check and eventually create
folders=( $libraries )
path_sources=${config}/sources

# PRODUCT repos and list of locales
release_l10n=${local_hg}/RELEASE_L10N
beta_l10n=${local_hg}/BETA_L10N
aurora_l10n=${local_hg}/AURORA_L10N
trunk_l10n=${local_hg}/TRUNK_L10N

release_source=${local_hg}/RELEASE_EN-US
beta_source=${local_hg}/BETA_EN-US
aurora_source=${local_hg}/AURORA_EN-US
trunk_source=${local_hg}/TRUNK_EN-US

trunk_locales=${path_sources}/central.txt
aurora_locales=${path_sources}/aurora.txt
beta_locales=${path_sources}/beta.txt
release_locales=${path_sources}/release.txt

folders+=( $release_l10n $beta_l10n $aurora_l10n $trunk_l10n \
           $release_source $beta_source $aurora_source $trunk_source )

# GAIA repos and list of locales
gaia_versions=${path_sources}/gaia_versions.txt
for gaia_version in $(cat ${gaia_versions})
do
    if [ "$gaia_version" == "gaia" ]
    then
        gaia=${local_hg}/GAIA
        gaia_locales=${path_sources}/gaia.txt
        folders+=( $gaia )
    else
        declare gaia_${gaia_version}=${local_hg}/GAIA_${gaia_version}
        declare gaia_locales_${gaia_version}=${path_sources}/gaia_${gaia_version}.txt
        var_name=gaia_${gaia_version}
        folders+=( ${!var_name} )
    fi
done

# Location of Dotlang-based repos
mozilla_org=$local_svn/mozilla_org/
folders+=( $mozilla_org )

# l20n test repo
l20n_test=$local_git/L20N_TEST
l20n_test_locales=${path_sources}/l20n_test.txt
folders+=( $l20n_test )
