#!/usr/bin/python

import os
import sys
import datetime
from optparse import OptionParser
from ConfigParser import SafeConfigParser

""" here we read the server configuration file """
parser = SafeConfigParser()
parser.read('web/inc/config.ini')
glossaire = parser.get('config', 'glossaire')
localdir  = parser.get('config', 'root') + '/TMX/'

sys.path.append(glossaire + '/silme/lib')

import silme.diff
import silme.core
import silme.format
import silme.io


silme.format.Manager.register('dtd', 'properties', 'ini', 'inc')

def escape(t):
    """HTML-escape the text in `t`."""
    return (t
        .replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
        .replace("'", "&#39;").replace('"', "&quot;")
        )

def get_string(package, directory):
    for item in package:
        aa = item[0]
        bb = item[1]
        if (type(bb) is not silme.core.structure.Blob) and not(isinstance(bb, silme.core.Package)):
            for id in bb:
                strings[directory + ":" + aa + ":" + id] = bb[id].get_value()

    for pack in package.packages():
        for item in pack:
            if isinstance(item[1], silme.core.Package):
                get_string(item[1], directory)
            else:
                aa = item[0]
                bb = item[1]
                if type(bb) is not silme.core.structure.Blob:
                    for id in bb:
                        strings[directory + ":" + aa + ":" + id] = bb[id].get_value()
    return strings

def tmx_header(fichier, sourcelang):
    from datetime import datetime
    header = '''<?xml version="1.0" encoding="UTF-8"?>
    <tmx version="1.4">
     <header o-tmf="plain text" o-encoding="UTF8" adminlang="en" creationdate="{creationdate}" creationtoolversion="0.1" creationtool="tmxmaker_transvision" srclang="{sourcelang}" segtype="sentence" datatype="plaintext">
     </header>
     <body>
     '''
    fichier.write(header.format(creationdate=str(datetime.now()), sourcelang=sourcelang))


def tmx_add_tu(ent, ch1, ch2, fichier, targetlang, sourcelang):
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

    fichier.write('    <tu tuid="' + ent + '" srclang="' + sourcelang + '">')
    fichier.write("\n")
    fichier.write("        <tuv xml:lang=\"" + sourcelang + "\"><seg>" + ch1.encode('utf-8') + "</seg></tuv>")
    fichier.write("\n")
    fichier.write("        <tuv xml:lang=\"" + targetlang + "\"><seg>" + ch2.encode('utf-8') + "</seg></tuv>")
    fichier.write("\n")
    fichier.write("    </tu>")
    fichier.write("\n")

def tmx_close(fichier):
    fichier.write("</body>\n</tmx>")

def php_header(fichier):
    fichier.write("<?php\n")

def php_add_to_array(ent,ch,fichier):
    ch = escape(ch)
    ch = ch.encode('utf-8')
    fichier.write('$tmx[\'' + ent.encode('utf-8') + "\'] = '" + ch + "';\n")


if __name__ == "__main__":
    usage = "test"
    parser = OptionParser(usage, version='%prog 0.1')
    (options, args) = parser.parse_args(sys.argv[1:])

    if len(args) < 1:
        fr = "../fr/"
        en_US = "../hg.frenchmozilla.fr/"
    else:
        if len(args) < 2:
            fr = args[0]
            en_US = "../hg.frenchmozilla.fr/"
        else:
            fr = args[0]
            en_US = args[1]

    if len(args) < 3:
        langcode1 = "fr"
        langcode2 = "en-US"

    if 4 > len(args) > 2:
        langcode1 = args[2]
        langcode2 = "en_US"

    if len(args) > 3:
        langcode1 = args[2]
        langcode2 = args[3]

    if len(args) > 4:
        depot = args[4]

    dirs1 = os.listdir(fr)
    dirs2 = ["b2g", "browser", "calendar", "dom", "editor", "embedding",
            "extensions", "layout", "mail", "mobile", "netwerk",
            "other-licenses", "security", "services", "suite",
            "testing", "toolkit"]
    dirs = filter(lambda x:x in dirs1, dirs2)

    #~ localdir    = "/home/pascalc/transvision/TMX/"
    localpath   = localdir + depot + "/" + langcode1
    nomfichier1 = localpath + "/memoire_" + langcode2 + "_" + langcode1 + ".tmx"
    nomfichier2 = localpath + "/cache_" + langcode2 + ".php"
    nomfichier3 = localpath + "/cache_" + langcode1 + ".php"

    fichier1 = open(nomfichier1, "w")
    fichier2 = open(nomfichier2, "w")
    fichier3 = open(nomfichier3, "w")
    tmx_header(fichier1, langcode2)
    php_header(fichier2)
    php_header(fichier3)
    total = {}
    total2 = {}
    for directory in dirs:
        path1 = en_US + directory
        path2 = fr + directory

        rcsClient = silme.io.Manager.get('file')
        l10nPackage = rcsClient.get_package(path1, object_type='entitylist')
        rcsClient2 = silme.io.Manager.get('file')
        l10nPackage2 = rcsClient.get_package(path2, object_type='entitylist')


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
            if len(chaine[entity]) > 2:
                tmx_add_tu(entity, chaine[entity], chaine2.get(entity,""), fichier1, langcode1, langcode2)
                php_add_to_array(entity, chaine[entity], fichier2)
                php_add_to_array(entity, chaine2.get(entity,""), fichier3)
                total[entity] = chaine.get(entity,"")
                total2[entity] = chaine2.get(entity,"")


    tmx_close(fichier1)
    fichier1.close()
    fichier2.close()
    fichier3.close()
