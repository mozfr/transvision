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
                'Firefox Desktop',
                [
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel'             => 'Bookmark',
                    'browser/chrome/browser/syncQuota.properties:collection.bookmarks.label'          => 'Bookmarks',
                    'browser/chrome/browser/places/bookmarkProperties.properties:dialogTitleAddMulti' => 'New bookmarks',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label'                    => 'Bookmark this page',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label2'                   => 'Blah',
                ],
                [
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel'             => 'Marque-page',
                    'browser/chrome/browser/syncQuota.properties:collection.bookmarks.label'          => 'Bookmarks',
                    'browser/chrome/browser/places/bookmarkProperties.properties:dialogTitleAddMulti' => 'Nouveaux marque-pages',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label'                    => 'Marquer cette page',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label2'                   => 'Blabla',
                ],

                [
                    'name'       => 'Firefox Desktop',
                    'total'      => 5,
                    'translated' => 5,
                    'missing'    => 0,
                    'identical'  => 1,
                ],
            ],
            [
                'Firefox Desktop',
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'    => 'Find in Finder',
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel' => 'Bookmark',
                ],
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label' => 'Find in Finder',
                ],
                [
                    'name'       => 'Firefox Desktop',
                    'total'      => 2,
                    'translated' => 1,
                    'missing'    => 1,
                    'identical'  => 1,
                ],
            ],
            [
                'Firefox Desktop',
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'    => 'Find in Finder',
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel' => 'Bookmark',
                ],
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label' => 'Ouvrir dans le Finder',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label2'    => 'Bookmark',
                ],
                [
                    'name'       => 'Firefox Desktop',
                    'total'      => 2,
                    'translated' => 2,
                    'missing'    => 1,
                    'identical'  => 0,
                ],
            ],
            [
                'Firefox Desktop',
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'    => 'Find in Finder',
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel' => 'Bookmark',
                ],
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'    => 'test',
                    'apps/settings/settings.properties:apps-free-space'                   => 'Libre',
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel' => 'Bookmark',
                ],
                [
                    'name'       => 'Firefox Desktop',
                    'total'      => 2,
                    'translated' => 3,
                    'missing'    => 0,
                    'identical'  => 1,
                ],
            ],
            [
                'Firefox Desktop',
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'    => 'Find in Finder',
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel' => 'Bookmark',
                ],
                [],
                [
                    'name'       => 'Firefox Desktop',
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
                ['stats' =>  [ 'commit' =>  [ 'commit' => '130584', 'author' => 'flodolo@mozilla.com', 'email' => 'flodolo@mozilla.com', 'date' => \DateTime::__set_state([ 'date' => '2014-07-28 06:45:15', 'timezone_type' => 1, 'timezone' => '-07:00']), 'summary' => 'home: remove tag from promo_makerparty, leave strings (should be reused later)', 'vcs' => 'svn'], 'commit_sum' => 712, 'repo' => 'mozilla_org']],
                '<div class="stats-panel"><div id="sub-mozilla_org" class="metrics tab active"><h4>Repo metrics:</h4><ul><li class="metric old">Last commit: <b>2 years ago</b> (July 28, 2014 at 06:45 GMT -07:00) by flodolo@mozilla.com</li><li class="metric">Number of commits: 712 commits</li></ul></div></div>',
            ],
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
