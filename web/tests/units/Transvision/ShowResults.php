<?php
namespace Transvision\tests\units;
require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class ShowResults extends atoum\test
{
    public function test_foo()
    {
        $obj = new \Transvision\ShowResults();
        $this->assert
                ->string($obj->foo())
                ->isEqualTo('bar');
    }
}
