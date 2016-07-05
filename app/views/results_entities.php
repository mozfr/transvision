<?php
namespace Transvision;

// Promote API view
include VIEWS . 'templates/api_promotion.php';

$table = "
<table class='collapsable'>
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
// Display results
foreach ($entities as $entity) {
    if (in_array($current_repo, ['firefox_ios', 'mozilla_org'])) {
        $path_locale1 = VersionControl::gitPath($source_locale, $current_repo, $entity);
        $path_locale2 = VersionControl::gitPath($locale, $current_repo, $entity);
    } else {
        $path_locale1 = VersionControl::hgPath($source_locale, $current_repo, $entity);
        $path_locale2 = VersionControl::hgPath($locale, $current_repo, $entity);
    }

    // Escape strings for HTML display
    $bz_target_string = $target_string = isset($tmx_target[$entity])
                                            ? Utils::secureText($tmx_target[$entity])
                                            : '';
    // Highlight non-breaking spaces only after strings have been escaped
    $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $target_string);

    $source_string = Utils::secureText($tmx_source[$entity]);

    // 3locales view
    if ($url['path'] == '3locales') {
        $bz_target_string2 = $target_string2 = isset($tmx_target2[$entity])
                                                    ? Utils::secureText($tmx_target2[$entity])
                                                    : '';
        // Highlight non-breaking spaces only after strings have been escaped
        $target_string2 = str_replace(' ', '<span class="highlight-gray"> </span>', $target_string2);

        if (in_array($current_repo, ['firefox_ios', 'mozilla_org'])) {
            $path_locale3 = VersionControl::gitPath($locale2, $current_repo, $entity);
        } else {
            $path_locale3 = VersionControl::hgPath($locale2, $current_repo, $entity);
        }

        // Link to entity
        $entity_link = "?sourcelocale={$source_locale}"
                     . "&locale={$locale2}"
                     . "&repo={$current_repo}"
                     . "&search_type=entities&recherche={$entity}";

        $file_bug = '<a class="bug_link" target="_blank" href="'
                    . Bugzilla::reportErrorLink($locale2, $entity, $source_string,
                                              $bz_target_string2, $current_repo, $entity_link)
                  . '">&lt;report a bug&gt;</a>';

        // Don't show meta links by default
        $meta_target2 = '';

        // If there is no target_string2, display an error, otherwise display the string + meta links
        if (! $target_string2) {
            $target_string2 = '<em class="error">Warning: Missing string</em>';
        } else {
            $meta_target2 = "
              <div dir='ltr' class='result_meta_link'>
                <a class='source_link' href='{$path_locale3}'>&lt;source&gt;</a>
                {$file_bug}
              </div>";
        }

        $extra_column_rows = "
    <td dir='{$direction3}'>
      <span class='celltitle'>{$locale2}</span>
      <div class='string'>{$target_string2}</div>
      {$meta_target2}
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
                 . "&search_type=entities&recherche={$entity}";

    $file_bug = '<a class="bug_link" target="_blank" href="'
                . Bugzilla::reportErrorLink($locale, $entity, $source_string,
                                          $bz_target_string, $current_repo, $entity_link)
              . '">&lt;report a bug&gt;</a>';
    $anchor_name = str_replace(['/', ':'], '_', $entity);

    $clipboard_target_string  = 'clip_' . md5($target_string);

    // Get the potential errors for $target_string (final dot, long/small string)
    $error_message = ShowResults::buildErrorString($source_string, $target_string);

    // Don't show meta links by default
    $meta_source = $meta_target = '';

    // If there is no source_string, display an error, otherwise display the string + meta links
    if (! $source_string) {
        $source_string = '<em class="error">Warning: Source string is empty</em>';
    } else {
        $meta_source =  "
          <div dir='ltr' class='result_meta_link'>
            <a class='source_link' href='{$path_locale1}'>&lt;source&gt;</a>
            <span>Translate with:</span>
            <a href='http://translate.google.com/#{$locale1_short_code}/{$locale2_short_code}/"
            // We use html_entity_decode twice because we can have strings as &amp;amp; stored
            . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
            . "' target='_blank'>Google</a>
            <a href='http://www.bing.com/translator/?from={$locale1_short_code}&to={$locale2_short_code}&text="
            . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
            . "' target='_blank'>BING</a>
          </div>";
    }

    // If there is no target_string, display an error, otherwise display the string + meta links
    if (! $target_string) {
        $target_string = '<em class="error">Warning: Missing string</em>';
    } else {
        $meta_target = "
          <div dir='ltr' class='result_meta_link'>
            <a class='source_link' href='{$path_locale2}'>&lt;source&gt;</a>
            {$file_bug}{$error_message}
            <span class='clipboard' data-clipboard-target='#{$clipboard_target_string}' alt='Copy to clipboard'><img src='/img/copy_icon_black_18x18.png'></span>
          </div>";
    }

    $table .= "
  <tr>
    <td>
      <span class='celltitle'>Entity</span>
      <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this result'>link</a>
      <a class='l10n tag' href='/string/?entity={$entity}&amp;repo={$current_repo}' title='List all translations for this entity'>l10n</a>
      <a class='link_to_entity' href='/{$entity_link}'>" . ShowResults::formatEntity($entity, $search->getSearchTerms()) . "</a>
    </td>
    <td dir='{$direction1}'>
      <span class='celltitle'>{$source_locale}</span>
      <div class='string'>{$source_string}</div>
      {$meta_source}
    </td>
    <td dir='{$direction2}'>
      <span class='celltitle'>{$locale}</span>
      <div class='string' id='{$clipboard_target_string}'>{$target_string}</div>
      {$meta_target}
    </td>
    {$extra_column_rows}
  </tr>\n";
}

$table .= "</tbody>\n</table>\n\n";
if ($entities) {
    print $table;
}
