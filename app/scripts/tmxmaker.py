#!/usr/bin/python

import argparse
import datetime
import os
import sys
from ConfigParser import SafeConfigParser

""" Here we read the server configuration file """
parser = SafeConfigParser()
# Get absolute path of ../config from current script location (not current folder)
config_folder = os.path.abspath(os.path.join(os.path.dirname( __file__ ), os.pardir, 'config'))
parser.read(os.path.join(config_folder, "config.ini"))
libraries = parser.get('config', 'libraries')
localdir = os.path.join(parser.get('config', 'root'), 'TMX')

sys.path.append(libraries + '/silme/lib')

import silme.diff
import silme.core
import silme.format
import silme.io

silme.format.Manager.register('dtd', 'properties', 'ini', 'inc')

def escape(t):
    """Escape quotes in `t`. Complicated replacements because some strings are already escaped in the repo"""
    return (t.replace("\\'", '_qu0te_')
        .replace("\\", "_sl@sh_")
        .replace("'", "\\'")
        .replace('_qu0te_', "\\'")
        .replace('_sl@sh_', '\\\\')
        )

def get_string(package, localdirectory):
    for item in package:
        if (type(item[1]) is not silme.core.structure.Blob) and not(isinstance(item[1], silme.core.Package)):
            for entity in item[1]:
                strings[localdirectory + "/" + item[0] + ":" + entity] = item[1][entity].get_value()
        elif (isinstance(item[1], silme.core.Package)):
            if (item[0] != 'en-US') and (item[0] != 'locales'):
                get_string(item[1], localdirectory + '/' + item[0])
            else:
                get_string(item[1], localdirectory)

    return strings

def php_header(target_file):
    target_file.write("<?php\n$tmx = [\n")

def php_add_to_array(ent,ch,target_file):
    ch = escape(ch)
    ch = ch.encode('utf-8')
    target_file.write('\'' + ent.encode('utf-8') + "\' => '" + ch + "',\n")

def php_close_array(target_file):
    target_file.write("];\n")

if __name__ == "__main__":
    # Read command line input parameters
    parser = argparse.ArgumentParser()
    parser.add_argument('locale_repo', help='Path to locale files')
    parser.add_argument('reference_repo', help='Path to reference files')
    parser.add_argument('locale_code', help='Locale language code')
    parser.add_argument('reference_code', help='Reference language code')
    parser.add_argument('repository', help='Repository name')
    args = parser.parse_args()

    exclusionlist = ['.hgtags', '.hg', '.git', '.gitignore']
    dirs_locale = os.listdir(args.locale_repo)
    if args.repository.startswith('gaia') or args.repository == 'l20n_test' :
        dirs_reference = os.listdir(args.reference_repo)
        dirs_reference = [x for x in dirs_reference if x not in exclusionlist]
    else:
        dirs_reference = [
            "browser", "calendar", "chat", "devtools", "dom", "editor",
            "extensions", "mail", "mobile", "netwerk", "other-licenses",
            "security", "services", "suite", "toolkit", "webapprt"
        ]

    dirs = filter(lambda x:x in dirs_locale, dirs_reference)

    localpath = os.path.join(localdir, args.locale_code)
    filename_locale = os.path.join(localpath, "cache_%s_%s.php" % (args.locale_code, args.repository))

    target_locale = open(filename_locale, "w")
    php_header(target_locale)

    for directory in dirs:
        path_reference = args.reference_repo + directory
        path_locale = args.locale_repo + directory

        rcsClient = silme.io.Manager.get('file')
        try:
            l10nPackage_reference = rcsClient.get_package(path_reference, object_type='entitylist')
        except:
            print 'Silme couldn\'t extract data for ' + path_reference
            continue

        try:
            l10nPackage_locale = rcsClient.get_package(path_locale, object_type='entitylist')
        except:
            print 'Silme couldn\'t extract data for ' + path_locale
            continue

        strings = {}
        strings_reference = get_string(l10nPackage_reference, directory)

        """
        get_string() is a recursive function that fills 'strings', a global array
        We need to reset that global array before calling the function again
        """
        del strings
        strings = {}
        strings_locale = get_string(l10nPackage_locale, directory)

        for entity in strings_reference:
            php_add_to_array(entity, strings_locale.get(entity, ""), target_locale)

    php_close_array(target_locale)
    target_locale.close()
