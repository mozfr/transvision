#! /usr/bin/env bash

# Set variables used by bash scripts

# List of folders setup.sh needs to check and eventually create
folders=( $libraries )
path_sources=${config}/sources

# PRODUCT repos, list of locales and other sources like Chatzilla
gecko_strings_path=${local_hg}/gecko_strings
gecko_strings_locales=${path_sources}/gecko_strings.txt
sources_path=${local_hg}/sources
folders+=( $gecko_strings_path $sources_path )

# Location of Dotlang-based repos
mozilla_org=$local_git/mozilla_org/
folders+=( $mozilla_org )

# Firefox for iOS, Android-l10n
firefox_ios=$local_git/firefox_ios/
android_l10n=$local_git/android_l10n/
folders+=( $firefox_ios $android_l10n)
