<?php
namespace Transvision;

/**
 * Xliff class
 *
 * This class is used to manipulate translation files in XLIFF format
 * used in Firefox for iOS.
 *
 * @package Transvision
 */
class Xliff
{
    /**
     *
     * Loads strings from a .xliff file
     *
     * @param string $xliff_path   Path to the .xliff to load
     * @param string $project_name The project this string belongs to
     *
     * @return array Array of strings as [string_id => translation]
     */
    public static function getStrings($xliff_path, $project_name)
    {
        $strings = [];
        if ($xml = simplexml_load_file($xliff_path)) {
            $namespaces = $xml->getDocNamespaces();
            $xml->registerXPathNamespace('x', $namespaces['']);
            /* Get all trans-units, which include both reference (source) and
            /* translation (target)
             */
            $trans_units = $xml->xpath('//x:trans-unit');
            foreach ($trans_units as $trans_unit) {
                /* File's name is 2 levels above in the hierarchy, stored as
                 * 'original' attribute of the <file> element.
                 */
                $file_node = $trans_unit->xpath('../..');
                $file_name = $file_node[0]['original'];

                $string_id = self::generateStringID($project_name, $file_name, $trans_unit['id']);
                $translation = str_replace("'", "\\'", $trans_unit->target);

                $strings[$string_id] = $translation;
            }
        }

        return $strings;
    }

    /**
     * Generate a unique id for a string to store in Transvision.
     * String ID can be identical to the source string in iOS, so it's more
     * reliable to generate a unique ID from it.
     *
     * @param string $project_name The project this string belongs to
     * @param string $file_name    'original' attribute of the <file> element
     * @param string $string_id    'id' attribute of the <trans-unit> element
     *
     * @return string unique ID such as firefox_ios/Client/Intro.strings:1cd1dc4e
     */
    public static function generateStringID($project_name, $file_name, $string_id)
    {
        return "{$project_name}/{$file_name}:" . hash('crc32', $string_id);
    }
}
