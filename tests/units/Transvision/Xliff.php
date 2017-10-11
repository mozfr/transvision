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
            ->string($strings['firefox_ios/firefox-ios.xliff:ed1347cca60701456f8611e3917b9062'])
                ->isEqualTo('Cronologia di navigazione');

        $this
            ->string($strings['firefox_ios/firefox-ios.xliff:017b147661dfd36460ddb40c7d269649'])
                ->isEqualTo('Segnalibri pc desktop');
    }

    public function generateStringID_DP()
    {
        return [
            [
                'firefox_ios',
                'firefox-ios.xliff',
                'Delete',
                'firefox_ios/firefox-ios.xliff:f2a6c498fb90ee345d997f888fce3b18',
            ],
            [
                'firefox_ios',
                'firefox-ios.xliff',
                'Are you sure you want to clear all of your data? This will also close all open tabs.',
                'firefox_ios/firefox-ios.xliff:401f0b9f25a4e3ea72f8b07ea3800ee4',
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
