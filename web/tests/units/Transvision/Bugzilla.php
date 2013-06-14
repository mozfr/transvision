<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class Bugzilla extends atoum\test
{

    public function collectLanguageComponentDataProvider()
    {
        $ini_array = parse_ini_file(__DIR__ . '/../../../inc/config.ini');
        define('CACHE', $ini_array['install'] . '/web/cache/');

        $obj = new \Transvision\Bugzilla();
        $components_list = $obj->getBugzillaComponents();
        return array(
            array(
                'en-GB',
                $components_list,
                'en-GB / English (United Kingdom)'
                ),
            array(
                'fr',
                $components_list,
                'fr / French'
                ),
            array(
                'unknow_LANG',
                $components_list,
                'Other'
            )
        );
    }

    /**
     * @dataProvider collectLanguageComponentDataProvider
     */
    public function testCollectLanguageComponent($a, $b, $c)
    {
        $obj = new \Transvision\Bugzilla();
        $this
            ->string($obj->collectLanguageComponent($a,$b))
                ->isEqualTo($c)
        ;
    }
}
