<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Dotlang;

require_once __DIR__ . '/../bootstrap.php';

class DotlangTest extends TestCase
{
    public static function getLangFilesListDP()
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

    #[DataProvider('getLangFilesListDP')]
    public function testGetLangFilesList($a, $b)
    {
        $obj = new Dotlang();
        $this
            ->assertSame($obj->getLangFilesList($a), $b)
        ;
    }

    public static function getFileDP()
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

    #[DataProvider('getFileDP')]
    public function testGetFile($a, $b)
    {
        $obj = new Dotlang();
        $this
            ->assertSame($obj->getFile($a), $b);
    }

    public static function getStringsDP()
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

    #[DataProvider('getStringsDP')]
    public function testGetStrings($a, $b, $c)
    {
        $obj = new Dotlang();
        $this
            ->assertEqualsCanonicalizing($obj->getStrings($a, $b), $c);
    }

    public static function generateStringID_DP()
    {
        return [
            ['mozilla_org/main.lang', 'Back to home page', 'mozilla_org/main.lang:a922fff6646b0eb4db05fe6e0894af7d'],
        ];
    }

    #[DataProvider('generateStringID_DP')]
    public function testGenerateStringID($a, $b, $c)
    {
        $obj = new Dotlang();
        $this
            ->assertSame($obj->generateStringID($a, $b), $c);
    }
}
