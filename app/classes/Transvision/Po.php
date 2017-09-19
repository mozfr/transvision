<?php
namespace Transvision;

use Gettext\Translations;

/**
 * Gettext class
 *
 * This class is used to manipulate translation files in Gettext (.po) format.
 *
 * @package Transvision
 */
class Po
{
    /**
     *
     * Loads strings from a .po file
     *
     * @param string  $po_path      Path to the .po to load
     * @param string  $file_name    Name of the file extracted
     * @param string  $project_name The project this string belongs to
     * @param boolean $template     If I'm looking at templates
     *
     * @return array Array of strings as [string_id => translation]
     */
    public static function getStrings($po_path, $file_name, $project_name, $template = false)
    {
        $translations = Translations::fromPoFile($po_path);
        $strings = [];

        foreach ($translations as $translation_obj) {
            $translated_string = $translation_obj->getTranslation();

            // Ignore fuzzy strings
            if (in_array('fuzzy', $translation_obj->getFlags())) {
                continue;
            }

            // Ignore empty (untranslated) strings
            if ($translated_string == '' && ! $template) {
                continue;
            }

            // In templates, use the original string as translation
            if ($template) {
                $translated_string = $translation_obj->getOriginal();
            }

            $string_id = self::generateStringID(
                $project_name,
                $file_name,
                $translation_obj->getContext() . '-' . $translation_obj->getOriginal()
            );
            $translated_string = str_replace("'", "\\'", $translated_string);
            $strings[$string_id] = $translated_string;

            // Check if there are plurals, in case put them as translation of
            // the only English plural form
            if ($translation_obj->hasPluralTranslations()) {
                $string_id = self::generateStringID(
                    $project_name,
                    $file_name,
                    $translation_obj->getContext() . '-' . $translation_obj->getPlural()
                );
                $translated_string = implode("\n", $translation_obj->getPluralTranslations());
                $translated_string = str_replace("'", "\\'", $translated_string);
                $strings[$string_id] = $translated_string;
            }
        }

        return $strings;
    }

    /**
     * Generate a unique id for a string to store in Transvision.
     *
     * @param string $project_name The project this string belongs to
     * @param string $file_name    'original' attribute of the <file> element
     * @param string $string_id    'id' attribute of the <trans-unit> element
     *
     * @return string unique ID such as firefox_ios/Client/Intro.strings:1cd1dc4e
     */
    public static function generateStringID($project_name, $file_name, $string_id)
    {
        return "{$project_name}/{$file_name}:" . hash('md5', $string_id);
    }
}
