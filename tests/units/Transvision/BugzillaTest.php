<?php
namespace tests\units\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Bugzilla;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Override Bugzilla to use a local file instead of querying the remote service.
 */
class ModifiedBugzilla extends Bugzilla
{
    public static function getURLencodedBugzillaLocale($locale, $type)
    {
        // Override the default function: use a local file based on the type.
        if ($type === 'www') {
            $local_json = TEST_FILES . 'cache/www.json';
        } else {
            $local_json = TEST_FILES . 'cache/desktop.json';
        }

        return rawurlencode(self::getBugzillaLocaleField($locale, $type, false, $local_json));
    }
}

class BugzillaTest extends TestCase
{
    public static function reportErrorLinkDP()
    {
        return [
            [
                'zh-TW',
                'browser/chrome/browser/preferences/main.dtd:startupHomePage.label',
                'Show my home page',
                '顯示首頁',
                'beta',
                '?sourcelocale=en-US&locale=zh-TW&repo=beta&search_type=entities&recherche=browser/chrome/browser/preferences/main.dtd:startupHomePage.label',
                'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component=zh-TW%20%2F%20Chinese%20%28Traditional%29&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D&bug_file_loc=https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dzh-TW%26repo%3Dbeta%26search_type%3Dentities%26recherche%3Dbrowser%2Fchrome%2Fbrowser%2Fpreferences%2Fmain.dtd%3AstartupHomePage.label&short_desc=%5Bzh-TW%5D%20Translation%20update%20proposed%20for%20browser%2Fchrome%2Fbrowser%2Fpreferences%2Fmain.dtd%3AstartupHomePage.label&comment=Source%20string%3A%0A%0A%60%60%60%0AShow%20my%20home%20page%0A%60%60%60%0A%0AIs%20translated%20as%3A%0A%0A%60%60%60%0A%E9%A1%AF%E7%A4%BA%E9%A6%96%E9%A0%81%0A%60%60%60%0A%0AAnd%20should%20be%3A%0A%0A%60%60%60%0A%28add%20your%20translation%20here%29%0A%60%60%60%0A%0AFeedback%20via%20%5BTransvision%5D%28https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dzh-TW%26repo%3Dbeta%26search_type%3Dentities%26recherche%3Dbrowser%2Fchrome%2Fbrowser%2Fpreferences%2Fmain.dtd%3AstartupHomePage.label%29.',
            ],
            [
                'fr',
                'mozilla_org/firefox/whatsnew.lang:c9ecbf83',
                'Learn more about our &lt;a href=&quot;{url}&quot;&gt;redesigned home page »&lt;/a&gt;',
                'Découvrez notre nouvelle &lt;a href=&quot;{url}&quot;&gt;page d\'accueil »&lt;/a&gt;',
                'mozilla_org',
                '?sourcelocale=en-US&locale=fr&repo=mozilla_org&search_type=entities&recherche=mozilla_org/firefox/whatsnew.lang:c9ecbf83',
                'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component=L10N&product=www.mozilla.org&status_whiteboard=%5Btransvision-feedback%5D&bug_file_loc=https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dfr%26repo%3Dmozilla_org%26search_type%3Dentities%26recherche%3Dmozilla_org%2Ffirefox%2Fwhatsnew.lang%3Ac9ecbf83&short_desc=%5Bfr%5D%20Translation%20update%20proposed%20for%20mozilla_org%2Ffirefox%2Fwhatsnew.lang%3Ac9ecbf83&comment=Source%20string%3A%0A%0A%60%60%60%0ALearn%20more%20about%20our%20%3Ca%20href%3D%22%7Burl%7D%22%3Eredesigned%20home%20page%20%C2%BB%3C%2Fa%3E%0A%60%60%60%0A%0AIs%20translated%20as%3A%0A%0A%60%60%60%0AD%C3%A9couvrez%20notre%20nouvelle%20%3Ca%20href%3D%22%7Burl%7D%22%3Epage%20d%27accueil%20%C2%BB%3C%2Fa%3E%0A%60%60%60%0A%0AAnd%20should%20be%3A%0A%0A%60%60%60%0A%28add%20your%20translation%20here%29%0A%60%60%60%0A%0AFeedback%20via%20%5BTransvision%5D%28https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dfr%26repo%3Dmozilla_org%26search_type%3Dentities%26recherche%3Dmozilla_org%2Ffirefox%2Fwhatsnew.lang%3Ac9ecbf83%29.&cf_locale=fr%20%2F%20French',
            ],
            [
                'bo',
                'android_l10n/mozilla-mobile/android-components/components/browser/errorpages/src/main/res/values/strings.xml:mozac_browser_errorpages_content_crashed_title',
                'Content crashed',
                '@@missing@@',
                'android_l10n',
                '?sourcelocale=en-US&locale=bo&repo=all_projects&search_type=entities&recherche=android_l10n/mozilla-mobile/android-components/components/browser/errorpages/src/main/res/values/strings.xml:mozac_browser_errorpages_content_crashed_title&entire_string=entire_string',
                'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component=bo%20%2F%20Tibetan&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D&bug_file_loc=https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dbo%26repo%3Dall_projects%26search_type%3Dentities%26recherche%3Dandroid_l10n%2Fmozilla-mobile%2Fandroid-components%2Fcomponents%2Fbrowser%2Ferrorpages%2Fsrc%2Fmain%2Fres%2Fvalues%2Fstrings.xml%3Amozac_browser_errorpages_content_crashed_title%26entire_string%3Dentire_string&short_desc=%5Bbo%5D%20Translation%20update%20proposed%20for%20android_l10n%2Fmozilla-mobile%2Fandroid-components%2Fcomponents%2Fbrowser%2Ferrorpages%2Fsrc%2Fmain%2Fres%2Fvalues%2Fstrings.xml%3Amozac_browser_errorpages_content_crashed_title&comment=Source%20string%3A%0A%0A%60%60%60%0AContent%20crashed%0A%60%60%60%0A%0AThis%20string%20has%20not%20been%20translated%20yet.%20Proposed%20translation%3A%0A%0A%60%60%60%0A%28add%20your%20translation%20here%29%0A%60%60%60%0A%0AFeedback%20via%20%5BTransvision%5D%28https%3A%2F%2Ftransvision.mozfr.org%2F%3Fsourcelocale%3Den-US%26locale%3Dbo%26repo%3Dall_projects%26search_type%3Dentities%26recherche%3Dandroid_l10n%2Fmozilla-mobile%2Fandroid-components%2Fcomponents%2Fbrowser%2Ferrorpages%2Fsrc%2Fmain%2Fres%2Fvalues%2Fstrings.xml%3Amozac_browser_errorpages_content_crashed_title%26entire_string%3Dentire_string%29.',
            ],
        ];
    }

    #[DataProvider('reportErrorLinkDP')]
    public function testReportErrorLink($a, $b, $c, $d, $e, $f, $g)
    {
        $obj = new ModifiedBugzilla();
        $result = $obj->reportErrorLink($a, $b, $c, $d, $e, $f);
        $this->assertSame($g, $result);
    }
}
