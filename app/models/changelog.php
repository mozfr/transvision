<?php
namespace Transvision;

include MODELS . 'changelog_data.php';

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
        case 'regression':
            $type = 'regression';
            $text = 'Regression';
            break;
        default:
            $type = '';
            $text = 'Other';
            break;
    }

    return "<span class=\"release_tag {$type}\">{$text}</span>";
};

$get_sections = function ($section) {
    switch ($section) {
        case 'end_user':
            $text = 'End user visible changes';
            break;
        case 'external_api':
            $text = 'External API changes';
            break;
        case 'developers':
            $text = 'Changes for Transvision developers';
            break;
        case 'code':
            $text = 'Code changes';
            break;
        default:
            $text = 'Other changes';
            break;
    }

    return $text;
};

// Helper to generate the list of patches for the version
$github_link = function ($release, $releases) {
    if ($release > 1) {
        $keys = array_keys($releases);
        $previous = $keys[array_search($release, $keys, true) - 1];

        return <<<LINK
<p class="github_link">See the complete list of
<a href="https://github.com/mozfr/transvision/compare/v{$previous}...v{$release}">code changes from version {$previous} on GitHub</a>.
</p>
LINK;
    }
};

// Helper to generate a release title block
$release_title = function ($version, $releases) {
    return <<<TITLE
<h2 class="release_number" id="v{$version}"><a href="#v{$version}">Version {$version}
<span class="release_date">{$releases[$version]}</span></a>
</h2>
TITLE;
};

// Helper to generate a GitHub issues link
$issue = function ($issues) {
    $link = '';
    foreach ($issues as $issue) {
        $link .= "<a href=\"https://github.com/mozfr/transvision/issues/{$issue}\" class=\"github_issue\">Issue {$issue}</a>";
        $link .= ($issue === end($issues)) ? '. ' : ' + ';
    }

    return $link;
};

// Helper to generate a GitHub commit link
$commit = function ($commits) {
    $link = '';
    foreach ($commits as $commit) {
        $link .= "<a href=\"https://github.com/mozfr/transvision/commit/{$commit}\" class=\"github_commit\">"
                . substr($commit, 0, 7) . '</a>';
        $link .= ($commit === end($commits)) ? '' : '+';
    }

    return $link;
};

// Helper to generate a link for each author
$authors = function ($list) use ($author_urls) {
    $text = '&nbsp;(';

    foreach ($list as $author) {
        if (isset($author_urls[$author])) {
            $text .= "<a href=\"{$author_urls[$author]}\">{$author}</a>";
        } else {
            $text .= $author;
        }
        $text .= ($author === end($list)) ? ')' : ', ';
    }

    return $text;
};

$latest_undocumented_release = function() use ($releases, $github_link, $release_title) {
  $text = '';
  $version_number = str_replace("\n", '', str_replace('v', '', file_get_contents(CACHE_PATH . 'tag.txt')));
  end($releases);
  $last_release = key($releases);

  if ($version_number != $last_release) {
    $github_relnotes = "https://github.com/mozfr/transvision/releases/tag/v{$version_number}";
    $releases[$version_number] = 'Unknown';
    $text .= $release_title($version_number, $releases);
    $text .= "<p>This release is undocumented, but you can know more in the <a href=\"{$github_relnotes}\">GitHub release notes</a> and with the list of commits below since the last documented release.</p>";
    $text .= $github_link($version_number, $releases);
  }

  return $text;
};
