<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Po as _Po;

require_once __DIR__ . '/../bootstrap.php';

class Po extends atoum\test
{
    public function testGetStrings()
    {
        $obj = new _Po();

        // Test standard file
        $strings = $obj->getStrings(TEST_FILES . 'po/it_app.po', 'focus_android');

        // Check total number of strings
        $this
            ->integer(count($strings))
                ->isEqualTo(17);

        // Check strings
        $this
            ->string($strings['focus_android/it_app.po:3071c8a7530990a564b943a69f4ac652'])
                ->isEqualTo('Cerca o inserisci un indirizzo');

        $this
            ->string($strings['focus_android/it_app.po:0d6ef5cb439f059884232d5680ac138f'])
                ->isEqualTo('Elementi traccianti bloccati');

        // Test plurals
        $strings = $obj->getStrings(TEST_FILES . 'po/ga_app.po', 'focus_android');

        // Check total number of strings
        $this
            ->integer(count($strings))
                ->isEqualTo(2);

        // Check strings
        $this
            ->string($strings['focus_android/ga_app.po:b8c8fe8a54683f91f8ed8a54d7f76dec'])
                ->isEqualTo('Thug {0} agus duine amháin eile freagra air');

        $this
            ->string($strings['focus_android/ga_app.po:1626bdedbed9939897d17cdeb4fe5e84'])
                ->isEqualTo("Thug {0} agus {1} dhuine eile freagra air\nThug {0} agus {1} dhuine eile freagra air\nThug {0} agus {1} nduine eile freagra air\nThug {0} agus {1} duine eile freagra air");

        // Test .pot file
        $strings = $obj->getStrings(TEST_FILES . 'po/app.pot', 'focus_android', true);

        // Check total number of strings
        $this
            ->integer(count($strings))
                ->isEqualTo(16);

        // Check strings
        $this
            ->string($strings['focus_android/app.po:5ad79e8b3a9423d5a964bd6cc2e87000'])
                ->isEqualTo('About');

        $this
            ->string($strings['focus_android/app.po:ebd82695e1086e20fd85d3532397b30f'])
                ->isEqualTo('Open with %1$s');
    }

    public function generateStringID_DP()
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

    /**
     * @dataProvider generateStringID_DP
     */
    public function testGenerateStringID($a, $b, $c, $d)
    {
        $obj = new _Po();
        $this
            ->string($obj->generateStringID($a, $b, $c))
                ->isEqualTo($d);
    }
}
