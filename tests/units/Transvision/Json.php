<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

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
            array(
                ['foo' => 'bar'],
                false,
                false,
                '{"foo":"bar"}'
                ),
            array(
                ['foo' => 'bar'],
                false,
                true,
                "{\n    " . '"foo": "bar"' . "\n}"
                ),
            array(
                ['foo' => 'bar'],
                'toto',
                false,
                'toto({"foo":"bar"})'
                )
        );
    }

    /**
     * @dataProvider outputDataProvider
     */
    public function testOutput($a, $b, $c, $d)
    {
        $obj = new \Transvision\Json();
        $this
            ->string($obj->output($a, $b, $c))
                ->isEqualTo($d)
        ;
    }

}
