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

        $extra_column_rows = "
    <td dir='{$direction3}'>
      <span class='celltitle'>{$locale2}</span>
      <div class='string'>{$target_string2}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale3}'>&lt;source&gt;</a>
        {$file_bug}
      </div>
    </td>";
    } else {
        $extra_column_rows = '';
    }

    // Errors
    $error_message = '';

    // Check for final dot
    if (substr(strip_tags($source_string), -1) == '.'
        && substr(strip_tags($target_string), -1) != '.') {
        $error_message = '<em class="error"> No final dot?</em>';
    }

    // Check abnormal string length
    $length_diff = Utils::checkAbnormalStringLength($source_string, $target_string);
    if ($length_diff) {
        switch ($length_diff) {
            case 'small':
                $error_message = $error_message . '<em class="error"> Small string?</em>';
                break;
            case 'large':
                $error_message = $error_message . '<em class="error"> Large String?</em>';
                break;
        }
    }

    // Missing string error
    if (! $source_string) {
        $source_string = '<em class="error">warning: missing string</em>';
        $error_message = '';
    }

    if (! $target_string) {
        $target_string = '<em class="error">warning: missing string</em>';
        $error_message = '';
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
      </div>
    </td>
    <td dir='{$direction2}'>
      <span class='celltitle'>{$locale}</span>
      <div class='string' id='{$clipboard_target_string}'>{$target_string}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale2}'>&lt;source&gt;</a>
        {$file_bug}{$error_message}
        <span class='clipboard' data-clipboard-target='#{$clipboard_target_string}' alt='Copy to clipboard'><img src='/img/copy_icon_black_18x18.png'></span>
      </div>
    </td>
    {$extra_column_rows}
  </tr>\n";
}

$table .= "</tbody>\n</table>\n\n";
if ($entities) {
    print $table;
}
