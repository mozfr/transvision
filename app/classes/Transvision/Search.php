<?php
namespace Transvision;

/**
 * Search class
 *
 * Allows searching for data in our repositories using a fluent interface.
 * Currently, only the regex part (definition of the search) is implemented.
 * e.g.:
 * $search = (new Search)
 *     ->setSearchTerms('Bookmark this page')
 *     ->setRegexWholeWords(true)
 *     ->setRegexCaseInsensitive(true)
 *     ->setRegexPerfectMatch(false);
 */
class Search
{
    /**
     * The trimmed string searched, we keep it   as the canonical reference
     * @var string
     */
    protected $search_terms;

    /**
     * The generated regex string updated dynamically via updateRegex()
     * @var string
     */
    protected $regex;

    /**
     * Case sensibility of the regex
     * @var string
     */
    protected $regex_case;

    /**
     * Consider the space separated string as a single word for search
     * @var string
     */
    protected $regex_whole_words;

    /**
     * Only return strings that match the search perfectly (case excluded)
     * @var boolean
     */
    protected $regex_perfect_match;

    /**
     * The search terms for the regex, these differ from $search_terms as
     * they can be changed dynamically via setRegexSearchTerms()
     * @var string
     */
    protected $regex_search_terms;

    /**
     * We set the default values for a search
     */
    public function __construct()
    {
        $this->search_terms = '';
        $this->regex = '';
        $this->regex_case = 'i';
        $this->regex_whole_words = '';
        $this->regex_perfect_match = false;
        $this->regex_search_terms = '';
    }

    /**
     * Store the searched string in $search_terms and in $regex_search_terms
     *
     * @param  string $string String we want to search for
     * @return $this
     */
    public function setSearchTerms($string)
    {
        $this->search_terms = trim($string);
        $this->regex_search_terms = $this->search_terms;
        $this->updateRegex();

        return $this;
    }

    /**
     * Allows setting a new searched term for the regex.
     * This is mostly useful when you have a multi-words search and need to
     * loop through all the words to return results.
     *
     * @param  string $string The string we want to update the regex for
     * @return $this
     */
    public function setRegexSearchTerms($string)
    {
        $this->regex_search_terms = $string;
        $this->updateRegex();

        return $this;
    }

    /**
     * Set the regex case to be insensitive.
     *
     * @param  boolean $flag True is sensitive, false insensitive
     * @return $this
     */
    public function setRegexCaseInsensitive($flag)
    {
        $this->regex_case = (boolean) $flag ? '' : 'i';
        $this->updateRegex();

        return $this;
    }

    /**
     * Set the regex to only return perfect matches for the searched string.
     * We cast the value to a boolean because we usually get it from a GET.
     *
     * @param  boolean $flag Set to True for a perfect match
     * @return $this
     */
    public function setRegexPerfectMatch($flag)
    {
        $this->regex_perfect_match = (boolean) $flag;
        $this->updateRegex();

        return $this;
    }

    /**
     * Set the regex so as that a multi-word search is taken as a single word.
     * We cast the value to a boolean because we usually get it from a GET.
     *
     * @param  boolean $flag A string evaluated to True will add \b to the regex
     * @return $this
     */
    public function setRegexWholeWords($flag)
    {
        $this->regex_whole_words = (boolean) $flag ? '\b' : '';
        $this->updateRegex();

        return $this;
    }

    /**
     * Update the $regex_search_terms value every time a setter to the regex
     * is called.
     *
     * @return $this
     */
    private function updateRegex()
    {
        if ($this->regex_perfect_match) {
            $search =  '^' . $this->regex_search_terms . '$';
        } else {
            $search = preg_quote($this->regex_search_terms, '~');
        }

        $this->regex =
            '~'
            . $this->regex_whole_words
            . $search
            . $this->regex_whole_words
            . '~'
            . $this->regex_case
            . 'u';

        return $this;
    }

    /**
     * Get the regex string
     *
     * @return string The regex
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Get the state of regex_perfect_match
     *
     * @return boolean True if the regex searches for a perfect string match
     */
    public function isPerfectMatch()
    {
        return $this->regex_perfect_match;
    }

    /**
     * Get search terms
     *
     * @return string Searched terms
     */
    public function getSearchTerms()
    {
        return $this->search_terms;
    }

    /**
     * Get search terms in regex
     *
     * @return string Searched terms in regex
     */
    public function getRegexSearchTerms()
    {
        return $this->regex_search_terms;
    }

    /**
     * Get the regex case
     *
     * @return string Return 'i' for case insensitive search, '' for sensitive
     */
    public function getRegexCase()
    {
        return $this->regex_case;
    }

    /**
     * Get the regex whole words
     *
     * @return boolean True if we have the 'whole words' option for the regex
     */
    public function isWholeWords()
    {
        return $this->regex_whole_words;
    }

    /**
     * Grep data in regex
     *
     * @param  array $source_strings The array of strings to be filtered
     * @return array Return an array of filtered strings
     */
    public function grep($source_strings)
    {
        return preg_grep($this->getRegex(), $source_strings);
    }
}
