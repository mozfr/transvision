<?php
namespace Transvision;

$search_id = 'search_entities';
$table = "
<table class='collapsable results_table sortable {$search_id}'>
  <thead>
    <tr class='column_headers'>
      <th>Entity</th>
      <th>{$source_locale}</th>
      <th>{$locale}</th>
      {$extra_column_header}
    </tr>
  </thead>
  <tbody>\n";

$current_repo = $search->getRepository();
$extra_locale = $url['path'] == '3locales';

$components = [];
// Display results
foreach ($entities as $entity) {
    $component = explode('/', $entity)[0];
    $components[] = $component;
    $path_locale1 = VersionControl::getPath($source_locale, $current_repo, $entity);
    $path_locale2 = VersionControl::getPath($locale, $current_repo, $entity);

    $unescaped_target_string = isset($tmx_target[$entity])
                                ? $tmx_target[$entity]
                                : '@@missing@@';
    // Escape strings for HTML display
    $bz_target_string = $target_string = htmlspecialchars($unescaped_target_string);

    if (strpos($entity, '.ftl:') !== false && strpos($tmx_source[$entity], ') ->') !== false) {
        $string_class = 'string ftl_string';
    } else {
        $string_class = 'string';
    }

    // Highlight special characters only after strings have been escaped
    $target_string = Strings::highlightSpecial($target_string);
    $source_string = Strings::highlightSpecial(htmlspecialchars($tmx_source[$entity]));

    $clipboard_target_string = 'clip_' . md5($target_string);
    $string_id = md5($entity . mt_rand());
    $regular_string_id = 'string_' . $string_id;

    /*
        Find if we need to transliterate the string.
        The string gets transliterated if the target local is serbian,
        if we aren't in the 3locales view and if we have a $target_string
    */
    $transliterate = $locale == 'sr' && ! $extra_locale && $target_string && $target_string != '@@missing@@';

    if ($transliterate) {
        $transliterated_string = htmlspecialchars($tmx_target[$entity]);
        $transliterated_string = ShowResults::getTransliteratedString(urlencode($transliterated_string), 'sr-Cyrl');
        $transliterated_string = Strings::highlightSpecial($transliterated_string);
        $transliterate_string_id = 'transliterate_' . $string_id;
    }

    // Don't show meta links by default
    $meta_source = $meta_target = $meta_target2 = '';

    // 3locales view
    if ($extra_locale) {
        $bz_target_string2 = $target_string2 = isset($tmx_target2[$entity])
                                                    ? htmlspecialchars($tmx_target2[$entity])
                                                    : '';
        // Highlight special characters only after strings have been escaped
        $target_string2 = Strings::highlightSpecial($target_string2);

        $clipboard_target_string2 = 'clip_' . md5($target_string2);

        $path_locale3 = VersionControl::getPath($locale2, $current_repo, $entity);

        // Link to entity
        $entity_link = "?sourcelocale={$source_locale}"
                     . "&locale={$locale2}"
                     . "&repo={$current_repo}"
                     . "&search_type=entities&recherche={$entity}"
                     . '&entire_string=entire_string';

        $file_bug = '<a class="bug_link" target="_blank" href="'
                    . Bugzilla::reportErrorLink($locale2, $entity, $source_string,
                                              $bz_target_string2, $current_repo, $entity_link)
                  . '">&lt;report a bug&gt;</a>';

        // If there is no target_string2, display an error
        if ($target_string2 == '@@missing@@') {
            $target_string2 = '<em class="error">Warning: Missing string</em>';
        } elseif ($target_string2 === '') {
            $target_string2 = '<em class="error">Warning: Empty string</em>';
        } else {
            $meta_target2 = "<span class='clipboard' data-clipboard-target='#{$clipboard_target_string2}' alt='Copy to clipboard'></span>";
        }

        $extra_column_rows = "
    <td dir='{$direction3}'>
      <span class='celltitle'>{$locale2}</span>
      <div class='{$string_class}' id='{$clipboard_target_string2}'>{$target_string2}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale3}'>&lt;source&gt;</a>
        {$file_bug}
        {$meta_target2}
      </div>
    </td>";
    } else {
        $extra_column_rows = '';
    }

    // Locale codes for machine translation services
    $temp = explode('-', $source_locale);
    $locale1_short_code = $temp[0];

    $temp = explode('-', $locale);
    $locale2_short_code = $temp[0];

    // Link to entity
    $entity_link = "?sourcelocale={$source_locale}"
                 . "&locale={$locale}"
                 . "&repo={$current_repo}"
                 . "&search_type=entities&recherche={$entity}"
                 . '&entire_string=entire_string';

    $file_bug = '<a class="bug_link" target="_blank" href="'
                . Bugzilla::reportErrorLink($locale, $entity, $source_string,
                                          $bz_target_string, $current_repo, $entity_link)
              . '">&lt;report a bug&gt;</a>';
    $anchor_name = str_replace(['/', ':'], '_', $entity);

    // Get the potential errors for $target_string (final dot, long/small string)
    $error_message = ShowResults::buildErrorString($source_string, $target_string);

    // If there is no source_string, display an error, otherwise display the string + meta links
    if ($source_string == '@@missing@@') {
        $source_string = '<em class="error">Warning: Source string is missing</em>';
    } elseif (! $source_string) {
        $source_string = '<em class="error">Warning: Source string is empty</em>';
    } else {
        $meta_source = "
          <span>Translate with:</span>
          <a href='https://translate.google.com/#{$locale1_short_code}/{$locale2_short_code}/"
          // We use html_entity_decode twice because we can have strings as &amp;amp; stored
          . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
          . "' target='_blank'>Google</a>
          <a href='https://www.bing.com/translator/?from={$locale1_short_code}&to={$locale2_short_code}&text="
          . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
          . "' target='_blank'>BING</a>";
    }

    // If there is no target_string, display an error, otherwise display the string + meta links
    if ($target_string == '@@missing@@') {
        $target_string = '<em class="error">Warning: Missing string</em>';
    } elseif ($target_string === '') {
        $target_string = '<em class="error">Warning: Empty string</em>';
    } else {
        $meta_target = "<span class='clipboard' data-clipboard-target='#{$regular_string_id}' alt='Copy to clipboard'></span>";
        if ($transliterate) {
            $meta_target .= "<input type='button' value='To Latin' data-transliterated-id='{$string_id}' class='transliterate_button button action'>";
        }
        $meta_target .= $error_message;
    }

    // Get the tool used to edit strings for the target locale
    $toolUsedByTargetLocale = Project::getLocaleTool($locale);

    $edit_link = $toolUsedByTargetLocale != ''
        ? ShowResults::getEditLink($toolUsedByTargetLocale, $current_repo, $entity, $unescaped_target_string, $locale)
        : '';

    $table .= "
  <tr class='{$component} {$search_id}'>
    <td>
      <span class='celltitle'>Entity</span>
      <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this result'>#</a>
      <a class='l10n tag' href='/string/?entity={$entity}&amp;repo={$current_repo}' title='List all translations for this entity'>all locales</a>
      <span class='link_to_entity'>
        <a href='/{$entity_link}'>" . ShowResults::formatEntity($entity, $search->getSearchTerms()) . "</a>
      </span>
    </td>
    <td dir='{$direction1}'>
      <span class='celltitle'>{$source_locale}</span>
      <div class='{$string_class}'>{$source_string}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale1}'>&lt;source&gt;</a>
        {$meta_source}
      </div>
    </td>
    <td dir='{$direction2}'>
      <span class='celltitle'>{$locale}</span>
      <div class='{$string_class}' id='{$regular_string_id}'>{$target_string}</div>";
    if ($transliterate) {
        $table .= "<div class='string toggle' id='{$transliterate_string_id}' style='display: none;'>{$transliterated_string}</div>";
    }
    $table .= "
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale2}'>&lt;source&gt;</a>
        {$edit_link}
        {$file_bug}
        {$meta_target}
      </div>
    </td>
    {$extra_column_rows}
  </tr>\n";
}

// Remove duplicated components and build components filter
$components = array_unique($components);
if (Project::isDesktopRepository($search->getRepository())) {
    $filter_block = ShowResults::buildComponentsFilter($components);
    if (isset($filter_block)) {
        print $filter_block;
    }
}

if (isset($warning_whitespaces)) {
    print $warning_whitespaces;
}

$table .= "</tbody>\n</table>\n\n";
if ($entities) {
    $message_count = $real_search_results > $limit_results
        ? "<span class=\"results_count_{$search_id}\">{$limit_results} results</span> out of {$real_search_results}"
        : "<span class=\"results_count_{$search_id}\">" . Utils::pluralize($real_search_results, 'result') . '</span>';
    print "<h2>Displaying {$message_count}:</h2>";
    print $table;
}

// Promote API view
include VIEWS . 'templates/api_promotion.php';
