<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class Utils extends atoum\test
{
    public function uniqueWordsDataProvider()
    {
        return array('Le système le style du couteau du suisse');
    }

    /**
     * @dataProvider uniqueWordsDataProvider
     */
    public function testUniqueWords($a)
    {
        $obj = new \Transvision\Utils();
        $this
            ->array($obj->uniqueWords($a))
                ->isEqualTo(
                    array(
                        'système',
                        'couteau',
                        'suisse',
                        'style',
                        'le',
                        'Le',
                        'du'
                    )
                );
    }

    public function checkboxDefaultOptionDataProvider1()
    {
        return array(
            array(
                'es-ES',
                'es-ES',
                ' checked="checked"'
                )
        );
    }

    /**
     * @dataProvider checkboxDefaultOptionDataProvider1
     */

    public function testCheckboxDefaultOption1($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkboxDefaultOptionDataProvider2()
    {
        return array(
            array(
                'es-ES',
                'en-US',
                false
                )
        );
    }

    /**
     * @dataProvider checkboxDefaultOptionDataProvider2
     */

    public function testCheckboxDefaultOption2($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->boolean($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkBoxStateDataProvider1()
    {
        $_GET['t2t'] = "somedata";
        return array(
            array($_GET['t2t'], '')
        );
    }

    /**
     * @dataProvider checkBoxStateDataProvider1
     */

    public function testCheckboxState1($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' disabled="disabled"');
    }

    public function checkBoxStateDataProvider2()
    {
        $_GET['t2t'] = "somedata";
        return array(
            array($_GET['t2t'], 't2t')
        );
    }

    /**
     * @dataProvider checkBoxStateDataProvider2
     */

    public function testCheckboxState2($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function checkBoxStateDataProvider3()
    {
        return array(
            array('some string', null)
        );
    }

    /**
     * @dataProvider checkBoxStateDataProvider3
     */

    public function testCheckboxState3($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }


    public function getHtmlSelectOptionsDataProvider()
    {
        return array(
            array(
                    array('strings' => 'Strings', 'entities'=> 'Entities', 'strings_entities' => 'Strings & Entities'),
                    'strings_entities',
                    true
            )
        );
    }

    /**
     * @dataProvider getHtmlSelectOptionsDataProvider
     */
    public function testGetHtmlSelectOptions($a, $b, $c)
    {

        $obj = new \Transvision\Utils();
        $this
            ->string($obj->getHtmlSelectOptions($a, $b, $c))
                ->isEqualTo('<option value=strings>Strings</option><option value=entities>Entities</option><option selected value=strings_entities>Strings & Entities</option>')
        ;
    }

    public function cleanSearchDataProvider()
    {
        return array(
            array(
                'pages web   fréquemment visitées (en cliquant sur « Recharger »,',
                'pages web fréquemment visitées (en cliquant sur « Recharger »,'
                ),
            array(
                'toto ',
                'toto'
            )
        );
    }

    /**
     * @dataProvider cleanSearchDataProvider
     */
    public function testCleanSearch($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->cleanSearch($a))
                ->isEqualTo($b)
        ;
    }

    public function checkAbnormalStringLengthDataProvider()
    {
        return array(
            array(
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème. Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème.',
                'large'
                ),
            array(
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et',
                'small'
                ),
            array(
                'Le système de marque-pages et',
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'large'
                ),
            array(
                'pages web',
                'pa',
                'small'
                )
        );
    }
    /**
     * @dataProvider checkAbnormalStringLengthDataProvider
     */
    public function testCheckAbnormalStringLength($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkAbnormalStringLength($a,$b))
                ->isEqualTo($c)
        ;
    }

    public function checkAbnormalStringLengthDataProvider2()
    {
        return array(
            array(
                'Add Bookmarks',
                'Ajouter des marque-pages',
                false
                ),
            array(
                'Add Bookmarks',
                '',
                false
            )
        );
    }
    /**
     * @dataProvider checkAbnormalStringLengthDataProvider2
     */
    public function testCheckAbnormalStringLength2($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->boolean($obj->checkAbnormalStringLength($a,$b))
                ->isEqualTo($c)
        ;
    }


    public function tmxDownloadTableDataProvider()
    {
        $ini_array = parse_ini_file(__DIR__ . '/../../../inc/config.ini');
        define('TMX', $ini_array['root'] . '/TMX/');
        return array(
            array(
                array('en-US'),
                '<table id="DownloadsTable"><tr><th colspan="6"><abbr title="Translation Memory eXchange">TMX</abbr> Download Page</th></tr><tr><th></th><th colspan="4">Desktop Software</th><th colspan="3">Firefox OS</th></tr><tr><th></th><th>Central</th><th>Aurora</th><th>Beta</th><th>Release</th><th>Gaia central</th><th>Gaia 1.1</th><th>Gaia 1.2</th></tr><tr><th>en-US</th><td><a href="/TMX/central/en-US/memoire_en-US_en-US.tmx">Download</a></td><td><a href="/TMX/aurora/en-US/memoire_en-US_en-US.tmx">Download</a></td><td><a href="/TMX/beta/en-US/memoire_en-US_en-US.tmx">Download</a></td><td><a href="/TMX/release/en-US/memoire_en-US_en-US.tmx">Download</a></td><td><a href="/TMX/gaia/en-US/memoire_en-US_en-US.tmx">Download</a></td><td><a href="/TMX/gaia_1_1/en-US/memoire_en-US_en-US.tmx">Download</a></td><td><a href="/TMX/gaia_1_2/en-US/memoire_en-US_en-US.tmx">Download</a></td></tr></table>'
                )
        );
    }

    /**
     * @dataProvider tmxDownloadTableDataProvider
     */
    public function testTmxDownloadTable($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->tmxDownloadTable($a))
                ->isEqualTo($b)
        ;
    }

}
