<?php
namespace Transvision;

/**
 * Bugzilla class
 *
 * This class is for all the methods we need to work with Bugzilla
 *
 * @package Transvision
 */
class Bugzilla
{
    /**
     * Cache file should be created by glossaire.sh (running bugzilla_query.py).
     * If it doesn't exist, connect to Bugzilla API and get components list.
     *
     * @return array list of Bugzilla components fetched in cache or from Bugzilla
     */
    public static function getBugzillaComponents()
    {
        $cache_file = CACHE_PATH . 'bugzilla_components.json';
        if (!file_exists($cache_file)) {
            $json_url = 'https://bugzilla.mozilla.org/jsonrpc.cgi?method=Product.get&params=[%20{%20%22names%22:%20[%22Mozilla%20Localizations%22]}%20]';
            file_put_contents($cache_file, file_get_contents($json_url));
        }

        $data = Json::fetch($cache_file);
        $components_list = $data['result']['products'][0]['components'];

        return $components_list;
    }

    /**
     * Collect the correct language component for bugzilla URL from a locale code
     *
     * @param string $actual_lang  Locale code
     * @param array $components_array List of Bugzilla components
     * @return string Component name for the locale code such as 'fr / French'
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

    /**
     * Generate a prefilled url with the right GET parameters to report a
     * string error for a locale in Bugzilla
     *
     * @param string $locale locale code for the wrong translation such as zh-TW
     * @param string $entity Entity reference in Transvision to the string
     * @param string $source_string Text of the original string
     * @param string $target_string Text of the translation
     * @param string $repo Repository where the string is locales
     * @param string $entity_link Transvision link for the entity
     * @return string url to use in a link that will prefill the report
     */
    public static function reportErrorLink($locale, $entity, $source_string, $target_string, $repo, $entity_link)
    {
        $bug_summary = rawurlencode("Translation update proposed for {$entity}");
        $bug_message = rawurlencode(
            html_entity_decode(
                "The string:\n{$source_string}\n\n"
                . "Is translated as:\n{$target_string}\n\n"
                . "And should be:\n\n\n\n"
                . "Feedback via Transvision:\n"
                . "http://transvision.mozfr.org/{$entity_link}"
            ));

        $bz_locale = rawurlencode(
            self::collectLanguageComponent($locale, self::getBugzillaComponents())
        );
        // Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them
        if ($repo == 'mozilla_org') {
            $bz_component = 'L10N';
            $bz_product = 'www.mozilla.org';
            $bz_extra = '&cf_locale=' . $bz_locale;
        } else {
            $bz_component = $bz_locale;
            $bz_product = 'Mozilla%20Localizations';
            $bz_extra = '';
        }

        return 'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component='
               . $bz_component
               . '&product='
               . $bz_product
               . '&status_whiteboard=%5Btransvision-feedback%5D'
               . '&short_desc='
               . $bug_summary
               . '&comment='
               . $bug_message
               . $bz_extra;
    }
}
