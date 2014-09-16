<?php
namespace Transvision;

class Desktop extends Project
{
    public $locale;
    public $repos;
    public static $components = ['browser', 'calendar', 'chat', 'dom', 'editor',
                          'extensions', 'mail', 'mobile', 'netwerk',
                          'other-licenses', 'security', 'services', 'suite',
                          'toolkit', 'webapprt'];

    public function __construct() {
        $this->repos = $this->getRepositories();
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Get the list of repositories for Desktop Applications
     *
     * @return array List of local repositories folder names
     */
    public static function getRepositories()
    {
        return array_diff(
            array_diff(parent::getRepositories(), ['mozilla_org']),
            Gaia::getRepositories()
        );
    }

    /**
     * Return the list of components by parsing a set of entities.
     * Components are folders at the root of desktop repos ("desktop", "mobile", etc.)
     *
     * @return array List of components
     */
    public static function getComponents() {
        return self::$components;
    }
}
