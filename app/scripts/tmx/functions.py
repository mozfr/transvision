from configparser import ConfigParser
import argparse
import os


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
