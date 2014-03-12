<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class VersionControl extends atoum\test
{
    /**
     * @dataProvider hgFileDataProvider
     */
    public function testHgFile($a, $b, $c, $d)
    {
        $obj = new \Transvision\VersionControl();
        $this
            ->string($obj->hgPath($a, $b, $c))
                ->isEqualTo($d)
        ;
    }

    public function hgFileDataProvider()
    {
        return array(
            array(
                'fr',
                'beta',
                'browser/updater/updater.ini:TitleText',
                'http://hg.mozilla.org/releases/l10n/mozilla-beta/fr/file/default/browser/updater/updater.ini'
                ),
            array(
                'es-ES',
                'gaia',
                'apps/settings/settings.properties:usb-tethering',
                'http://hg.mozilla.org/gaia-l10n/es/file/default/apps/settings/settings.properties'
                ),
            array(
                'de',
                'gaia',
                'shared/date/date.properties:month-7-long',
                'http://hg.mozilla.org/gaia-l10n/de/file/default/shared/date/date.properties'
                ),
            array(
                'sr-Cyrl',
                'gaia_1_2',
                'shared/date/date.properties:month-7-long',
                'http://hg.mozilla.org/releases/gaia-l10n/v1_2/sr-Cyrl/file/default/shared/date/date.properties'
                ),
        );
    }

    /**
     * @dataProvider svnFileDataProvider
     */
    public function testSvnFile($a, $b, $c, $d)
    {
        $obj = new \Transvision\VersionControl();
        $this
            ->string($obj->svnPath($a, $b, $c))
                ->isEqualTo($d)
        ;
    }

    public function svnFileDataProvider()
    {
        return array(
            [
                'sr',
                'mozilla_org',
                'mozilla_org/download_button.lang:ab34ff81',
                'http://viewvc.svn.mozilla.org/vc/projects/mozilla.com/trunk/locales/sr/download_button.lang?view=markup'
            ],
            [
                'es-ES',
                'mozilla_org',
                'mozilla_org/firefox/os/faq.lang:c71a7a50',
                'http://viewvc.svn.mozilla.org/vc/projects/mozilla.com/trunk/locales/es-ES/firefox/os/faq.lang?view=markup'
             ],
        );
    }
}
