#! /usr/bin/env bash

# Set variables used by bash scripts

# List of folders setup.sh needs to check and eventually create
folders=( $libraries )
path_sources=${config}/sources

# Path and list of locales for gecko-strings
gecko_strings_path=${local_hg}/gecko_strings
gecko_strings_locales=${path_sources}/gecko_strings.txt
folders+=( $gecko_strings_path )

# Path and list of locales for comm-l10n
comm_l10n_locales=${path_sources}/comm_l10n.txt
comm_l10n_path=${local_hg}/comm_l10n
folders+=( $comm_l10n_path )

# Location of mozilla.org repository (Fluent based)
mozilla_org=$local_git/mozilla_org/
folders+=( $mozilla_org )

# Firefox for iOS, Android-l10n, VPN Client
android_l10n=$local_git/android_l10n/
firefox_ios=$local_git/firefox_ios/
vpn_client=$local_git/vpn_client/
folders+=( $firefox_ios $android_l10n $vpn_client)
