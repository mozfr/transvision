#!/usr/bin/python

import os
import sys
import datetime
from optparse import OptionParser
from ConfigParser import SafeConfigParser

""" here we read the server configuration file """
parser = SafeConfigParser()
parser.read('app/config/config.ini')
libraries = parser.get('config', 'libraries')
localdir  = parser.get('config', 'root') + '/TMX/'

sys.path.append(libraries + '/silme/lib')

import silme.diff
import silme.core
import silme.format
import silme.io


silme.format.Manager.register('dtd', 'properties', 'ini', 'inc')

def escape(t):
    """HTML-escape the text in `t`. We first hide real common entities to avoid double escaping"""
    return (t
        .replace("&quot;", '@quot;')
        .replace("&amp;", "@amp;").replace("&lt;", "@lt;").replace("&gt;", "@gt;")

        .replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
        .replace("'", "&#39;").replace('"', "&quot;")
        .replace("\\", "&#92;")

        .replace("@quot;", '&quot;')
        .replace("@amp;", "&amp;").replace("@lt;", "&lt;").replace("@gt;", "&gt;")

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

def tmx_header(target_file, sourcelang):
    from datetime import datetime
    header = '''<?xml version="1.0" encoding="UTF-8"?>
    <tmx version="1.4">
     <header o-tmf="plain text" o-encoding="UTF8" adminlang="en" creationdate="{creationdate}" creationtoolversion="0.1" creationtool="tmxmaker_transvision" srclang="{sourcelang}" segtype="sentence" datatype="plaintext">
     </header>
     <body>
     '''
    target_file.write(header.format(creationdate=str(datetime.now()), sourcelang=sourcelang))


def tmx_add_tu(ent, ch1, ch2, target_file, targetlang, sourcelang):
    ch1 = ch1.replace('&', '&amp;')
    ch2 = ch2.replace('&', '&amp;')
    ch1 = ch1.replace('<', '&lt;')
    ch1 = ch1.replace('>', '&gt;')
    ch2 = ch2.replace('<', '&lt;')
    ch2 = ch2.replace('>', '&gt;')
    ch1 = ch1.replace('"', '')
    ch2 = ch2.replace('"', '')
    ch1 = ch1.replace('\\', '')
    ch2 = ch2.replace('\\', '')

    target_file.write('    <tu tuid="' + ent + '" srclang="' + sourcelang + '">')
    target_file.write("\n")
    target_file.write("        <tuv xml:lang=\"" + sourcelang + "\"><seg>" + ch1.encode('utf-8') + "</seg></tuv>")
    target_file.write("\n")
    target_file.write("        <tuv xml:lang=\"" + targetlang + "\"><seg>" + ch2.encode('utf-8') + "</seg></tuv>")
    target_file.write("\n")
    target_file.write("    </tu>")
    target_file.write("\n")

def tmx_close(target_file):
    target_file.write("</body>\n</tmx>")

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

    localpath   = localdir + repo + "/" + langcode1
    filename1 = localpath + "/memoire_" + langcode2 + "_" + langcode1 + ".tmx"
    filename2 = localpath + "/cache_" + langcode2 + ".php"
    filename3 = localpath + "/cache_" + langcode1 + ".php"

    target_file1 = open(filename1, "w")
    target_file2 = open(filename2, "w")
    target_file3 = open(filename3, "w")
    tmx_header(target_file1, langcode2)
    php_header(target_file2)
    php_header(target_file3)

    for directory in dirs:

        path1 = en_US_repo + directory
        path2 = locale_repo + directory

        rcsClient = silme.io.Manager.get('file')
        try:
            l10nPackage = rcsClient.get_package(path1, object_type='entitylist')
        except:
            pass

        rcsClient2 = silme.io.Manager.get('file')
        try:
            l10nPackage2 = rcsClient.get_package(path2, object_type='entitylist')
        except:
            pass

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
            tmx_add_tu(entity, chaine[entity], chaine2.get(entity,""), target_file1, langcode1, langcode2)
            php_add_to_array(entity, chaine[entity], target_file2)
            php_add_to_array(entity, chaine2.get(entity,""), target_file3)
    tmx_close(target_file1)
    php_close_array(target_file2)
    php_close_array(target_file3)
    target_file1.close()
    target_file2.close()
    target_file3.close()
