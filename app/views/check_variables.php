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
        <table class='collapsable results_table {$search_id}'>
          <thead>
            <tr class='column_headers'>
              <th>Entity</th>
              <th>{$source_locale}</th>
              <th>{$locale}</th>
            </tr>
          </thead>
          <tbody>\n";

    foreach ($var_errors as $string_id) {
        // Link to entity
        $string_id_link = "?sourcelocale={$source_locale}" .
                          "&locale={$locale}" .
                          "&repo={$repo}" .
                          "&search_type=entities&recherche={$string_id}";
        $bugzilla_link = Bugzilla::reportErrorLink(
            $locale, $string_id, $source[$string_id],
            $target[$string_id], $repo, $string_id_link
        );

        $path_locale1 = VersionControl::hgPath($source_locale, $repo, $string_id);
        $path_locale2 = VersionControl::hgPath($locale, $repo, $string_id);

        $component = explode('/', $string_id)[0];
        $content .= "<tr class='{$component} {$search_id}'>
                       <td>
                          <span class='celltitle'>Entity</span>
                          <a class='link_to_entity' href=\"/{$string_id_link}\">" . ShowResults::formatEntity($string_id) . "</a>
                       </td>
                       <td dir='{$direction1}'>
                          <span class='celltitle'>{$source_locale}</span>
                          <div class='string'>" . Utils::secureText($source[$string_id]) . "</div>
                          <div class='result_meta_link'>
                            <a class='source_link' href='{$path_locale1}'><em>&lt;source&gt;</em></a>
                          </div>
                       </td>
                        <td dir='{$direction2}'>
                          <span class='celltitle'>$locale</span>
                          <div class='string'>" . Utils::secureText($target[$string_id]) . "</div>
                          <div class='result_meta_link'>
                            <a class='source_link' href='{$path_locale2}'><em>&lt;source&gt;</em></a>
                            <a class='bug_link' target='_blank' href='{$bugzilla_link}'>&lt;report a bug&gt;</a>
                          </div>
                       </td>
                     </tr>\n";
    }
    $content .= "</tbody>\n</table>\n";
} else {
    $content = "<h2>Congratulations, no errors found.</h2>";
}

print $content;
