<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

if ($error_count > 0) {
    $content = "<h2>{$error_count} " .
               ($error_count == 1 ? 'result' : 'results') .
               " found</h2>\n";

    if (isset($filter_block)) {
        $content .= "<div id='filters'>" .
                    "  <h4>Filter by folder:</h4>\n" .
                    "  <a href='#showall' id='showall' class='filter'>Show all results</a>\n" .
                    $filter_block .
                    "</div>\n";
    }

    $content .= "<table class='collapsable'>\n" .
                "  <tr>\n" .
                "    <th>Entity</th>\n" .
                "    <th>{$source_locale}</th>\n" .
                "    <th>{$locale}</th>\n" .
                "  </tr>\n";

    foreach ($var_errors as $string_id) {
        // Link to entity
        $string_id_link = "?sourcelocale={$source_locale}" .
                          "&locale={$locale}" .
                          "&repo={$repo}" .
                          "&search_type=entities&recherche={$string_id}";
        $bug_summary = rawurlencode("Translation update proposed for {$string_id}");
        $bug_message = rawurlencode(
            html_entity_decode(
                "The string:\n{$source[$string_id]}\n\n" .
                "Is translated as:\n{$target[$string_id]}\n\n" .
                "And should be:\n\n\n\n" .
                "Feedback via Transvision:\n" .
                "https://transvision.mozfr.org/{$string_id_link}"
            )
        );
        $bugzilla_link = "{$bugzilla_base_link}&short_desc={$bug_summary}&comment={$bug_message}";

        $path_locale1 = VersionControl::hgPath($source_locale, $repo, $string_id);
        $path_locale2 = VersionControl::hgPath($locale, $repo, $string_id);

        $component = explode('/', $string_id)[0];
        $content .= "<tr class='{$component}'>
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
    $content .= "</table>\n";
} else {
    $content = "<h2>Congratulations, no errors found.</h2>";
}

print $content;
