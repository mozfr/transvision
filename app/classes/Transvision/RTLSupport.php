<?php
namespace Transvision;

/**
 * VersionControl class
 *
 * Utility class for Right to Left languages, mostly used for templating
 *
 * @package Transvision
 */
class RTLSupport
{
    /*
     * List of locales that are Right to Left
     */
    public static $rtl = ['ar', 'fa', 'he', 'ur'];

    /**
     * Get the text direction from a locale code
     *
     * @param  string $locale locale code
     * @return string rtl or ltr
     */
    public static function getDirection($locale)
    {
        return in_array($locale, self::$rtl) ? 'rtl' : 'ltr';
    }

    /**
     * Is this locale code a RTL locale?
     *
     * @param  type    $locale locale code we want to test
     * @return boolean true if RTL, false if LTR
     */
    public static function isRTL($locale)
    {
        return in_array($locale, self::$rtl) ? true : false;
    }
}
