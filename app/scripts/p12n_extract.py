#! /usr/bin/env python

# This script is designed to work in Transvision
# https://github.com/mozfr/transvision

import collections
import glob
import json
import os
import re
import StringIO
from ConfigParser import SafeConfigParser
from optparse import OptionParser
from time import strftime, localtime
from xml.dom import minidom


def extract_sp_product(searchpath, product, locale, channel, jsondata, splist_enUS, images_list, html_output):
    try:
        sp_list = []
        error_details = " (" + locale + ", " + product + ", " + channel + ")"
        if locale != "en-US":
            # Read the list of searchplugins from list.txt
            file_list = os.path.join(searchpath, "list.txt")
            if os.path.isfile(file_list):
                sp_list = open(file_list, "r").read().splitlines()
                # Remove empty lines
                sp_list = filter(bool, sp_list)
                # Check for duplicates
                if len(sp_list) != len(set(sp_list)):
                    # set(sp_list) removes duplicates. If I'm here, there are
                    # duplicated elements in list.txt, which is an error
                    duplicated_items = [x for x, y in collections.Counter(sp_list).items() if y > 1]
                    duplicated_items_str =  ", ".join(duplicated_items)
                    html_output.append("<p><span class='error'>Error:</span> there are duplicated items (" +
                                        duplicated_items_str + ") in the list" + error_details + ".</p>")
        else:
            # en-US is different: I must analyze all xml files in the folder,
            # since some searchplugins are not used in en-US but from other
            # locales
            sp_list = splist_enUS

        if locale != "en-US" and len(sp_list)>0:
            # Get a list of all files inside searchpath
            for singlefile in glob.glob(os.path.join(searchpath, "*")):
                # Remove extension
                filename = os.path.basename(singlefile)
                filename_noext = os.path.splitext(filename)[0]
                if filename_noext in splist_enUS:
                    # File exists but has the same name of an en-US searchplugin.
                    html_output.append("<p><span class='error'>Error:</span> file " + filename +
                        " should not exist in the locale folder, same name of en-US searchplugin" +
                        error_details + ".</p>")
                else:
                    if filename_noext not in sp_list and filename != "list.txt":
                        # Extra file or unused searchplugin, should be removed
                        html_output.append("<p><span class='error'>Error:</span> file " + filename +
                        " not in list.txt" + error_details + ".</p>")

        # For each searchplugin check if the file exists (localized version) or
        # not (using en-US version to extract data)
        for sp in sp_list:
            sp_file = os.path.join(searchpath, sp + ".xml")
            existingfile = os.path.isfile(sp_file)

            if locale != "en-US" and sp in splist_enUS and existingfile:
                # There's a problem: file exists but has the same name of an
                # en-US searchplugin. This file will never be picked at build
                # time, so let's analyze en-US and use it for json, acting
                # like the file doesn't exist, and print an error
                existingfile = False

            if existingfile:
                try:
                    searchplugin_info = "(" + locale + ", " + product + ", " + channel + ", " + sp + ".xml)"

                    try:
                        xmldoc = minidom.parse(sp_file)
                    except Exception as e:
                        # Some searchplugins have preprocessing instructions
                        # (#define, #if), so they fail validation. In order to
                        # extract the information I need, I read the file,
                        # remove lines starting with # and parse that content
                        # instead of the original XML file
                        preprocessor = False
                        newspcontent = ""
                        for line in open(sp_file, "r").readlines():
                            if re.match("#", line):
                                # Line starts with a #
                                preprocessor = True
                            else:
                                # Line is OK, adding it to newspcontent
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
                        if len(node) == 0:
                            node = xmldoc.getElementsByTagName("os:ShortName")
                        name = node[0].childNodes[0].nodeValue
                    except Exception as e:
                        html_output.append("<p><span class='error'>Error:</span> problem extracting name from " +
                            "searchplugin " + searchplugin_info + "</p>")
                        name = "not available"

                    try:
                        node = xmldoc.getElementsByTagName("Description")
                        if len(node) == 0:
                            node = xmldoc.getElementsByTagName("os:Description")
                        description = node[0].childNodes[0].nodeValue
                    except Exception as e:
                        # We don't really use description anywhere, and it's usually removed on mobile,
                        # so I don't print errors
                        description = "not available"

                    try:
                        # I can have more than one url element, for example one
                        # for searches and one for suggestions
                        secure = 0

                        nodes = xmldoc.getElementsByTagName("Url")
                        if len(nodes) == 0:
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
                        if len(nodes) == 0:
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
                            if product == "mobile":
                                if "%" in image:
                                    html_output.append("<p><span class='warning'>Warning:</span> searchplugin's image " +
                                        "on mobile can't contain % character " + searchplugin_info + "</p>")

                    except Exception as e:
                        html_output.append("<p><span class='error'>Error:</span> problem extracting image from searchplugin " +
                            searchplugin_info + "</p>")
                        images.append(images_list[0])

                    # Check if node for locale already exists
                    if locale not in jsondata:
                        jsondata[locale] = {}
                    # Check if node for locale->product already exists
                    if product not in jsondata[locale]:
                        jsondata[locale][product] = {}
                    # Check if node for locale->product->channel already exists
                    if channel not in jsondata[locale][product]:
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
                    if locale not in jsondata:
                        jsondata[locale] = {}
                    # Check if node for locale->product already exists
                    if product not in jsondata[locale]:
                        jsondata[locale][product] = {}
                    # Check if node for locale->product->channel already exists
                    if channel not in jsondata[locale][product]:
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
    # Extract p12n information from region.properties.
    try:
        available_searchplugins = []
        if channel in jsondata[locale][product]:
            # I need to proceed only if I have searchplugins for this branch+product+locale
            for element in jsondata[locale][product][channel].values():
                # Store the "name" attribute of each searchplugin, used to validate search.order
                if "name" in element:
                    available_searchplugins.append(element["name"])

            existingfile = os.path.isfile(source)
            if existingfile:
                try:
                    # Read region.properties, ignore comments and empty lines
                    values = {}
                    for line in open(source):
                        li = line.strip()
                        if not li.startswith("#") and li != "":
                            try:
                                # Split considering only the first =
                                key, value = li.split("=", 1)
                                # Remove whitespaces, some locales use key = value instead of key=value
                                values[key.strip()] = value.strip()
                            except:
                                html_output.append("<p><span class='error'>Error:</span> problem parsing " + source +
                                    " (" + locale + ", " + product + ", " + channel + ")</p>")
                except:
                    html_output.append("<p><span class='error'>Error:</span> problem reading " + source + " (" +
                        locale + ", " + product + ", " + channel + ")</p>")

                # Check if node for locale already exists
                if locale not in jsondata:
                    jsondata[locale] = {}
                # Check if node for locale->product already exists
                if product not in jsondata[locale]:
                    jsondata[locale][product] = {}
                # Check if node for locale->product->channel already exists
                if channel not in jsondata[locale][product]:
                    jsondata[locale][product][channel] = {}

                defaultenginename = "-"
                searchorder = {}
                feedhandlers = {}
                handlerversion = "-"
                contenthandlers = {}

                for key, value in values.iteritems():
                    lineok = False

                    # Default search engine name. Example:
                    # browser.search.defaultenginename=Google
                    if key.startswith("browser.search.defaultenginename"):
                        lineok = True
                        defaultenginename = values["browser.search.defaultenginename"]
                        if unicode(defaultenginename, "utf-8") not in available_searchplugins:
                            html_output.append("<p><span class='error'>Error:</span> [" + product + "] " +
                                defaultenginename + " is set as default but not available in searchplugins (check if " +
                                "the name is spelled correctly)</p>")

                    # Search engines order. Example:
                    # browser.search.order.1=Google
                    if key.startswith("browser.search.order."):
                        lineok = True
                        searchorder[key[-1:]] = value
                        if unicode(value, "utf-8") not in available_searchplugins:
                            if value != "":
                                html_output.append("<p><span class='error'>Error:</span> [" + product + "] <span class='code'>" +
                                    value + "</span> is defined in searchorder but not available in searchplugins " +
                                    "(check if the name is spelled correctly)</p>")
                            else:
                                html_output.append("<p><span class='error'>Error:</span> [" + product + "] <span class='code'>" +
                                    key + "</span> is empty")

                    # Feed handlers. Example:
                    # browser.contentHandlers.types.0.title=My Yahoo!
                    # browser.contentHandlers.types.0.uri=http://add.my.yahoo.com/rss?url=%s
                    if key.startswith("browser.contentHandlers.types."):
                        lineok = True
                        if key.endswith(".title"):
                            feedhandler_number = key[-7:-6]
                            if feedhandler_number not in feedhandlers:
                                feedhandlers[feedhandler_number] = {}
                            feedhandlers[feedhandler_number]["title"] = value
                            # Print warning for Google Reader
                            if "google" in value.lower():
                                html_output.append("<p><span class='warning'>Warning:</span> [" + product + "] Google Reader " +
                                    "has been dismissed, see bug 882093 (<span class='code'>" + key + "</span>)</p>")
                        if key.endswith(".uri"):
                            feedhandler_number = key[-5:-4]
                            if feedhandler_number not in feedhandlers:
                                feedhandlers[feedhandler_number] = {}
                            feedhandlers[feedhandler_number]["uri"] = value

                    # Handler version. Example:
                    # gecko.handlerService.defaultHandlersVersion=4
                    if key.startswith("gecko.handlerService.defaultHandlersVersion"):
                        lineok = True
                        handlerversion = values["gecko.handlerService.defaultHandlersVersion"]

                    # Service handlers. Example:
                    # gecko.handlerService.schemes.webcal.0.name=30 Boxes
                    # gecko.handlerService.schemes.webcal.0.uriTemplate=https://30boxes.com/external/widget?refer=ff&url=%s
                    if key.startswith("gecko.handlerService.schemes."):
                        lineok = True
                        splittedkey = key.split(".")
                        ch_type = splittedkey[3]
                        ch_number = splittedkey[4]
                        ch_param = splittedkey[5]
                        if ch_type not in contenthandlers:
                            contenthandlers[ch_type] = {}
                        if ch_number not in contenthandlers[ch_type]:
                            contenthandlers[ch_type][ch_number] = {}
                        if ch_param == "name":
                            contenthandlers[ch_type][ch_number]["name"] = value
                        if ch_param == "uriTemplate":
                            contenthandlers[ch_type][ch_number]["uri"] = value

                    # Ignore some keys for mail and seamonkey
                    if product == "suite" or product == "mail":
                        ignored_keys = ["mail.addr_book.mapit_url.format", "mailnews.messageid_browser.url", "mailnews.localizedRe",
                                        "browser.translation.service", "browser.search.defaulturl", "browser.throbber.url",
                                        "startup.homepage_override_url", "browser.startup.homepage", "browser.translation.serviceDomain",
                                        "browser.validate.html.service", "app.update.url.details"]
                        if key in ignored_keys:
                            lineok = True

                    # Unrecognized line, print warning (not for en-US)
                    if not lineok and locale != "en-US":
                        html_output.append("<p><span class='warning'>Warning:</span> [" + product +
                                           "] unknown key in region.properties</p>")
                        html_output.append("<p><span class='code'>" + key + " = " + value + "</span></p>")

                try:
                    if product != "suite":
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
                        if "/common/region.properties" in source:
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
                except:
                    html_output.append("<p><span class='error'>Error:</span> problem saving data into json from " +
                        source + " (" + locale + ", " + product + ", " + channel + ")</p>")

            else:
                html_output.append("<p><span class='warning'>Warning:</span> file does not exist " + source
                        + " (" + locale + ", " + product + ", " + channel + ")</p>")
    except:
        html_output.append("<p>[" + product + "] No searchplugins available for this locale</p>")


def extract_splist_enUS (pathsource, splist_enUS):
    # Store in splist_enUS a list of en-US searchplugins (*.xml) in pathsource.
    try:
        for singlefile in glob.glob(os.path.join(pathsource, "*.xml")):
            filename = os.path.basename(singlefile)
            filename_noext = os.path.splitext(filename)[0]
            splist_enUS.append(filename_noext)
    except:
        print " Error: problem reading list of en-US searchplugins from " + pathsource


def extract_p12n_channel(clproduct, pathsource, pathl10n, localeslist, channel, jsondata, clp12n, images_list, html_output):
    try:
        # Analyze en-US searchplugins
        html_output.append("<h2>Repository: <a id='" + channel + "' href='#" + channel + "'>" + channel + "</a></h2>")
        html_output.append("<h3>Locale: <a id='en-US-" + channel + "' href='#en-US-" + channel + "'>en-US</a> (" + channel + ")</h3>")

        searchpathbase = os.path.join(pathsource, "COMMUN")
        searchpathenUS = {
            "browser_sp"   : os.path.join(searchpathbase, "browser", "locales", "en-US", "en-US", "searchplugins") + os.sep,
            "browser_p12n" : os.path.join(searchpathbase, "browser", "locales", "en-US", "en-US", "chrome", "browser-region", "region.properties"),
            "mobile_sp"    : os.path.join(searchpathbase, "mobile", "locales", "en-US", "en-US", "searchplugins") + os.sep,
            "mobile_p12n"  : os.path.join(searchpathbase, "mobile", "locales", "en-US", "en-US", "chrome", "region.properties"),
            "mail_sp"      : os.path.join(searchpathbase, "mail", "locales", "en-US", "en-US", "searchplugins") + os.sep,
            "mail_p12n"    : os.path.join(searchpathbase, "mail", "locales", "en-US", "en-US", "chrome", "messenger-region", "region.properties"),
            "suite_sp"     : os.path.join(searchpathbase, "suite", "locales", "en-US", "en-US", "searchplugins") + os.sep,
            "suite_p12n_a" : os.path.join(searchpathbase, "suite", "locales", "en-US", "en-US", "chrome", "browser", "region.properties"),
            "suite_p12n_b" : os.path.join(searchpathbase, "suite", "locales", "en-US", "en-US", "chrome", "common", "region.properties"),
        }

        # Create a list of en-US searchplugins for each channel.
        if clproduct=="all" or clproduct=="browser":
            # Get a list of all .xml files inside the en-US searchplugins folder
            splistenUS_browser = []
            extract_splist_enUS(searchpathenUS["browser_sp"], splistenUS_browser)
            extract_sp_product(searchpathenUS["browser_sp"], "browser", "en-US", channel, jsondata, splistenUS_browser, images_list, html_output)
            if clp12n:
                extract_p12n_product(searchpathenUS["browser_p12n"], "browser", "en-US", channel, jsondata, html_output)

        if clproduct=="all" or clproduct=="mobile":
            splistenUS_mobile = []
            extract_splist_enUS(searchpathenUS["mobile_sp"], splistenUS_mobile)
            extract_sp_product(searchpathenUS["mobile_sp"], "mobile", "en-US", channel, jsondata, splistenUS_mobile, images_list, html_output)
            if clp12n:
                extract_p12n_product(searchpathenUS["mobile_p12n"], "mobile", "en-US", channel, jsondata, html_output)

        if clproduct=="all" or clproduct=="mail":
            splistenUS_mail = []
            extract_splist_enUS(searchpathenUS["mail_sp"], splistenUS_mail)
            extract_sp_product(searchpathenUS["mail_sp"], "mail", "en-US", channel, jsondata, splistenUS_mail, images_list, html_output)
            if clp12n:
                extract_p12n_product(searchpathenUS["mail_p12n"], "mail", "en-US", channel, jsondata, html_output)

        if clproduct=="all" or clproduct=="suite":
            splistenUS_suite = []
            extract_splist_enUS(searchpathenUS["suite_sp"], splistenUS_suite)
            extract_sp_product(searchpathenUS["suite_sp"], "suite", "en-US", channel, jsondata, splistenUS_suite, images_list, html_output)
            if clp12n:
                extract_p12n_product(searchpathenUS["suite_p12n_a"], "suite", "en-US", channel, jsondata, html_output)
                extract_p12n_product(searchpathenUS["suite_p12n_b"], "suite", "en-US", channel, jsondata, html_output)

        locale_list = open(localeslist, "r").read().splitlines()
        for locale in locale_list:
            anchor_id = locale + "-" + channel
            html_output.append("<h3>Locale: <a id='" + anchor_id + "' href='#" + anchor_id + "'>" + locale + "</a> (" + channel + ")</h3>")

            searchpathl10nbase = os.path.join(pathl10n, locale)
            searchpathl10n = {
                "browser_sp"   : os.path.join(searchpathl10nbase, "browser", "searchplugins") + os.sep,
                "browser_p12n" : os.path.join(searchpathl10nbase, "browser", "chrome", "browser-region", "region.properties"),
                "mobile_sp"    : os.path.join(searchpathl10nbase, "mobile", "searchplugins") + os.sep,
                "mobile_p12n"  : os.path.join(searchpathl10nbase, "mobile", "chrome", "region.properties"),
                "mail_sp"      : os.path.join(searchpathl10nbase, "mail", "searchplugins") + os.sep,
                "mail_p12n"    : os.path.join(searchpathl10nbase, "mail", "chrome", "messenger-region", "region.properties"),
                "suite_sp"     : os.path.join(searchpathl10nbase, "suite", "searchplugins") + os.sep,
                "suite_p12n_a" : os.path.join(searchpathl10nbase, "suite", "chrome", "browser", "region.properties"),
                "suite_p12n_b" : os.path.join(searchpathl10nbase, "suite", "chrome", "common", "region.properties"),
            }

            if clproduct=="all" or clproduct=="browser":
                extract_sp_product(searchpathl10n["browser_sp"], "browser", locale, channel, jsondata, splistenUS_browser, images_list, html_output)
                if clp12n:
                    extract_p12n_product(searchpathl10n["browser_p12n"], "browser", locale, channel, jsondata, html_output)
            if clproduct=="all" or clproduct=="mobile":
                extract_sp_product(searchpathl10n["mobile_sp"], "mobile", locale, channel, jsondata, splistenUS_mobile, images_list, html_output)
                if clp12n:
                    extract_p12n_product(searchpathl10n["mobile_p12n"], "mobile", locale, channel, jsondata, html_output)
            if clproduct=="all" or clproduct=="mail":
                extract_sp_product(searchpathl10n["mail_sp"], "mail", locale, channel, jsondata, splistenUS_mail, images_list, html_output)
                if clp12n:
                    extract_p12n_product(searchpathl10n["mail_p12n"], "mail", locale, channel, jsondata, html_output)
            if clproduct=="all" or clproduct=="suite":
                extract_sp_product(searchpathl10n["suite_sp"], "suite", locale, channel, jsondata, splistenUS_suite, images_list, html_output)
                if clp12n:
                    extract_p12n_product(searchpathl10n["suite_p12n_a"], "suite", locale, channel, jsondata, html_output)
                    extract_p12n_product(searchpathl10n["suite_p12n_b"], "suite", locale, channel, jsondata, html_output)
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

    # Get absolute path of ../config from current script location (not current folder)
    config_folder = os.path.abspath(os.path.join(os.path.dirname( __file__ ), os.pardir, "config"))
    parser.read(os.path.join(config_folder, "config.ini"))

    local_install = parser.get("config", "install")
    local_hg      = parser.get("config", "local_hg")
    config_files  = parser.get("config", "config")

    # Set Transvision's folders and locale files
    release_l10n = os.path.join(local_hg, "RELEASE_L10N") + os.sep
    beta_l10n    = os.path.join(local_hg, "BETA_L10N") + os.sep
    aurora_l10n  = os.path.join(local_hg, "AURORA_L10N") + os.sep
    trunk_l10n   = os.path.join(local_hg, "TRUNK_L10N") + os.sep

    release_source = os.path.join(local_hg, "RELEASE_EN-US") + os.sep
    beta_source    = os.path.join(local_hg, "BETA_EN-US") + os.sep
    aurora_source  = os.path.join(local_hg, "AURORA_EN-US") + os.sep
    trunk_source   = os.path.join(local_hg, "TRUNK_EN-US") + os.sep

    trunk_locales   = os.path.join(config_files, "central.txt")
    aurora_locales  = os.path.join(config_files, "aurora.txt")
    beta_locales    = os.path.join(config_files, "beta.txt")
    release_locales = os.path.join(config_files, "release.txt")

    # Create web/p12n if missing
    web_p12n_folder = os.path.join(local_install, "web", "p12n")
    if not os.path.exists(web_p12n_folder):
        os.makedirs(web_p12n_folder)

    jsonfilename = os.path.join(web_p12n_folder, "searchplugins.json")
    jsondata = {}

    htmlfilename = os.path.join(web_p12n_folder, "index.html")
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
    html_output.append("<p>Last update: " + strftime("%Y-%m-%d %H:%M:%S", localtime()) + "<br/>")
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

    if clbranch=="all" or clbranch=="trunk":
        extract_p12n_channel(clproduct, trunk_source, trunk_l10n, trunk_locales, "trunk", jsondata, clp12n, images_list, html_output)
    if clbranch=="all" or clbranch=="aurora":
        extract_p12n_channel(clproduct, aurora_source, aurora_l10n, aurora_locales, "aurora", jsondata, clp12n, images_list, html_output)
    if clbranch=="all" or clbranch=="beta":
        extract_p12n_channel(clproduct, beta_source, beta_l10n, beta_locales, "beta", jsondata, clp12n, images_list, html_output)
    if clbranch=="all" or clbranch=="release":
        extract_p12n_channel(clproduct, release_source, release_l10n, release_locales, "release", jsondata, clp12n, images_list, html_output)

    # Create images json structure and save it to file
    image_data = {}
    for index, value in enumerate(images_list):
        image_data[index] = value
    jsondata["images"] = image_data
    jsondata["creation_date"] = strftime("%Y-%m-%d %H:%M:%S", localtime())

    # Write back updated json data
    jsonfile = open(jsonfilename, "w")
    jsonfile.write(json.dumps(jsondata))
    jsonfile.close()

    # Finalize and write html log
    html_output.append("</body>")
    html_code = "\n".join(html_output)
    html_file = open(htmlfilename, "w")
    html_file.write(html_code)
    html_file.close()


if __name__ == "__main__":
    main()
