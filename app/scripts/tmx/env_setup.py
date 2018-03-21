#! /usr/bin/env python

import os
import shutil
import subprocess
import sys


def import_library(libraries_path, type, name, url, version):
    library_path = os.path.join(libraries_path, name)

    if os.path.isdir(library_path) and not os.path.isdir(os.path.join(library_path, '.{0}'.format(type))):
        print('Folder {0} is not the expected type of repository. Removing...'.format(
            library_path))
        shutil.rmtree(library_path)

    if not os.path.isdir(library_path):
        try:
            print('Cloning {0}...'.format(name))
            if type == 'hg':
                commands = [
                    'hg', 'clone', url, library_path,
                    '-u', 'default' if version == '' else version]
                cmd_status = subprocess.check_output(commands,
                                                     stderr=subprocess.STDOUT,
                                                     shell=False)
                print(cmd_status)
            elif type == 'git':
                commands = ['git', 'clone', url, library_path]
                cmd_status = subprocess.check_output(commands,
                                                     stderr=subprocess.STDOUT,
                                                     shell=False)
                print(cmd_status)
                if version != '':
                    commands = ['git', '-C', library_path,
                                'checkout', '-q', version]
                    cmd_status = subprocess.check_output(commands,
                                                         stderr=subprocess.STDOUT,
                                                         shell=False)
                    print(cmd_status)
        except Exception as e:
            print(e)
    sys.path.insert(0, library_path)
