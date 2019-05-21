<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

if (! empty($commandkey_results)) {
    $search_id = 'commandkeys';
    $content = '';
    if (! empty($error_messages)) {
        $content .= '<p class="error">' .
            implode('<br/>', $error_messages) .
            '</p>';
    }
    $content .= "<h2><span class=\"results_count_{$search_id}\">"
        . Utils::pluralize(count($commandkey_results), 'potential keyboard shortcuts error')
        . "</span> found</h2>\n";

    if (isset($filter_block)) {
        $content .= $filter_block;
    }

    $content .= "
        <table class='collapsable results_table sortable {$search_id}'>
          <thead>
            <tr class='column_headers'>
              <th>Entity</th>
              <th>{$reference_locale}</th>
              <th>{$locale}</th>
            </tr>
          </thead>
          <tbody>\n";

    // Get the tool used to edit strings for the target locale
    $toolUsedByTargetLocale = Project::getLocaleTool($locale);

    foreach ($commandkey_results as $commandkey_result) {
        $key = $commandkey_result['id'];
        $translated_shortcut = $commandkey_result['target_shortcut'];
        $source_shortcut = $commandkey_result['source_shortcut'];
        $entity = ShowResults::formatEntity($key);
        $component = explode('/', $key)[0];

        $entity_link = "?sourcelocale={$reference_locale}"
            . "&locale={$locale}"
            . "&repo={$repo}"
            . "&search_type=entities&recherche={$key}"
            . '&entire_string=entire_string';

        $edit_link = $toolUsedByTargetLocale != ''
            ? ShowResults::getEditLink($toolUsedByTargetLocale, $repo, $key, $translated_shortcut, $locale)
            : '';

        $bugzilla_link = [Bugzilla::reportErrorLink(
            $locale, $key, $source_shortcut, $translated_shortcut, $repo, $entity_link
        )];

        $reference_path = VersionControl::getPath($reference_locale, $repo, $key);
        $locale_path = VersionControl::getPath($locale, $repo, $key);

        if (! $source_shortcut) {
            $source_shortcut = '<em class="error">(empty)</em>';
        }
        if ($translated_shortcut == '@@missing@@') {
            $translated_shortcut = '<em class="error">Missing string</em>';
        } elseif ($translated_shortcut == '') {
            $translated_shortcut = '<em class="error">(empty)</em>';
        }

        // Replace / and : in the key name and use it as an anchor name
        $anchor_name = str_replace(['/', ':'], '_', $key);

        $content .= "
            <tr class='{$component} {$search_id}'>
              <td>
                <span class='celltitle'>Entity</span>
                <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this string'>#</a>
                <a class='l10n tag' href='/string/?entity={$key}&amp;repo={$repo}' title='List all translations for this entity'>all locales</a>
                <span class='link_to_entity'>
                  <a href=\"/{$entity_link}\">{$entity}</a>
                </span>
              </td>
              <td dir='ltr' lang='{$reference_locale}'>
                <span class='celltitle'>{$reference_locale}</span>
                <div class='string'>
                  {$source_shortcut}
                </div>
                <div dir='ltr' class='result_meta_link'>
                  <a class='source_link' href='{$reference_path}'>
                    &lt;source&gt;
                  </a>
                </div>
              </td>
              <td dir='{$text_direction}' lang='{$locale}'>
                <span class='celltitle'>{$locale}</span>
                <div class='string'>{$translated_shortcut}</div>
                <div dir='ltr' class='result_meta_link'>
                  <a class='source_link' href='{$locale_path}'>
                    &lt;source&gt;
                  </a>
                  {$edit_link}
                  &nbsp;
                  <a class='bug_link' target='_blank' href='{$bugzilla_link[0]}'>
                    &lt;report a bug&gt;
                  </a>
                </div>
              </td>
            </tr>";
    }
    $content .= "</tbody>\n</table>\n";
} else {
    $content = '<h2>Congratulations, no errors found.</h2>';
}

print $content;
