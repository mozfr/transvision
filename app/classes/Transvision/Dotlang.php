<?php
namespace Transvision;

/**
 * Dotlang class
 *
 * This class is used to manipulat translation files in .lang format
 * used in www.mozilla.org, firefox health report, start.mozilla.org,
 * surveys and other small projects on svn mostly related to engagement.
 *
 * @package Transvision
 */
class Dotlang
{
    /**
     * Recursively find all lang files, ignore common VCS dot files
     * @param  string $path Path to the directory to scan
     * @return array  All the file paths
     */
    public static function getLangFilesList($path)
    {
        $files = [];

        foreach (scandir($path) as $sub_path) {
            if (Strings::startsWith($sub_path, '.')) {
                // ignore '.', '..' and other hidden files such as .svn
               continue;
            }

            $file_path = $path . '/' . $sub_path;

            if (is_file($file_path)) {
                if (pathinfo($file_path, PATHINFO_EXTENSION) == 'lang') {
                    // we want to avoid double slashes in the path
                    $files[] = str_replace('//', '/', $file_path);
                }
                continue;
            }

            foreach (self::getLangFilesList($file_path) as $value) {
                $files[] = $value;
            }
        }

        return $files;
    }

    /**
     * Loads the lang file and returns a cleaned up array of the lines
     *
     * @param  string $file path to the local file to load
     * @return array  All the significant lines in the file as an array
     */
    public static function getFile($file)
    {
        if (! is_file($file)) {
            return [];
        }

        $file = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (count($file) > 0) {
            // remove BOM
            $file[0] = trim($file[0], "\xEF\xBB\xBF");
        }

        return (array) $file;
    }

    /**
     *
     * Loads strings from a .lang file into an array. File format is:
     *  ;String in english
     *  translated string
     *
     * @param  string  $file             .lang file to load
     * @param  boolean $reference_locale True if the current locale is the reference locale
     * @return array   Array of strings as [original => translation]
     */
    public static function getStrings($file, $reference_locale)
    {
        // get all the lines in an array
        $f = self::getFile($file);

        $strings = [];

        for ($i = 0, $lines = count($f); $i < $lines; $i++) {

            // skip comments and metadata
            if (Strings::startsWith($f[$i], '#')) {
                continue;
            }

            if (Strings::startsWith($f[$i], ';') && !empty($f[$i + 1])) {
                $english = trim(substr($f[$i], 1));
                if (Strings::startsWith($f[$i + 1], '#') ||
                    Strings::startsWith($f[$i + 1], ';')) {
                    /* String is not translated, next line is a comment or
                     * the next source string. I consider the string untranslated
                     */
                    $translation = $english;
                } else {
                    // Next line is an actual translation
                    $translation = trim($f[$i + 1]);
                }

                // If untranslated, I need to store an empty string as translation
                // unless it's the reference locale
                if ($reference_locale) {
                    $strings[$english] = $english;
                } else {
                    $strings[$english] = ($translation == $english) ? '' : $translation;
                }

                $i++;
            }
        }

        return $strings;
    }

    /**
     * Generate a unique id for a string to store that in Transvision with
     * the unique ids for products. Since there are no names for strings
     * we generate a short hash of the string.
     * @param  string $file_path the file path to the .lang file
     * @param  string $string    the English string
     * @return string unique id such as mozilla_org/mozorg/about.lang:a4a7eabd
     */
    public static function generateStringID($file_path, $string)
    {
        return $file_path . ':' . hash('crc32', $string);
    }
}
