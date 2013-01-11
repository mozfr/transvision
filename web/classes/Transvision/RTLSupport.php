<?php

namespace Transvision;

class RTLSupport
{
    protected $rtl; // array

    public function __construct()
    {
        $this->rtl = array('ar', 'fa', 'he', 'ur');
    }
    
    public static function getDirection($locale)
    {
        return in_array($locale, $self->$rtl) ? 'rtl' : 'ltr';
    }
    
    public static function isRTL($locale)
    {
        return in_array($locale, $self->$rtl) ? true : false;
    }
}
