<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

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
        return array(
            array(
                array('en-US'),
                '<table id="DownloadsTable"><tr><th></th><th colspan="4">Desktop Software</th><th colspan="4">Firefox OS</th></tr><tr><th></th><th>Central</th><th>Aurora</th><th>Beta</th><th>Release</th><th>Gaia central</th><th>Gaia 1.2</th><th>Gaia 1.3</th><th>Gaia 1.4</th></tr><tr><th>en-US</th><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td><td><span class="red">TMX Not Available</span></td></tr></table>'
                )
        );
    }
    /**
     * @dataProvider tmxDownloadTableDataProvider
     */
    public function testTmxDownloadTable($a, $b)
    {
        if (!getenv('TRAVIS')) {
            $obj = new \Transvision\Utils();
            $this
                ->string($obj->tmxDownloadTable($a))
                    ->isEqualTo($b)
            ;
        }
    }


    public function getRepoStringsDataProvider()
    {
        return array(
            array(
                'fr',
                'central',
                'Ouvrir dans le Finder'
                ),
            array(
                'es-ES',
                'gaia', // test fallback to es locale for gaia
                'Hogar'
                ),
        );
    }

    /**
     * @dataProvider getRepoStringsDataProvider
     */
    public function testGetRepoStrings($a, $b, $c)
    {
        if (!getenv('TRAVIS')) {
            $obj = new \Transvision\Utils();
            $this
                ->array($obj->getRepoStrings($a, $b))
                    ->contains($c)
            ;
        }
    }
}
