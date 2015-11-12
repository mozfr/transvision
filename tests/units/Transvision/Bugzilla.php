<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Bugzilla as _Bugzilla;

require_once __DIR__ . '/../bootstrap.php';

class _BugzillaTest extends _Bugzilla
{
    public static function getURLencodedBugzillaLocale($locale, $type)
    {
        // Override default function to use a local file instead of query remote service
        if ($type == 'www') {
            $local_json = TEST_FILES . 'cache/www.json';
        } else {
            $local_json = TEST_FILES . 'cache/desktop.json';
        }

        return rawurlencode(self::getBugzillaLocaleField($locale, $type, false, $local_json));
    }
}

class Bugzilla extends atoum\test
{
    public function reportErrorLinkDP()
    {
        return [
            [
                'zh-TW',
                'browser/chrome/browser/preferences/main.dtd:startupHomePage.label',
                'Show my home page',
                '顯示首頁',
                'beta',
                '?sourcelocale=en-US&locale=zh-TW&repo=beta&search_type=entities&recherche=browser/chrome/browser/preferences/main.dtd:startupHomePage.label',
                'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component=zh-TW%20%2F%20Chinese%20%28Traditional%29&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D&bug_file_loc=https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dzh-TW%26repo%3Dbeta%26search_type%3Dentities%26recherche%3Dbrowser%2Fchrome%2Fbrowser%2Fpreferences%2Fmain.dtd%3AstartupHomePage.label&short_desc=%5Bzh-TW%5D%20Translation%20update%20proposed%20for%20browser%2Fchrome%2Fbrowser%2Fpreferences%2Fmain.dtd%3AstartupHomePage.label&comment=The%20string%3A%0AShow%20my%20home%20page%0A%0AIs%20translated%20as%3A%0A%E9%A1%AF%E7%A4%BA%E9%A6%96%E9%A0%81%0A%0AAnd%20should%20be%3A%0A%0A%0A%0AFeedback%20via%20Transvision%3A%0Ahttps%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dzh-TW%26repo%3Dbeta%26search_type%3Dentities%26recherche%3Dbrowser%2Fchrome%2Fbrowser%2Fpreferences%2Fmain.dtd%3AstartupHomePage.label',
            ],
            [
                'fr',
                'mozilla_org/firefox/whatsnew.lang:c9ecbf83',
                'Learn more about our &lt;a href=&quot;{url}&quot;&gt;redesigned home page »&lt;/a&gt;',
                'Découvrez notre nouvelle &lt;a href=&quot;{url}&quot;&gt;page d&#039;accueil »&lt;/a&gt;',
                'mozilla_org',
                '?sourcelocale=en-US&locale=fr&repo=mozilla_org&search_type=entities&recherche=mozilla_org/firefox/whatsnew.lang:c9ecbf83',
                'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component=L10N&product=www.mozilla.org&status_whiteboard=%5Btransvision-feedback%5D&bug_file_loc=https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dfr%26repo%3Dmozilla_org%26search_type%3Dentities%26recherche%3Dmozilla_org%2Ffirefox%2Fwhatsnew.lang%3Ac9ecbf83&short_desc=%5Bfr%5D%20Translation%20update%20proposed%20for%20mozilla_org%2Ffirefox%2Fwhatsnew.lang%3Ac9ecbf83&comment=The%20string%3A%0ALearn%20more%20about%20our%20%3Ca%20href%3D%22%7Burl%7D%22%3Eredesigned%20home%20page%20%C2%BB%3C%2Fa%3E%0A%0AIs%20translated%20as%3A%0AD%C3%A9couvrez%20notre%20nouvelle%20%3Ca%20href%3D%22%7Burl%7D%22%3Epage%20d%26%23039%3Baccueil%20%C2%BB%3C%2Fa%3E%0A%0AAnd%20should%20be%3A%0A%0A%0A%0AFeedback%20via%20Transvision%3A%0Ahttps%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dfr%26repo%3Dmozilla_org%26search_type%3Dentities%26recherche%3Dmozilla_org%2Ffirefox%2Fwhatsnew.lang%3Ac9ecbf83&cf_locale=fr%20%2F%20French',
            ],
        ];
    }

    /**
     * @dataProvider reportErrorLinkDP
     */
    public function testReportErrorLink($a, $b, $c, $d, $e, $f, $g)
    {
        $obj = new _BugzillaTest();
        $this
            ->string($obj->reportErrorLink($a, $b, $c, $d, $e, $f))
                ->isEqualTo($g);
    }
}
