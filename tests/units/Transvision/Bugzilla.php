<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../web/vendor/autoload.php';

use atoum;

class Bugzilla extends atoum\test
{

    public function collectLanguageComponentDataProvider()
    {
        $ini_array = parse_ini_file(__DIR__ . '/../../../web/inc/config.ini');
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
                'sr-Cyrl',
                $components_list,
                'sr / Serbian'
                ),
            array(
                'sr-Latn',
                $components_list,
                'sr / Serbian'
                ),
            array(
                'es',
                $components_list,
                'es-ES / Spanish'
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

    public function bugzillaLocaleCodeDP()
    {
        return array(
            array( 'fr', 'fr'),
            array( 'es', 'es-ES'),
            array( 'pa', 'pa-IN'),
            array( 'sr', 'sr'),
            array( 'sr-Cyrl', 'sr'),
            array( 'sr-Latn', 'sr'),
        );
    }

    /**
     * @dataProvider bugzillaLocaleCodeDP
     */
    public function testBugzillaLocaleCode($a, $b)
    {
        $obj = new \Transvision\Bugzilla();
        $this
            ->string($obj->bugzillaLocaleCode($a))
                ->isEqualTo($b)
        ;
    }
}
