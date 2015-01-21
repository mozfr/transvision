<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\API as _API;

require_once __DIR__ . '/../bootstrap.php';

class API extends atoum\test
{
    public function getParametersDP()
    {
        return [
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Bookmark/',
                ["v1", "tm", "release", "en-US", "fr", "Bookmark"],
            ],
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Vive%20le%20vent/',
                ["v1", "tm", "release", "en-US", "fr", "Vive le vent"],
            ],
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/%20trailing%20spaces%20',
                ["v1", "tm", "release", "en-US", "fr", "trailing spaces"],
            ],
        ];
    }

    /**
     * @dataProvider getParametersDP
     */
    public function testGetParameters($a, $b)
    {
        $url = parse_url($a);
        $obj = new _API($url);
        $this
            ->array($obj->getParameters($url['path']))
                ->isEqualTo($b);
    }

    public function getExtraParametersDP()
    {
        return [
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Bookmark/?foo=bar',
                ['foo' => 'bar'],
            ],
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Bookmark/?foo',
                ['foo' => ''],
            ],
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Bookmark/?foo=',
                ['foo' => ''],
            ],
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Bookmark/?foo=&bar=10',
                ['foo' => '', 'bar' => 10],
            ],
            [
                'http://foobar.com/api/v1/tm/release/en-US/fr/Bookmark/?foo=bar&foo2=bar2',
                ['foo' => 'bar', 'foo2' => 'bar2'],
            ],
        ];
    }

    /**
     * @dataProvider getExtraParametersDP
     */
    public function testGetExtraParameters($a, $b)
    {
        $url = parse_url($a);
        $obj = new _API($url);
        $this
            ->array($obj->getExtraParameters($url['query']))
                ->isEqualTo($b);
    }

    public function isValidRequestDP()
    {
        return [
            // General
            ['http://foobar/api/v1/tm/central/en-US/fr/Bookmark/', true],
            ['http://foobar/api/v1/', false], // Not enough parameters
            ['http://foobar/api/wrong_version/tm/central/en-US/fr/Bookmark/', false],
            ['http://foobar/api/v1/wrong_service/central/en-US/fr/hello world', false],

            // entity service
            ['http://foobar/api/v1/entity/central/?id=myid', true],
            ['http://foobar/api/v1/entity/wrong_repo/', false],
            ['http://foobar/api/v1/entity/central/', false], // Missing id

            // locales service
            ['http://foobar/api/v1/locales/', false], // Not enough parameters
            ['http://foobar/api/v1/locales/wrong_repo/', false],

            // search service
            ['http://foobar/api/v1/search/strings/central/en-US/fr/Add%20%20Bookmarks/', true],
            ['http://foobar/api/v1/search/entities/central/en-US/fr/edit-Bookmark/', true],
            ['http://foobar/api/v1/search/wrong_search_type/central/en-US/fr/edit-Bookmark/', false],
            ['http://foobar/api/v1/search/strings/central/en-US/fr/', false], // Not enough parameters
            ['http://foobar/api/v1/search/strings/wrong_repo/en-US/fr/', false],
            ['http://foobar/api/v1/search/strings/central/wrong_source/fr/', false],
            ['http://foobar/api/v1/search/strings/central/en-US/wrong_target/', false],

            // tm service
            ['http://foobar/api/v1/tm/central/en-US/fr/', false], // Not enough parameters
            ['http://foobar/api/v1/tm/wrong_repo/en-US/fr/hello world', false],
            ['http://foobar/api/v1/tm/central/wrong_source/fr/hello world', false],
            ['http://foobar/api/v1/tm/central/en-US/wrong_target/hello world', false],

            // repositories service
            ['http://foobar/api/v1/repositories/', true],
            ['http://foobar/api/v1/repositories/fr/', true],
            ['http://foobar/api/v1/repositories/foobar/', false],
            ['http://foobar/api/v1/repositories/en-US/', true],

            // versions service
            ['http://foobar/api/versions/', true],
        ];
    }

    /**
     * @dataProvider isValidRequestDP
     */
    public function testIsValidRequest($a, $b)
    {
        $url = parse_url($a);
        $obj = new _API($url);
        $obj->logging = false; // logging interfers with Atoum
        $this
            ->boolean($obj->isValidRequest())
                ->isEqualTo($b);
    }

    public function getServiceDP()
    {
        return [
            ['http://foobar/api/v1/', 'Invalid service'],
            ['http://foobar/api/v1/wrong_service/central/en-US/fr/hello world', 'Invalid service'],
            ['http://foobar/api/wrong_version/tm/central/en-US/fr/Bookmark/', true],
            ['http://foobar/api/v1/entity/central/?id=myid', true],
            ['http://foobar/api/v1/locales/', true],
            ['http://foobar/api/v1/search/strings/central/en-US/fr/Add%20%20Bookmarks/', true],
            ['http://foobar/api/v1/tm/central/en-US/fr/', true],
            ['http://foobar/api/versions/', true],
        ];
    }

    /**
     * @dataProvider getServiceDP
     */
    public function testGetService($a, $b)
    {
        $url = parse_url($a);
        $obj = new _API($url);
        $obj->logging = false; // logging interfers with Atoum
        $this
            ->variable($obj->getService())
                ->isEqualTo($b);
    }
}
