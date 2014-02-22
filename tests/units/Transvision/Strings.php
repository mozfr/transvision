<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../web/vendor/autoload.php';

use atoum;

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
}
