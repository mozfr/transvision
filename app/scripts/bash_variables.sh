#! /usr/bin/env bash

# Set variables used by bash scripts

# List of folders setup.sh needs to check and eventually create
folders=( $libraries )
path_sources=${config}/sources

# PRODUCT repos and list of locales
release_l10n=${local_hg}/RELEASE_L10N
beta_l10n=${local_hg}/BETA_L10N
trunk_l10n=${local_hg}/TRUNK_L10N

release_source=${local_hg}/RELEASE_EN-US
beta_source=${local_hg}/BETA_EN-US
trunk_source=${local_hg}/TRUNK_EN-US

trunk_locales=${path_sources}/central.txt
beta_locales=${path_sources}/beta.txt
release_locales=${path_sources}/release.txt

folders+=( $release_l10n $beta_l10n $trunk_l10n \
           $release_source $beta_source $trunk_source )

# Location of Dotlang-based repos
mozilla_org=$local_git/mozilla_org/
folders+=( $mozilla_org )

# Firefox for iOS (XLIFF)
firefox_ios=$local_git/firefox_ios/
folders+=( $firefox_ios )
