#!/usr/bin/env python

from configparser import ConfigParser
import argparse
import codecs
import json
import logging
import os
import sys

logging.basicConfig()
# Get absolute path of ../../config from the current script location (not the
# current folder)
config_folder = os.path.abspath(
    os.path.join(os.path.dirname(__file__), os.pardir, os.pardir, "config")
)

# Read Transvision's configuration file from ../../config/config.ini
# If not available use a default storage folder to store data
config_file = os.path.join(config_folder, "config.ini")
if not os.path.isfile(config_file):
    print(
        "Configuration file /app/config/config.ini is missing. "
        "Default settings will be used."
    )
    root_folder = os.path.abspath(os.path.join(os.path.dirname(__file__), os.pardir))
    storage_path = os.path.join(root_folder, "TMX")
    os.makedirs(storage_path, exist_ok=True)
else:
    config_parser = ConfigParser()
    config_parser.read(config_file)
    storage_path = os.path.join(config_parser.get("config", "root"), "TMX")

try:
    from compare_locales import parser
except ImportError as e:
    print("FATAL: make sure that dependencies are installed")
    print(e)
    sys.exit(1)


class StringExtraction:
    def __init__(self, storage_path, locale, reference_locale, repository_name):
        """Initialize object."""

        # Set defaults
        self.supported_formats = [
            ".dtd",
            ".ftl",
            ".inc",
            ".ini",
            ".properties",
        ]
        self.storage_append = False
        self.storage_prefix = ""
        self.file_list = []
        self.translations = {}

        # Set instance variables
        self.storage_path = storage_path
        self.locale = locale
        self.reference_locale = reference_locale

        # Define the local storage filenames
        self.storage_file = os.path.join(
            storage_path, locale, f"cache_{locale}_{repository_name}"
        )

        self.reference_storage_file = os.path.join(
            storage_path,
            reference_locale,
            f"cache_{reference_locale}_{repository_name}",
        )

    def setRepositoryPath(self, path):
        """Set path to repository."""

        # Strip trailing '/' from repository path
        self.repository_path = path.rstrip(os.path.sep)

    def setStorageAppendMode(self, prefix):
        """Set storage mode and prefix."""

        self.storage_append = True
        # Strip trailing '/' from storage_prefix
        self.storage_prefix = prefix.rstrip(os.path.sep)

    def extractFileList(self):
        """Extract the list of supported files."""

        for root, dirs, files in os.walk(self.repository_path, followlinks=True):
            for file in files:
                for supported_format in self.supported_formats:
                    if file.endswith(supported_format):
                        self.file_list.append(os.path.join(root, file))
        self.file_list.sort()

    def getRelativePath(self, file_name):
        """
        Get the relative path of a filename, prepend prefix_storage if
        defined.
        """

        relative_path = file_name[len(self.repository_path) + 1 :]
        # Prepend storage_prefix if defined
        if self.storage_prefix != "":
            relative_path = f"{self.storage_prefix}/{relative_path}"

        return relative_path

    def extractStrings(self):
        """Extract strings from all files."""

        # If storage mode is append, read existing translations (if available)
        # before overriding them
        if self.storage_append:
            file_name = f"{self.storage_file}.json"
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
                entities = file_parser.parse()
                for entity in entities:
                    # Ignore Junk
                    if isinstance(entity, parser.Junk):
                        continue
                    string_id = f"{self.getRelativePath(file_name)}:{entity}"
                    if file_extension == ".ftl":
                        if entity.raw_val is not None:
                            self.translations[string_id] = entity.raw_val
                        # Store attributes
                        for attribute in entity.attributes:
                            attr_string_id = f"{self.getRelativePath(file_name)}:{entity}.{attribute}"
                            self.translations[attr_string_id] = attribute.raw_val
                    else:
                        self.translations[string_id] = entity.raw_val
            except Exception as e:
                print(f"Error parsing file: {file_name}")
                print(e)

        # Remove extra strings from locale
        if self.reference_locale != self.locale:
            # Read the JSON cache for reference locale if available
            file_name = f"{self.reference_storage_file}.json"
            if os.path.isfile(file_name):
                with open(file_name) as f:
                    reference_strings = json.load(f)
                f.close()

                for string_id in list(self.translations.keys()):
                    if string_id not in reference_strings:
                        del self.translations[string_id]

    def storeTranslations(self, output_format):
        """
        Store translations on file.
        If no format is specified, both JSON and PHP formats will
        be stored on file.
        """

        if output_format != "php":
            # Store translations in JSON format
            json_output = json.dumps(self.translations, sort_keys=True)
            with open(f"{self.storage_file}.json", "w") as f:
                f.write(json_output)

        if output_format != "json":
            # Store translations in PHP format (array)
            string_ids = list(self.translations.keys())
            string_ids.sort()

            # Generate output before creating an handle for the file
            output_php = []
            output_php.append("<?php\n$tmx = [\n")
            for string_id in string_ids:
                translation = self.escape(self.translations[string_id])
                string_id = self.escape(string_id)
                output_php.append(f"'{string_id}' => '{translation}',\n")
            output_php.append("];\n")

            file_name = f"{self.storage_file}.php"
            with codecs.open(file_name, "w", encoding="utf-8") as f:
                f.writelines(output_php)

    def escape(self, translation):
        """
        Escape quotes and backslahes in translation. There are two
        issues:
        * Internal Python escaping: the string "this is a \", has an internal
          representation as "this is a \\".
          Also, "\\test" is equivalent to r"\test" (raw string).
        * We need to print these strings into a file, with the format of a
          PHP array delimited by single quotes ('id' => 'translation'). Hence
          we need to escape single quotes, but also escape backslashes.
          "this is a 'test'" => "this is a \'test\'"
          "this is a \'test\'" => "this is a \\\'test\\\'"
        """

        # Escape slashes
        escaped_translation = translation.replace("\\", "\\\\")
        # Escape single quotes
        escaped_translation = escaped_translation.replace("'", "\\'")

        return escaped_translation


def main():
    # Read command line input parameters
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "--path",
        dest="repo_path",
        help="Path to locale files",
        required=True,
    )
    parser.add_argument(
        "--locale",
        dest="locale_code",
        help="Locale code",
        required=True,
    )
    parser.add_argument(
        "--ref",
        dest="reference_code",
        help="Reference locale code",
        required=True,
    )
    parser.add_argument(
        "--repo", dest="repository_name", help="Repository name", required=True
    )
    parser.add_argument(
        "--append",
        dest="append_mode",
        action="store_true",
        help="If set to 'append', translations will be added to an existing cache file",
    )
    parser.add_argument(
        "--prefix",
        dest="storage_prefix",
        nargs="?",
        help="This prefix will be prependended to the identified "
        "path in string IDs (e.g. extensions/irc for Chatzilla)",
        default="",
    )
    parser.add_argument(
        "--output",
        nargs="?",
        type=str,
        choices=["json", "php"],
        help="Store only one type of output.",
        default="",
    )
    args = parser.parse_args()

    extracted_strings = StringExtraction(
        storage_path, args.locale_code, args.reference_code, args.repository_name
    )

    extracted_strings.setRepositoryPath(args.repo_path)
    if args.append_mode:
        extracted_strings.setStorageAppendMode(args.storage_prefix)

    extracted_strings.extractStrings()
    extracted_strings.storeTranslations(args.output)


if __name__ == "__main__":
    main()
