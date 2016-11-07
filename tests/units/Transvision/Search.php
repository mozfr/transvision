<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Search as _Search;

require_once __DIR__ . '/../bootstrap.php';

class Search extends atoum\test
{
    public function testConstructor()
    {
        $obj = new _Search();
        $this
            ->string($obj->getSearchTerms())
                ->isEqualTo('');
        $this
            ->string($obj->getRegex())
                ->isEqualTo('');
        $this
            ->boolean($obj->isCaseSensitive())
                ->isEqualTo(false);
        $this
            ->boolean($obj->isEachWord())
                ->isEqualTo(false);
        $this
            ->boolean($obj->isEntireString())
                ->isEqualTo(false);
        $this
            ->string($obj->getRegexSearchTerms())
                ->isEqualTo('');
        $this
            ->string($obj->getRepository())
                ->isEqualTo('aurora');
        $this
            ->string($obj->getSearchType())
                ->isEqualTo('strings');
        $this
            ->string($obj->getLocale('source'))
                ->isEqualTo('');
        $this
            ->array($obj->getFormSearchOptions())
                ->isEqualTo(
                    [
                        'case_sensitive', 'entire_string', 'repo',
                        'search_type', 't2t', 'each_word', 'entire_words',
                    ]);
        $this
            ->array($obj->getFormCheckboxes())
                ->isEqualTo(
                    [
                        'case_sensitive', 'entire_string', 't2t',
                        'each_word', 'entire_words',
                    ]);
    }

    public function testSetSearchTerms()
    {
        $obj = new _Search();
        $obj->setSearchTerms(' foobar ');
        $this
            ->string($obj->getSearchTerms())
                ->isEqualTo(' foobar ');
        $this
            ->string($obj->getRegexSearchTerms())
                ->isEqualTo(' foobar ');
    }

    public function testSetRegexSearchTerms()
    {
        $obj = new _Search();
        $obj->setRegexSearchTerms('A new hope');
        $this
            ->string($obj->getRegexSearchTerms())
                ->isEqualTo('A new hope')
            ->string($obj->getRegex())
                ->isEqualTo('~A new hope~iu');
    }

    public function testSetRegexCaseInsensitive()
    {
        $obj = new _Search();

        // Test strings (as passed from GET)
        $obj->setRegexCaseInsensitive('sensitive');
        $this
            ->string($obj->getRegex())
                ->isEqualTo('~~u');

        $obj->setRegexCaseInsensitive('');
        $this
            ->string($obj->getRegex())
                ->isEqualTo('~~iu');

        // Test boolean values
        $obj->setRegexCaseInsensitive(true);
        $this
            ->string($obj->getRegex())
                ->isEqualTo('~~u');

        $obj->setRegexCaseInsensitive(false);
        $this
            ->string($obj->getRegex())
                ->isEqualTo('~~iu');
    }

    public function testSetRegexEntireWords()
    {
        $obj = new _Search();
        $obj->setRegexEntireWords('entire_words');

        $this
            ->boolean($obj->isEntireWords())
                ->isEqualTo(true)
            ->string($obj->getRegex())
                ->isEqualTo('~\b\b~iu');

        $obj->setRegexEntireWords(false);
        $this
            ->boolean($obj->isEntireWords())
                ->isEqualTo(false)
            ->string($obj->getRegex())
                ->isEqualTo('~~iu');
    }

    public function testSetRegexEntireString()
    {
        $obj = new _Search();
        $obj->setRegexEntireString('entire_string');
        $this
            ->boolean($obj->isEntireString())
                ->isEqualTo(true)
            ->string($obj->getRegex())
                ->isEqualTo('~^$~iu');

        $obj->setRegexEntireString(false);
        $this
            ->boolean($obj->isEntireString())
                ->isEqualTo(false)
            ->string($obj->getRegex())
                ->isEqualTo('~~iu');
    }

    public function testMultipleRegexChanges()
    {
        $obj = new _Search();
        $obj
            ->setSearchTerms('A new hope')
            ->setRegexEntireString(false)
            ->setRegexCaseInsensitive('sensitive');
        $this->string($obj->getRegex())
                ->isEqualTo('~A new hope~u');

        $obj->setSearchTerms('Return of the jedi')
            ->setRegexEntireString(true)
            ->setRegexCaseInsensitive('');
        $this
            ->string($obj->getRegex())
                ->isEqualTo('~^Return of the jedi$~iu');
    }

    public function testGrep()
    {
        include_once TMX . 'fr/cache_fr_central.php';
        $obj = new _Search();
        $obj
            ->setSearchTerms('marque');
        $this->array($obj->grep($tmx))
            ->isEqualTo(
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
        $this->array($obj->grep($tmx))
            ->isEqualTo(
                [
                    'mobile/android/base/android_strings.dtd:bookmark'             => 'Marquer cette page',
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label' => 'Marquer cette page',
                ]
            );

        $obj
            ->setSearchTerms('...')
            ->setRegexEntireString('entire_string');

        $this->array($obj->grep($tmx))
            ->isEqualTo(
                [
                    'browser/chrome/browser/browser.dtd:bookmarkThisPageCmd.label2' => '...',
                ]
            );
    }

    public function testSetRepository()
    {
        $obj = new _Search();
        $obj->setRepository('foobar');
        $this->string($obj->getRepository())
            ->isEqualTo('aurora');

        $obj->setRepository('release');
        $this->string($obj->getRepository())
            ->isEqualTo('release');
    }

    public function testSetSearchType()
    {
        $obj = new _Search();
        $obj->setSearchType('foobar');
        $this->string($obj->getSearchType())
            ->isEqualTo('strings');

        $obj->setSearchType('entities');
        $this->string($obj->getSearchType())
            ->isEqualTo('entities');
    }

    public function testGetSearchTypes()
    {
        $obj = new _Search();
        $this->array($obj->getSearchTypes())
            ->isEqualTo(['strings', 'entities', 'strings_entities']);
    }

    public function testSetLocales()
    {
        $obj = new _Search();
        $obj->setLocales(['en-US', 'fr', 'de', 'it']);
        $this->string($obj->getLocale('source'))
            ->isEqualTo('en-US');
        $this->string($obj->getLocale('target'))
            ->isEqualTo('fr');
        $this->string($obj->getLocale('extra'))
            ->isEqualTo('de');

        $obj->setLocales(['en-US', 'fr', 'fr']);
        $this->string($obj->getLocale('source'))
            ->isEqualTo('en-US');
        $this->string($obj->getLocale('target'))
            ->isEqualTo('fr');
        $this->string($obj->getLocale('extra'))
            ->isEqualTo('');
    }
}
