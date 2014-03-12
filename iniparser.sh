#!/bin/bash

cfg_parser ()
{
    ini="$(<$1)"                # read the file
    ini="${ini//[/\[}"          # escape [
    ini="${ini//]/\]}"          # escape ]
    IFS=$'\n' && ini=( ${ini} ) # convert to line-array
    ini=( ${ini[*]//;*/} )      # remove comments with ;
    ini=( ${ini[*]/\    =/=} )  # remove tabs before =
    ini=( ${ini[*]/=\   /=} )   # remove tabs be =
    ini=( ${ini[*]/\ =\ /=} )   # remove anything with a space around =
    ini=( ${ini[*]/#\\[/\}$'\n'cfg.section.} ) # set section prefix
    ini=( ${ini[*]/%\\]/ \(} )    # convert text2function (1)
    ini=( ${ini[*]/=/=\( } )    # convert item to array
    ini=( ${ini[*]/%/ \)} )     # close array parenthesis
    ini=( ${ini[*]/%\\ \)/ \\} ) # the multiline trick
    ini=( ${ini[*]/%\( \)/\(\) \{} ) # convert text2function (2)
    ini=( ${ini[*]/%\} \)/\}} ) # remove extra parenthesis
    ini[0]="" # remove first element
    ini[${#ini[*]} + 1]='}'    # add the last brace
    eval "$(echo "${ini[*]}")" # eval the result
}

# We need to store the current directory value for the CRON job
DIR=`dirname "$0"`
cfg_parser $DIR/web/inc/config.ini

# enable section called 'config' for reading
cfg.section.config

# List of locations of our local hg repos
release_l10n=$local_hg/RELEASE_L10N
beta_l10n=$local_hg/BETA_L10N
aurora_l10n=$local_hg/AURORA_L10N
trunk_l10n=$local_hg/TRUNK_L10N

release_source=$local_hg/RELEASE_EN-US
beta_source=$local_hg/BETA_EN-US
aurora_source=$local_hg/AURORA_EN-US
trunk_source=$local_hg/TRUNK_EN-US

# Location of Gaia source
gaia=$local_hg/GAIA
gaia_1_1=$local_hg/GAIA_1_1
gaia_1_2=$local_hg/GAIA_1_2
gaia_1_3=$local_hg/GAIA_1_3

# Location of Dotlang-based repos
mozilla_org=$local_svn/mozilla_org/

# Location of l20n test repo
l20n_test=$local_git/L20N_TEST

# List of locales per branch
trunk_locales=$install/central.txt
aurora_locales=$install/aurora.txt
beta_locales=$install/beta.txt
release_locales=$install/release.txt
gaia_locales=$install/gaia.txt
gaia_locales_1_1=$install/gaia_1_1.txt
gaia_locales_1_2=$install/gaia_1_2.txt
gaia_locales_1_3=$install/gaia_1_3.txt
l20n_test_locales=$install/l20n_test.txt
