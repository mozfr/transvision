<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

if ($error_count > 0) {
    $search_id = 'check_variable';
    $content = "<h2><span class=\"results_count_{$search_id}\">"
        . Utils::pluralize($error_count, 'result')
        . "</span> found</h2>\n";

    if (isset($filter_block)) {
        $content .= "<div id='filters'>" .
                    "  <h4>Filter by folder:</h4>\n" .
                    "  <a href='#showall' id='showall' class='filter'>Show all results</a>\n" .
                    $filter_block .
                    "</div>\n";
    }

    $content .= "
        <table class='collapsable results_table sortable {$search_id}'>
          <thead>
            <tr class='column_headers'>
              <th>Entity</th>
              <th>{$source_locale}</th>
              <th>{$locale}</th>
            </tr>
          </thead>
          <tbody>\n";

    // Get the tool used to edit strings for the target locale
    $toolUsedByTargetLocale = Project::getLocaleTool($locale);

    foreach ($var_errors as $string_id) {
        // Link to entity
        $string_id_link = "?sourcelocale={$source_locale}" .
                          "&locale={$locale}" .
                          "&repo={$repo}" .
                          "&search_type=entities&recherche={$string_id}" .
                          '&entire_string=entire_string';
        $bugzilla_link = Bugzilla::reportErrorLink(
            $locale, $string_id, $source[$string_id],
            $target[$string_id], $repo, $string_id_link
        );

        $path_source_locale = VersionControl::hgPath($source_locale, $repo, $string_id);
        $path_target_locale = VersionControl::hgPath($locale, $repo, $string_id);
        $edit_link = $toolUsedByTargetLocale != ''
            ? ShowResults::getEditLink($repo, $string_id, $target[$string_id], $locale)
            : '';

        $component = explode('/', $string_id)[0];
        $content .= "<tr class='{$component} {$search_id}'>
                       <td>
                          <span class='celltitle'>Entity</span>
                          <span class='link_to_entity'>
                            <a href=\"/{$string_id_link}\">" . ShowResults::formatEntity($string_id) . "</a>
                          </span>
                       </td>
                       <td dir='{$direction1}'>
                          <span class='celltitle'>{$source_locale}</span>
                          <div class='string'>" . Utils::secureText($source[$string_id]) . "</div>
                          <div class='result_meta_link'>
                            <a class='source_link' href='{$path_source_locale}'>&lt;source&gt;</a>
                          </div>
                       </td>
                        <td dir='{$direction2}'>
                          <span class='celltitle'>$locale</span>
                          <div class='string'>" . Utils::secureText($target[$string_id]) . "</div>
                          <div class='result_meta_link'>
                            <a class='source_link' href='{$path_target_locale}'>&lt;source&gt;</a>
                            {$edit_link}
                            <a class='bug_link' target='_blank' href='{$bugzilla_link}'>&lt;report a bug&gt;</a>
                          </div>
                       </td>
                     </tr>\n";
    }
    $content .= "</tbody>\n</table>\n";
} else {
    $content = '<h2>Congratulations, no errors found.</h2>';
}

print $content;
