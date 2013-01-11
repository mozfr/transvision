<?php

namespace Transvision;

class RTLSupport
{
    public static $rtl = array('ar', 'fa', 'he', 'ur'); // array
    
    public static function getDirection($locale)
    {
        return in_array($locale, self::$rtl) ? 'rtl' : 'ltr';
    }
    
    public static function isRTL($locale)
    {
        return in_array($locale, self::$rtl) ? true : false;
    }
}
