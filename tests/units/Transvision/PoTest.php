<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Po;

require_once __DIR__ . '/../bootstrap.php';

class PoTest extends TestCase
{
    public function testGetStrings()
    {
        $obj = new Po();

        // Test standard file
        $strings = $obj->getStrings(TEST_FILES . 'po/it_app.po', 'focus_android');

        // Check total number of strings
        $this
            ->assertCount(17, $strings);

        // Check strings
        $this
            ->assertSame($strings['focus_android/it_app.po:3071c8a7530990a564b943a69f4ac652'], 'Cerca o inserisci un indirizzo');

        $this
            ->assertSame($strings['focus_android/it_app.po:0d6ef5cb439f059884232d5680ac138f'], 'Elementi traccianti bloccati');

        // Test plurals
        $strings = $obj->getStrings(TEST_FILES . 'po/ga_app.po', 'focus_android');

        // Check total number of strings
        $this
            ->assertCount(2, $strings);

        // Check strings
        $this
            ->assertSame($strings['focus_android/ga_app.po:b8c8fe8a54683f91f8ed8a54d7f76dec'], 'Thug {0} agus duine amháin eile freagra air');

        $this
            ->assertSame($strings['focus_android/ga_app.po:1626bdedbed9939897d17cdeb4fe5e84'], "Thug {0} agus {1} dhuine eile freagra air\nThug {0} agus {1} dhuine eile freagra air\nThug {0} agus {1} nduine eile freagra air\nThug {0} agus {1} duine eile freagra air");

        // Test .pot file
        $strings = $obj->getStrings(TEST_FILES . 'po/app.pot', 'focus_android', true);

        // Check total number of strings
        $this
            ->assertCount(16, $strings);

        // Check strings
        $this
            ->assertSame($strings['focus_android/app.po:5ad79e8b3a9423d5a964bd6cc2e87000'], 'About');

        $this
            ->assertSame($strings['focus_android/app.po:ebd82695e1086e20fd85d3532397b30f'], 'Open with %1$s');
    }

    public static function generateStringID_DP()
    {
        return [
            [
                'focus_android',
                'app.po',
                'Add to Home screen',
                'focus_android/app.po:1dafea7725862ca854c408f0e2df9c88',
            ],
            [
                'focus_android',
                'app.po',
                'Your browsing history has been erased.',
                'focus_android/app.po:e62a7bce66ff3ed794cf9fb19081a066',
            ],
        ];
    }

    #[DataProvider('generateStringID_DP')]
    public function testGenerateStringID($a, $b, $c, $d)
    {
        $obj = new Po();
        $this
            ->assertSame($obj->generateStringID($a, $b, $c), $d);
    }
}
