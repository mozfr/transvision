#!/usr/bin/env python

import argparse

# Python 2/3 compatibility
try:
    from ConfigParser import SafeConfigParser
except ImportError:
    from configparser import ConfigParser as SafeConfigParser


def main():
    # Parse command line options
    cl_parser = argparse.ArgumentParser()
    cl_parser.add_argument(
        'config_path', help='Path to INI configuration file')
    args = cl_parser.parse_args()

    parser = SafeConfigParser()
    with open(args.config_path, 'r') as f:
        if 'read_file' in dir(SafeConfigParser):
            # Python 3
            parser.read_file(f)
        else:
            # Python 2 fallback
            parser.readfp(f)

    for sec in parser.sections():
        for key, val in parser.items(sec):
            print('{}="{}"'.format(key, val))


if __name__ == '__main__':
    main()
