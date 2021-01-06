#!/usr/bin/env python

import argparse
from configparser import ConfigParser


def main():
    # Parse command line options
    cl_parser = argparse.ArgumentParser()
    cl_parser.add_argument(
        'config_path', help='Path to INI configuration file')
    args = cl_parser.parse_args()

    parser = ConfigParser()
    with open(args.config_path, 'r') as f:
        parser.read_file(f)

    for sec in parser.sections():
        for key, val in parser.items(sec):
            print('{}="{}"'.format(key, val))


if __name__ == '__main__':
    main()
