<?php
namespace Transvision;

$releases = [
    '1.0'   => '2012-07-27',
    '1.1'   => '2012-08-01',
    '1.2'   => '2012-08-10',
    '1.3'   => '2012-08-17',
    '1.4'   => '2012-09-04',
    '1.5'   => '2012-10-02',
    '1.6'   => '2012-10-18',
    '1.7'   => '2012-10-24',
    '1.8'   => '2013-01-04',
    '1.9'   => '2013-01-11',
    '2.0'   => '2013-01-18',
    '2.1'   => '2013-01-30',
    '2.2'   => '2013-02-28',
    '2.3'   => '2013-03-22',
    '2.4'   => '2013-04-10',
    '2.5'   => '2013-04-18',
    '2.6'   => '2013-06-14',
    '2.7'   => '2013-07-05',
    '2.8'   => '2013-08-09',
    '2.9'   => '2013-10-26',
    '3.0'   => '2013-12-18',
    '3.1'   => '2014-02-24',
    '3.2'   => '2014-03-17',
    '3.3'   => '2014-05-20',
    '3.4'   => '2014-06-25',
    '3.5'   => '2014-09-29',
    '3.5.1' => '2014-10-07',
    '3.6'   => '2015-01-22',
    '3.7'   => '2015-04-09',
];

// Helper to generate CSS class tags
$relnotes = function ($tag) {
    switch ($tag) {
        case 'better':
            $type = 'improvement';
            $text = 'Improvement';
            break;
        case 'bug':
            $type = 'bugfix';
            $text = 'Bug fix';
            break;
        case 'change':
            $type = 'change';
            $text = 'Change';
            break;
        case 'experimental':
            $type = 'exp_feature';
            $text = 'Experimental';
            break;
        case 'new':
            $type = 'new_feature';
            $text = 'New feature';
            break;
        default:
            $type = '';
            $text = 'Other';
            break;
    }

    return "<span class=\"release_tag {$type}\">{$text}</span> ";
};

// Helper to generate the list of patches for the version
$github_link = function ($release) use ($releases) {
    $keys = array_keys($releases);
    $previous = $keys[array_search($release, $keys) - 1];

    return <<<LINK
<p class="github_link">See the complete list of
<a href="https://github.com/mozfr/transvision/compare/v{$previous}...v{$release}">code changes from version {$previous} on Github</a>.
</p>
LINK;
};

// Helper to generate a release title block
$release_title = function ($version) use ($releases) {
    return <<<TITLE
<h2 class="release_number" id="v{$version}"><a href="#v{$version}">Version {$version}
<span class="release_date">{$releases[$version]}</span></a>
</h2>
TITLE;
};

// Helper to generate a GitHub issues link
$issue = function () {
    $link = '';
    $issues = func_get_args();
    foreach ($issues as $issue) {
        $link .= "<a href=\"https://github.com/mozfr/transvision/issues/{$issue}\" class=\"github_issue\">Issue {$issue}</a>";
        $link .=  ($issue === end($issues)) ? '. ' : ' + ';
    }

    return $link;
};
