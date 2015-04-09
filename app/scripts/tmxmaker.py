#!/usr/bin/python

import os
import sys
import datetime
from optparse import OptionParser
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
    usage = "test"
    parser = OptionParser(usage, version='%prog 0.1')
    (options, args) = parser.parse_args(sys.argv[1:])

    locale_repo = args[0]
    en_US_repo = args[1]
    langcode1 = args[2]
    langcode2 = args[3]
    repo = args[4]

    exclusionlist = ['.hgtags', '.hg', '.git', '.gitignore']
    dirs1 = os.listdir(locale_repo)
    if repo.startswith('gaia') or repo == 'l20n_test' :
        dirs2 = os.listdir(en_US_repo)
        dirs2 = [x for x in dirs2 if x not in exclusionlist]
    else:
        dirs2 = ["browser", "calendar", "chat", "dom", "editor",
                "extensions", "mail", "mobile", "netwerk", "other-licenses",
                "security", "services", "suite", "toolkit", "webapprt"]

    dirs = filter(lambda x:x in dirs1, dirs2)

    localpath = os.path.join(localdir, repo, langcode1)
    filename1 = os.path.join(localpath, "cache_" + langcode2 + ".php")
    filename2 = os.path.join(localpath, "cache_" + langcode1 + ".php")

    target_file1 = open(filename1, "w")
    target_file2 = open(filename2, "w")
    php_header(target_file1)
    php_header(target_file2)

    for directory in dirs:

        path1 = en_US_repo + directory
        path2 = locale_repo + directory

        rcsClient = silme.io.Manager.get('file')
        try:
            l10nPackage = rcsClient.get_package(path1, object_type='entitylist')
        except:
            print 'Silme couldn\'t extract data for ' + path1
            continue

        try:
            l10nPackage2 = rcsClient.get_package(path2, object_type='entitylist')
        except:
            print 'Silme couldn\'t extract data for ' + path2
            continue

        strings = {}
        chaine = get_string(l10nPackage, directory)

        """
        get_string() is a recursive function that fills 'strings', a global array
        We need to reset that global array before calling the function again
        """
        del strings
        strings = {}
        chaine2 = get_string(l10nPackage2, directory)


        for entity in chaine:
            php_add_to_array(entity, chaine[entity], target_file1)
            php_add_to_array(entity, chaine2.get(entity, ""), target_file2)
    php_close_array(target_file1)
    php_close_array(target_file2)
    target_file1.close()
    target_file2.close()
