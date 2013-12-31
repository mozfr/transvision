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

        $data = Json::fetch($cache_file);
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

    /*
     * Get a prefilled url to report a string error for a locale in Bugzilla
     *
     * @param $locale string
     * @return link string
     */
    public static function reportErrorLink($locale, $entity, $source_string, $target_string, $entity_link)
    {
        // Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them
        $bz_component = rawurlencode(
            self::collectLanguageComponent($locale, self::getBugzillaComponents())
        );

        $bug_summary = rawurlencode("Translation update proposed for {$entity}");
        $bug_message = rawurlencode(
            html_entity_decode(
                "The string:\n{$source_string}\n\n"
                . "Is translated as:\n{$target_string}\n\n"
                . "And should be:\n\n\n\n"
                . "Feedback via Transvision:\n"
                . "http://transvision.mozfr.org/{$entity_link}"
            )
        );

        return 'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component='
               . $bz_component
               . '&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D'
               . '&short_desc='
               . $bug_summary
               . '&comment='
               . $bug_message;
    }

}
