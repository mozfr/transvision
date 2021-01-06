<?php
namespace tests\units\Transvision;

use atoum\atoum;
use Transvision\Project as _Project;

require_once __DIR__ . '/../bootstrap.php';

class Project extends atoum\test
{
    public function testGetRepositories()
    {
        $obj = new _Project();
        $repos = ['gecko_strings', 'mozilla_org'];
        $this
            ->array($obj->getRepositories())
                ->isEqualTo($repos);
    }

    public function testGetRepositoriesNames()
    {
        $obj = new _Project();
        $repos = [
            'gecko_strings' => 'Gecko Products',
            'mozilla_org'   => 'mozilla.org',
        ];
        $this
            ->array($obj->getRepositoriesNames())
                ->isEqualTo($repos);
    }

    public function testGetDesktopRepositories()
    {
        $obj = new _Project();
        $repos = ['gecko_strings'];
        $this
            ->array($obj->getDesktopRepositories())
                ->isEqualTo($repos);
    }

    public function isDesktopRepositoryDP()
    {
        return [
            ['gecko_strings', true],
            ['firefox_ios', false],
            ['mozilla_org', false],
            ['randomrepo', false],
        ];
    }

    /**
     * @dataProvider isDesktopRepositoryDP
     */
    public function testIsDesktopRepository($a, $b)
    {
        $obj = new _Project();
        $this
            ->boolean($obj->isDesktopRepository($a))
                ->isEqualTo($b);
    }

    public function getRepositoryLocalesDP()
    {
        return [
            ['gecko_strings', ['en-US', 'fr', 'it'], []],
            ['gecko_strings', ['fr', 'it'], ['en-US']],
            ['gecko_strings', ['it'], ['en-US', 'fr']],
        ];
    }

    /**
     * @dataProvider getRepositoryLocalesDP
     */
    public function testGetRepositoryLocales($a, $b, $c)
    {
        $obj = new _Project();
        $this
            ->array($obj->getRepositoryLocales($a, $b))
                ->isEqualTo($c);
    }

    public function getLocaleRepositoriesDP()
    {
        return [
            ['fr', ['gecko_strings', 'mozilla_org']],
            ['foobar', []],
        ];
    }

    /**
     * @dataProvider getLocaleRepositoriesDP
     */
    public function testGetLocaleRepositories($a, $b)
    {
        $obj = new _Project();
        $this
            ->array($obj->getLocaleRepositories($a))
                ->isEqualTo($b);
    }

    public function testGetReferenceLocale()
    {
        $obj = new _Project();
        $this
            ->string($obj->getReferenceLocale('gecko_strings'))
                ->isEqualTo('en-US');
        $this
            ->string($obj->getReferenceLocale('mozilla_org'))
                ->isEqualTo('en');
    }

    public function testIsValidRepository()
    {
        $obj = new _Project();
        $this
            ->boolean($obj->isValidRepository('gecko_strings'))
                ->isEqualTo(true);
        $this
            ->boolean($obj->isValidRepository('foo'))
                ->isEqualTo(false);
    }

    public function getLocaleInContextDP()
    {
        return [
            ['fr', 'bugzilla', 'fr'],
            ['es', 'bugzilla', 'es-ES'],
            ['pa', 'bugzilla', 'pa-IN'],
            ['gu', 'bugzilla', 'gu-IN'],
            ['sr', 'bugzilla', 'sr'],
            ['sr-Cyrl', 'bugzilla', 'sr'],
            ['sr-Latn', 'bugzilla', 'sr'],
            ['es', 'mozilla_org', 'es-ES'],
            ['es-AR', 'mozilla_org', 'es-AR'],
            ['sr-Cyrl', 'mozilla_org', 'sr'],
            ['es-ES', 'foobar', 'es-ES'],
            ['fr', 'foobar', 'fr'],
            ['es-ES', 'firefox_ios', 'es'],
            ['es', 'firefox_ios', 'es'],
            ['son', 'firefox_ios', 'ses'],
        ];
    }

    /**
     * @dataProvider getLocaleInContextDP
     */
    public function testGetLocaleInContext($a, $b, $c)
    {
        $obj = new _Project();
        $this
            ->string($obj->getLocaleInContext($a, $b))
                ->isEqualTo($c);
    }

    public function getLocaleToolDP()
    {
        return [
            ['fr', ''],
            ['sr', 'pontoon'],
            ['te', 'pontoon'],
        ];
    }

    /**
     * @dataProvider getLocaleToolDP
     */
    public function testGetLocaleTool($a, $b)
    {
        $obj = new _Project();
        $this
            ->string($obj->getLocaleTool($a))
                ->isEqualTo($b);
    }
}
