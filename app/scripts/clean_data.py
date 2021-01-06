#!/usr/bin/env python3

from configparser import ConfigParser
import argparse
import glob
import json
import os
import shutil
import sys

def main():
    # Parse command line options
    cl_parser = argparse.ArgumentParser()
    cl_parser.add_argument('--delete',
                           help='Delete files', action='store_true')
    args = cl_parser.parse_args()

    # Get absolute path of ../config from the current script location (not the
    # current folder)
    config_folder = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir, 'config'))

    # Read Transvision's configuration file from ../config/config.ini
    config_file = os.path.join(config_folder, 'config.ini')
    if not os.path.isfile(config_file):
        print('Configuration file /app/config/config.ini is missing.')
        sys.exit(1)
    else:
        config_parser = ConfigParser()
        config_parser.read(config_file)
        storage_path = os.path.join(config_parser.get('config', 'root'), 'TMX')

    # Load supported repositories and store information like folder name,
    # supported locales.
    sources_path = os.path.join(config_folder, 'sources')
    sources_file = open(os.path.join(
        sources_path, 'supported_repositories.json'))
    supported_repositories = {}
    json_repositories = json.load(sources_file)

    folder_mapping = {}

    known_folders = []
    known_cache_files = []
    for id, repository in json_repositories.items():
        repository_id = repository['id']

        # Check if this repository is mapped to a special folder name
        # (e.g. central -> trunk), otherwise use the repository ID
        # (transformed to uppercase) with '_L10N' as folder name.
        folder_name = folder_mapping.get(repository_id, repository_id)
        known_folders.append(folder_name)

        # Store supported locales for this repository
        locales_file = os.path.join(sources_path, repository_id + '.txt')
        supported_locales = open(locales_file, 'r').read().splitlines()

        # Make sure en-US and en are included in the list of supported locales
        if 'en-US' not in supported_locales:
            supported_locales.append('en-US')
        if repository_id == 'mozilla_org':
            supported_locales.append('en')

        supported_repositories[repository_id] = {
            'folder_name': folder_name,
            'locales': supported_locales
        }

        # Store a list of acceptable cache file names
        for locale in supported_locales:
            known_cache_files.append('{0}/{1}/cache_{1}_{2}.php'.format(
                storage_path, locale, repository_id))

    # List all .txt files in /sources
    print('--\nAnalyzing sources in config/sources')

    need_cleanup = False
    for txtfile in glob.glob(os.path.join(sources_path, '*.txt')):
        filename = os.path.splitext(os.path.basename(txtfile))[0]
        if filename not in list(supported_repositories.keys()):
            print('{}.txt is not a supported repository.'.format(filename))
            need_cleanup = True
            if args.delete:
                print('Removing file: {}'.format(txtfile))
                os.remove(txtfile)
    if not need_cleanup:
        print('Nothing to remove.')

    # Check all repositories for extra folders
    print('--\nAnalyzing folders in supported repositories')
    # Besides standard VCS folders or templates, we need to exclude some
    # folders in specific projects.
    excluded_folders = {
        'firefox_ios': ['.git', 'templates'],
        'mozilla_org': ['.git', 'configs', 'en'],
    }

    '''
        To use if a project has locales in a subfolder, not in the root.
        Example:

        l10n_subfolders = {
            'project_name': 'locales',
        }
    '''
    l10n_subfolders = {}

    hg_path = config_parser.get('config', 'local_hg')
    git_path = config_parser.get('config', 'local_git')

    need_cleanup = False
    for repository_id, repository in supported_repositories.items():
        # Check if the folder exists as a Mercurial repository. If it doesn't
        # assume it's a Git repository.
        print('--\nAnalyze: {}'.format(repository_id))
        if os.path.isdir(os.path.join(hg_path, repository['folder_name'])):
            folder_path = os.path.join(hg_path, repository['folder_name'])
        else:
            folder_path = os.path.join(git_path, repository['folder_name'])

        folder_path = os.path.join(
            folder_path, l10n_subfolders.get(repository_id, ''))
        if not os.path.isdir(folder_path):
            print('SKIPPED. Check sources: {} '
                  'does not exist'.format(folder_path))
        else:
            # Ignore android-l10n
            if repository_id == 'android_l10n':
                continue

            available_folders = next(os.walk(folder_path))[1]
            available_folders.sort()
            for folder in available_folders:
                if folder in excluded_folders.get(repository_id, []):
                    continue
                if folder not in repository['locales']:
                    # This folder is inside the repository but doesn't match
                    # any supported locale.
                    print('{} is not a supported locale'.format(folder))
                    need_cleanup = True
                    if args.delete:
                        full_path = os.path.join(folder_path, folder)
                        print('Removing folder: {}'.format(full_path))
                        shutil.rmtree(full_path)
    if not need_cleanup:
        print('Nothing to remove.')

    # Check cache files
    print('--\nAnalyze cache files in TMX')
    available_folders = next(os.walk(storage_path))[1]

    need_cleanup = False
    for folder in available_folders:
        for filename in glob.glob(os.path.join(storage_path, folder, '*.php')):
            if filename not in known_cache_files:
                print('{} is not a known cache file'.format(filename))
                need_cleanup = True
                if args.delete:
                    print('Removing file: {}'.format(filename))
                    os.remove(filename)
    if not need_cleanup:
        print('Nothing to remove.')


if __name__ == '__main__':
    main()
