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

                $string_id = self::generateStringID($project_name, $file_name, $file_orig, $trans_unit['id'], $strings);

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
     *
     * If the string ID includes a space, assume it's text, and generate a
     * unique identifier based on the file and ID. If there is already a string
     * with that ID extracted, add the hash.
     *
     *
     * @param string $project_name The project this string belongs to
     * @param string $file_name    .xliff file name
     * @param string $file_orig    'original' attribute of the element's parent
     * @param string $string_id    'id' attribute of the <trans-unit> element
     * @param string $strings      strings already extracted from the file
     *
     * @return string Either string ID, or unique ID based on the file and ID
     *                firefox_ios/firefox-ios.xliff:1dafea7725862ca854c408f0e2df9c88
     */
    public static function generateStringID($project_name, $file_name, $file_orig, $string_id, $strings)
    {
        // If $string_id contains a space, generate unique ID.
        if (strpos($string_id, ' ') !== false) {
            return "{$project_name}/{$file_name}:" . hash('md5', $file_orig . $string_id);
        }

        $generated_id = "{$project_name}/{$file_name}:{$string_id}";

        // If we already extracted a string with the same ID, use a more unique ID.
        if (array_key_exists($generated_id, $strings)) {
            // If already defined, fall back to the generated ID.
            return "{$generated_id}-" . hash('md5', $file_orig . $string_id);
        }

        return $generated_id;
    }
}
