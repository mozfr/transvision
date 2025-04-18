<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Xliff;

require_once __DIR__ . '/../bootstrap.php';

class XliffTest extends TestCase
{
    public function testGetStrings()
    {
        $obj = new Xliff();
        $strings = $obj->getStrings(TEST_FILES . 'xliff/firefox-ios.xliff', 'firefox-ios.xliff', 'firefox_ios');

        // Check total number of strings
        $this
            ->assertCount(15, $strings);

        // Check strings
        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:4c8cc9416b11b77e88809ff17e7f180e'], 'Cronologia di navigazione');

        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:f6b6d1aff4ade5b867d563d74b01a429'], 'Segnalibri pc desktop');

        // Check escaped single straight quotes
        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:e15c1a9a6082aa32623205328418a603'], "Test con \'");

        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:1348465d2e7136641805937598daaeda'], "Test con \\\\\' già escaped");
    }

    public function testGetStringsReference()
    {
        $obj = new Xliff();
        $strings = $obj->getStrings(TEST_FILES . 'xliff/firefox-ios.xliff', 'firefox-ios.xliff', 'firefox_ios', true);

        // Check total number of strings
        $this
            ->assertCount(16, $strings);

        // Check strings
        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:4c8cc9416b11b77e88809ff17e7f180e'], 'Browsing History');

        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:f6b6d1aff4ade5b867d563d74b01a429'], 'Desktop Bookmarks');

        // Check escaped single straight quotes
        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:e15c1a9a6082aa32623205328418a603'], "Test with \'");

        $this
            ->assertSame($strings['firefox_ios/firefox-ios.xliff:1348465d2e7136641805937598daaeda'], "Test with \\\\\' already escaped");
    }

    public static function generateStringID_DP()
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

    #[DataProvider('generateStringID_DP')]
     public function testGenerateStringID($a, $b, $c, $d, $e)
    {
        $obj = new Xliff();
        $this
            ->assertSame($obj->generateStringID($a, $b, $c, $d), $e);
    }
}
