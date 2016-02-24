<?php
namespace Transvision;

/**
 * Consistency class
 *
 * Functions used to analyze translation consistency
 *
 * @package Transvision
 */
class Consistency
{
    /**
     * Find duplicated strings (strings with the same text, ignoring case).
     *
     * @param array $strings_array Array of strings in the form
     *                             string_id => string_value
     *
     * @return array Array of duplicated strings, same format as the
     *               input array
     */
    public static function findDuplicates($strings_array)
    {
        $duplicates = [];
        // Use array_filter to exclude empty strings
        $strings_array = array_filter($strings_array);
        asort($strings_array);

        $previous_key = '';
        $previous_value = '';
        foreach ($strings_array as $key => $value) {
            if (strcasecmp($previous_value, $value) === 0) {
                $duplicates[$previous_key] = $previous_value;
                $duplicates[$key] = $value;
            }
            $previous_value = $value;
            $previous_key = $key;
        }

        return $duplicates;
    }

     /**
      * Filter out strings that should not be evaluted for consistency.
      *
      * @param  array $strings_array Array of strings in the form
      *                              string_id => string_value
      * @param  string Repository identifier
      *
      * @return array Array of filtered strings, with known false positives
      *               removed
      */
     public static function filterStrings($strings_array, $repo)
     {
         if (in_array($repo, Project::getDesktopRepositories())) {
             $repository_type = 'desktop';
         } elseif (in_array($repo, Project::getGaiaRepositories())) {
             $repository_type = 'gaia';
         } else {
             $repository_type = $repo;
         }

         // Determine if a string should be excluded
         $ignore_string = function ($key, $value) use ($repository_type) {
             // Ignore strings of length 1 (e.g. accesskeys) and empty strings.
             if (strlen($value) <= 1) {
                 return true;
             }

             // Exclude CSS rules, "width:" or "height:"
             if (Strings::inString($value, ['width:', 'height:'])) {
                 return true;
             }

             // Exclude CSS width values like '38em'
             if (preg_match('/[\d|\.]+em/', $value)) {
                 return true;
             }

             if ($repository_type == 'gaia') {
                 // Ignore plural forms containing [] in the key
                 if (Strings::inString($key, ['[', ']'])) {
                     return true;
                 }

                 // Ignore accessibility strings
                 if (strpos($key, 'accessibility.properties') !== false) {
                     return true;
                 }
             }

             if ($repository_type == 'desktop') {
                 /*
                    Ignore some specific files:
                    - AccessFu.properties: there are accessibility
                      strings that remain identical for English in full and
                      abbreviated form.
                    - defines.inc (language pack attribution)
                    - region.properties
                 */
                 if (Strings::inString($key, ['AccessFu.properties', 'defines.inc', 'region.properties'])) {
                     return true;
                 }
             }

             return false;
         };

         foreach ($strings_array as $key => $value) {
             if ($ignore_string($key, $value)) {
                 unset($strings_array[$key]);
             }
         }

         return $strings_array;
     }
}
