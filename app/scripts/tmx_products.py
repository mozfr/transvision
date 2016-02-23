#!/usr/bin/python

import argparse
import datetime
import os
import subprocess
import sys
from ConfigParser import SafeConfigParser

# Get absolute path of ../config from the current script location (not the
# current folder)
config_folder = os.path.abspath(
    os.path.join(os.path.dirname(__file__), os.pardir, 'config'))

# Read Transvision's configuration file from ../config/config.ini
# If not available use default a /storage folder to store data
config_file = os.path.join(config_folder, 'config.ini')
if not os.path.isfile(config_file):
    print 'Configuration file /app/config/config.ini is missing. Default folders will be used.'
    storage_path = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir))
    library_path = os.path.join(storage_path, 'libraries')
    storage_path = os.path.join(storage_path, 'tests', 'testfiles', 'output')
else:
    config_parser = SafeConfigParser()
    config_parser.read(config_file)
    library_path = config_parser.get('config', 'libraries')
    storage_path = os.path.join(config_parser.get('config', 'root'), 'TMX')

# Import Silme library (http://hg.mozilla.org/l10n/silme/)
silme_path = os.path.join(library_path, 'silme')

if not os.path.isdir(silme_path):
    try:
        print 'Cloning silme...'
        cmd_status = subprocess.check_output(
            ['hg', 'clone', 'https://hg.mozilla.org/l10n/silme',
                silme_path, '-u', 'silme-0.8.0'],
            stderr=subprocess.STDOUT,
            shell=False)
        print cmd_status
    except Exception as e:
        print e

sys.path.append(os.path.join(silme_path, 'lib'))
try:
    import silme.core
    import silme.io
    import silme.format
    silme.format.Manager.register('dtd', 'properties', 'ini', 'inc')
except ImportError:
    print 'Error importing Silme library'
    sys.exit(1)


def escape(translation):
    '''
        Escape quotes and backslahes in translation. There are two issues:
        * Internal Python escaping: the string "this is a \", has an internal
          representation as "this is a \\".
          Also, "\\ test" is equivalent to r"\ test" (raw string).
        * We need to print these strings into a file, with the format of a
          PHP array delimited by single quotes ('id' => 'translation'). Hence
          we need to escape single quotes, but also escape backslashes.
          "this is a 'test'" => "this is a \'test\'"
          "this is a \'test\'" => "this is a \\\'test\\\'"
    '''

    # Escape slashes
    escaped_translation = translation.replace('\\', '\\\\')
    # Escape single quotes
    escaped_translation = escaped_translation.replace('\'', '\\\'')

    return escaped_translation


def get_strings(package, local_directory, strings_array):
    '''Store recursively translations from files in local_directory in a list of string'''
    for item in package:
        if (type(item[1]) is not silme.core.structure.Blob) and not(isinstance(item[1], silme.core.Package)):
            for entity in item[1]:
                # String ID is the format folder/filename:entity. Make
                # sure to remove a starting '/' from the folder's name
                string_id = u'{0}/{1}:{2}'.format(
                    local_directory.lstrip('/'), item[0], entity)
                strings_array[string_id] = item[1][entity].get_value()
        elif (isinstance(item[1], silme.core.Package)):
            if (item[0] != 'en-US') and (item[0] != 'locales'):
                get_strings(item[1], local_directory + '/' + item[0],
                            strings_array)
            else:
                get_strings(item[1], local_directory, strings_array)


def create_directories_list(locale_repo, reference_repo, repository):
    ''' Create a list of folders to analyze '''
    if repository.startswith('gaia'):
        # Examine the entire folder
        dirs = ['']
    else:
        dirs_locale = os.listdir(locale_repo)
        dirs_reference = [
            'browser', 'calendar', 'chat', 'devtools', 'dom', 'editor',
            'extensions', 'mail', 'mobile', 'netwerk', 'other-licenses',
            'security', 'services', 'suite', 'toolkit', 'webapprt'
        ]
        dirs = filter(lambda x: x in dirs_locale, dirs_reference)

    return dirs


def create_tmx_content(reference_repo, locale_repo, dirs):
    ''' Extract strings from repository, return them as a list of PHP array
        elements. '''
    tmx_content = []
    for directory in dirs:
        path_reference = os.path.join(reference_repo, directory)
        path_locale = os.path.join(locale_repo, directory)

        rcsClient = silme.io.Manager.get('file')
        try:
            l10nPackage_reference = rcsClient.get_package(
                path_reference, object_type='entitylist')
        except Exception as e:
            print 'Silme couldn\'t extract data for', path_reference
            print e
            continue

        if not os.path.isdir(path_locale):
            # Folder doesn't exist for this locale, don't log a warning,
            # just continue to the next folder.
            continue

        try:
            l10nPackage_locale = rcsClient.get_package(
                path_locale, object_type='entitylist')
        except Exception as e:
            print 'Silme couldn\'t extract data for', path_locale
            print e
            continue

        strings_reference = {}
        strings_locale = {}
        get_strings(l10nPackage_reference, directory, strings_reference)
        get_strings(l10nPackage_locale, directory, strings_locale)
        for entity in strings_reference:
            # Append string to tmx_content, using the format of a PHP array
            # element
            translation = escape(
                strings_locale.get(entity, '')).encode('utf-8')
            tmx_content.append("'{0}' => '{1}'".format(
                entity.encode('utf-8'), translation))
    tmx_content.sort()

    return tmx_content


def write_php_file(filename, tmx_content):
    ''' Write TMX content as a PHP array on file '''
    target_locale_file = open(filename, 'w')
    target_locale_file.write('<?php\n$tmx = [\n')
    for line in tmx_content:
        target_locale_file.write(line + ',\n')
    target_locale_file.write('];\n')
    target_locale_file.close()


def main():
    # Read command line input parameters
    parser = argparse.ArgumentParser()
    parser.add_argument('locale_repo', help='Path to locale files')
    parser.add_argument('reference_repo', help='Path to reference files')
    parser.add_argument('locale_code', help='Locale language code')
    parser.add_argument('reference_code', help='Reference language code')
    parser.add_argument('repository', help='Repository name')
    args = parser.parse_args()

    dirs = create_directories_list(
        args.reference_repo, args.locale_repo, args.repository
    )
    tmx_content = create_tmx_content(
        args.reference_repo, args.locale_repo, dirs
    )

    # Store the actual file on disk
    filename_locale = os.path.join(
        os.path.join(storage_path, args.locale_code),
        'cache_{0}_{1}.php'.format(args.locale_code, args.repository)
    )
    write_php_file(filename_locale, tmx_content)


if __name__ == '__main__':
    main()
