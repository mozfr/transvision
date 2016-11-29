#!/usr/bin/python

import argparse
import json
import logging
import os
import subprocess
import sys
from ConfigParser import SafeConfigParser

logging.basicConfig()
# Get absolute path of ../config from the current script location (not the
# current folder)
config_folder = os.path.abspath(
    os.path.join(os.path.dirname(__file__), os.pardir, 'config'))

# Read Transvision's configuration file from ../config/config.ini
# If not available use a default storage folder to store data
config_file = os.path.join(config_folder, 'config.ini')
if not os.path.isfile(config_file):
    print('Configuration file /app/config/config.ini is missing. '
          'Default settings will be used.')
    root_folder = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir))
    library_path = os.path.join(root_folder, 'libraries')
else:
    config_parser = SafeConfigParser()
    config_parser.read(config_file)
    library_path = config_parser.get('config', 'libraries')
    storage_path = os.path.join(config_parser.get('config', 'root'), 'TMX')

# Import compare-locales (http://hg.mozilla.org/l10n/compare-locales/)
# and add it to the system's path
compare_locales_path = os.path.join(library_path, 'compare-locales')
if not os.path.isdir(compare_locales_path):
    try:
        print('Cloning compare-locales...')
        cmd_status = subprocess.check_output(
            ['hg', 'clone', 'https://hg.mozilla.org/l10n/compare-locales',
                compare_locales_path, '-u', 'RELEASE_1_2_1'],
            stderr=subprocess.STDOUT,
            shell=False)
        print(cmd_status)
    except Exception as e:
        print(e)
sys.path.insert(0, compare_locales_path)

try:
    from compare_locales import parser
except ImportError:
    print('Error importing compare-locales library')
    sys.exit(1)


class StringExtraction():

    def __init__(self, storage_path, locale, reference_locale, repository_name):
        ''' Initialize object '''

        # Set defaults
        self.supported_formats = ['.dtd', '.properties', '.ini', '.inc']
        self.storage_mode = ''
        self.storage_prefix = ''
        self.file_list = []
        self.translations = {}

        # Set instance variables
        self.storage_path = storage_path
        self.locale = locale
        self.reference_locale = reference_locale

        # Define the locale storage filenames
        self.storage_file = os.path.join(
            storage_path, locale,
            'cache_{0}_{1}'.format(locale, repository_name))

        self.reference_storage_file = os.path.join(
            storage_path, reference_locale,
            'cache_{0}_{1}'.format(reference_locale, repository_name))

    def setRepositoryPath(self, path):
        ''' Set path to repository '''

        # Strip trailing '/' from repository path
        self.repository_path = path.rstrip(os.path.sep)

    def setStorageMode(self, mode, prefix):
        ''' Set storage mode and prefix. Currently supported '''

        self.storage_mode = mode
        # Strip trailing '/' from storage_prefix
        self.storage_prefix = prefix.rstrip(os.path.sep)

    def extractFileList(self):
        ''' Extract the list of supported files '''

        for root, dirs, files in os.walk(self.repository_path, followlinks=True):
            for file in files:
                for supported_format in self.supported_formats:
                    if file.endswith(supported_format):
                        self.file_list.append(os.path.join(root, file))
        self.file_list.sort()

    def getRelativePath(self, file_name):
        ''' Get the relative path of a filename, prepend prefix_storage if
        defined '''

        relative_path = file_name[len(self.repository_path) + 1:]
        # Prepend storage_prefix if defined
        if self.storage_prefix != '':
            relative_path = '{0}/{1}'.format(self.storage_prefix,
                                             relative_path)
        # Hack to work around Transvision symlink mess
        relative_path = relative_path.replace(
            'locales/en-US/en-US/', '')

        return relative_path

    def extractStrings(self):
        ''' Extract strings from all files '''

        # If storage_mode is append, read existing translations (if available)
        # before overriding them
        if self.storage_mode == 'append':
            file_name = self.storage_file + '.json'
            if os.path.isfile(file_name):
                with open(file_name) as f:
                    self.translations = json.load(f)
                f.close()

        # Create a list of files to analyze
        self.extractFileList()

        for file_name in self.file_list:
            file_extension = os.path.splitext(file_name)[1]

            file_parser = parser.getParser(file_extension)
            file_parser.readFile(file_name)
            try:
                entities, map = file_parser.parse()
                for entity in entities:
                    string_id = u'{0}:{1}'.format(
                        self.getRelativePath(file_name), unicode(entity))
                    if not isinstance(entity, parser.Junk):
                        self.translations[string_id] = entity.raw_val
            except Exception as e:
                print 'Error parsing file: {0}'.format(file_name)
                print e

        # Remove extra strings from locale
        if self.reference_locale != self.locale:
            # Read the JSON cache for reference locale if available
            file_name = self.reference_storage_file + '.json'
            if os.path.isfile(file_name):
                with open(file_name) as f:
                    reference_strings = json.load(f)
                f.close()

                for string_id in self.translations.keys():
                    if string_id not in reference_strings:
                        del(self.translations[string_id])

    def storeTranslations(self, output_format):
        '''
            Store translations on file.
            If no format is specified, both JSON and PHP formats will
            be stored on file.
        '''

        if output_format != 'php':
            # Store translations in JSON format
            f = open(self.storage_file + '.json', 'w')
            f.write(json.dumps(self.translations, sort_keys=True))
            f.close()

        if output_format != 'json':
            # Store translations in PHP format (array)
            string_ids = self.translations.keys()
            string_ids.sort()

            f = open(self.storage_file + '.php', 'w')
            f.write('<?php\n$tmx = [\n')
            for string_id in string_ids:
                translation = self.escape(
                    self.translations[string_id].encode('utf-8'))
                string_id = self.escape(string_id.encode('utf-8'))
                line = "'{0}' => '{1}',\n".format(string_id, translation)
                f.write(line)
            f.write('];\n')
            f.close()

    def escape(self, translation):
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


def main():
    # Read command line input parameters
    parser = argparse.ArgumentParser()
    parser.add_argument('repo_path', help='Path to locale files')
    parser.add_argument('locale_code', help='Locale language code')
    parser.add_argument('reference_code', help='Reference language code')
    parser.add_argument('repository_name', help='Repository name')
    parser.add_argument('--output', nargs='?', type=str, choices=['json', 'php'],
                        help='Store only one type of output.', default='')
    parser.add_argument('storage_mode', nargs='?',
                        help='If set to \'append\', translations will be added to an existing cache file', default='')
    parser.add_argument('storage_prefix', nargs='?',
                        help='This prefix will be prependended to the identified path in string IDs (e.g. extensions/irc for Chatzilla)', default='')
    args = parser.parse_args()

    extracted_strings = StringExtraction(
        storage_path, args.locale_code, args.reference_code, args.repository_name)

    extracted_strings.setRepositoryPath(args.repo_path.rstrip('/'))
    if args.storage_mode == 'append':
        extracted_strings.setStorageMode('append', args.storage_prefix)

    extracted_strings.extractStrings()
    extracted_strings.storeTranslations(args.output)


if __name__ == '__main__':
    main()
