#!/usr/bin/env python

from ConfigParser import SafeConfigParser
import sys

parser = SafeConfigParser()
parser.readfp(sys.stdin)

for sec in parser.sections():
    for key, val in parser.items(sec):
        print '%s="%s"' % (key, val)
