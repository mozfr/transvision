<?php
namespace Transvision;

/**
 * Xliff class
 *
 * This class is used to manipulate translation files in XLIFF format.
 *
 * @package Transvision
 */
class Xliff
{
    /**
     *
     * Loads strings from a .xliff file
     *
     * @param string  $xliff_path       Path to the .xliff to load
     * @param string  $relative_file    Relative path of the file within the locale
     * @param string  $project_name     The project this string belongs to
     * @param boolean $reference_locale If the current file belongs to the reference locale
     *
     * @return array Array of strings as [string_id => translation]
     */
    public static function getStrings($xliff_path, $relative_file, $project_name, $reference_locale = false)
    {
        $strings = [];
        if ($xml = simplexml_load_file($xliff_path)) {
            $file_name = $relative_file;
            $namespaces = $xml->getDocNamespaces();
            $xml->registerXPathNamespace('x', $namespaces['']);
            /*
                Get all trans-units, which include both reference (source) and
                translation (target).
            */
            $trans_units = $xml->xpath('//x:trans-unit');

            foreach ($trans_units as $trans_unit) {
                $file_node = $trans_unit->xpath('../..');
                $file_orig = $file_node[0]['original'];

                $string_id = self::generateStringID($project_name, $file_name, $file_orig, $trans_unit['id']);

                if ($reference_locale) {
                    // If it's the reference locale, we use the source instead of the target

                    $strings[$string_id] = addslashes($trans_unit->source);
                } elseif (isset($trans_unit->target)) {
                    /*
                        We only store the translation if the target is set.
                        simplexml returns an empty string if the element is
                        missing.
                    */
                    $strings[$string_id] = addslashes($trans_unit->target);
                }
            }
        }

        return $strings;
    }

    /**
     * Generate a unique ID for a string to store in Transvision.
     * String ID can be identical to the source string in iOS, so it's more
     * reliable to generate a unique ID from it.
     *
     * @param string $project_name The project this string belongs to
     * @param string $file_name    .xliff file name
     * @param string $file_orig    'original' attribute of the element's parent
     * @param string $string_id    'id' attribute of the <trans-unit> element
     *
     * @return string unique ID such as firefox_ios/firefox-ios.xliff:1dafea7725862ca854c408f0e2df9c88
     */
    public static function generateStringID($project_name, $file_name, $file_orig, $string_id)
    {
        return "{$project_name}/{$file_name}:" . hash('md5', $file_orig . $string_id);
    }
}
