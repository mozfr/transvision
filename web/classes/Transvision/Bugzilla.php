<?php

namespace Transvision;

class Bugzilla
{
    /*
     * Check if we have a cache file with the components (languages) and
     * the cache file is not one week old. If not Connect to Bugzilla API
     * and get components list
     *
     * @return $components_list
     */
    public static function getBugzillaComponents()
    {
        $cache_file = CACHE . 'bugzilla_components.json';
        if (!file_exists($cache_file) || filemtime($cache_file) + (4 * 7 * 24 * 60 * 60) < time()) {
            $json_url = 'https://bugzilla.mozilla.org/jsonrpc.cgi?method=Product.get&params=[%20{%20%22names%22:%20[%22Mozilla%20Localizations%22]}%20]';
            file_put_contents($cache_file, file_get_contents($json_url));
        }

        $data = json_decode(file_get_contents($cache_file), true);
        $components_list = $data['result']['products'][0]['components'];

        return $components_list;
    }

    /*
     * Collect the correct language component for bugzilla URL
     *
     * @param $actual_lng string
     * @param $components_array array
     * @return $component_string
     */
    public static function collectLanguageComponent($actual_lng, $components_array)
    {
        $component_string = 'Other';
        $actual_lng = $actual_lng . ' /';

        foreach ($components_array as $component) {
            if (Strings::startsWith($component['name'], $actual_lng)) {
                $component_string = $component['name'];
                break;
            }
        }
        return $component_string;
    }
}
