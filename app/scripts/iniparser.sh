#! /usr/bin/env bash

# We need to store the current directory value for the CRON job
DIR=`dirname "$0"`
# Convert .ini file in bash variables
eval $(cat $DIR/../config/config.ini | $DIR/ini_to_bash.py)

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
gaia_1_3=$local_hg/GAIA_1_3
gaia_1_4=$local_hg/GAIA_1_4
gaia_2_0=$local_hg/GAIA_2_0

# Location of Dotlang-based repos
mozilla_org=$local_svn/mozilla_org/

# Location of l20n test repo
l20n_test=$local_git/L20N_TEST

# List of locales per branch
trunk_locales=$config/central.txt
aurora_locales=$config/aurora.txt
beta_locales=$config/beta.txt
release_locales=$config/release.txt
gaia_locales=$config/gaia.txt
gaia_locales_1_3=$config/gaia_1_3.txt
gaia_locales_1_4=$config/gaia_1_4.txt
gaia_locales_2_0=$config/gaia_2_0.txt
l20n_test_locales=$config/l20n_test.txt
