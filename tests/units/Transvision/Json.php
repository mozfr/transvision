<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Json as _Json;

require_once __DIR__ . '/../bootstrap.php';

class Json extends atoum\test
{
    public function fetchDP()
    {
        return [
            [TEST_FILES . 'json/empty.json', []],
            [TEST_FILES . 'json/test1.json', ['user_name' => 'Pascal']],
        ];
    }

    /**
     * @dataProvider fetchDP
     */
    public function testFetch($a, $b)
    {
        $obj = new _Json();
        $this
            ->array($obj->fetch($a))
                ->isEqualTo($b);
    }

    public function outputDP()
    {
        return [
            [
                ['foo' => 'bar'],
                false,
                false,
                '{"foo":"bar"}',
            ],
            [
                ['foo' => 'bar'],
                false,
                true,
                "{\n    " . '"foo": "bar"' . "\n}",
            ],
            [
                ['foo' => 'bar'],
                'toto',
                false,
                'toto({"foo":"bar"})',
            ],
        ];
    }

    /**
     * @dataProvider outputDP
     */
    public function testOutput($a, $b, $c, $d)
    {
        $obj = new _Json();
        $this
            ->string($obj->output($a, $b, $c))
                ->isEqualTo($d);
    }
}
