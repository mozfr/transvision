<?php
namespace Transvision;

class Gaia extends Project
{
    public $locale;
    public $repos;

    public function __construct() {
        $this->repos = $this->getRepositories();
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Get the list of repositories for Gaia.
     * The list is sorted by age (latest master -> older branch)
     *
     * @return array list of local repositories for Gaia
     */
    public static function getRepositories()
    {
        $gaia_repos = array_filter(
            parent::getRepositories(),
            function($value) {
                if (Strings::startsWith($value, 'gaia_')) {
                    return $value;
                }
            }
        );

        // Sort repos from latest branch to oldest branch
        rsort($gaia_repos);
        // gaia repo is the latest master branch, always first
        array_unshift($gaia_repos, 'gaia');

        return $gaia_repos;
    }
}
