import argparse
import os

from configparser import ConfigParser
from typing import Union

from moz.l10n.formats import Format
from moz.l10n.message import serialize_message
from moz.l10n.model import (
    CatchallKey,
    Entry,
    Message,
    PatternMessage,
    Resource,
    SelectMessage,
)


def get_config() -> str:
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
        root_folder = os.path.abspath(
            os.path.join(os.path.dirname(__file__), os.pardir)
        )
        storage_path = os.path.join(root_folder, "TMX")
        os.makedirs(storage_path, exist_ok=True)
    else:
        config_parser = ConfigParser()
        config_parser.read(config_file)
        storage_path = os.path.join(config_parser.get("config", "root"), "TMX")

    return storage_path


def get_cli_parameters(config: bool = False) -> argparse.Namespace:
    # Read command line input parameters
    parser = argparse.ArgumentParser()

    if config:
        parser.add_argument("toml_path", help="Path to root l10n.toml file")
        parser.add_argument(
            "--android",
            dest="android_project",
            action="store_true",
            help="If passed, the script will parse the config file using Android locale codes",
            default=False,
        )
    else:
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

    # Common parameters
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

    return parser.parse_args()


def parse_file(
    resource: Resource,
    storage: dict[str, str],
    filename: str,
    id_format: str,
) -> None:
    def get_entry_value(value: Message) -> str:
        entry_value = serialize_message(resource.format, value)
        if resource.format == Format.android:
            # In Android resources, unescape quotes
            entry_value = entry_value.replace('\\"', '"').replace("\\'", "'")

        return entry_value

    def serialize_select_variants(entry: Entry) -> str:
        msg: SelectMessage = entry.value
        lines: list[str] = []
        for key_tuple, pattern in msg.variants.items():
            key: Union[str, CatchallKey] = key_tuple[0] if key_tuple else "other"
            default = "*" if isinstance(key, CatchallKey) else ""
            label: str | None = key.value if isinstance(key, CatchallKey) else str(key)
            lines.append(
                f"{default}[{label}] {serialize_message(resource.format, PatternMessage(pattern))}"
            )
        return "\n".join(lines)

    try:
        for section in resource.sections:
            for entry in section.entries:
                if isinstance(entry, Entry):
                    if resource.format == Format.ini:
                        entry_id = ".".join(entry.id)
                    else:
                        entry_id = ".".join(section.id + entry.id)
                    string_id = f"{id_format}:{entry_id}"
                    if entry.properties:
                        # Store the value of an entry with attributes only
                        # if the value is not empty.
                        if not entry.value.is_empty():
                            storage[string_id] = get_entry_value(entry.value)
                        for attribute, attr_value in entry.properties.items():
                            attr_id = f"{string_id}.{attribute}"
                            storage[attr_id] = get_entry_value(attr_value)
                    else:
                        if resource.format == Format.android:
                            # If it's a plural string in Android, each variant
                            # is stored within the message, following a format
                            # similar to Fluent.
                            if hasattr(entry.value, "variants"):
                                storage[string_id] = serialize_select_variants(entry)
                            else:
                                storage[string_id] = get_entry_value(entry.value)
                        else:
                            storage[string_id] = get_entry_value(entry.value)
    except Exception as e:
        print(f"Error parsing file: {filename}")
        print(e)
