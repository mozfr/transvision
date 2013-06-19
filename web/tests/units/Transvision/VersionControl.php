<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class VersionControl extends atoum\test
{

    /**
     * @dataProvider pathFileDataProvider
     */
    public function testPathFile($a, $b, $c, $d)
    {
        $obj = new \Transvision\VersionControl();
        $this
            ->string($obj->filePath($a, $b, $c))
                ->isEqualTo($d)
        ;
    }

    public function pathFileDataProvider()
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
        );
    }
}
