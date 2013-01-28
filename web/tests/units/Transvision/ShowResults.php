<?php
namespace Transvision\tests\units;

require __DIR__ . '/../../../classes/Transvision/ShowResults.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use Transvision;
use atoum;

class ShowResults extends atoum\test
{
    public function test_foo()
    {
        $obj = new Transvision\ShowResults();
        $this->assert
                    ->string($obj->foo())
                    ->isEqualTo('bar');
    }

}
