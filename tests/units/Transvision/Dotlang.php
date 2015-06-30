<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Dotlang as _Dotlang;

require_once __DIR__ . '/../bootstrap.php';

class Dotlang extends atoum\test
{
    public function getLangFilesListDP()
    {
        return [
            [
                TEST_FILES,
                [
                    TEST_FILES . 'langfiles/foo/bar/baz.lang',
                    TEST_FILES . 'langfiles/toto.lang',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getLangFilesListDP
     */
    public function testGetLangFilesList($a, $b)
    {
        $obj = new _Dotlang();
        $this
            ->array($obj->getLangFilesList($a))
                ->isEqualTo($b)
        ;
    }

    public function getFileDP()
    {
        return [
            [
                TEST_FILES . 'langfiles/toto.lang',
                [
                    '## I am a tag ##',
                    '## NOTE: I am metadata',
                    '# I am a comment',
                    ';Browser',
                    'Navigateur',
                    ';Mail',
                    'Courrier',
                    ';Empty string',
                    '# another comment',
                    ';Hello',
                    'Bonjour',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getFileDP
     */
    public function testGetFile($a, $b)
    {
        $obj = new _Dotlang();
        $this
            ->array($obj->getFile($a))
                ->isEqualTo($b);
    }

    public function getStringsDP()
    {
        return [
            [
                TEST_FILES . 'langfiles/toto.lang',
                false,
                [
                    'Browser'      => 'Navigateur',
                    'Mail'         => 'Courrier',
                    'Hello'        => 'Bonjour',
                    'Empty string' => '',
                ],
            ],
            [
                TEST_FILES . 'langfiles/toto.lang',
                true,
                [
                    'Browser'      => 'Browser',
                    'Mail'         => 'Mail',
                    'Hello'        => 'Hello',
                    'Empty string' => 'Empty string',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getStringsDP
     */
    public function testGetStrings($a, $b, $c)
    {
        $obj = new _Dotlang();
        $this
            ->array($obj->getStrings($a, $b))
                ->isEqualTo($c);
    }

    public function generateStringID_DP()
    {
        return [
            ['mozilla_org/main.lang', 'Back to home page', 'mozilla_org/main.lang:8295a9f6'],
        ];
    }

    /**
     * @dataProvider generateStringID_DP
     */
    public function testGenerateStringID($a, $b, $c)
    {
        $obj = new _Dotlang();
        $this
            ->string($obj->generateStringID($a, $b))
                ->isEqualTo($c);
    }
}
