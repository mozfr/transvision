#! /usr/bin/env bash

# Use 'start.sh -remote' if you want to access the server from another machine (or a VM)
SERVER="localhost:8082"

if [ $# -gt 0 ]
then
    if [ $1 = '-remote' ]
    then
        SERVER="0.0.0.0:8082"
    elif [ $1 = '-help' ]
    then
        echo "Usage: start.sh (PHP web server will be listening to localhost:8082)"
        echo "Usage: start.sh -remote (PHP web server will be listening to 0.0.0.0:8082 and accessible from the outside)"
        echo "Additional parameters will be ignored."
        exit 1
    else
        echo "Unknown parameter \`${1}\`. Type \`start.sh -help\` to know what the valid parameters are."
    fi
fi

./app/scripts/dev-setup.sh

echo -e $(tput setaf 2; tput bold)"Launching PHP development server (php -S ${SERVER} -t web/ app/inc/router.php)"$(tput sgr0)
php -S ${SERVER} -t web/ app/inc/router.php
