#! /usr/bin/env bash

script_path=$(dirname "$0")
root_path=$script_path/../..

function setupVirtualEnv() {
    # Create virtualenv folder if missing
    cd $root_path
    if [ ! -d python-venv ]
    then
        echo "Setting up new virtualenv..."
        virtualenv python-venv || exit 1
    fi

    # Install or update dependencies
    echo "Installing dependencies in virtualenv..."
    source python-venv/bin/activate || exit 1
    pip install -r $root_path/requirements.txt --upgrade --quiet
    deactivate
}

# Setup virtualenv
setupVirtualEnv

# Activate virtualenv
echo "Activating virtualenv..."
source $root_path/python-venv/bin/activate || exit 1

# Running main script
python $script_path/clean_data.py $@
