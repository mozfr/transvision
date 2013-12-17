<?php
namespace Transvision;

// rtl support
$rtl = array('ar', 'fa', 'he');
$direction1 = (in_array($source_locale, $rtl)) ? 'rtl' : 'ltr';
$direction2 = (in_array($locale, $rtl)) ? 'rtl' : 'ltr';

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

$table = "<table>
            <tr>
                <th>Entity</th>\n
                <th>" . $source_locale . "</th>
                <th>" . $locale . "</th>
            </tr>";

foreach ($entities as $val) {

    $path_locale1 = VersionControl::filePath($source_locale, $check['repo'], $val);
    $path_locale2 = VersionControl::filePath($locale, $check['repo'], $val);

    if (isset($tmx_target[$val])) {
        // nbsp highlight
        $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $tmx_target[$val]);
    } else {
        $target_string = '';
    }

    $source_string = $tmx_source[$val];

    // Link to entity
    $entity_link = "?sourcelocale={$source_locale}"
                 . "&locale={$locale}"
                 . "&repo={$check['repo']}"
                 . "&search_type=entities&recherche={$val}";

    $bug_summary = rawurlencode("Translation update proposed for {$val}");
    $bug_message = rawurlencode(
        html_entity_decode(
            "The string:\n{$source_string}\n\n"
            . "Is translated as:\n{$target_string}\n\n"
            . "And should be:\n\n\n\n"
            . "Feedback via Transvision:\n"
            . "http://transvision.mozfr.org/{$entity_link}"
        )
    );

    $complete_link = $bugzilla_link . '&short_desc=' . $bug_summary . '&comment=' . $bug_message;

    $table .= "<tr>
                    <td>" . ShowResults::formatEntity($val, $my_search) . "</a></td>
                    <td dir='{$direction1}'>
                       <div class='string'>{$sourceString}</div>
                       <div class='infos'>
                        <a class='source_link' href='{$path_locale1}'><em>&lt;source&gt;</em></a>
                       </div>
                    </td>
                     <td dir='{$direction2}'>
                       <div class='string'>{$target_string}</div>
                       <div class='infos'>
                        <a class='source_link' href='{$path_locale2}'><em>&lt;source&gt;</em></a>
                        <a class='bug_link' target='_blank' href='{$complete_link}'>
                        &lt;report a bug&gt;
                      </a>
                       </div>
                    </td>
                </tr>";
}

$table .= "  </table>\n\n";

echo $table;
