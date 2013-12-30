<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class Json extends atoum\test
{
    public function fetchDataProvider()
    {
        $local = __DIR__ . '/data/';
        return array(
            array($local . '/empty.json', array()),
            array($local . '/test1.json', array('user_name' => 'Pascal'))
        );
    }

    /**
     * @dataProvider fetchDataProvider
     */
    public function testFetch($a, $b)
    {
        $obj = new \Transvision\Json();
        $this
            ->array($obj->fetch($a))
                ->isEqualTo($b)
        ;
    }



    public function outputDataProvider()
    {
        return array(
            // Simple json output
            array(
                ['foo' => 'bar'],
                false,
                false,
                true,
                false,
                '{"foo":"bar"}'
                ),
            // Simple json output without conversion
            array(
                '{"foo":"bar"}',
                false,
                false,
                false,
                false,
                '{"foo":"bar"}'
                ),
            // Pretty json output
            array(
                ['foo' => 'bar'],
                false,
                true,
                true,
                false,
                "{\n    " . '"foo": "bar"' . "\n}"
                ),
            // Simple jsonp output
            array(
                ['foo' => 'bar'],
                'toto',
                false,
                true,
                false,
                'toto({"foo":"bar"})'
                ),
            // Pretty jsonp output
            array(
                ['foo' => 'bar'],
                'toto',
                true,
                true,
                false,
                "toto({\n    " . '"foo": "bar"' . "\n})"
                ),
        );
    }

    /**
     * @dataProvider outputDataProvider
     */
    public function testOutput($a, $b, $c, $d, $e, $f)
    {
        $obj = new \Transvision\Json();
        $this
            ->string($obj->output($a, $b, $c, $d, $e))
                ->isEqualTo($f)
        ;
    }

}
