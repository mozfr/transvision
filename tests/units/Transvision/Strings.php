<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class Strings extends atoum\test
{
    public function mtrimDataProvider()
    {
        return array('Le cheval  blanc ');
    }

    /**
     * @dataProvider mtrimDataProvider
     */
    public function testMtrim($a)
    {
        $obj = new \Transvision\Strings();
        $this
            ->string($obj->mtrim($a))
                ->isEqualTo('Le cheval blanc');
    }

    public function startsWithDataProvider()
    {
        return array(
            array(
                'it is raining',
                'it',
                true
                ),
            array(
                ' foobar starts with a nasty space',
                'foobar',
                false
            )
        );
    }
    /**
     * @dataProvider startsWithDataProvider
     */
    public function testStartsWith($a, $b, $c)
    {
        $obj = new \Transvision\Strings();
        $this
            ->boolean($obj->startsWith($a,$b))
                ->isEqualTo($c)
        ;
    }


    public function endsWithDataProvider()
    {
        return array(
            array(
                'it is raining',
                'ing',
                true
                ),
            array(
                'foobar ends with a nasty space ',
                'space',
                false
            )
        );
    }
    /**
     * @dataProvider endsWithDataProvider
     */
    public function testEndsWith($a, $b, $c)
    {
        $obj = new \Transvision\Strings();
        $this
            ->boolean($obj->endsWith($a, $b))
                ->isEqualTo($c)
        ;
    }

    public function inStringWithDataProvider()
    {
        return array(
            array(
                'La maison est blanche',
                'blanche',
                true
                ),
            array(
                'Le ciel est bleu',
                'noir',
                false
            ),
            array(
                'Le ciel est bleu',
                'Le',
                true
            )
        );
    }
    /**
     * @dataProvider inStringWithDataProvider
     */
    public function testInString($a, $b, $c)
    {
        $obj = new \Transvision\Strings();
        $this
            ->boolean($obj->inString($a, $b))
                ->isEqualTo($c)
        ;
    }

    public function multipleStringReplacedataProvider()
    {
        return array(
            array(
                array(
                    ' '        => '<span class="highlight-gray" title="Non breakable space"> </span>', // nbsp highlight
                    ' '        => '<span class="highlight-red" title="Thin space"> </span>', // thin space highlight
                    '…'        => '<span class="highlight-gray">…</span>', // right ellipsis highlight
                    '&hellip;' => '<span class="highlight-gray">…</span>', // right ellipsis highlight
                ),
                '&hellip;  …',
                '<span class="highlight-gray">…</span><span class="highlight-gray" title="Non breakable space"> </span><span class="highlight-red" title="Thin space"> </span><span class="highlight-gray">…</span>'
            )
        );
    }

    /**
     * @dataProvider multipleStringReplacedataProvider
     */
    public function testmultipleStringReplace($a, $b, $c)
    {
        $obj = new \Transvision\Strings();
        $this
            ->string($obj->multipleStringReplace($a, $b))
                ->isEqualTo($c)
        ;
    }

    public function getLengthDataProvider()
    {
        return array(
            ['Le cheval  blanc ', 17],
            ['મારુ ઘર પાનું બતાવો', 19],
        );
    }

    /**
     * @dataProvider getLengthDataProvider
     */
    public function testGetLength($a, $b)
    {
        $obj = new \Transvision\Strings();
        $this
            ->integer($obj->getLength($a))
                ->isEqualTo($b);
    }

    public function getSimilarDataProvider()
    {
        return array(
            array(
                'maison',
                ['maçon', 'melon', 'blanche', 'navet'],
                1,
                ['maçon'],
                ),
            array(
                'toto',
                ['maçon', 'melon', 'blanche', 'navet'],
                2,
                ['navet', 'melon'],
                )
        );
    }

    /**
     * @dataProvider getSimilarDataProvider
     */
    public function testGetSimilar($a, $b, $c, $d)
    {
        $obj = new \Transvision\Strings();
        $this
            ->array($obj->getSimilar($a, $b, $c))
                ->isEqualTo($d);
    }


    public function getLevenshteinUTF8DP()
    {
        return array(
            ['notre', 'nôtre', 1],
            ['웹', '으', 1],
            ['हिस्सा', 'हमारे', 5],
            ['hello', 'melon', 3],
            ['കട', 'കടല', 1],
            ['കട', 'കല', 1],
            ['കട', 'കടി', 1],
        );
    }

    /**
     * @dataProvider getLevenshteinUTF8DP
     */
    public function testLevenshteinUTF8($a, $b, $c)
    {
        $obj = new \Transvision\Strings();
        $this
            ->integer($obj->LevenshteinUTF8($a, $b))
                ->isEqualTo($c);
    }

    public function getLevenshteinQualityDP()
    {
        // We use divisions so as to ave real precise numbers for float comparizon
        return array(
            ['notre', 'nôtre', (float) 80],
            ['웹', '으', (float) 0],
            ['हिस्सा', 'हमारे', (float) 100/6],
            ['hello', 'melon', (float) 40],
            ['കട', 'കടല', (float) 100/1.5],
            ['കട', 'കല', (float) 50],
            ['കട', 'കടി', (float) 100/1.5],
        );
    }

    /**
     * @dataProvider getLevenshteinQualityDP
     */
    public function testLevenshteinQuality($a, $b, $c)
    {
        $obj = new \Transvision\Strings();
        $this
            ->float($obj->levenshteinQuality($a, $b))
                ->isNearlyEqualTo($c);
    }
}
