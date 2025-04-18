<?php
namespace tests\Transvision;

use PHPUnit\Framework\TestCase;
use Transvision\Search;

require_once __DIR__ . '/../bootstrap.php';

class SearchTest extends TestCase
{
    public function testConstructor()
    {
        $obj = new Search();
        $this
            ->assertSame($obj->getSearchTerms(), '');
        $this
            ->assertSame($obj->getRegex(), '');
        $this
            ->assertSame($obj->isCaseSensitive(), false);
        $this
            ->assertSame($obj->isEachWord(), false);
        $this
            ->assertSame($obj->isEntireString(), false);
        $this
            ->assertSame($obj->getRegexSearchTerms(), '');
        $this
            ->assertSame($obj->getRepository(), 'gecko_strings');
        $this
            ->assertSame($obj->getSearchType(), 'strings');
        $this
            ->assertSame($obj->getLocale('source'), '');
        $this
            ->assertEqualsCanonicalizing($obj->getFormSearchOptions(),
                    [
                        'case_sensitive', 'entire_string', 'repo',
                        'search_type', 'each_word', 'entire_words',
                    ]);
        $this
            ->assertEqualsCanonicalizing($obj->getFormCheckboxes(),
                    [
                        'case_sensitive', 'entire_string',
                        'each_word', 'entire_words',
                    ]);
    }

    public function testSetSearchTerms()
    {
        $obj = new Search();
        $obj->setSearchTerms(' foobar ');
        $this
            ->assertSame($obj->getSearchTerms(), ' foobar ');
        $this
            ->assertSame($obj->getRegexSearchTerms(), ' foobar ');
    }

    public function testSetRegexSearchTerms()
    {
        $obj = new Search();
        $obj->setRegexSearchTerms('A new hope');
        $this
            ->assertSame($obj->getRegexSearchTerms(), 'A new hope');
        $this
            ->assertSame($obj->getRegex(), '~A new hope~iu');
    }

    public function testSetRegexCaseInsensitive()
    {
        $obj = new Search();

        // Test assertSames (as passed from GET)
        $obj->setRegexCaseInsensitive('sensitive');
        $this
            ->assertSame($obj->getRegex(), '~~u');

        $obj->setRegexCaseInsensitive('');
        $this
            ->assertSame($obj->getRegex(), '~~iu');

        // Test boolean values
        $obj->setRegexCaseInsensitive(true);
        $this
            ->assertSame($obj->getRegex(), '~~u');

        $obj->setRegexCaseInsensitive(false);
        $this
            ->assertSame($obj->getRegex(), '~~iu');
    }

    public function testSetRegexEntireWords()
    {
        $obj = new Search();
        $obj->setRegexEntireWords('entire_words');

        $this
            ->assertSame($obj->isEntireWords(), true);
        $this
            ->assertSame($obj->getRegex(), '~\b\b~iu');

        $obj->setRegexEntireWords(false);
        $this
            ->assertSame($obj->isEntireWords(), false);
        $this
            ->assertSame($obj->getRegex(), '~~iu');
    }

    public function testSetRegexEntireString()
    {
        $obj = new Search();
        $obj->setRegexEntireString('entire_string');
        $this
            ->assertSame($obj->isEntireString(), true);
        $this
            ->assertSame($obj->getRegex(), '~^$~iu');

        $obj->setRegexEntireString(false);
        $this
            ->assertSame($obj->isEntireString(), false);
        $this
            ->assertSame($obj->getRegex(), '~~iu');
    }

    public function testMultipleRegexChanges()
    {
        $obj = new Search();
        $obj
            ->setSearchTerms('A new hope')
            ->setRegexEntireString(false)
            ->setRegexCaseInsensitive('sensitive');
        $this->assertSame($obj->getRegex(), '~A new hope~u');

        $obj->setSearchTerms('Return of the jedi')
            ->setRegexEntireString(true)
            ->setRegexCaseInsensitive('');
        $this
            ->assertSame($obj->getRegex(), '~^Return of the jedi$~iu');
    }

    public function testGrep()
    {
        include TMX . 'fr/cache_fr_gecko_strings.php';
        $obj = new Search();
        $obj
            ->setSearchTerms('marque');
        $this->assertEqualsCanonicalizing($obj->grep($tmx),
                [
                    'mobile/android/base/android_strings.dtd:bookmark'                                => 'Marquer cette page',
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel'             => 'Marque-page',
                    'browser/chrome/browser/syncQuota.properties:collection.bookmarks.label'          => 'Marque-pages',
                    'browser/chrome/browser/places/bookmarkProperties.properties:dialogTitleAddMulti' => 'Nouveaux marque-pages',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label'                    => 'Marquer cette page',
                ]
            );

        $obj
            ->setSearchTerms('marquer cette');
        $this->assertEqualsCanonicalizing($obj->grep($tmx),
                [
                    'mobile/android/base/android_strings.dtd:bookmark'             => 'Marquer cette page',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label' => 'Marquer cette page',
                ]
            );

        $obj
            ->setSearchTerms('...')
            ->setRegexEntireString('entire_string');

        $this->assertEqualsCanonicalizing($obj->grep($tmx),
                [
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label2' => '...',
                ]
            );

        $obj
            ->setSearchTerms('...')
            ->setRegexEntireString('entire_string');

        $this->assertEqualsCanonicalizing($obj->grep(['test_repo' => $tmx], false),
                [
                    'test_repo' => ['browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label2' => '...'],
                ]
            );
    }

    public function testSetRepository()
    {
        $obj = new Search();
        $obj->setRepository('foobar');
        $this->assertSame($obj->getRepository(), 'gecko_strings');

        $obj->setRepository('gecko_strings');
        $this->assertSame($obj->getRepository(), 'gecko_strings');
    }

    public function testSetSearchType()
    {
        $obj = new Search();
        $obj->setSearchType('foobar');
        $this->assertSame($obj->getSearchType(), 'strings');

        $obj->setSearchType('entities');
        $this->assertSame($obj->getSearchType(), 'entities');
    }

    public function testGetSearchTypes()
    {
        $obj = new Search();
        $this->assertEqualsCanonicalizing($obj->getSearchTypes(), ['strings', 'entities', 'strings_entities']);
    }

    public function testSetLocales()
    {
        $obj = new Search();
        $obj->setLocales(['en-US', 'fr', 'de', 'it']);
        $this->assertSame($obj->getLocale('source'), 'en-US');
        $this->assertSame($obj->getLocale('target'), 'fr');
        $this->assertSame($obj->getLocale('extra'), 'de');

        $obj->setLocales(['en-US', 'fr', 'fr']);
        $this->assertSame($obj->getLocale('source'), 'en-US');
        $this->assertSame($obj->getLocale('target'), 'fr');
        $this->assertSame($obj->getLocale('extra'), '');
    }
}
