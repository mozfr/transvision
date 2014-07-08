<?php
namespace Transvision;

require_once INC . 'l10n-init.php';

// rtl support
$direction1 = RTLSupport::getDirection($source_locale);
$direction2 = RTLSupport::getDirection($locale);

if (isset($_GET['locale'])) {
    $requested_locale = Project::getLocaleInContext($_GET['locale'], $repo);
    if (in_array($requested_locale, $all_locales)) {
        $locale = $requested_locale;
    }
}

$source = Utils::getRepoStrings('en-US', $repo);
$target = Utils::getRepoStrings($locale, $repo);

$channel_selector = Utils::getHtmlSelectOptions(
    array_intersect_key(
        $repos_nice_names,
        array_flip($repos)
    ),
    $repo,
    true
);

// build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions(
    Project::getRepositoryLocales($repo),
    $locale
);

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

$source = array_map(['Transvision\AnalyseStrings', 'cleanUpEntities'], $source);
$target = array_map(['Transvision\AnalyseStrings', 'cleanUpEntities'], $target);

$mismatch = AnalyseStrings::differences($source, $target, $repo);

// Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them
$bugzilla_component = rawurlencode(
    Bugzilla::collectLanguageComponent(
        $locale,
        Bugzilla::getBugzillaComponents()
    )
);

$bugzilla_link = 'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component='
               . $bugzilla_component
               . '&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D';


$table = "<table class='collapsable'><tr><th>Entity</th><th>en-US</th><th>{$locale}</th></tr>";

foreach ($mismatch as $entity) {

    if ($repo == 'mozilla_org') {
        $path_locale1 = VersionControl::svnPath('en-US', $repo, $entity);
        $path_locale2 = VersionControl::svnPath($locale, $repo, $entity);
    } else {
        $path_locale1 = VersionControl::hgPath('en-US', $repo, $entity);
        $path_locale2 = VersionControl::hgPath($locale, $repo, $entity);
    }

    // Link to entity
    $entity_link = "?sourcelocale=en-US"
                 . "&locale={$locale}"
                 . "&repo={$repo}"
                 . "&search_type=entities&recherche={$entity}";

    $bug_summary = rawurlencode("Translation update proposed for {$entity}");
    $bug_message = rawurlencode(
        html_entity_decode(
            "The string:\n{$source[$entity]}\n\n"
            . "Is translated as:\n{$target[$entity]}\n\n"
            . "And should be:\n\n\n\n"
            . "Feedback via Transvision:\n"
            . "http://transvision.mozfr.org/{$entity_link}"
        )
    );

    $complete_link = $bugzilla_link . '&short_desc=' . $bug_summary . '&comment=' . $bug_message;

    $table .= "<tr>
                    <td>
                       <span class='celltitle'>Entity</span>
                       <a class='linktoentity' href=\"/{$entity_link}\">" . ShowResults::formatEntity($entity) . "</a>
                    </td>
                    <td dir='{$direction1}'>
                       <span class='celltitle'>en-US</span>
                       <div class='string'>{$source[$entity]}</div>
                       <div class='infos'>
                        <a class='source_link' href='{$path_locale1}'><em>&lt;source&gt;</em></a>
                       </div>
                    </td>
                     <td dir='{$direction2}'>
                       <span class='celltitle'>$locale</span>
                       <div class='string'>{$target[$entity]}</div>
                       <div class='infos'>
                        <a class='source_link' href='{$path_locale2}'><em>&lt;source&gt;</em></a>
                        <a class='bug_link' target='_blank' href='{$complete_link}'>
                        &lt;report a bug&gt;
                      </a>
                       </div>
                    </td>
                </tr>";
}
$table .= '</table>';

print $table;
