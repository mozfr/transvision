<?php
namespace tests\Transvision;

use Cache\Cache as _Cache;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Utils;

require_once __DIR__ . '/../bootstrap.php';

class UtilsTest extends TestCase
{
    public static function secureTextDP()
    {
        return [
            ["achat des couteaux\nsuisses", 'achat des couteaux suisses'],
            ['<b>foo</b>', '&#60;b&#62;foo&#60;/b&#62;'],
        ];
    }

    #[DataProvider('secureTextDP')]
    public function testSecureText($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->secureText($a), $b);
    }

    public static function uniqueWordsDP()
    {
        return [
            [
                'achat des couteaux suisses',
                ['couteaux', 'suisses', 'achat', 'des'],
            ],
            [
                'achat     des  couteaux suisses   ',
                ['couteaux', 'suisses', 'achat', 'des'],
            ],
            [
                'Set a cookie',
                ['cookie', 'Set'],
            ],
        ];
    }

    #[DataProvider('uniqueWordsDP')]
    public function testUniqueWords($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->uniqueWords($a), $b);
    }

    public static function checkboxDefaultOption1DP()
    {
        return [
            ['es-ES', 'es-ES', ' checked="checked"'],
        ];
    }

    #[DataProvider('checkboxDefaultOption1DP')]
    public function testCheckboxDefaultOption1($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->checkboxDefaultOption($a, $b), $c);
    }

    public static function checkboxDefaultOption2DP()
    {
        return [
            ['es-ES', 'en-US', false],
        ];
    }

    #[DataProvider('checkboxDefaultOption2DP')]
    public function testCheckboxDefaultOption2($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->checkboxDefaultOption($a, $b), $c);
    }

    public static function checkBoxState2DP()
    {
        $_GET['t2t'] = 'somedata';

        return [
            [$_GET['t2t'], 't2t'],
        ];
    }

    #[DataProvider('checkBoxState2DP')]
    public function testCheckboxState2($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->checkboxState($a, $b), ' checked="checked"');
    }

    public static function checkBoxState3DP()
    {
        return [
            ['some string', null],
        ];
    }

    #[DataProvider('checkBoxState3DP')]
    public function testCheckboxState3($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->checkboxState($a, $b), ' checked="checked"');
    }

    public static function getHtmlSelectOptionsDP()
    {
        return [
            [
                ['strings' => 'Strings', 'entities' => 'Entities', 'strings_entities' => 'Strings & Entities'],
                'strings_entities',
                true,
            ],
        ];
    }

    #[DataProvider('getHtmlSelectOptionsDP')]
    public function testGetHtmlSelectOptions($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertEqualsCanonicalizing($obj->getHtmlSelectOptions($a, $b, $c), '<option value=strings>Strings</option><option value=entities>Entities</option><option selected value=strings_entities>Strings & Entities</option>');
    }

    public static function cleanStringDP()
    {
        return [
            [
                [],
                '',
            ],
            [
                "don\u0027t strip unicode escaped chars",
                "don\u0027t strip unicode escaped chars",
            ],
        ];
    }

    #[DataProvider('cleanStringDP')]
    public function testCleanString($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->cleanString($a), $b);
    }

    public static function checkAbnormalStringLengthDP()
    {
        return [
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème. Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème.',
                'large',
            ],
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et',
                'small',
            ],
            [
                'Le système de marque-pages et',
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'large',
            ],
            [
                'pages web',
                'pa',
                'small',
            ],
        ];
    }

    #[DataProvider('checkAbnormalStringLengthDP')]
    public function testCheckAbnormalStringLength($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertEqualsCanonicalizing($obj->checkAbnormalStringLength($a, $b), $c);
    }

    public static function checkAbnormalStringLength2DP()
    {
        return [
            ['Add Bookmarks', 'Ajouter des marque-pages', false],
            ['Add Bookmarks', '', false],
        ];
    }

    #[DataProvider('checkAbnormalStringLength2DP')]
    public function testCheckAbnormalStringLength2($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertEqualsCanonicalizing($obj->checkAbnormalStringLength($a, $b), $c);
    }

    public function testGetRepoStrings()
    {
        $obj = new Utils();
        $this
            ->assertContains('Ouvrir dans le Finder', $obj->getRepoStrings('fr', 'gecko_strings'));

        $results = $obj->getRepoStrings('fr', 'gecko_strings', false);
        $this
            ->assertArrayHasKey('gecko_strings', $results);
        $this
            ->assertContains('Ouvrir dans le Finder', $results['gecko_strings']);
    }

    public static function flattenTMXDP()
    {
        return [
            [
                [
                    'test_repo' => [
                        'entity'  => 'text',
                        'entity2' => 'text2',
                    ],
                ],
                [
                    ['test_repo', 'entity', 'text'],
                    ['test_repo', 'entity2', 'text2'],
                ],
            ],
        ];
    }

    #[DataProvider('flattenTMXDP')]
    public function testFlattenTMX($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->flattenTMX($a), $b);
    }

    public static function getOrSetDP()
    {
        return [
            [
                ['en-US', 'ar', 'fr'],
                'fr',
                'en-US',
                'fr',
                ],
            [
                ['fa', 'es-ES', 'fr'],
                'it',
                'en-US',
                'en-US',
                ],
            [
                ['fa', 'es-ES', 'FR'],
                'fr',
                'en-US',
                'en-US',
                ],
            [
                ['fa', 'es-ES', 'FR'],
                'es',
                'en-US',
                'en-US',
                ],
        ];
    }

    #[DataProvider('getOrSetDP')]
    public function testGetOrSet($a, $b, $c, $d)
    {
        $obj = new Utils();
        $this
            ->assertEqualsCanonicalizing($obj->getOrSet($a, $b, $c), $d);
    }

    public function afterTestMethod($method)
    {
        switch ($method) {
            case 'testGetRepoStrings':
                // Destroy cached files created by getRepoStrings()
                $obj = new _Cache();
                $obj->flush();
            break;
        }
    }

    public static function redYellowGreenDP()
    {
        return [
            ['34.13', '255,168,0'],
            ['99.92', '10,255,0'],
        ];
    }

    #[DataProvider('redYellowGreenDP')]
    public function testRedYellowGreen($a, $b)
    {
        $obj = new Utils();
        $this
            ->assertEqualsCanonicalizing($obj->redYellowGreen($a), $b);
    }

    public static function pluralizeDP()
    {
        return [
            ['42', 'test', '42 tests'],
            ['1', 'test', '1 test'],
        ];
    }

    #[DataProvider('pluralizeDP')]
    public function testPluralize($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->pluralize($a, $b), $c);
    }

    public static function agoDP()
    {
        $data = [
            [
                (new DateTime())->modify('-30 seconds'),
                (new DateTime()),
                '30 seconds ago',
            ],
            [
                (new DateTime())->modify('-10 hours'),
                (new DateTime()),
                '10 hours ago',
            ],
            [
                (new DateTime())->modify('-1 day'),
                (new DateTime()),
                '1 day ago',
            ],
            [
                (new DateTime())->modify('+2 months +1 day'),
                (new DateTime()),
                '2 months',
            ],
            [
                (new DateTime())->modify('+1 year +1 day'),
                (new DateTime()),
                '1 year',
            ],
         ];

        return $data;
    }

    #[DataProvider('agoDP')]
    public function testAgo($a, $b, $c)
    {
        $obj = new Utils();
        $this
            ->assertSame($obj->ago($a, $b), $c);
    }

    public static function redirectToAPIDP()
    {
        return [
            [
                '/string/?entity=browser/chrome/browser/downloads/downloads.properties:stateStarting&repo=gecko_strings',
                '/string/?entity=browser/chrome/browser/downloads/downloads.properties:stateStarting&repo=gecko_strings&json',
            ],
            [
                '/v1/versions/',
                '/v1/versions/?json',
            ],
            [
                '/?recherche=home&repo=gecko_strings&sourcelocale=en-US&locale=fr&search_type=strings',
                '/?recherche=home&repo=gecko_strings&sourcelocale=en-US&locale=fr&search_type=strings&json',
            ],
        ];
    }

    #[DataProvider('redirectToAPIDP')]
    public function testRedirectToAPI($a, $b)
    {
        $obj = new Utils();
        $_SERVER['REQUEST_URI'] = $a;
        $_SERVER['QUERY_STRING'] = isset(parse_url($a)['query'])
            ? parse_url($a)['query']
            : null;
        $this
            ->assertSame($obj->redirectToAPI(), $b);
    }
    public static function APIPromotionDP()
    {
        return [
            [
                'en-US',
                'fr',
                '/?recherche=test&repo=gecko_strings&sourcelocale=fr&locale=en-US&search_type=strings',
                '/?recherche=test&repo=gecko_strings&sourcelocale=en-US&locale=fr&search_type=strings&json=true',
            ],
            [
                'it',
                'en-US',
                '/?recherche=Cookies&repo=gecko_strings&sourcelocale=en-US&locale=it&search_type=strings_entities&case_sensitive=case_sensitive&each_word=each_word&entire_string=entire_string',
                '/?recherche=Cookies&repo=gecko_strings&sourcelocale=it&locale=en-US&search_type=strings_entities&case_sensitive=case_sensitive&each_word=each_word&entire_string=entire_string&json=true',
            ],
        ];
    }
    #[DataProvider('APIPromotionDP')]
    public function testAPIPromotion($a, $b, $c, $d)
    {
        $obj = new Utils();
        $_SERVER['REQUEST_URI'] = $c;
        $_SERVER['QUERY_STRING'] = isset(parse_url($c)['query'])
            ? parse_url($c)['query']
            : null;
        $this
            ->assertSame($obj->APIPromotion($a, $b), $d);
    }

    public function testGetScriptPerformances()
    {
        $obj = new Utils();
        $data = $obj->getScriptPerformances();
        $this
            ->assertCount(3, $data);
        $this
            ->assertIsInt($data[0]);
        $this
            ->assertIsFloat($data[1]);
        $this
            ->assertIsFloat($data[2]);
    }
}
