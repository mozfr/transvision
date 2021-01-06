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
else:
    config_parser = ConfigParser()
    config_parser.read(config_file)
    storage_path = os.path.join(config_parser.get("config", "root"), "TMX")

try:
    from compare_locales import paths
    from compare_locales.parser import getParser
except ImportError as e:
    print("FATAL: make sure that dependencies are installed")
    print(e)
    sys.exit(1)


class StringExtraction:
    def __init__(self, toml_path, storage_path, reference_locale, repository_name):
        """Initialize object."""

        # Set defaults
        self.translations = {}

        # Set instance variables
        self.toml_path = toml_path
        self.storage_path = storage_path
        self.reference_locale = reference_locale
        self.repository_name = repository_name

    def extractStrings(self):
        """Extract strings from all locales."""

        basedir = os.path.dirname(self.toml_path)
        project_config = paths.TOMLParser().parse(self.toml_path, env={"l10n_base": ""})
        basedir = os.path.join(basedir, project_config.root)

        reference_cache = {}
        self.translations[self.reference_locale] = {}
        for locale in project_config.all_locales:
            files = paths.ProjectFiles(locale, [project_config])
            self.translations[locale] = {}
            for l10n_file, reference_file, _, _ in files:
                if not os.path.exists(l10n_file):
                    # File not available in localization
                    continue

                if not os.path.exists(reference_file):
                    # File not available in reference
                    continue

                key_path = os.path.relpath(reference_file, basedir)
                try:
                    p = getParser(reference_file)
                except UserWarning:
                    continue
                if key_path not in reference_cache:
                    p.readFile(reference_file)
                    reference_cache[key_path] = set(p.parse().keys())
                    self.translations[self.reference_locale].update(
                        (
                            "{}/{}:{}".format(
                                self.repository_name, key_path, entity.key
                            ),
                            entity.raw_val,
                        )
                        for entity in p.parse()
                    )

                p.readFile(l10n_file)
                self.translations[locale].update(
                    (
                        "{}/{}:{}".format(self.repository_name, key_path, entity.key),
                        entity.raw_val,
                    )
                    for entity in p.parse()
                )

    def storeTranslations(self, output_format):
        """
        Store translations on file.
        If no format is specified, both JSON and PHP formats will
        be stored on file.
        """

        for locale in self.translations:
            translations = self.translations[locale]
            storage_folder = os.path.join(self.storage_path, locale)
            storage_file = os.path.join(
                storage_folder, "cache_{}_{}".format(locale, self.repository_name)
            )

            # Make sure that the TMX folder exists
            if not os.path.exists(storage_folder):
                os.mkdir(storage_folder)

            if output_format != "php":
                # Store translations in JSON format
                json_output = json.dumps(translations, sort_keys=True)
                with open("{}.json".format(storage_file), "w") as f:
                    f.write(json_output)

            if output_format != "json":
                # Store translations in PHP format (array)
                string_ids = list(translations.keys())
                string_ids.sort()

                # Generate output before creating an handle for the file
                output_php = []
                output_php.append("<?php\n$tmx = [\n")
                for string_id in string_ids:
                    translation = self.escape(translations[string_id])
                    string_id = self.escape(string_id)
                    output_php.append(u"'{}' => '{}',\n".format(string_id, translation))
                output_php.append("];\n")

                file_name = "{}.php".format(storage_file)
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
    parser.add_argument("toml_path", help="Path to root l10n.toml file")
    parser.add_argument("reference_code", help="Reference language code")
    parser.add_argument("repository_name", help="Repository name")
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
        args.toml_path, storage_path, args.reference_code, args.repository_name
    )
    extracted_strings.extractStrings()
    extracted_strings.storeTranslations(args.output)


if __name__ == "__main__":
    main()
