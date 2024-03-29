<?php
namespace Transvision;

/**
 * Search class
 *
 * Allows searching for data in our repositories using a fluent interface.
 * Currently, only the regex part (definition of the search) and defining the
 * search options are implemented.
 * e.g.:
 * $search = (new Search)
 *     ->setSearchTerms('Bookmark this page')
 *     ->setRegexCaseInsensitive(true)
 *     ->setRegexEntireString(false)
 *     ->setEachWord(false)
 *     ->setEntireWords(false)
 *     ->setRepository('gecko_strings')
 *     ->setSearchType('strings')
 *     ->setLocales(['en-US', 'fr']);
 */
class Search
{
    /**
     * The trimmed string searched, we keep it as the canonical reference
     *
     * @var string
     */
    protected $search_terms;

    /**
     * The generated regex string updated dynamically via updateRegex()
     *
     * @var string
     */
    protected $regex;

    /**
     * Case sensibility of the regex
     *
     * @var string
     */
    protected $regex_case;

    /**
     * Only return strings that entirely match the search (case excluded)
     *
     * @var boolean
     */
    protected $regex_entire_string;

    /**
     * Only return strings where entire words match the search (case excluded)
     *
     * @var boolean
     */
    protected $regex_entire_words;

    /**
     * Set to search for each word in the query instead of using it as a whole.
     *
     * @var boolean
     */
    protected $each_word;

    /**
     * The search terms for the regex, these differ from $search_terms as
     * they can be changed dynamically via setRegexSearchTerms()
     *
     * @var string
     */
    protected $regex_search_terms;

    /**
     * The repository we want to search strings in
     *
     * @var string
     */
    protected $repository;

    /**
     * The type of search we will perform
     *
     * @var string
     */
    protected $search_type;

    /**
     * The different types of search we can perform
     *
     * @var array
     */
    protected $search_types = ['strings', 'entities', 'strings_entities'];

    /**
     * The different options associated to searches
     *
     * @var array
     */
    protected $form_search_options = [
        'case_sensitive', 'entire_string', 'repo',
        'search_type', 'each_word', 'entire_words',
    ];

    /**
     * The different checkboxes for the search Form
     *
     * @var array
     */
    protected $form_checkboxes;

    /**
     * The different locales we will use in views
     *
     * @var array
     */
    protected $locales;

    /**
     * We set the default values for a search
     */
    public function __construct()
    {
        $this->search_terms = '';
        $this->regex = '';
        $this->regex_case = 'i';
        $this->regex_entire_string = false;
        $this->regex_entire_words = false;
        $this->each_word = false;
        $this->regex_search_terms = '';
        $this->repository = 'gecko_strings';
        $this->search_type = 'strings';
        $this->locales = [];
        $this->form_checkboxes = array_diff(
            $this->form_search_options,
            ['repo', 'search_type']
        );
        $this->form_checkboxes = array_values($this->form_checkboxes); // Reset keys
    }

    /**
     * Store the searched string in $search_terms and in $regex_search_terms
     *
     * @param string $string String we want to search for
     *
     * @return $this
     */
    public function setSearchTerms($string)
    {
        $this->search_terms = $string;
        $this->regex_search_terms = $this->search_terms;
        $this->updateRegex();

        return $this;
    }

    /**
     * Allows setting a new searched term for the regex.
     * This is mostly useful when you have a multi-words search and need to
     * loop through all the words to return results.
     *
     * @param string $string The string we want to update the regex for
     *
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
     * @param boolean $flag True is sensitive, false insensitive
     *
     * @return $this
     */
    public function setRegexCaseInsensitive($flag)
    {
        $this->regex_case = (boolean) $flag ? '' : 'i';
        $this->updateRegex();

        return $this;
    }

    /**
     * Set the regex to only return strings that entirely match the
     * searched string.
     * We cast the value to a boolean because we usually get it from a GET.
     *
     * @param boolean $flag Set to True for an entire string match
     *
     * @return $this
     */
    public function setRegexEntireString($flag)
    {
        $this->regex_entire_string = (boolean) $flag;
        $this->updateRegex();

        return $this;
    }

    /**
     * Set the regex to only return strings where entire words match
     * the searched string.
     * We cast the value to a boolean because we usually get it from a GET.
     *
     * @param boolean $flag Set to True for an entire words match
     *
     * @return $this
     */
    public function setRegexEntireWords($flag)
    {
        $this->regex_entire_words = (boolean) $flag ? '\b' : '';
        $this->updateRegex();

        return $this;
    }

    /**
     * Set to search for each word in the query instead of using it as a whole.
     * We cast the value to a boolean because we usually get it from a GET.
     *
     * @param boolean $flag Set to True to search for each word.
     *
     * @return $this
     */
    public function setEachWord($flag)
    {
        $this->each_word = (boolean) $flag;

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
        $search = preg_quote($this->regex_search_terms);
        if ($this->regex_entire_string) {
            $search = "^{$search}$";
        }

        $this->regex =
            '~'
            . $this->regex_entire_words
            . $search
            . $this->regex_entire_words
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
     * Get the state of regex_entire_string
     *
     * @return boolean True if the regex searches for an entire string match
     */
    public function isEntireString()
    {
        return $this->regex_entire_string;
    }

    /**
     * Get the state of each_word
     *
     * @return boolean True if the search should be for each word.
     */
    public function isEachWord()
    {
        return $this->each_word;
    }

    /**
     * Get the state of entire_words
     *
     * @return boolean True if the search should be only for entire word.
     */
    public function isEntireWords()
    {
        return $this->regex_entire_words == '\b' ? true : false;
    }

    /**
     * Get the state of case_sensitive
     *
     * @return boolean False if the search should be case sensitive
     */
    public function isCaseSensitive()
    {
        return $this->regex_case == 'i' ? false : true;
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
     * Grep data in regex
     *
     * @param array   $source_strings The array of strings to be filtered
     * @param boolean $flat_source    If true, the source source is a flat array
     *
     * @return array Return an array of filtered strings
     */
    public function grep($source_strings, $flat = true)
    {
        if ($flat) {
            return preg_grep($this->getRegex(), $source_strings);
        } else {
            $results = [];
            foreach ($source_strings as $repo => $strings) {
                $results[$repo] = preg_grep($this->getRegex(), $strings);
            }

            return $results;
        }
    }

    /**
     * Set the repository we want to search strings in
     *
     * @param string $repository The name of the repository
     *
     * @return $this
     */
    public function setRepository($repository)
    {
        if (Project::isValidRepository($repository)) {
            $this->repository = $repository;
        }

        return $this;
    }

    /**
     * Get the repository we are searching strings in
     *
     * @return string Name of the repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set the type of search we want to perform
     *
     * @param string $type The type of search we will perform
     *
     * @return $this
     */
    public function setSearchType($type)
    {
        if (in_array($type, $this->search_types)) {
            $this->search_type = $type;
        }

        return $this;
    }

    /**
     * Get the type of search we want to perform
     *
     * @return string Type of search
     */
    public function getSearchType()
    {
        return $this->search_type;
    }

    /**
     * Get all the types of search we can perform
     *
     * @return array Types of search
     */
    public function getSearchTypes()
    {
        return $this->search_types;
    }

    /**
     * Get all the options we use in the search form
     *
     * @return array Form options
     */
    public function getFormSearchOptions()
    {
        return $this->form_search_options;
    }

    /**
     * Get all the checkboxes we use in the search form
     *
     * @return array Form checkboxes
     */
    public function getFormCheckboxes()
    {
        return $this->form_checkboxes;
    }

    /**
     * Set the locales we will use for the search with this structure:
     * $this->locales =
     * [
     * 	   'source' => 'en-US',
     * 	   'target' => 'de',
     * 	   'extra'  => 'ar',
     * ];
     *
     * The 'extra' value is optional and used in views comparing
     * data for 3 locales.
     *
     * @param array $locales The locale codes
     *
     * @return $this
     */
    public function setLocales(array $locales)
    {
        // We only allow up to 3 locales for analysis
        $locales = array_slice($locales, 0, 3);

        $this->locales = [
            'source' => $locales[0],
            'target' => $locales[1],
        ];

        if (count($locales) == 3) {
            // We check if the 3rd locale is the same as the 2nd one
            if ($locales[2] != $locales[1]) {
                $this->locales['extra'] = $locales[2];
            }
        }

        return $this;
    }

    /**
     * Get a locale used for searching
     *
     * @param string $type The type of locale we want, can be
     *                     'source', 'target' or 'extra'.
     *
     * @return string Locale code or an empty string
     */
    public function getLocale($type)
    {
        return isset($this->locales[$type]) ? $this->locales[$type] : '';
    }
}
