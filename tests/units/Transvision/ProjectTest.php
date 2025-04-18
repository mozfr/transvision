<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Project;

require_once __DIR__ . '/../bootstrap.php';

class ProjectTest extends TestCase
{
    public function testGetRepositories()
    {
        $obj = new Project();
        $repos = ['gecko_strings', 'mozilla_org'];
        $this
            ->assertSame($obj->getRepositories(true), $repos);

        $repos = ['all_projects', 'gecko_strings', 'mozilla_org'];
        $this
            ->assertSame($obj->getRepositories(), $repos);
    }

    public function testMetaRepository()
    {
        $obj = new Project();
        $this
            ->assertSame($obj->isMetaRepository('all_projects'), true);
        $this
            ->assertSame($obj->isMetaRepository('gecko_strings'), false);
        $this
            ->assertSame($obj->getMetaRepository(), 'all_projects');
    }

    public function testIsReferenceLocale()
    {
        $obj = new Project();
        $this
            ->assertSame($obj->isReferenceLocale('en-US', 'all_projects'), true);
        $this
            ->assertSame($obj->isReferenceLocale('en', 'mozilla_org'), true);
        $this
            ->assertSame($obj->isReferenceLocale('fr', 'mozilla_org'), false);
    }

    public function testGetRepositoriesNames()
    {
        $obj = new Project();
        $repos = [
            'gecko_strings' => 'Gecko Products',
            'mozilla_org'   => 'mozilla.org',
        ];
        $this
            ->assertEqualsCanonicalizing($obj->getRepositoriesNames(true), $repos);

        $repos = [
            'all_projects'   => 'All Projects',
            'gecko_strings'  => 'Gecko Products',
            'mozilla_org'    => 'mozilla.org',
        ];
        $this
            ->assertEqualsCanonicalizing($obj->getRepositoriesNames(), $repos);
    }

    public function testGetDesktopRepositories()
    {
        $obj = new Project();
        $repos = ['gecko_strings', 'seamonkey', 'thunderbird'];
        $this
            ->assertSame($obj->getDesktopRepositories(), $repos);
    }

    public static function isDesktopRepositoryDP()
    {
        return [
            ['gecko_strings', true],
            ['firefox_ios', false],
            ['mozilla_org', false],
            ['randomrepo', false],
        ];
    }

    #[DataProvider('isDesktopRepositoryDP')]
    public function testIsDesktopRepository($a, $b)
    {
        $obj = new Project();
        $this
            ->assertSame($obj->isDesktopRepository($a), $b);
    }

    public static function getRepositoryLocalesDP()
    {
        return [
            ['gecko_strings', ['en-US', 'fr', 'it'], []],
            ['gecko_strings', ['fr', 'it'], ['en-US']],
            ['gecko_strings', ['it'], ['en-US', 'fr']],
        ];
    }

    #[DataProvider('getRepositoryLocalesDP')]
    public function testGetRepositoryLocales($a, $b, $c)
    {
        $obj = new Project();
        $this
            ->assertSame($obj->getRepositoryLocales($a, $b), $c);
    }

    public static function getLocaleRepositoriesDP()
    {
        return [
            ['fr', ['gecko_strings', 'mozilla_org']],
            ['foobar', []],
        ];
    }

    #[DataProvider('getLocaleRepositoriesDP')]
    public function testGetLocaleRepositories($a, $b)
    {
        $obj = new Project();
        $this
            ->assertSame($obj->getLocaleRepositories($a), $b);
    }

    public function testGetReferenceLocale()
    {
        $obj = new Project();
        $this
            ->assertSame($obj->getReferenceLocale('gecko_strings'), 'en-US');
        $this
            ->assertSame($obj->getReferenceLocale('mozilla_org'), 'en');
    }

    public function testIstReferenceLocale()
    {
        $obj = new Project();
        $this
            ->assertSame($obj->isReferenceLocale('en-US', 'gecko_strings'), True);
        $this
            ->assertSame($obj->isReferenceLocale('en', 'gecko_strings'), False);
        $this
            ->assertSame($obj->isReferenceLocale('en', 'mozilla_org'), True);
    }

    public function testGetAllLocales()
    {
        $obj = new Project();
        $this
            ->assertSame($obj->getAllLocales(), ['en-US', 'fr', 'it']);
    }

    public function testIsValidRepository()
    {
        $obj = new Project();
        $this
            ->assertSame($obj->isValidRepository('gecko_strings'), true);
        $this
            ->assertSame($obj->isValidRepository('foo'), false);
    }

    public static function getLocaleInContextDP()
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

    #[DataProvider('getLocaleInContextDP')]
    public function testGetLocaleInContext($a, $b, $c)
    {
        $obj = new Project();
        $this
            ->assertSame($obj->getLocaleInContext($a, $b), $c);
    }

    public static function getLocaleToolDP()
    {
        return [
            ['fr', ''],
            ['sr', 'pontoon'],
            ['te', 'pontoon'],
        ];
    }

    #[DataProvider('getLocaleToolDP')]
    public function testGetLocaleTool($a, $b)
    {
        $obj = new Project();
        $this
            ->assertSame($obj->getLocaleTool($a), $b);
    }
}
