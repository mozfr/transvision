#!/usr/bin/env python

# Python 2/3 compatibility
try:
    from ConfigParser import SafeConfigParser
except ImportError:
    from configparser import ConfigParser as SafeConfigParser
import sys

parser = SafeConfigParser()
parser.readfp(sys.stdin)

for sec in parser.sections():
    for key, val in parser.items(sec):
        print('{}="{}"'.format(key, val))
