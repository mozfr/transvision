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

def extract_sp_product(searchpath, product, locale, channel, json_data,
                       splist_enUS, images_list, json_errors):
    try:
        sp_list = []
        errors = []
        warnings = []

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
                    duplicated_items = [
                        x for x, y in
                            collections.Counter(sp_list).items() if y > 1
                    ]
                    duplicated_items_str =  ", ".join(duplicated_items)
                    errors.append(
                        "there are duplicated items (%s) in the list"
                        % duplicated_items_str
                    )
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
                    # File exists but has the same name of an en-US
                    # searchplugin.
                    errors.append(
                        "file %s should not exist in the locale folder, "
                        "same name of en-US searchplugin" % filename
                    )
                else:
                    if filename_noext not in sp_list and filename != "list.txt":
                        # Extra file or unused searchplugin, should be removed
                        errors.append(
                           "file %s not in list.txt" % filename
                        )

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
                    searchplugin_info = "(%s, %s, %s, %s.xml)" \
                                        % (locale, product, channel, sp)
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
                            warnings.append(
                                "searchplugin contains preprocessor "
                                "instructions (e.g. #define, #if) that have "
                                "been stripped in order to parse the XML %s"
                                 % searchplugin_info
                            )
                            try:
                                xmldoc = minidom.parse(
                                            StringIO.StringIO(newspcontent)
                                         )
                            except Exception as e:
                                errors.append(
                                    "error parsing XML %s"
                                    % searchplugin_info
                                )
                        else:
                            errors.append(
                                    "error parsing XML %s <code>%s</code>"
                                    % (searchplugin_info, str(e))
                            )

                    # Some searchplugins use the form <tag>, others <os:tag>
                    try:
                        node = xmldoc.getElementsByTagName("ShortName")
                        if len(node) == 0:
                            node = xmldoc.getElementsByTagName("os:ShortName")
                        name = node[0].childNodes[0].nodeValue
                    except Exception as e:
                        errors.append(
                            "error extracting name %s"
                            % searchplugin_info
                        )
                        name = "not available"

                    try:
                        node = xmldoc.getElementsByTagName("Description")
                        if len(node) == 0:
                            node = xmldoc.getElementsByTagName("os:Description")
                        description = node[0].childNodes[0].nodeValue
                    except Exception as e:
                        # We don't really use description anywhere, and it's
                        # usually removed on mobile, so I don't print errors
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
                        errors.append(
                            "error extracting URL %s"
                            % searchplugin_info
                        )
                        url = "not available"

                    try:
                        # Since bug 900137, searchplugins can have multiple
                        # images
                        images = []
                        nodes = xmldoc.getElementsByTagName("Image")
                        if len(nodes) == 0:
                            nodes = xmldoc.getElementsByTagName("os:Image")
                        for node in nodes:
                            image = node.childNodes[0].nodeValue
                            if image in images_list:
                                # Image already stored. In the json record store
                                # only the index
                                images.append(images_list.index(image))
                            else:
                                # Store image in images_list, get index and
                                # store in json
                                images_list.append(image)
                                images.append(len(images_list)-1)

                            # On mobile we can't have % characters, see for
                            # example bug 850984. Print a warning in this case
                            if product == "mobile":
                                if "%" in image:
                                    warnings.append(
                                        "searchplugin's image on mobile can't "
                                        "contain % character %s"
                                        % searchplugin_info
                                    )

                    except Exception as e:
                        errors.append(
                            "error extracting image %s"
                            % searchplugin_info
                        )
                        images.append(images_list[0])

                    # No images in the searchplugin
                    if len(images) == 0:
                        errors.append(
                            'no images available %s'
                            % searchplugin_info
                        )
                        # Use default empty image
                        images = [images_list[0]]

                    json_data[locale][product][channel][sp] = {
                        "file": "%s.xml" % sp,
                        "name": name,
                        "description": description,
                        "url": url,
                        "secure": secure,
                        "images": images,
                    }

                except Exception as e:
                    errors.append(
                            "error analyzing searchplugin %s <code>%s</code>"
                            % (searchplugin_info, str(e))
                    )
            else:
                # File does not exists, locale is using the same plugin of en-
                # US, I have to retrieve it from the dictionary
                if sp in json_data["en-US"][product][channel]:
                    searchplugin_enUS = json_data["en-US"][product][channel][sp]

                    json_data[locale][product][channel][sp] = {
                        "file": "%s.xml" % sp,
                        "name": searchplugin_enUS["name"],
                        "description": "(en-US) %s" \
                                       % searchplugin_enUS["description"],
                        "url": searchplugin_enUS["url"],
                        "secure": searchplugin_enUS["secure"],
                        "images": searchplugin_enUS["images"]
                    }
                else:
                    # File does not exist but we don't have the en-US either.
                    # This means that list.txt references a non existing
                    # plugin, which will cause the build to fail
                    errors.append(
                        "file referenced in list.txt but not available "
                        "(%s, %s, %s, %s.xml)"
                        % (locale, product, channel, sp)
                    )

        # Save errors and warnings
        if len(errors)>0:
            json_errors[locale][product][channel]['errors'] = errors
        if len(warnings)>0:
            json_errors[locale][product][channel]['warnings'] = warnings
    except Exception as e:
        errors.append(
            "[%s] problem reading %s" % (locale, file_list)
        )


def extract_p12n_product(source, product, locale, channel,
                         json_data, json_errors):
    # Extract p12n information from region.properties.
    errors = []
    warnings = []
    nested_dict = lambda: collections.defaultdict(nested_dict)

    try:
        available_searchplugins = []
        if channel in json_data[locale][product]:
            # I need to proceed only if I have searchplugins for this
            # branch+product+locale
            for element in json_data[locale][product][channel].values():
                # Store the "name" attribute of each searchplugin, used to
                # validate search.order
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
                                # Remove whitespaces, some locales use key =
                                # value instead of key=value
                                values[key.strip()] = value.strip()
                            except:
                                errors.append(
                                    "problem parsing %s (%s, %s, %s)"
                                    % (source, locale, product , channel)
                                )
                except:
                    errors.append(
                        "problem reading %s (%s, %s, %s)"
                        % (source, locale, product , channel)
                    )

                defaultenginename = "-"
                searchorder = nested_dict()
                feedhandlers = nested_dict()
                handlerversion = "-"
                contenthandlers = nested_dict()

                for key, value in values.iteritems():
                    lineok = False

                    # Default search engine name. Example:
                    # browser.search.defaultenginename=Google
                    property_name = "browser.search.defaultenginename"
                    if key.startswith(property_name):
                        lineok = True
                        defaultenginename = values[property_name]
                        if unicode(defaultenginename, "utf-8") not in \
                                available_searchplugins:
                            errors.append(
                                "%s is set as default but not available in "
                                "searchplugins (check if the name is spelled "
                                "correctly)" % defaultenginename
                            )

                    # Search engines order. Example:
                    # browser.search.order.1=Google
                    if key.startswith("browser.search.order."):
                        lineok = True
                        searchorder[key[-1:]] = value
                        if (unicode(value, "utf-8") not in
                            available_searchplugins):
                            if value != "":
                                errors.append(
                                    "%s is defined in searchorder but not "
                                    "available in searchplugins (check if the "
                                    "name is spelled correctly)" % value
                                )
                            else:
                                errors.append(
                                    "<code>%s</code> is empty" % key
                                )

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
                                warnings.append(
                                    "Google Reader has been dismissed, "
                                    "see bug 882093 (<code>%s</code>)"
                                    % key
                                )
                        if key.endswith(".uri"):
                            feedhandler_number = key[-5:-4]
                            feedhandlers[feedhandler_number]["uri"] = value

                    # Handler version. Example:
                    # gecko.handlerService.defaultHandlersVersion=4
                    property_name = "gecko.handlerService.defaultHandlersVersion"
                    if key.startswith(property_name):
                        lineok = True
                        handlerversion = values[property_name]

                    # Service handlers. Example:
                    # gecko.handlerService.schemes.webcal.0.name=30 Boxes
                    # gecko.handlerService.schemes.webcal.0.uriTemplate=https://30boxes.com/external/widget?refer=ff&url=%s
                    if key.startswith("gecko.handlerService.schemes."):
                        lineok = True
                        splittedkey = key.split(".")
                        ch_type = splittedkey[3]
                        ch_number = splittedkey[4]
                        ch_param = splittedkey[5]
                        if ch_param == "name":
                            contenthandlers[ch_type][ch_number]["name"] = value
                        if ch_param == "uriTemplate":
                            contenthandlers[ch_type][ch_number]["uri"] = value

                    # Ignore some keys for mail and seamonkey
                    if product == "suite" or product == "mail":
                        ignored_keys = [
                            "app.update.url.details"
                            "browser.search.defaulturl",
                            "browser.startup.homepage",
                            "browser.throbber.url",
                            "browser.translation.service",
                            "browser.translation.serviceDomain",
                            "browser.validate.html.service",
                            "mail.addr_book.mapit_url.format",
                            "mailnews.localizedRe",
                            "mailnews.messageid_browser.url",
                            "startup.homepage_override_url",
                        ]
                        if key in ignored_keys:
                            lineok = True

                    # Unrecognized line, print warning (not for en-US)
                    if not lineok and locale != "en-US":
                        warnings.append(
                            "unknown key in region.properties "
                            "<code>%s=%s</code>"
                            % (key, value)
                        )

                try:
                    if product != "suite":
                        json_data[locale][product][channel]["p12n"] = {
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
                        # When analyzing common in ony update
                        # search.order and default
                        tmp_data = json_data[locale][product][channel]["p12n"]
                        if "/common/region.properties" in source:
                            tmp_data["defaultenginename"] = defaultenginename
                            tmp_data["searchorder"] =  searchorder
                        else:
                            tmp_data = {
                                "defaultenginename": defaultenginename,
                                "searchorder": searchorder,
                                "feedhandlers": feedhandlers,
                                "handlerversion": handlerversion,
                                "contenthandlers": contenthandlers
                            }
                        json_data[locale][product][channel]["p12n"] = tmp_data
                except:
                    errors.append(
                        "problem saving data into json from %s (%s, %s, %s)"
                        % (source, locale, product, channel)
                    )
            else:
                errors.append(
                    "file does not exist %s (%s, %s, %s)"
                    % (source, locale, product, channel)
                )
        # Save errors and warnings
        if len(errors)>0:
            json_errors[locale][product][channel]['p12n_errors'] = errors
        if len(warnings)>0:
            json_errors[locale][product][channel]['p12n_warnings'] = warnings
    except:
        errors.append(
            "[%s] No searchplugins available for this locale"
            % product
        )


def extract_splist_enUS (pathsource, splist_enUS):
    # Store in splist_enUS a list of en-US searchplugins (*.xml) in pathsource.
    try:
        for singlefile in glob.glob(os.path.join(pathsource, "*.xml")):
            filename = os.path.basename(singlefile)
            filename_noext = os.path.splitext(filename)[0]
            splist_enUS.append(filename_noext)
    except:
        print "Error: problem reading list of en-US searchplugins from " \
              + pathsource


def extract_p12n_channel(clproduct, pathsource, pathl10n, localeslist, channel,
                         json_data, clp12n, images_list, json_errors):
    try:
        # Analyze en-US searchplugins
        searchpathbase = os.path.join(pathsource, "COMMUN")
        searchpathenUS = {
            "browser_sp"   : os.path.join(searchpathbase, "browser", "locales",
                             "en-US", "en-US", "searchplugins") + os.sep,
            "browser_p12n" : os.path.join(searchpathbase, "browser", "locales",
                             "en-US", "en-US", "chrome", "browser-region",
                             "region.properties"),
            "mobile_sp"    : os.path.join(searchpathbase, "mobile", "locales",
                             "en-US", "en-US", "searchplugins") + os.sep,
            "mobile_p12n"  : os.path.join(searchpathbase, "mobile", "locales",
                             "en-US", "en-US", "chrome", "region.properties"),
            "mail_sp"      : os.path.join(searchpathbase, "mail", "locales",
                             "en-US", "en-US", "searchplugins") + os.sep,
            "mail_p12n"    : os.path.join(searchpathbase, "mail", "locales",
                             "en-US", "en-US", "chrome", "messenger-region",
                             "region.properties"),
            "suite_sp"     : os.path.join(searchpathbase, "suite", "locales",
                             "en-US", "en-US", "searchplugins") + os.sep,
            "suite_p12n_a" : os.path.join(searchpathbase, "suite", "locales",
                             "en-US", "en-US", "chrome", "browser",
                             "region.properties"),
            "suite_p12n_b" : os.path.join(searchpathbase, "suite", "locales",
                             "en-US", "en-US", "chrome", "common",
                             "region.properties"),
        }

        # Create a list of en-US searchplugins for each channel.
        if clproduct=="all" or clproduct=="browser":
            # Get a list of all .xml files inside en-US searchplugins folder
            splistenUS_browser = []
            extract_splist_enUS(
                searchpathenUS["browser_sp"],
                splistenUS_browser
            )
            extract_sp_product(
                searchpathenUS["browser_sp"], "browser", "en-US", channel,
                json_data, splistenUS_browser, images_list, json_errors
            )
            if clp12n:
                extract_p12n_product(
                    searchpathenUS["browser_p12n"], "browser",
                    "en-US", channel, json_data, json_errors
                )

        if clproduct=="all" or clproduct=="mobile":
            splistenUS_mobile = []
            extract_splist_enUS(
                searchpathenUS["mobile_sp"],
                splistenUS_mobile
            )
            extract_sp_product(
                searchpathenUS["mobile_sp"], "mobile", "en-US", channel,
                json_data, splistenUS_mobile, images_list, json_errors
            )
            if clp12n:
                extract_p12n_product(
                    searchpathenUS["mobile_p12n"], "mobile", "en-US",
                    channel, json_data, json_errors
                )

        if clproduct=="all" or clproduct=="mail":
            splistenUS_mail = []
            extract_splist_enUS(
                searchpathenUS["mail_sp"],
                splistenUS_mail
            )
            extract_sp_product(
                searchpathenUS["mail_sp"], "mail", "en-US", channel,
                json_data, splistenUS_mail, images_list, json_errors
            )
            if clp12n:
                extract_p12n_product(
                    searchpathenUS["mail_p12n"], "mail", "en-US",
                    channel, json_data, json_errors
                )

        if clproduct=="all" or clproduct=="suite":
            splistenUS_suite = []
            extract_splist_enUS(
                searchpathenUS["suite_sp"],
                splistenUS_suite
            )
            extract_sp_product(
                searchpathenUS["suite_sp"], "suite", "en-US", channel,
                json_data, splistenUS_suite, images_list, json_errors
            )
            if clp12n:
                extract_p12n_product(
                    searchpathenUS["suite_p12n_a"], "suite", "en-US",
                    channel, json_data, json_errors
                )
                extract_p12n_product(
                    searchpathenUS["suite_p12n_b"], "suite", "en-US",
                    channel, json_data, json_errors
                )

        locale_list = open(localeslist, "r").read().splitlines()
        for locale in locale_list:
            searchpathl10nbase = os.path.join(pathl10n, locale)
            searchpathl10n = {
                "browser_sp"   : os.path.join(
                                    searchpathl10nbase, "browser",
                                    "searchplugins"
                                 ) + os.sep,
                "browser_p12n" : os.path.join(
                                    searchpathl10nbase, "browser", "chrome",
                                    "browser-region", "region.properties"
                                 ),
                "mobile_sp"    : os.path.join(
                                    searchpathl10nbase, "mobile",
                                    "searchplugins"
                                 ) + os.sep,
                "mobile_p12n"  : os.path.join(
                                    searchpathl10nbase, "mobile", "chrome",
                                    "region.properties"
                                 ),
                "mail_sp"      : os.path.join(
                                    searchpathl10nbase, "mail",
                                    "searchplugins"
                                 ) + os.sep,
                "mail_p12n"    : os.path.join(
                                    searchpathl10nbase, "mail", "chrome",
                                    "messenger-region", "region.properties"
                                 ),
                "suite_sp"     : os.path.join(
                                    searchpathl10nbase, "suite",
                                    "searchplugins"
                                 ) + os.sep,
                "suite_p12n_a" : os.path.join(
                                    searchpathl10nbase, "suite", "chrome",
                                    "browser", "region.properties"
                                 ),
                "suite_p12n_b" : os.path.join(
                                    searchpathl10nbase, "suite", "chrome",
                                    "common", "region.properties"
                                 ),
            }

            if clproduct=="all" or clproduct=="browser":
                extract_sp_product(
                    searchpathl10n["browser_sp"], "browser", locale, channel,
                    json_data, splistenUS_browser, images_list, json_errors
                )
                if clp12n:
                    extract_p12n_product(
                        searchpathl10n["browser_p12n"], "browser", locale,
                        channel, json_data, json_errors
                    )
            if clproduct=="all" or clproduct=="mobile":
                extract_sp_product(
                    searchpathl10n["mobile_sp"], "mobile", locale, channel,
                    json_data, splistenUS_mobile, images_list, json_errors
                )
                if clp12n:
                    extract_p12n_product(
                        searchpathl10n["mobile_p12n"], "mobile", locale,
                        channel, json_data, json_errors
                    )
            if clproduct=="all" or clproduct=="mail":
                extract_sp_product(
                    searchpathl10n["mail_sp"], "mail", locale, channel,
                    json_data, splistenUS_mail, images_list, json_errors
                )
                if clp12n:
                    extract_p12n_product(
                        searchpathl10n["mail_p12n"], "mail", locale, channel,
                        json_data, json_errors
                    )
            if clproduct=="all" or clproduct=="suite":
                extract_sp_product(
                    searchpathl10n["suite_sp"], "suite", locale, channel,
                    json_data, splistenUS_suite, images_list, json_errors
                )
                if clp12n:
                    extract_p12n_product(
                        searchpathl10n["suite_p12n_a"], "suite", locale,
                        channel, json_data, json_errors
                    )
                    extract_p12n_product(
                        searchpathl10n["suite_p12n_b"], "suite", locale,
                        channel, json_data, json_errors
                    )
    except Exception as e:
        print "Error reading list of locales from " + localeslist
        print e


def main():
    # Parse command line options
    clparser = OptionParser()
    clparser.add_option("-p", "--product", help="Choose a specific product",
                        choices=["browser", "mobile", "mail", "suite", "all"],
                        default="all")
    clparser.add_option("-b", "--branch", help="Choose a specific branch",
                        choices=["release", "beta", "aurora", "trunk", "all"],
                        default="all")
    clparser.add_option("-n", "--noproductization",
                        help="Disable productization checks",
                        action="store_true")
    (options, args) = clparser.parse_args()
    clproduct = options.product
    clbranch = options.branch
    clp12n = False if options.noproductization else True

    # Read configuration file
    parser = SafeConfigParser()

    # Get absolute path of ../config from current script location (not current
    # folder)
    config_folder = os.path.abspath(
                        os.path.join(
                            os.path.dirname( __file__ ),
                            os.pardir, "config"
                        )
                    )
    parser.read(os.path.join(config_folder, "config.ini"))

    local_install = parser.get("config", "install")
    local_hg      = parser.get("config", "local_hg")
    config_files  = os.path.join(parser.get("config", "config"), "sources")

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


    nested_dict = lambda: collections.defaultdict(nested_dict)
    data_filename = os.path.join(web_p12n_folder, "searchplugins.json")
    json_data = nested_dict()

    errors_filename = os.path.join(web_p12n_folder, "errors.json")
    json_errors = nested_dict()

    images_list = [
        "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34A"
        "AAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAV5JREFUSImt1k1K"
        "JEEQhuFHzzC40u7RpZ5CL+FP4yFEGdFzCPYFxOnxAiOCt3DWouhCd44ulG7aRVVBkZ2"
        "ZVa0dEBRkRL1f5E9F5Zy8rWAL61jDj3L8Gf9wjXPcNnAmbBkXGGHc4CMM0G0L38VbC3"
        "Dor+g1wQ+/AA59P1f5d+GV74Tw5ciyDHFSPlOgVM5/dOoCfyIvbpaxzYRIPWc7knNew"
        "VdMnpaTYIahSB1eWT9gjJQn67ihulAkFuslZnkIV5FATqQtfIxLeEwEUyJt4WPcw0cm"
        "ISfSBB/jfT5T3czsIVPBTJZomk3umew3OZG/cDQFvDqmbUV+wU/NH1oIiImEH9pQrV0"
        "MIsHthurqIrGcs7p6V9HPQ8BpAl7P6UdyXrAYzFAvA5rWkyfvYAbwvRS8sh1FP58W/J"
        "KrPLSOop+3+ekPFRu6FAPNNQh1FdeWDaxioRx/wo3i2vIbdynAJ3C4ViylVaDnAAAAA"
        "ElFTkSuQmCC"
    ]

    if clbranch=="all" or clbranch=="trunk":
        extract_p12n_channel(clproduct, trunk_source, trunk_l10n,
                             trunk_locales, "trunk", json_data,
                             clp12n, images_list, json_errors)
    if clbranch=="all" or clbranch=="aurora":
        extract_p12n_channel(clproduct, aurora_source, aurora_l10n,
                             aurora_locales, "aurora", json_data,
                             clp12n, images_list, json_errors)
    if clbranch=="all" or clbranch=="beta":
        extract_p12n_channel(clproduct, beta_source, beta_l10n,
                             beta_locales, "beta", json_data, clp12n,
                             images_list, json_errors)
    if clbranch=="all" or clbranch=="release":
        extract_p12n_channel(clproduct, release_source, release_l10n,
                             release_locales, "release", json_data,
                             clp12n, images_list, json_errors)

    # Create images json structure and save it to file
    image_data = {}
    for index, value in enumerate(images_list):
        image_data[index] = value
    json_data["images"] = image_data
    json_errors["metadata"] = {
        "creation_date": strftime("%Y-%m-%d %H:%M %Z", localtime())
    }

    # Write back updated json with data
    json_file = open(data_filename, "w")
    json_file.write(json.dumps(json_data, sort_keys=True))
    json_file.close()

    # Finalize and write json with errors
    json_errors["metadata"] = {
        "creation_date": strftime("%Y-%m-%d %H:%M %Z", localtime())
    }
    errors_file = open(errors_filename, "w")
    errors_file.write(json.dumps(json_errors, sort_keys=True))
    errors_file.close()


if __name__ == "__main__":
    main()
