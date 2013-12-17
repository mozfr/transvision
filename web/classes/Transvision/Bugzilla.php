<?php

namespace Transvision;

class Bugzilla
{
    /*
     * Cache file should be created by glossaire.sh (running bugzilla_query.py).
     * If it doesn't exist, connect to Bugzilla API and get components list.
     *
     * @return $components_list
     */
    public static function getBugzillaComponents()
    {
        $cache_file = CACHE . 'bugzilla_components.json';
        if (!file_exists($cache_file)) {
            $json_url = 'https://bugzilla.mozilla.org/jsonrpc.cgi?method=Product.get&params=[%20{%20%22names%22:%20[%22Mozilla%20Localizations%22]}%20]';
            file_put_contents($cache_file, file_get_contents($json_url));
        }

        $data = Json::fetchJson($cache_file);
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
    public static function collectLanguageComponent($actual_lang, $components_array)
    {
        $actual_lang = ($actual_lang == 'es') ? 'es-ES' : $actual_lang;
        $actual_lang = ($actual_lang == 'pa') ? 'pa-IN' : $actual_lang;
        $actual_lang = Bugzilla::bugzillaLocaleCode($actual_lang);

        $component_string = 'Other';
        $actual_lang = $actual_lang . ' /';

        foreach ($components_array as $component) {
            if (Strings::startsWith($component['name'], $actual_lang)) {
                $component_string = $component['name'];
                break;
            }
        }

        return $component_string;
    }

    /*
     * Get the locale code we use on Bugzilla for components
     * It can differ from what we have in the products for historical reasons
     *
     * @param $locale string
     * @return $locale string
     */
    public static function bugzillaLocaleCode($locale)
    {
        $locale = ($locale == 'es') ? 'es-ES' : $locale;
        $locale = ($locale == 'pa') ? 'pa-IN' : $locale;
        $locale = ($locale == 'sr-Cyrl' || $locale == 'sr-Latn') ? 'sr' : $locale;

        return $locale;
    }
}
