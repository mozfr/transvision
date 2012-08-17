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


cfg_parser 'web/inc/config.ini'

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


# List of locales per branch
trunk_locales=$install/central.txt
aurora_locales=$install/aurora.txt
beta_locales=$install/beta.txt
release_locales=$install/release.txt
