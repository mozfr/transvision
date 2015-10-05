<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Xliff as _Xliff;

require_once __DIR__ . '/../bootstrap.php';

class Xliff extends atoum\test
{
    public function testGetStrings()
    {
        $obj = new _Xliff();
        $strings = $obj->getStrings(TEST_FILES . 'xliff/firefox-ios.xliff', 'firefox_ios');

        // Check total number of strings
        $this
            ->integer(count($strings))
                ->isEqualTo(12);

        // Check strings
        $this
            ->string($strings['firefox_ios/Client/ClearPrivateData.strings:3f1f90ab'])
                ->isEqualTo('Cronologia di navigazione');

        $this
            ->string($strings['firefox_ios/Client/BookmarkPanel.strings:d4a1140e'])
                ->isEqualTo('Segnalibri pc desktop');
    }

    public function generateStringID_DP()
    {
        return [
            [
                'firefox_ios',
                'Client/BookmarkPanel.strings',
                'Delete',
                'firefox_ios/Client/BookmarkPanel.strings:1c63676f',
            ],
            [
                'firefox_ios',
                'Client/ClearPrivateData.strings',
                'Are you sure you want to clear all of your data? This will also close all open tabs.',
                'firefox_ios/Client/ClearPrivateData.strings:0f4d892c',
            ],
        ];
    }

    /**
     * @dataProvider generateStringID_DP
     */
    public function testGenerateStringID($a, $b, $c, $d)
    {
        $obj = new _Xliff();
        $this
            ->string($obj->generateStringID($a, $b, $c))
                ->isEqualTo($d);
    }
}
