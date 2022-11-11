<?php
namespace tests\units\Transvision;

use atoum\atoum;
use Cache\Cache as _Cache;
use DateTime;
use Transvision\Utils as _Utils;

require_once __DIR__ . '/../bootstrap.php';

class Utils extends atoum\test
{
    public function secureTextDP()
    {
        return [
            ["achat des couteaux\nsuisses", 'achat des couteaux suisses'],
            ['<b>foo</b>', '&#60;b&#62;foo&#60;/b&#62;'],
        ];
    }

    /**
     * @dataProvider secureTextDP
     */
    public function testSecureText($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->secureText($a))
                ->isEqualTo($b);
    }

    public function uniqueWordsDP()
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

    /**
     * @dataProvider uniqueWordsDP
     */
    public function testUniqueWords($a, $b)
    {
        $obj = new _Utils();
        $this
            ->array($obj->uniqueWords($a))
                ->isEqualTo($b);
    }

    public function checkboxDefaultOption1DP()
    {
        return [
            ['es-ES', 'es-ES', ' checked="checked"'],
        ];
    }

    /**
     * @dataProvider checkboxDefaultOption1DP
     */
    public function testCheckboxDefaultOption1($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkboxDefaultOption2DP()
    {
        return [
            ['es-ES', 'en-US', false],
        ];
    }

    /**
     * @dataProvider checkboxDefaultOption2DP
     */
    public function testCheckboxDefaultOption2($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->boolean($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkBoxState1DP()
    {
        $_GET['t2t'] = 'somedata';

        return [
            [$_GET['t2t'], ''],
        ];
    }

    /**
     * @dataProvider checkBoxState1DP
     */
    public function testCheckboxState1($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' disabled="disabled"');
    }

    public function checkBoxState2DP()
    {
        $_GET['t2t'] = 'somedata';

        return [
            [$_GET['t2t'], 't2t'],
        ];
    }

    /**
     * @dataProvider checkBoxState2DP
     */
    public function testCheckboxState2($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function checkBoxState3DP()
    {
        return [
            ['some string', null],
        ];
    }

    /**
     * @dataProvider checkBoxState3DP
     */
    public function testCheckboxState3($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function getHtmlSelectOptionsDP()
    {
        return [
            [
                ['strings' => 'Strings', 'entities' => 'Entities', 'strings_entities' => 'Strings & Entities'],
                'strings_entities',
                true,
            ],
        ];
    }

    /**
     * @dataProvider getHtmlSelectOptionsDP
     */
    public function testGetHtmlSelectOptions($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->getHtmlSelectOptions($a, $b, $c))
                ->isEqualTo('<option value=strings>Strings</option><option value=entities>Entities</option><option selected value=strings_entities>Strings & Entities</option>');
    }

    public function cleanStringDP()
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

    /**
     * @dataProvider cleanStringDP
     */
    public function testCleanString($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->cleanString($a))
                ->isEqualTo($b);
    }

    public function checkAbnormalStringLengthDP()
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

    /**
     * @dataProvider checkAbnormalStringLengthDP
     */
    public function testCheckAbnormalStringLength($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkAbnormalStringLength($a, $b))
                ->isEqualTo($c);
    }

    public function checkAbnormalStringLength2DP()
    {
        return [
            ['Add Bookmarks', 'Ajouter des marque-pages', false],
            ['Add Bookmarks', '', false],
        ];
    }

    /**
     * @dataProvider checkAbnormalStringLength2DP
     */
    public function testCheckAbnormalStringLength2($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->boolean($obj->checkAbnormalStringLength($a, $b))
                ->isEqualTo($c);
    }

    public function getRepoStringsDP()
    {
        return [
            ['fr', 'gecko_strings', 'Ouvrir dans le Finder'],
        ];
    }

    /**
     * @dataProvider getRepoStringsDP
     */
    public function testGetRepoStrings($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->array($obj->getRepoStrings($a, $b))
                ->contains($c);
    }

    public function flattenTMXDP()
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

    /**
     * @dataProvider flattenTMXDP
     */
    public function testFlattenTMX($a, $b)
    {
        $obj = new _Utils();
        $this
            ->array($obj->flattenTMX($a))
                ->isEqualTo($b);
    }

    public function getOrSetDP()
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

    /**
     * @dataProvider getOrSetDP
     */
    public function testGetOrSet($a, $b, $c, $d)
    {
        $obj = new _Utils();
        $this
            ->string($obj->getOrSet($a, $b, $c))
                ->isEqualTo($d);
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

    public function redYellowGreenDP()
    {
        return [
            ['34.13', '255,168,0'],
            ['99.92', '10,255,0'],
        ];
    }

    /**
     * @dataProvider redYellowGreenDP
     */
    public function testRedYellowGreen($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->redYellowGreen($a))
                ->contains($b);
    }

    public function pluralizeDP()
    {
        return [
            ['42', 'test', '42 tests'],
            ['1', 'test', '1 test'],
        ];
    }

    /**
     * @dataProvider pluralizeDP
     */
    public function testPluralize($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->pluralize($a, $b))
                ->contains($c);
    }

    public function agoDP()
    {
        $data = [
            [
                (new DateTime())->modify('-2 seconds'),
                (new DateTime()),
                '2 seconds ago',
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

        $data = array_merge(
            $data,
            [
                [
                    (new DateTime())->modify('-2 seconds'),
                    '',
                    '2 seconds ago',
                ],
                [
                    (new DateTime())->modify('-1 hour'),
                    '',
                    '1 hour ago',
                ],
            ]
        );

        return $data;
    }

    /**
     * @dataProvider agoDP
     */
    public function testAgo($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->ago($a, $b))
                ->contains($c);
    }

    public function redirectToAPIDP()
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

    /**
     * @dataProvider redirectToAPIDP
     */
    public function testRedirectToAPI($a, $b)
    {
        $obj = new _Utils();
        $_SERVER['REQUEST_URI'] = $a;
        $_SERVER['QUERY_STRING'] = isset(parse_url($a)['query'])
            ? parse_url($a)['query']
            : null;
        $this
            ->string($obj->redirectToAPI())
                ->isEqualTo($b);
    }
    public function APIPromotionDP()
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
    /**
     * @dataProvider APIPromotionDP
     */
    public function testAPIPromotion($a, $b, $c, $d)
    {
        $obj = new _Utils();
        $_SERVER['REQUEST_URI'] = $c;
        $_SERVER['QUERY_STRING'] = isset(parse_url($c)['query'])
            ? parse_url($c)['query']
            : null;
        $this
            ->string($obj->APIPromotion($a, $b))
                ->isEqualTo($d);
    }

    public function testGetScriptPerformances()
    {
        $obj = new _Utils();
        $data = $obj->getScriptPerformances();
        $this
            ->array($data)
                ->size->isEqualTo(3)
            ->integer($data[0])
            ->float($data[1])
            ->float($data[2]);
    }
}
