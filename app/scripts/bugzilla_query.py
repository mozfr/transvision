#! /usr/bin/env python


import json
import os
import urllib2
from ConfigParser import SafeConfigParser

def main():

	# Read the config file
	try:
		parser = SafeConfigParser()
		# Get absolute path of ../config from current script location (not current folder)
		config_folder = os.path.abspath(os.path.join(os.path.dirname( __file__ ), os.pardir, 'config'))
		parser.read(os.path.join(config_folder, "config.ini"))
		cache_filename = os.path.join(parser.get("config", "install"), "cache", "bugzilla_components.json")
		print "Writing cache to " + cache_filename
	except Exception as e:
		print "Error reading config file ../config/config.ini"
		print e

	json_url = "https://bugzilla.mozilla.org/jsonrpc.cgi?method=Product.get&amp;params=[%20{%20%22names%22:%20[%22Mozilla%20Localizations%22]}%20]";

	try:
		response = urllib2.urlopen(json_url)
		# I use the same structure of the original json. In this way, if
		# something goes wrong, php can still grab the original json file and use it.
		json_components = {
			"result": {
				"products": [{
					"components": []
				}]
			}
		}
		json_data = json.load(response)
		try:
			for component in json_data["result"]["products"][0]["components"]:
				json_components["result"]["products"][0]["components"].append({"name": component["name"]})

			# Write list of components name
			cache_file = open(cache_filename, "w")
			cache_file.write(json.dumps(json_components))
			cache_file.close()
		except Exception as e:
			print "Error extracting data from json response"
			print e
	except Exception as e:
		print "Error reading json reply from " + json_url
		print e


if __name__ == "__main__":
    main()
