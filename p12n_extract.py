#! /usr/bin/env python

# This script is designed to work inside a Transvision's folder
# (https://github.com/mozfr/transvision)

import collections
import glob
import json
import os
import re
import StringIO
from ConfigParser import SafeConfigParser
from optparse import OptionParser
from time import strftime, gmtime
from xml.dom import minidom


def extract_sp_product(path, product, locale, channel, jsondata, splist_enUS, images_list, html_output):
    try:
        sp_list = []
        if locale != "en-US":
            # Read the list of searchplugins
            if (product != "metro"):
                file_list = path + "list.txt"
            else:
                file_list = path + "metrolist.txt"

            if os.path.isfile(file_list):
                sp_list = open(file_list, "r").read().splitlines()
                # Remove empty lines
                sp_list = filter(bool, sp_list)

                # Check for duplicates
                if (len(sp_list) != len(set(sp_list))):
                    # set(sp_list) remove duplicates. If I'm here, there are
                    # duplicated elements in list.txt, which is an error
                    duplicated_items = [x for x, y in collections.Counter(sp_list).items() if y > 1]
                    duplicated_items_str =  ", ".join(duplicated_items)
                    html_output.append("<p><span class='error'>Error:</span> there are duplicated items (" +
                                        duplicated_items_str + ") in the list (" + locale +
                                        ", " + product + ", " + channel + ").</p>")
        else:
            # en-US is different: I must analyze all xml files in the folder,
            # since some searchplugins are not used in en-US but from other
            # locales
            sp_list = splist_enUS

        output = ""

        if (locale != "en-US" and len(sp_list)>0):
            # Get a list of all files inside path
            for singlefile in glob.glob(path+"*"):
                # Remove extension
                filename = os.path.basename(singlefile)
                filename_noext = os.path.splitext(filename)[0]
                if (filename_noext in splist_enUS):
                    # There's a problem: file exists but has the same name of an
                    # en-US searchplugin. Warn about this
                    html_output.append("<p><span class='error'>Error:</span> file " + filename +
                        " should not exist in the locale folder, same name of en-US searchplugin (" +
                        locale + ", " + product + ", " + channel + ").</p>")
                else:
                    # File is not in use, should be removed
                    sp_list_extended = sp_list

                    # For browser I need to check both "metro" and "browser" for missing files
                    if (product == "metro" or product == "browser"):
                        if (product == "metro"):
                            file_list = path + "list.txt"
                        else:
                            file_list = path + "metrolist.txt"
                        if os.path.isfile(file_list):
                            sp_list_secondary = open(file_list, "r").read().splitlines()
                            # Remove empty lines
                            sp_list_secondary = filter(bool, sp_list_secondary)
                            # Create a unique list, remove duplicates
                            sp_list_extended = list(set(sp_list + sp_list_secondary))

                    if (filename_noext not in sp_list_extended) & (filename != "list.txt") & (filename != "metrolist.txt"):
                        html_output.append("<p><span class='error'>Error:</span> [" + product + "] file " + filename +
                        " not in list.txt")

        # For each searchplugin check if the file exists (localized version) or
        # not (using en-US version)
        for sp in sp_list:
            sp_file = path + sp + ".xml"

            existingfile = os.path.isfile(sp_file)

            if (locale != "en-US") & (sp in splist_enUS) & (existingfile):
                # There's a problem: file exists but has the same name of an
                # en-US searchplugin. This file will never be picked at build
                # time, so let's analyze en-US and use it for json, acting
                # like the file doesn't exist, and print an error
                existingfile = False

            if (existingfile):
                try:
                    searchplugin_info = "(" + locale + ", " + product + ", " + channel + ", " + sp + ".xml)"

                    try:
                        xmldoc = minidom.parse(sp_file)
                    except Exception as e:
                        # Some search plugin has preprocessing instructions
                        # (#define, #if), so they fail validation. In order to
                        # extract the information I need I read the file,
                        # remove lines starting with # and parse that content
                        # instead of the original XML file
                        preprocessor = False
                        newspcontent = ""
                        for line in open(sp_file, "r").readlines():
                            if re.match("#", line):
                                # Line starts with a #
                                preprocessor = True
                            else:
                                # Line is ok, adding it to newspcontent
                                newspcontent = newspcontent + line
                        if preprocessor:
                            html_output.append("<p><span class='warning'>Warning:</span> searchplugin contains " +
                                    "preprocessor instructions (e.g. #define, #if) that have been stripped in order " +
                                    "to parse the XML " + searchplugin_info + "</p>")
                            try:
                                xmldoc = minidom.parse(StringIO.StringIO(newspcontent))
                            except Exception as e:
                                html_output.append("<p><span class='error'>Error:</span> problem parsing XML for " +
                                "searchplugin " + searchplugin_info + "<br/>")
                        else:
                            html_output.append("<p><span class='error'>Error:</span> problem parsing XML for "+
                                "searchplugin " + searchplugin_info + "<br/>")
                            html_output.append("<span class='code'>" + str(e) + "</span></p>")

                    # Some searchplugins use the form <tag>, others <os:tag>
                    try:
                        node = xmldoc.getElementsByTagName("ShortName")
                        if (len(node) == 0):
                            node = xmldoc.getElementsByTagName("os:ShortName")
                        name = node[0].childNodes[0].nodeValue
                    except Exception as e:
                        html_output.append("<p><span class='error'>Error:</span> problem extracting name from " +
                            "searchplugin " + searchplugin_info + "</p>")
                        name = "not available"

                    try:
                        node = xmldoc.getElementsByTagName("Description")
                        if (len(node) == 0):
                            node = xmldoc.getElementsByTagName("os:Description")
                        description = node[0].childNodes[0].nodeValue
                    except Exception as e:
                        # We don't really use description anywhere, so I don't print errors
                        description = "not available"

                    try:
                        # I can have more than one url element, for example one
                        # for searches and one for suggestions
                        secure = 0

                        nodes = xmldoc.getElementsByTagName("Url")
                        if (len(nodes) == 0):
                            nodes = xmldoc.getElementsByTagName("os:Url")
                        for node in nodes:
                            if node.attributes["type"].nodeValue == "text/html":
                                url = node.attributes["template"].nodeValue
                        p = re.compile("^https://")

                        if p.match(url):
                            secure = 1
                    except Exception as e:
                        html_output.append("<p><span class='error'>Error:</span> problem extracting url from " +
                            "searchplugin " + searchplugin_info + "</p>")
                        url = "not available"

                    try:
                        # Since bug 900137, searchplugins can have multiple images
                        images = []
                        nodes = xmldoc.getElementsByTagName("Image")
                        if (len(nodes) == 0):
                            nodes = xmldoc.getElementsByTagName("os:Image")
                        for node in nodes:
                            image = node.childNodes[0].nodeValue
                            if image in images_list:
                                # Image already stored. In the json record store only the index
                                images.append(images_list.index(image))
                            else:
                                # Store image in images_list, get index and store in json
                                images_list.append(image)
                                images.append(len(images_list)-1)

                            # On mobile we can't have % characters, see for example bug 850984. Print a warning in this case
                            if (product == "mobile"):
                                if ("%" in image):
                                    html_output.append("<p><span class='warning'>Warning:</span> searchplugin's image " +
                                        "on mobile can't contain % character " + searchplugin_info + "</p>")

                    except Exception as e:
                        html_output.append("<p><span class='error'>Error:</span> problem extracting image from searchplugin " +
                            searchplugin_info + "</p>")
                        images.append(images_list[0])

                    # Check if node for locale already exists
                    if (locale not in jsondata):
                        jsondata[locale] = {}
                    # Check if node for locale->product already exists
                    if (product not in jsondata[locale]):
                        jsondata[locale][product] = {}
                    # Check if node for locale->product->channel already exists
                    if (channel not in jsondata[locale][product]):
                        jsondata[locale][product][channel] = {}

                    jsondata[locale][product][channel][sp] = {
                        "file": sp + ".xml",
                        "name": name,
                        "description": description,
                        "url": url,
                        "secure": secure,
                        "images": images,
                    }

                except Exception as e:
                    html_output.append("<p><span class='error'>Error:</span> problem analyzing searchplugin " +
                        searchplugin_info + "<br/>")
                    html_output.append("<span class='code'>" + str(e) + "</span></p>")
            else:
                # File does not exists, locale is using the same plugin of en-
                # US, I have to retrieve it from the dictionary
                try:
                    searchplugin_enUS = jsondata["en-US"][product][channel][sp]

                    # Check if node for locale already exists
                    if (locale not in jsondata):
                        jsondata[locale] = {}
                    # Check if node for locale->product already exists
                    if (product not in jsondata[locale]):
                        jsondata[locale][product] = {}
                    # Check if node for locale->product->channel already exists
                    if (channel not in jsondata[locale][product]):
                        jsondata[locale][product][channel] = {}

                    jsondata[locale][product][channel][sp] = {
                        "file": sp + ".xml",
                        "name": searchplugin_enUS["name"],
                        "description": "(en-US) " + searchplugin_enUS["description"],
                        "url": searchplugin_enUS["url"],
                        "secure": searchplugin_enUS["secure"],
                        "images": searchplugin_enUS["images"]
                    }
                except Exception as e:
                    # File does not exist but we don't have the en-US either.
                    # This means that list.txt references a non existing
                    # plugin, which will cause the build to fail
                    html_output.append("<p><span class='error'>Error:</span> file referenced in list.txt but not available (" +
                        locale + ", " + product + ", " + channel + ", " + sp + ".xml)</p>")

    except Exception as e:
        html_output.append("<p><span class='error'>Error:</span> [" + locale + "] problem reading " + file_list + "</p>")




def extract_p12n_product(source, product, locale, channel, jsondata, html_output):
    # Use jsondata to create a list of all Searchplugins' descriptions
    try:
        available_searchplugins = []
        if (channel in jsondata[locale][product]):
            # I need to proceed only if I have searchplugin for this branch+product+locale
            for element in jsondata[locale][product][channel].values():
                if ("name" in element):
                    available_searchplugins.append(element["name"])

            existingfile = os.path.isfile(source)
            if existingfile:
                try:
                    # Read region.properties, ignore comments and empty lines
                    values = {}
                    for line in open(source):
                        li = line.strip()
                        if (not li.startswith("#")) & (li != ""):
                            try:
                                # Split considering only the firs =
                                key, value = li.split('=', 1)
                                # Remove whitespaces, some locales use key = value instead of key=value
                                values[key.strip()] = value.strip()
                            except Exception as e:
                                html_output.append("<p><span class='error'>Error:</span> problem parsing " + source +
                                    " (" + locale + ", " + product + ", " + channel + ")</p>")
                except Exception as e:
                    html_output.append("<p><span class='error'>Error:</span> problem reading " + source + " (" +
                        locale + ", " + product + ", " + channel + ")</p>")

                # Check if node for locale already exists
                if (locale not in jsondata):
                    jsondata[locale] = {}
                # Check if node for locale->product already exists
                if (product not in jsondata[locale]):
                    jsondata[locale][product] = {}
                # Check if node for locale->product->channel already exists
                if (channel not in jsondata[locale][product]):
                    jsondata[locale][product][channel] = {}

                defaultenginename = '-'
                searchorder = {}
                feedhandlers = {}
                handlerversion = '-'
                contenthandlers = {}

                for key, value in values.iteritems():
                    lineok = False

                    # Default search engine name. Example:
                    # browser.search.defaultenginename=Google
                    if key.startswith('browser.search.defaultenginename'):
                        lineok = True
                        defaultenginename = values["browser.search.defaultenginename"]
                        if (unicode(defaultenginename, "utf-8") not in available_searchplugins):
                            html_output.append("<p><span class='error'>Error:</span> [" + product + "] " +
                                defaultenginename + " is set as default but not available in searchplugins (check if " +
                                "the name is spelled correctly)</p>")

                    # Search engines order. Example:
                    # browser.search.order.1=Google
                    if key.startswith('browser.search.order.'):
                        lineok = True
                        searchorder[key[-1:]] = value
                        if (unicode(value, "utf-8") not in available_searchplugins):
                            if (value != ""):
                                html_output.append("<p><span class='error'>Error:</span> [" + product + "] <span class='code'>" +
                                    value + "</span> is defined in searchorder but not available in searchplugins " +
                                    "(check if the name is spelled correctly)</p>")
                            else:
                                html_output.append("<p><span class='error'>Error:</span> [" + product + "] <span class='code'>" +
                                    key + "</span> is empty")

                    # Feed handlers. Example:
                    # browser.contentHandlers.types.0.title=My Yahoo!
                    # browser.contentHandlers.types.0.uri=http://add.my.yahoo.com/rss?url=%s
                    if key.startswith('browser.contentHandlers.types.'):
                        lineok = True
                        if key.endswith('.title'):
                            feedhandler_number = key[-7:-6]
                            if (feedhandler_number not in feedhandlers):
                                feedhandlers[feedhandler_number] = {}
                            feedhandlers[feedhandler_number]["title"] = value
                            # Print warning for Google Reader
                            if (value.lower() == 'google'):
                                html_output.append("<p><span class='warning'>Warning:</span> [" + product + "] Google Reader " +
                                    "has been dismissed, see bug 882093 (<span class='code'>" + key + "</span>)</p>")
                        if key.endswith('.uri'):
                            feedhandler_number = key[-5:-4]
                            if (feedhandler_number not in feedhandlers):
                                feedhandlers[feedhandler_number] = {}
                            feedhandlers[feedhandler_number]["uri"] = value

                    # Handler version. Example:
                    # gecko.handlerService.defaultHandlersVersion=4
                    if key.startswith('gecko.handlerService.defaultHandlersVersion'):
                        lineok = True
                        handlerversion = values["gecko.handlerService.defaultHandlersVersion"]

                    # Service handlers. Example:
                    # gecko.handlerService.schemes.webcal.0.name=30 Boxes
                    # gecko.handlerService.schemes.webcal.0.uriTemplate=https://30boxes.com/external/widget?refer=ff&url=%s
                    if key.startswith('gecko.handlerService.schemes.'):
                        lineok = True
                        splittedkey = key.split('.')
                        ch_type = splittedkey[3]
                        ch_number = splittedkey[4]
                        ch_param = splittedkey[5]
                        if (ch_type not in contenthandlers):
                            contenthandlers[ch_type] = {}
                        if (ch_number not in contenthandlers[ch_type]):
                            contenthandlers[ch_type][ch_number] = {}
                        if (ch_param == "name"):
                            contenthandlers[ch_type][ch_number]["name"] = value
                        if (ch_param == "uriTemplate"):
                            contenthandlers[ch_type][ch_number]["uri"] = value

                    # Ignore some keys for mail and seamonkey
                    if (product == "suite") or (product == "mail"):
                        ignored_keys = ['mail.addr_book.mapit_url.format', 'mailnews.messageid_browser.url', 'mailnews.localizedRe',
                                        'browser.translation.service', 'browser.search.defaulturl', 'browser.throbber.url',
                                        'startup.homepage_override_url', 'browser.startup.homepage', 'browser.translation.serviceDomain',
                                        'browser.validate.html.service', 'app.update.url.details']
                        if key in ignored_keys:
                            lineok = True

                    # Unrecognized line, print warning
                    if (not lineok):
                        html_output.append("<p><span class='warning'>Warning:</span> [" + product +
                                           "] unknown key in region.properties</p>")
                        html_output.append("<p><span class='code'>" + key + " = " + value + "</span></p>")

                try:
                    if (product != "suite"):
                        jsondata[locale][product][channel]["p12n"] = {
                            "defaultenginename": defaultenginename,
                            "searchorder": searchorder,
                            "feedhandlers": feedhandlers,
                            "handlerversion": handlerversion,
                            "contenthandlers": contenthandlers
                        }
                    else:
                        # Seamonkey has 2 different region.properties files:
                        # browser: has contenthandlers
                        # common: has search.order
                        # When analyzing common in ony update search.order and default
                        if ("/common/region.properties" in source):
                            jsondata[locale][product][channel]["p12n"]["defaultenginename"] = defaultenginename
                            jsondata[locale][product][channel]["p12n"]["searchorder"] =  searchorder
                        else:
                            jsondata[locale][product][channel]["p12n"] = {
                                "defaultenginename": defaultenginename,
                                "searchorder": searchorder,
                                "feedhandlers": feedhandlers,
                                "handlerversion": handlerversion,
                                "contenthandlers": contenthandlers
                            }
                except Exception as e:
                    html_output.append("<p><span class='error'>Error:</span> problem saving data into json from " +
                        source + " (" + locale + ", " + product + ", " + channel + ")</p>")

            else:
                html_output.append("<p><span class='warning'>Warning:</span> file does not exist " + source
                        + " (" + locale + ", " + product + ", " + channel + ")</p>")
    except Exception as e:
        html_output.append("<p>[" + product + "] No searchplugins available for this locale</p>")




def diff(a, b):
    b = set(b)
    return [aa for aa in a if aa not in b]




def check_p12nmetro(locale, channel, jsondata, html_output):
    # Compare Metro and Desktop list of searchplugin
    try:
        metro_searchplugins = []
        desktop_searchplugins = []
        if (("metro" in jsondata[locale]) and (channel in jsondata[locale]["metro"])):
            for sp in jsondata[locale]["metro"][channel]:
                if (sp != "p12n"):
                    element = jsondata[locale]["metro"][channel][sp]
                    if (element["file"] == "google.xml"):
                        html_output.append("<p><span class='metro'>Metro:</span> use googlemetrofx.xml instead of google.xml</p>")
                    if (element["file"] == "bing.xml"):
                        html_output.append("<p><span class='metro'>Metro:</span> use bingmetrofx.xml instead of bing.xml</p>")
                    if ("yahoo" in element["file"]) and (not "metrofx" in element["file"]) and (locale not in ['ja', 'ja-JP-mac']):
                        # Ignore Yahoo warning for Japanese
                        html_output.append("<p><span class='metro'>Metro:</span> (" + element["file"] + ") use metrofx version of Yahoo for your locale (bug 967388)</p>")

                    # Strip .xml from the filename
                    searchplugin_name = element["file"][:-4]
                    # If it's a Metro version, strip "metrofx" from the name
                    if ("metrofx" in searchplugin_name):
                        searchplugin_name = searchplugin_name[:-7]
                    metro_searchplugins.append(searchplugin_name)
            metro_searchplugins.sort()

            for sp in jsondata[locale]["browser"][channel]:
                if (sp != "p12n"):
                    element = jsondata[locale]["browser"][channel][sp]
                    # Strip .xml from the filename
                    searchplugin_name = element["file"][:-4]
                    desktop_searchplugins.append(searchplugin_name)
            desktop_searchplugins.sort()

            differences = diff(metro_searchplugins, desktop_searchplugins)
            if differences:
                html_output.append("<p><span class='metro'>Metro:</span> there are differences between the searchplugins " +
                    "used in Metro and Desktop</p>")
                html_output.append("<p>Metro searchplugins: ")
                for element in metro_searchplugins:
                    html_output.append(element + " ")
                html_output.append("</p>")

                html_output.append("<p>Desktop searchplugins: ")
                for element in desktop_searchplugins:
                    html_output.append(element + " ")
                html_output.append("</p>")
    except Exception as e:
        print "(check_p12metro) Error analyzing " + locale + " " + channel

    # Check Metro status for region.properties
    try:
        if (("metro" in jsondata[locale]) and (channel in jsondata[locale]["metro"])):
            if ("p12n" in jsondata[locale]["metro"][channel]):
                # I have p12n for Metro
                default_metro = jsondata[locale]["metro"][channel]["p12n"]["defaultenginename"]
                default_desktop = jsondata[locale]["browser"][channel]["p12n"]["defaultenginename"]
                if (default_desktop != default_metro):
                    html_output.append("<p><span class='metro'>Metro:</span> default engine on Desktop (" + default_desktop +
                        ") is different from default engine on Metro (" + default_metro + ")</p>")

                order_metro = jsondata[locale]["metro"][channel]["p12n"]["searchorder"]
                order_desktop = jsondata[locale]["browser"][channel]["p12n"]["searchorder"]
                if (default_desktop != default_metro):
                    html_output.append("<p><span class='metro'>Metro:</span> search engine order on Metro is different from Desktop.</p>")
                    html_output.append("<p>Desktop:</p>")
                    html_output.append("  <ul>")
                    for number in sorted(order_desktop.iterkeys()):
                        html_output.append("    <li>" + number + ":" + order_desktop[number] + "</li>")
                    html_output.append("  </ul>")
                    html_output.append("<p>Metro:</p>")
                    html_output.append("  <ul>")
                    for number in sorted(order_metro.iterkeys()):
                        html_output.append("    <li>" + number + ":" + order_metro[number] + "</li>")
                    html_output.append("  </ul>")
    except Exception as e:
        print "(check_p12metro) Error analyzing " + locale + " " + channel




def extract_splist_enUS (pathsource, splist_enUS):
    # Create a list of en-US searchplugins in pathsource, store this data in
    # splist_enUS
    try:
        for singlefile in glob.glob(pathsource+"*.xml"):
            filename = os.path.basename(singlefile)
            filename_noext = os.path.splitext(filename)[0]
            splist_enUS.append(filename_noext)

    except Exception as e:
        print " Error: problem reading list of en-US searchplugins from " + pathsource




def extract_p12n_channel(clproduct, pathsource, pathl10n, localeslist, channel, jsondata, clp12n, images_list, html_output):
    try:
        # Analyze en-US searchplugins
        html_output.append("<h2>Repository: <a id='" + channel + "' href='#" + channel + "'>" + channel + "</a></h2>")
        html_output.append("<h3>Locale: <a id='en-US-" + channel + "' href='#en-US-" + channel + "'>en-US</a> (" + channel + ")</h3>")
        path = pathsource + "COMMUN/"

        # Create a list of en-US searchplugins for each channel. If list.txt
        # for a locale contains a searchplugin with the same name of the en-US
        # one (e.g. "google"), this will have precedence. Therefore a file with
        # this name should not exist in the locale folder
        if (clproduct=="all") or (clproduct=="browser"):
            # Get a list of all .xml files in the en-US searchplugins folder (both Metro and Desktop)
            splistenUS_browser = []
            extract_splist_enUS(path + "browser/locales/en-US/en-US/searchplugins/", splistenUS_browser)

            extract_sp_product(path + "browser/locales/en-US/en-US/searchplugins/", "browser", "en-US", channel, jsondata, splistenUS_browser, images_list, html_output)
            extract_sp_product(path + "browser/locales/en-US/en-US/searchplugins/", "metro", "en-US", channel, jsondata, splistenUS_browser, images_list, html_output)
            if clp12n:
                extract_p12n_product(path + "browser/locales/en-US/en-US/chrome/browser-region/region.properties", "browser", "en-US", channel, jsondata, html_output)
                extract_p12n_product(path + "browser/metro/locales/en-US/en-US/chrome/region.properties", "metro", "en-US", channel, jsondata, html_output)

        if (clproduct=="all") or (clproduct=="mobile"):
            splistenUS_mobile = []
            extract_splist_enUS(path + "mobile/locales/en-US/en-US/searchplugins/", splistenUS_mobile)
            extract_sp_product(path + "mobile/locales/en-US/en-US/searchplugins/", "mobile", "en-US", channel, jsondata, splistenUS_mobile, images_list, html_output)
            if clp12n:
                extract_p12n_product(path + "mobile/locales/en-US/en-US/chrome/region.properties", "mobile", "en-US", channel, jsondata, html_output)

        if (clproduct=="all") or (clproduct=="mail"):
            splistenUS_mail = []
            extract_splist_enUS(path + "mail/locales/en-US/en-US/searchplugins/", splistenUS_mail)
            extract_sp_product(path + "mail/locales/en-US/en-US/searchplugins/", "mail", "en-US", channel, jsondata, splistenUS_mail, images_list, html_output)
            if clp12n:
                extract_p12n_product(path + "mail/locales/en-US/en-US/chrome/messenger-region/region.properties", "mail", "en-US", channel, jsondata, html_output)

        if (clproduct=="all") or (clproduct=="suite"):
            splistenUS_suite = []
            extract_splist_enUS(path + "suite/locales/en-US/en-US/searchplugins/", splistenUS_suite)
            extract_sp_product(path + "suite/locales/en-US/en-US/searchplugins/", "suite", "en-US", channel, jsondata, splistenUS_suite, images_list, html_output)
            if clp12n:
                extract_p12n_product(path + "suite/locales/en-US/en-US/chrome/browser/region.properties", "suite", "en-US", channel, jsondata, html_output)
                extract_p12n_product(path + "suite/locales/en-US/en-US/chrome/common/region.properties", "suite", "en-US", channel, jsondata, html_output)

        locale_list = open(localeslist, "r").read().splitlines()
        for locale in locale_list:
            anchor_id = locale + "-" + channel
            html_output.append("<h3>Locale: <a id='" + anchor_id + "' href='#" + anchor_id + "'>" + locale + "</a> (" + channel + ")</h3>")
            path = pathl10n + locale + "/"
            if (clproduct=="all") or (clproduct=="browser"):
                extract_sp_product(path + "browser/searchplugins/", "browser", locale, channel, jsondata, splistenUS_browser, images_list, html_output)
                extract_sp_product(path + "browser/searchplugins/", "metro", locale, channel, jsondata, splistenUS_browser, images_list, html_output)
                if clp12n:
                    extract_p12n_product(path + "browser/chrome/browser-region/region.properties", "browser", locale, channel, jsondata, html_output)
                    extract_p12n_product(path + "browser/metro/chrome/region.properties", "metro", locale, channel, jsondata, html_output)
                    # Do checks specific for Metro
                    check_p12nmetro(locale, channel, jsondata, html_output)
            if (clproduct=="all") or (clproduct=="mobile"):
                extract_sp_product(path + "mobile/searchplugins/", "mobile", locale, channel, jsondata, splistenUS_mobile, images_list, html_output)
                if clp12n:
                    extract_p12n_product(path + "mobile/chrome/region.properties", "mobile", locale, channel, jsondata, html_output)
            if (clproduct=="all") or (clproduct=="mail"):
                extract_sp_product(path + "mail/searchplugins/", "mail", locale, channel, jsondata, splistenUS_mail, images_list, html_output)
                if clp12n:
                    extract_p12n_product(path + "mail/chrome/messenger-region/region.properties", "mail", locale, channel, jsondata, html_output)
            if (clproduct=="all") or (clproduct=="suite"):
                extract_sp_product(path + "suite/searchplugins/", "suite", locale, channel, jsondata, splistenUS_suite, images_list, html_output)
                if clp12n:
                    extract_p12n_product(path + "suite/chrome/browser/region.properties", "suite", locale, channel, jsondata, html_output)
                    extract_p12n_product(path + "suite/chrome/common/region.properties", "suite", locale, channel, jsondata, html_output)
    except Exception as e:
        print "Error reading list of locales from " + localeslist
        print e




def main():

    # Parse command line options
    clparser = OptionParser()
    clparser.add_option("-p", "--product", help="Choose a specific product", choices=["browser", "mobile", "mail", "suite", "all"], default="all")
    clparser.add_option("-b", "--branch", help="Choose a specific branch", choices=["release", "beta", "aurora", "trunk", "all"], default="all")
    clparser.add_option("-n", "--noproductization", help="Disable productization checks", action="store_true")

    (options, args) = clparser.parse_args()
    clproduct = options.product
    clbranch = options.branch
    clp12n = False if options.noproductization else True

    # Read configuration file
    parser = SafeConfigParser()
    parser.read("web/inc/config.ini")
    local_hg = parser.get("config", "local_hg")
    install_folder = parser.get("config", "install")

    # Set Transvision's folders and locale files
    release_l10n = local_hg + "/RELEASE_L10N/"
    beta_l10n = local_hg + "/BETA_L10N/"
    aurora_l10n = local_hg + "/AURORA_L10N/"
    trunk_l10n = local_hg + "/TRUNK_L10N/"

    release_source = local_hg + "/RELEASE_EN-US/"
    beta_source = local_hg + "/BETA_EN-US/"
    aurora_source = local_hg + "/AURORA_EN-US/"
    trunk_source = local_hg + "/TRUNK_EN-US/"

    trunk_locales = install_folder + "/central.txt"
    aurora_locales = install_folder + "/aurora.txt"
    beta_locales = install_folder + "/beta.txt"
    release_locales = install_folder + "/release.txt"

    if not os.path.exists("web/p12n"):
        os.makedirs("web/p12n")

    jsonfilename = "web/p12n/searchplugins.json"
    jsondata = {}

    htmlfilename = "web/p12n/index.html"
    html_output = ['''<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset=utf-8>
            <title>p12n status</title>
            <style type="text/css">
                body {background-color: #FFF; font-family: Arial, Verdana; font-size: 14px; padding: 10px;}
                p {margin-top: 2px;}
                span.warning {color: #FFBF00; font-weight: bold;}
                span.error {color: #FF0000; font-weight: bold;}
                span.metro {color: #AE00FF; font-weight: bold;}
                span.code {font-family: monospace; font-size: 12px; background-color: #CCC;}
                h2 {clear: both;}
                div.navigation {width: 100%; clear: both;}
                ul.switcher {float: left;}
                ul.switcher li {float: left; display: block; margin: 0 5px; width: 80px; text-align: center; padding: 10px 5px; background-color: #DCDCDC; text-transform: uppercase; border: 1px solid #000; list-style: none;}
                ul.switcher li a {text-decoration: none;}
            </style>
        </head>

        <body>
            <h1>Productization analysis</h1>
        ''']
    html_output.append("<p>Last update: " + strftime("%Y-%m-%d %H:%M:%S", gmtime()) + "<br/>")
    html_output.append("Analyzing product: " + clproduct + "<br/>")
    html_output.append("Branch: " + clbranch + "</p>")


    images_list = ['''data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAC/0lEQVR
                4XoWSbUiTexjG7x6d0OZW4FD3IigqaFEfJHRMt7WVGLQ9CZpR8pSiHwIZHHGdzbmzovl2tjnb8WjzBe2NCCnMFzycJ578
                kktwUZRDkCKhVDgouJdEn9n+/Sssy+Rc8Ptwc3FxX/z/NzQBwIBMxpsZHBx51d9fheddNeVwwHRLywV/b+/Yzfz8eMAix
                DicRVEPuBsbun1crkfR1FT5q/BTHI4EApQwPr53P0Inc8vLh27I5fHwyGKx+Lu60OvubuTF+Pr6WK/V+kOTKacTJs3mCn
                9rKzvndKL3PT1o0eOJ+qzWK8R/U1Pu8OLio/lgEDbX1mBvKMSJSUz05DU0fGkyabfD+srK+b0cTg8KhzkxsbHwMRRCyws
                LE3NerwuwwC2VcseNRtpnsyGmuRn9g/E6HCxjNFZjKp+YTOxkTQ2awb6/sTH6rL6e6UxP58F23dJo+KN1dfT9+npEWyzo
                MYax2SK0wcCOURSa0OvRc7M56jUYmNsajWArtwe26ZpYzE0rKXm4trpayBEKgWBZWF9aAi72eCkpKAowMTc8TOrn5z/Ab
                hpQqfjXjh9/UScUotYjR9BfhYXoXnEx+levfzmgVAp+DhDbh/GGBoCEhNJ3s7MHgsvL8Mbng7fT0xAJhyGyuZklyM4+ve
                udjJpM4CkpOX9RImGrANBn9ASBfo+JQUbM1YMH0ShFRUaqq3feyZDBAF0kWfGbWMwW4+AZTGVsbNSlVjN/HztGV3E46A8
                A1B4Xh9qzs9nbOt33O3lQWwsdJEmViURsKQ5SmDKCiLaqVEy3TCbokcv5nWo1fRm3qMWeFXNDJIrcJcmvTdpJsqwGh09i
                Q405jTe3KJWMSyr99s9tSUlcl0pFX8JNnADIjvkzOZm9c+rUWXBrtYpzaWmBMmxo8WazQsFcz83d8dqevDy+R6mkrbiJA
                QB1pKYGbmq1R7+YHTqdojwzc/VKfj7TJpHwYBc5ExO5bQUFtCMjI9i/Fd7CXVR0yJ6TI4D/kSMnh3/9xInDW/MnJPlM3r
                rfgeYAAAAASUVORK5CYII=''']

    html_output.append('''
    <div class="navigation">
        <ul class="switcher">
            <li><a href="#trunk">Trunk</a></li>
            <li><a href="#aurora">Aurora</a></li>
            <li><a href="#beta">Beta</a></li>
            <li><a href="#release">Release</a></li>
        </ul>
    </div>''')

    if (clbranch=="all") or (clbranch=="trunk"):
        extract_p12n_channel(clproduct, trunk_source, trunk_l10n, trunk_locales, "trunk", jsondata, clp12n, images_list, html_output)
    if (clbranch=="all") or (clbranch=="aurora"):
        extract_p12n_channel(clproduct, aurora_source, aurora_l10n, aurora_locales, "aurora", jsondata, clp12n, images_list, html_output)
    if (clbranch=="all") or (clbranch=="beta"):
        extract_p12n_channel(clproduct, beta_source, beta_l10n, beta_locales, "beta", jsondata, clp12n, images_list, html_output)
    if (clbranch=="all") or (clbranch=="release"):
        extract_p12n_channel(clproduct, release_source, release_l10n, release_locales, "release", jsondata, clp12n, images_list, html_output)

    # Create images json structure and save it to file
    image_data = {}
    for index, value in enumerate(images_list):
        image_data[index] = value
    jsondata["images"] = image_data
    jsondata["creation_date"] = strftime("%Y-%m-%d %H:%M:%S", gmtime())


    # Write back updated json data
    jsonfile = open(jsonfilename, "w")
    #jsonfile.write(json.dumps(jsondata, indent=4, sort_keys=True))
    jsonfile.write(json.dumps(jsondata))
    jsonfile.close()

    # Finalize and write html
    html_output.append("</body>")
    html_code = "\n".join(html_output)
    html_file = open(htmlfilename, "w")
    html_file.write(html_code)
    html_file.close()




if __name__ == "__main__":
    main()
