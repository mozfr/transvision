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
            ->string($obj->getRegexCase())
                ->isEqualTo('i');
        $this
            ->string($obj->isWholeWords())
                ->isEqualTo('');
        $this
            ->boolean($obj->isPerfectMatch())
                ->isEqualTo(false);
        $this
            ->string($obj->getRegexSearchTerms())
                ->isEqualTo('');
        $this
            ->string($obj->getRepository())
                ->isEqualTo('aurora');
    }

    public function testSetSearchTerms()
    {
        $obj = new _Search();
        $obj->setSearchTerms(' foobar ');
        $this
            ->string($obj->getSearchTerms())
                ->isEqualTo('foobar');
        $this
            ->string($obj->getRegexSearchTerms())
                ->isEqualTo('foobar');
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

    public function testSetRegexPerfectMatch()
    {
        $obj = new _Search();
        $obj->setRegexPerfectMatch('perfect_match');
        $this
            ->boolean($obj->isPerfectMatch())
                ->isEqualTo(true)
            ->string($obj->getRegex())
                ->isEqualTo('~^$~iu');

        $obj->setRegexPerfectMatch(false);
        $this
            ->boolean($obj->isPerfectMatch())
                ->isEqualTo(false)
            ->string($obj->getRegex())
                ->isEqualTo('~~iu');
    }

    public function testSetRegexWholeWords()
    {
        $obj = new _Search();
        $obj->setRegexWholeWords('whole_word');
        $this
            ->string($obj->isWholeWords())
                ->isEqualTo(true)
            ->string($obj->getRegex())
                ->isEqualTo('~\b\b~iu');

        $obj->setRegexWholeWords(false);
        $this
            ->string($obj->isWholeWords())
                ->isEqualTo(false)
            ->string($obj->getRegex())
                ->isEqualTo('~~iu');
    }

    public function testMultipleRegexChanges()
    {
        $obj = new _Search();
        $obj
            ->setSearchTerms('A new hope')
            ->setRegexWholeWords('whole_word')
            ->setRegexPerfectMatch(false)
            ->setRegexCaseInsensitive('sensitive');
        $this->string($obj->getRegex())
                ->isEqualTo('~\bA new hope\b~u');

        $obj->setSearchTerms('Return of the jedi')
            ->setRegexWholeWords('')
            ->setRegexPerfectMatch(true)
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
            ->setSearchTerms('Marque')
            ->setRegexWholeWords('whole_word');
        $this->array($obj->grep($tmx))
            ->isEqualTo(
                [
                    'browser/chrome/browser/places/places.properties:bookmarkResultLabel'             => 'Marque-page',
                    'browser/chrome/browser/syncQuota.properties:collection.bookmarks.label'          => 'Marque-pages',
                    'browser/chrome/browser/places/bookmarkProperties.properties:dialogTitleAddMulti' => 'Nouveaux marque-pages',
                ]
            );

        $obj
            ->setRegexWholeWords('')
            ->setSearchTerms('...')
            ->setRegexPerfectMatch('perfect_match');

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
}
