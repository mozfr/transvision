<?php
namespace Transvision;

use Bugzilla\Bugzilla as _Bugzilla;

/**
 * Bugzilla class
 *
 * This class is for all the methods we need to work with Bugzilla
 *
 * @package Transvision
 */
class Bugzilla extends _Bugzilla
{
    /**
     * Return URL encoded component name.
     *
     * @param string $locale  Locale code for the wrong translation such as zh-TW
     * @param string $product Product on Bugzilla (Mozilla Localizations or www.mozilla.org)
     *
     * @return string Encoded URL for the component name
     */
    public static function getURLencodedBugzillaLocale($locale, $type)
    {
        return rawurlencode(self::getBugzillaLocaleField($locale, $type));
    }

    /**
     * Generate a prefilled url with the right GET parameters to report a
     * string error for a locale in Bugzilla
     *
     * @param string $locale        Locale code for the wrong translation such as zh-TW
     * @param string $entity        Entity reference in Transvision to the string
     * @param string $source_string Text of the original string
     * @param string $target_string Text of the translation
     * @param string $repo          Repository where the string is locales
     * @param string $entity_link   Transvision link for the entity
     *
     * @return string URL to use in a link that will prefill the report
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

        if ($repo == 'mozilla_org') {
            $bz_component = 'L10N';
            $bz_product = 'www.mozilla.org';
            $bz_extra = '&cf_locale=' . self::getURLencodedBugzillaLocale($locale, 'www');
        } else {
            $bz_component = self::getURLencodedBugzillaLocale($locale, 'products');
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
