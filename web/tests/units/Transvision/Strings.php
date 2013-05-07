<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

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
            ->boolean($obj->endsWith($a,$b))
                ->isEqualTo($c)
        ;
    }
}
