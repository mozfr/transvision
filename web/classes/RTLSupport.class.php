<?php

namespace tinyL10n;

class RTL
{
    public static function getDirection($locale)
    {
        $rtl = array('ar', 'fa', 'he', 'ur');
        return in_array($locale, $rtl) ? 'rtl' : 'ltr';
    }
}
