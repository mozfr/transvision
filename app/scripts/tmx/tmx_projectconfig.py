#!/usr/bin/env python

from compare_locales import parser
from configparser import ConfigParser
from moz.l10n.paths import L10nConfigPaths, get_android_locale
import argparse
import codecs
import json
import logging
import os

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


class StringExtraction:
    def __init__(
        self,
        toml_path,
        storage_path,
        reference_locale,
        repository_name,
        android_project,
    ):
        """Initialize object."""

        # Set defaults
        self.translations = {}
        self.storage_append = False
        self.storage_prefix = ""

        # Set instance variables
        self.toml_path = toml_path
        self.storage_path = storage_path
        self.reference_locale = reference_locale
        self.repository_name = repository_name
        self.android_project = android_project

    def setStorageAppendMode(self, prefix):
        """Set storage mode and prefix."""

        self.storage_append = True
        # Strip trailing '/' from storage_prefix
        self.storage_prefix = prefix.rstrip(os.path.sep)

    def extractStrings(self):
        """Extract strings from all locales."""

        def readExistingJSON(locale):
            """Read translations from existing JSON file"""
            translations = {}
            storage_file = os.path.join(
                os.path.join(self.storage_path, locale),
                f"cache_{locale}_{self.repository_name}",
            )
            file_name = f"{storage_file}.json"
            if os.path.isfile(file_name):
                with open(file_name) as f:
                    translations = json.load(f)

            return translations

        def readFiles(locale):
            """Read files for locale"""

            if locale != self.reference_locale:
                locale_files = [
                    (
                        os.path.abspath(ref_path),
                        os.path.abspath(tgt_path),
                    )
                    for (
                        ref_path,
                        raw_tgt_path,
                    ), locales in project_config_paths.all().items()
                    if locale in locales
                    and os.path.exists(
                        tgt_path := project_config_paths.format_target_path(
                            raw_tgt_path, locale
                        )
                    )
                ]
            else:
                locale_files = [
                    (os.path.abspath(ref_path), os.path.abspath(ref_path))
                    for ref_path in project_config_paths.ref_paths
                ]

            for reference_file, l10n_file in locale_files:
                if not os.path.exists(l10n_file):
                    # File not available in localization
                    continue

                if not os.path.exists(reference_file):
                    # File not available in reference
                    continue

                key_path = os.path.relpath(reference_file, basedir)
                # Prepend storage_prefix if defined
                if self.storage_prefix != "":
                    key_path = f"{self.storage_prefix}/{key_path}"
                try:
                    p = parser.getParser(reference_file)
                except UserWarning:
                    continue

                p.readFile(l10n_file)
                if isinstance(p, parser.android.AndroidParser):
                    # As of https://github.com/mozilla/pontoon/pull/3611, Pontoon
                    # uses moz.l10n for resource parsing, resulting in quotes being
                    # escaped. compare-locales doesn't escape them, so need to
                    # manually remove escapes.
                    self.translations[locale].update(
                        (
                            f"{self.repository_name}/{key_path}:{entity.key}",
                            entity.raw_val.replace("\\'", "'").replace('\\"', '"'),
                        )
                        for entity in p.parse()
                    )
                else:
                    self.translations[locale].update(
                        (
                            f"{self.repository_name}/{key_path}:{entity.key}",
                            entity.raw_val,
                        )
                        for entity in p.parse()
                    )

        basedir = os.path.dirname(self.toml_path)
        if self.android_project:
            project_config_paths = L10nConfigPaths(
                self.toml_path, locale_map={"android_locale": get_android_locale}
            )
        else:
            project_config_paths = L10nConfigPaths(self.toml_path)

        # Read strings for reference locale
        self.translations[self.reference_locale] = (
            readExistingJSON(self.reference_locale) if self.storage_append else {}
        )
        readFiles(self.reference_locale)

        locales = list(project_config_paths.all_locales)
        locales.sort()
        for locale in locales:
            # If storage mode is append, read existing translations (if available)
            self.translations[locale] = (
                readExistingJSON(locale) if self.storage_append else {}
            )
            readFiles(locale)

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
                storage_folder, f"cache_{locale}_{self.repository_name}"
            )

            # Make sure that the TMX folder exists
            if not os.path.exists(storage_folder):
                os.mkdir(storage_folder)

            if output_format != "php":
                # Store translations in JSON format
                json_output = json.dumps(translations, sort_keys=True)
                with open(f"{storage_file}.json", "w") as f:
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
                    output_php.append(f"'{string_id}' => '{translation}',\n")
                output_php.append("];\n")

                file_name = f"{storage_file}.php"
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
    parser.add_argument(
        "--ref",
        dest="reference_code",
        help="Reference language code",
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
        "--android",
        dest="android_project",
        action="store_true",
        help="If passed, the script will parse the config file using Android locale codes",
        default=False,
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
        args.toml_path,
        storage_path,
        args.reference_code,
        args.repository_name,
        args.android_project,
    )
    if args.append_mode:
        extracted_strings.setStorageAppendMode(args.storage_prefix)

    extracted_strings.extractStrings()
    extracted_strings.storeTranslations(args.output)


if __name__ == "__main__":
    main()
