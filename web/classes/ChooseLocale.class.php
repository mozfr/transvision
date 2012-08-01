<?php

/* ChooseLocale
 *
 * Licence: MPL 2/GPL 2.0/LGPL 2.1
 * Author: Pascal Chevrel, Mozilla <pascal@mozilla.com>, Mozilla
 * Contributor: Stanislaw Malolepszy <stas@mozilla.com>, Mozilla
 * Date : 2012-07-08
 * version: 0.3

 * Description:
 * Class to choose the locale we will show to the visitor
 * based on http accept-lang headers and our list of supported locales.
 *
 *
*/

namespace tinyL10n;

class ChooseLocale
{
    public    $HTTPAcceptLang;
    public    $supportedLocales;
    protected $detectedLocale;
    protected $defaultLocale;
    public    $mapLonglocales;

    public function __construct($list=array('en-US'))
    {
        $this->HTTPAcceptLang   = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $this->supportedLocales = array_unique($list);
        $this->setDefaultLocale('en-US');
        $this->setCompatibleLocale();
        $this->mapLonglocales = true;
    }

    public function getAcceptLangArray()
    {
        if (empty($this->HTTPAcceptLang)) {
            return null;
        } else {
            return explode(',', $this->HTTPAcceptLang);
        }
    }

    public function getCompatibleLocale()
    {
        $acclang = $this->getAcceptLangArray();

        if(!is_array($acclang)) {
            return $this->defaultLocale;
        }

        foreach ($acclang as $var) {
            $locale      = $this->_cleanHTTPlocaleCode($var);
            $shortLocale = array_shift((explode('-', $locale)));

            if (in_array($locale, $this->supportedLocales)) {
                return $locale;
            }

            if (in_array($shortLocale, $this->supportedLocales)) {
                return $shortLocale;
            }

            // check if we map visitors short locales to site long locales
            // like en->en-GB
            if ($this->mapLonglocales == true) {
                foreach ($this->supportedLocales as $supported) {
                    $shortSupportedLocale = array_shift((explode('-', $supported)));
                    if ($shortLocale == $shortSupportedLocale) {
                        return $supported;
                    }
                }
            }
        }

        return $this->defaultLocale;
    }

    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    public function setCompatibleLocale()
    {
        $this->detectedLocale  = $this->getCompatibleLocale();
    }

    public function setDefaultLocale($locale)
    {

        // the default locale should always be among the site locales
        // if not, the first locale in the supportedLocales array is default
        if (!in_array($locale, $this->supportedLocales)) {
            $this->defaultLocale = $this->supportedLocales[0];
        } else {
            $this->defaultLocale = $locale;
        }

        return;
    }

    private function _cleanHTTPlocaleCode($str)
    {
        $locale = explode(';', $str);
        $locale = trim($locale[0]);

        return $locale;
    }
}
