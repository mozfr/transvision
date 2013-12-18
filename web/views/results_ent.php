<?php
namespace Transvision;

// rtl support
$direction1 = RTLSupport::getDirection($source_locale);
$direction2 = RTLSupport::getDirection($locale);

if ($url['path'] == '3locales') {
    $direction3 = RTLSupport::getDirection($locale2);
    $extra_column_header = "<th>{$locale2}</th>";
} else {
    $extra_column_header = '';
}

$table  = "<table>
              <tr>
                <th>Entity</th>
                <th>{$source_locale}</th>
                <th>{$locale}</th>
                {$extra_column_header}
              </tr>";


foreach ($entities as $entity) {

    $path_locale1 = VersionControl::filePath($source_locale, $check['repo'], $entity);
    $path_locale2 = VersionControl::filePath($locale, $check['repo'], $entity);
    if ($url['path'] == '3locales') {
        if (isset($tmx_target2[$entity])) {
            // nbsp highlight
            $target_string2 = str_replace(' ', '<span class="highlight-gray"> </span>', $tmx_target2[$entity]);
        } else {
            $target_string2 = '';
        }

        $path_locale3 = VersionControl::filePath($locale2, $check['repo'], $entity);
    }

    if (isset($tmx_target[$entity])) {
        // nbsp highlight
        $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $tmx_target[$entity]);
    } else {
        $target_string = '';
    }

    $source_string = $tmx_source[$entity];

    // 3locales view
    if ($url['path'] == '3locales') {
        // Link to entity
        $entity_link = "?sourcelocale={$source_locale}"
                     . "&locale={$locale2}"
                     . "&repo={$check['repo']}"
                     . "&search_type=entities&recherche={$entity}";

        $file_bug = '<a class="bug_link" target="_blank" href="'
                    . Bugzilla::reportErrorLink($locale2, $entity, $source_string,
                                              $target_string2, $entity_link)
                  . '">&lt;report a bug&gt;</a>';

        $extra_column_rows = "
        <td dir='{$direction3}'>
            <div class='string'>{$target_string2}</div>
            <div dir='ltr' class='infos'>
              <a class='source_link' href='{$path_locale3}'>
                <em>&lt;source&gt;</em>
              </a>
              {$file_bug}
            </div>
          </td>
        </tr>";

    } else {
        $extra_column_rows = '';
    }

    // Link to entity
    $entity_link = "?sourcelocale={$source_locale}"
                 . "&locale={$locale}"
                 . "&repo={$check['repo']}"
                 . "&search_type=entities&recherche={$entity}";

    $file_bug = '<a class="bug_link" target="_blank" href="'
                . Bugzilla::reportErrorLink($locale, $entity, $source_string,
                                          $target_string, $entity_link)
              . '">&lt;report a bug&gt;</a>';
    $table .= "<tr>
                    <td>" . ShowResults::formatEntity($entity, $my_search) . "</a></td>
                    <td dir='{$direction1}'>
                       <div class='string'>{$source_string}</div>
                       <div dir='ltr' class='infos'>
                        <a class='source_link' href='{$path_locale1}'><em>&lt;source&gt;</em></a>
                       </div>
                    </td>
                     <td dir='{$direction2}'>
                       <div class='string'>{$target_string}</div>
                       <div dir='ltr' class='infos'>
                        <a class='source_link' href='{$path_locale2}'><em>&lt;source&gt;</em></a>
                        {$file_bug}
                       </div>
                    </td>
                {$extra_column_rows}
                </tr>";
}

$table .= "  </table>\n\n";

print $table;
