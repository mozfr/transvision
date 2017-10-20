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
                ->isEqualTo(14);

        // Check strings
        $this
            ->string($strings['firefox_ios/firefox-ios.xliff:4c8cc9416b11b77e88809ff17e7f180e'])
                ->isEqualTo('Cronologia di navigazione');

        $this
            ->string($strings['firefox_ios/firefox-ios.xliff:f6b6d1aff4ade5b867d563d74b01a429'])
                ->isEqualTo('Segnalibri pc desktop');
    }

    public function generateStringID_DP()
    {
        return [
            [
                'firefox_ios',
                'firefox-ios.xliff',
                'AccountTests/Info.plist',
                'Delete',
                'firefox_ios/firefox-ios.xliff:bb46926d0fcd6d43155f706a22b0f3fc',
            ],
            [
                'firefox_ios',
                'firefox-ios.xliff',
                'DiffentFile/Info.plist',
                'Delete',
                'firefox_ios/firefox-ios.xliff:e3b9ee7a5b6b4e96f70c539d87aff9b0',
            ],
            [
                'firefox_ios',
                'firefox-ios.xliff',
                'Client/3DTouchActions.strings',
                'Are you sure you want to clear all of your data? This will also close all open tabs.',
                'firefox_ios/firefox-ios.xliff:46e4ec3c64a0ce5a5d9c5f8bebd74325',
            ],
        ];
    }

    /**
     * @dataProvider generateStringID_DP
     */
    public function testGenerateStringID($a, $b, $c, $d, $e)
    {
        $obj = new _Xliff();
        $this
            ->string($obj->generateStringID($a, $b, $c, $d))
                ->isEqualTo($e);
    }
}
