<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class Cache extends atoum\test
{

    public function beforeTestMethod($method)
    {
        // Executed *before each* test method.
        switch ($method)
        {
            case 'testFlush':
                // Prepare testing environment for testFlush().
                $files_to_flush = new \Transvision\Cache();
                // create a few files to delete
                $files_to_flush->setKey('file_1', 'foobar');
                $files_to_flush->setKey('file_2', 'foobar');
                $files_to_flush->setKey('file_3', 'foobar');
                break;

            case 'testGetKey':
                // Prepare testing environment for testGetKey().
                $files = new \Transvision\Cache();
                $files->setKey('this_test', 'foobar');
                // Change the timestamp to 100 seconds in the past so we can test expiration
                touch(CACHE_PATH . sha1('this_test') . '.cache', time()-100);
                break;
        }
    }

    public function testSetKey()
    {
        $obj = new \Transvision\Cache();
        $this
            ->boolean($obj->setKey('this_test', 'foobar'))
                ->isEqualTo(true)
        ;
    }

    public function getKeyDP()
    {
        return array(
            ['this_test', 0, 'foobar'],         // valid key
            ['this_test', 2, false],            // expired key
            ['id_that_doesnt_exist', 0, false], // non-existing key
        );
    }

    /**
     * @dataProvider getKeyDP
     */
    public function testGetKey($a, $b, $c)
    {
        $obj = new \Transvision\Cache();
        $this
            ->variable($obj->getKey($a, $b))
                ->isEqualTo($c)
        ;
    }

    public function testFlush()
    {
        $obj = new \Transvision\Cache();
        $this
            ->boolean($obj->flush())
                ->isEqualTo(true)
        ;
    }
}
