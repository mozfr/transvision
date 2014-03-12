<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class Dotlang extends atoum\test
{
    public function getLangFilesListDP()
    {
        $path = realpath(__DIR__ . '/../../../tests/testfiles/');

        return array(
            [
                $path,
                [
                    $path . '/langfiles/foo/bar/baz.lang',
                    $path . '/langfiles/toto.lang',
                ]
            ]
        );
    }

    /**
     * @dataProvider getLangFilesListDP
     */
    public function testGetLangFilesList($a, $b)
    {
        $obj = new \Transvision\Dotlang();
        $this
            ->array($obj->getLangFilesList($a))
                ->isEqualTo($b)
        ;
    }

    public function getFileDP()
    {
        $test_file = __DIR__ . '/../../testfiles/langfiles/toto.lang';

        return array(
            [
                $test_file,
                [
                    '## I am a tag ##',
                    '## NOTE: I am metadata',
                    '# I am a comment',
                    ';Browser',
                    'Navigateur',
                    ';Mail',
                    'Courrier',
                    '# another comment',
                    ';Hello',
                    'Bonjour',
                ]
            ]
        );
    }

    /**
     * @dataProvider getFileDP
     */
    public function testGetFile($a, $b)
    {
        $obj = new \Transvision\Dotlang();
        $this
            ->array($obj->getFile($a))
                ->isEqualTo($b)
        ;
    }

    public function getStringsDP()
    {
        $test_file = __DIR__ . '/../../testfiles/langfiles/toto.lang';

        return array(
            [
                $test_file,
                [
                    'Browser' => 'Navigateur',
                    'Mail'    => 'Courrier',
                    'Hello'   => 'Bonjour',
                ]
            ]
        );
    }

    /**
     * @dataProvider getStringsDP
     */
    public function testGetStrings($a, $b)
    {
        $obj = new \Transvision\Dotlang();
        $this
            ->array($obj->getStrings($a))
                ->isEqualTo($b)
        ;
    }

    public function generateStringID_DP()
    {

        return array(
            [
                'mozilla_org/main.lang', 'Back to home page', 'mozilla_org/main.lang:8295a9f6'
            ],
        );
    }

    /**
     * @dataProvider generateStringID_DP
     */
    public function testGenerateStringID($a, $b, $c)
    {
        $obj = new \Transvision\Dotlang();
        $this
            ->string($obj->generateStringID($a, $b))
                ->isEqualTo($c)
        ;
    }
}
