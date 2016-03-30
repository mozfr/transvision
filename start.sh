#! /usr/bin/env bash

standard_color=$(tput sgr0)
green=$(tput setab 2)
red=$(tput setab 1)

function echored() {
    echo -e "$red$*$standard_color"
}

function echogreen() {
    echo -e "$green$*$standard_color"
}

# Use 'start.sh -remote' if you want to access the server from another machine (or a VM)
SERVER="localhost:8082"

SUPPORTED_COMMANDS=(
    '-help'
    '-remote'
    '-tests'
    '-tests-server'
)

if [ $# -gt 0 ]
then
    supported_command=false
    for command in ${SUPPORTED_COMMANDS[@]};
    do
        if [ "${command}" == "$1" ];
        then
            supported_command=true
            break
        fi
    done
    if [ "${supported_command}" = false ];
    then
        echo "Unknown parameter \`${1}\`. Type \`start.sh -help\` to know what the valid parameters are."
        exit 1
    fi
fi

if [ $# -gt 0 ] && [ $1 = '-help' ]
then
    echo "Usage: start.sh (PHP web server will be listening to localhost:8082)"
    echo "Usage: start.sh -remote (PHP web server will be listening to 0.0.0.0:8082 and accessible from the outside)"
    echo "Usage: start.sh -tests (Run all the unit and functional tests and check PHP syntax with PHP-cs-fixer)"
    echo "Usage: start.sh -tests-server (Internal use — Run PHP web server listening to localhost:8083)"
    echo "Additional parameters will be ignored."
    exit 1
fi

./app/scripts/dev-setup.sh

case "$1" in
    -remote)
        SERVER="0.0.0.0:8082"
        ;;
    -tests)
        php ./vendor/atoum/atoum/bin/atoum -d tests/units/
        php ./tests/functional/api.php
        php ./tests/functional/pages.php
        vendor/bin/php-cs-fixer --diff --dry-run -v fix
        echo "Checking JavaScript files with ESLint..."
        if hash eslint 2>/dev/null
        then
            if eslint web/js
            then
                echogreen "No issues found in JavaScript files."
            fi
        else
            echored "WARNING: eslint is not available. See README.md for instructions."
        fi
        exit 1
        ;;
    -tests-server)
        SERVER="localhost:8083"
        ;;
esac

echo -e $(tput setaf 2; tput bold)"Launching PHP development server (php -S ${SERVER} -t web/ app/inc/router.php)"$(tput sgr0)
php -S ${SERVER} -t web/ app/inc/router.php
