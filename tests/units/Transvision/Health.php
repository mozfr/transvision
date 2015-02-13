<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Health as _Health;

require_once __DIR__ . '/../bootstrap.php';

class Health extends atoum\test
{
    public function testGetRepositories()
    {
        $obj = new _Health();
        $col = ['name', 'total', 'translated', 'missing', 'identical'];
        $this
            ->array($obj->getColumnsKeys())
                ->isEqualTo($col);
    }

    public function getStatusDP()
    {
        return [
            [
                'Gaia master',
                [
                    'apps/settings/settings.properties:apps-free-space'          => 'Left',
                    'apps/sms/sms.properties:files-too-large[few]'               => 'The files you have selected are too large.',
                    'apps/settings/settings.properties:pinAttemptMsg3[few]'      => 'You have {{n}} tries left to enter the correct code before locking the SIM card.',
                    'apps/settings/settings.properties:downloads-header'         => '{{downloads}}',
                    'apps/settings/settings.properties:continue'                 => 'Continue',
                    'apps/system/system.properties:confirmNewSimPinMsg'          => 'Confirm new PIN',
                    'apps/costcontrol/costcontrol.properties:fte-postpaid3-hint' => 'Set an alert to avoid using too much data.',
                    'apps/settings/settings.properties:apz-overscroll'           => 'Overscrolling',
                ],
                [
                    'apps/settings/settings.properties:apps-free-space'          => 'Libre',
                    'apps/sms/sms.properties:files-too-large[few]'               => 'Les fichiers sélectionnés sont trop volumineux.',
                    'apps/settings/settings.properties:pinAttemptMsg3[few]'      => 'Il vous reste {{n}} essais pour saisir le code correct avant de bloquer la carte SIM.',
                    'apps/settings/settings.properties:downloads-header'         => 'Téléchargements',
                    'apps/settings/settings.properties:continue'                 => 'Continuer',
                    'apps/system/system.properties:confirmNewSimPinMsg'          => 'Confirmer le nouveau code PIN',
                    'apps/costcontrol/costcontrol.properties:fte-postpaid3-hint' => 'Définir une alerte pour éviter d’utiliser trop de données.',
                    'apps/settings/settings.properties:apz-overscroll'           => 'Overscrolling',
                ],

                [
                    'name'       => 'Gaia master',
                    'total'      => 8,
                    'translated' => 8,
                    'missing'    => 0,
                    'identical'  => 1,
                ],
            ],
            [
                'Gaia master',
                [
                    'apps/settings/settings.properties:apps-free-space' => 'Left',
                    'apps/settings/settings.properties:apz-overscroll'  => 'Overscrolling',
                ],
                [
                    'apps/settings/settings.properties:apz-overscroll' => 'Overscrolling',
                ],
                [
                    'name'       => 'Gaia master',
                    'total'      => 2,
                    'translated' => 1,
                    'missing'    => 1,
                    'identical'  => 1,
                ],
            ],
            [
                'Gaia master',
                [
                    'apps/settings/settings.properties:apps-free-space' => 'Left',
                    'apps/settings/settings.properties:test'            => 'Overscrolling',
                ],
                [
                    'apps/settings/settings.properties:apps-free-space' => 'Libre',
                    'apps/settings/settings.properties:apz-overscroll'  => 'Overscrolling',
                ],
                [
                    'name'       => 'Gaia master',
                    'total'      => 2,
                    'translated' => 2,
                    'missing'    => 1,
                    'identical'  => 0,
                ],
            ],
            [
                'Gaia master',
                [
                    'apps/settings/settings.properties:apps-free-space' => 'Left',
                    'apps/settings/settings.properties:apz-overscroll'  => 'Overscrolling',
                ],
                [
                    'apps/settings/settings.properties:test'            => 'test',
                    'apps/settings/settings.properties:apps-free-space' => 'Libre',
                    'apps/settings/settings.properties:apz-overscroll'  => 'Overscrolling',
                ],
                [
                    'name'       => 'Gaia master',
                    'total'      => 2,
                    'translated' => 3,
                    'missing'    => 0,
                    'identical'  => 1,
                ],
            ],
            [
                'Gaia master',
                [
                    'apps/settings/settings.properties:apps-free-space' => 'Left',
                    'apps/settings/settings.properties:apz-overscroll'  => 'Overscrolling',
                ],
                [],
                [
                    'name'       => 'Gaia master',
                    'total'      => 2,
                    'translated' => 0,
                    'missing'    => 2,
                    'identical'  => 0,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getStatusDP
     */
    public function testGetStatus($a, $b, $c, $d)
    {
        $obj = new _Health();
        $this
            ->array($obj->getStatus($a, $b, $c))
                ->isEqualTo($d);
    }

    public function addLinkDP()
    {
        return [
            ['Other repositories', 'test-id', true, '<li class="active"><a href=\'#test-id\'>Other repositories</a></li>' . "\n"],
            ['Other repositories', 'test-id', false, '<li><a href=\'#test-id\'>Other repositories</a></li>' . "\n"],
        ];
    }

    /**
     * @dataProvider addLinkDP
     */
    public function testaddLink($a, $b, $c, $d)
    {
        $obj = new _Health();
        $this
            ->string($obj->addLink($a, $b, $c))
                ->isEqualTo($d);
    }

    public function addTabDP()
    {
        return [
            ['test-id', true, '<div class="active tab" id="test-id">'],
            ['test-id', false, '<div class="tab" id="test-id">'],
        ];
    }

    /**
     * @dataProvider addTabDP
     */
    public function testaddTab($a, $b, $c)
    {
        $obj = new _Health();
        $this
            ->string($obj->addTab($a, $b))
                ->isEqualTo($c);
    }

    public function addRowDP()
    {
        return [
            [
                ['name', 'translated'],
                ['name' => 'Test App', 'translated' => 1300],
                "<tr><td>Test App</td><td>1300</td></tr>\n",
            ],
        ];
    }

    /**
     * @dataProvider addRowDP
     */
    public function testaddRow($a, $b, $c)
    {
        $obj = new _Health();
        $this
            ->string($obj->addRow($a, $b))
                ->isEqualTo($c);
    }

    // Disabled test until we find a workaround for the relative time in the strings
    /*public function getStatsPaneDP()
    {
        return [
            [
                ['stats' => array ( 'commit' => array ( 'commit' => '130584', 'author' => 'flodolo@mozilla.com', 'email' => 'flodolo@mozilla.com', 'date' => \DateTime::__set_state(array( 'date' => '2014-07-28 06:45:15', 'timezone_type' => 1, 'timezone' => '-07:00', )), 'summary' => 'home: remove tag from promo_makerparty, leave strings (should be reused later)', 'vcs' => 'svn', ), 'commit_sum' => 712, 'repo' => 'mozilla_org', )],
                '<div class="stats-panel"><div id="sub-mozilla_org" class="metrics tab active"><h4>Repo metrics:</h4><ul><li class="metric">Last commit: 07/28/2014 at 06:45:15 GMT -07:00 (7 days ago) by flodolo@mozilla.com</li><li class="metric">Number of commits: 712</li></ul></div></div>'
            ],
            [
                ['gaia' => array ( 'name' => 'Gaia master', 'total' => 4125, 'translated' => 3982, 'missing' => 143, 'identical' => 341, 'stats' => array ( 'commit' => array ( 'commit' => '369:981eb53f1f40', 'author' => 'Theo Chevalier', 'email' => 'theo.chevalier11@gmail.com', 'date' => \DateTime::__set_state(array( 'date' => '2014-07-28 11:50:07', 'timezone_type' => 1, 'timezone' => '-07:00', )), 'summary' => 'Missing shared/elements files + system update battery threshold', 'vcs' => 'hg', ), 'commit_sum' => 370, 'repo' => 'gaia', ), ), 'gaia_1_2' => array ( 'name' => 'Gaia 1.2', 'total' => 3060, 'translated' => 3060, 'missing' => 0, 'identical' => 259, 'stats' => array ( 'commit' => array ( 'commit' => '251:44a109205087', 'author' => 'Theo Chevalier', 'email' => 'theo.chevalier11@gmail.com', 'date' => \DateTime::__set_state(array( 'date' => '2014-01-21 12:52:42', 'timezone_type' => 1, 'timezone' => '+01:00', )), 'summary' => '[qa] Uplift QA from 1.3/1.4', 'vcs' => 'hg', ), 'commit_sum' => 252, 'repo' => 'gaia_1_2', ), ), 'gaia_1_3' => array ( 'name' => 'Gaia 1.3', 'total' => 3303, 'translated' => 3303, 'missing' => 0, 'identical' => 272, 'stats' => array ( 'commit' => array ( 'commit' => '274:055f2b5412e4', 'author' => 'Theo Chevalier', 'email' => 'theo.chevalier11@gmail.com', 'date' => \DateTime::__set_state(array( 'date' => '2014-07-11 17:47:03', 'timezone_type' => 1, 'timezone' => '-07:00', )), 'summary' => 'Bug 1037586 - Enter SIM {{n}} PIN header is truncated in multiple locales', 'vcs' => 'hg', ), 'commit_sum' => 275, 'repo' => 'gaia_1_3', ), ), 'gaia_1_4' => array ( 'name' => 'Gaia 1.4', 'total' => 3541, 'translated' => 3541, 'missing' => 0, 'identical' => 298, 'stats' => array ( 'commit' => array ( 'commit' => '304:676b83517252', 'author' => 'Theo Chevalier', 'email' => 'theo.chevalier11@gmail.com', 'date' => \DateTime::__set_state(array( 'date' => '2014-07-11 17:18:07', 'timezone_type' => 1, 'timezone' => '-07:00', )), 'summary' => 'Bug 1037586 - Enter SIM {{n}} PIN header is truncated in multiple locales', 'vcs' => 'hg', ), 'commit_sum' => 305, 'repo' => 'gaia_1_4', ), )],
                '
            <div class="stats-panel">
                <div class="tabs">
                    <ul class="tab-links">
                        <li class="active"><a href="#sub-gaia">Gaia master</a></li>
<li><a href="#sub-gaia_1_2">Gaia 1.2</a></li>
<li><a href="#sub-gaia_1_3">Gaia 1.3</a></li>
<li><a href="#sub-gaia_1_4">Gaia 1.4</a></li>

                    </ul>
                    <div class="tab-content">
                        <div id="sub-gaia" class="metrics tab active"><h4>Repo metrics:</h4><ul><li class="metric">Last commit: 07/28/2014 at 11:50:07 GMT -07:00 (7 days ago) by Theo Chevalier</li><li class="metric">Number of commits: 370</li></ul></div><div id="sub-gaia_1_2" class="metrics tab"><h4>Repo metrics:</h4><ul><li class="metric">Last commit: 01/21/2014 at 12:52:42 GMT +01:00 (6 months ago) by Theo Chevalier</li><li class="metric">Number of commits: 252</li></ul></div><div id="sub-gaia_1_3" class="metrics tab"><h4>Repo metrics:</h4><ul><li class="metric">Last commit: 07/11/2014 at 17:47:03 GMT -07:00 (24 days ago) by Theo Chevalier</li><li class="metric">Number of commits: 275</li></ul></div><div id="sub-gaia_1_4" class="metrics tab"><h4>Repo metrics:</h4><ul><li class="metric">Last commit: 07/11/2014 at 17:18:07 GMT -07:00 (24 days ago) by Theo Chevalier</li><li class="metric">Number of commits: 305</li></ul></div>
                    </div>
                </div>
            </div>'
            ]
        ];
    }*/

    /**
     * @dataProvider getStatsPaneDP
     */
    /*public function testGetStatsPane($a, $b)
    {
        $obj = new _Health();
        $this
            ->string($obj->getStatsPane($a))
                ->isEqualTo($b);
    }*/
}
