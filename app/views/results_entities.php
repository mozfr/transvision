<?php
namespace Transvision;

// Promote API view
include VIEWS . 'templates/api_promotion.php';

$table = "
<table class='collapsable'>
  <tr>
    <th>Entity</th>
    <th>{$source_locale}</th>
    <th>{$locale}</th>
    {$extra_column_header}
  </tr>";

// Display results
foreach ($entities as $entity) {
    if ($check['repo'] == 'mozilla_org') {
        $path_locale1 = VersionControl::gitPath($source_locale, $check['repo'], $entity);
        $path_locale2 = VersionControl::gitPath($locale, $check['repo'], $entity);
    } elseif ($check['repo'] == 'firefox_ios') {
        $path_locale1 = VersionControl::svnPath($source_locale, $check['repo'], $entity);
        $path_locale2 = VersionControl::svnPath($locale, $check['repo'], $entity);
    } else {
        $path_locale1 = VersionControl::hgPath($source_locale, $check['repo'], $entity);
        $path_locale2 = VersionControl::hgPath($locale, $check['repo'], $entity);
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

        if ($check['repo'] == 'mozilla_org') {
            $path_locale3 = VersionControl::gitPath($locale2, $check['repo'], $entity);
        } elseif ($check['repo'] == 'firefox_ios') {
            $path_locale3 = VersionControl::svnPath($locale2, $check['repo'], $entity);
        } else {
            $path_locale3 = VersionControl::hgPath($locale2, $check['repo'], $entity);
        }

        // Link to entity
        $entity_link = "?sourcelocale={$source_locale}"
                     . "&locale={$locale2}"
                     . "&repo={$check['repo']}"
                     . "&search_type=entities&recherche={$entity}";

        $file_bug = '<a class="bug_link" target="_blank" href="'
                    . Bugzilla::reportErrorLink($locale2, $entity, $source_string,
                                              $bz_target_string2, $check['repo'], $entity_link)
                  . '">&lt;report a bug&gt;</a>';

        $extra_column_rows = "
    <td dir='{$direction3}'>
      <span class='celltitle'>{$locale2}</span>
      <div class='string'>{$target_string2}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale3}'><em>&lt;source&gt;</em></a>
        {$file_bug}
      </div>
    </td>";
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
                                          $bz_target_string, $check['repo'], $entity_link)
              . '">&lt;report a bug&gt;</a>';
    $anchor_name = str_replace(['/', ':'], '_', $entity);
    $table .= "
  <tr>
    <td>
      <span class='celltitle'>Entity</span>
      <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this result'>link</a>
      <a class='l10n tag' href='/string/?entity={$entity}&amp;repo={$check['repo']}' title='List all translations for this entity'>l10n</a>
      <a class='link_to_entity' href='/{$entity_link}'>" . ShowResults::formatEntity($entity, $initial_search) . "</a>
    </td>
    <td dir='{$direction1}'>
      <span class='celltitle'>{$source_locale}</span>
      <div class='string'>{$source_string}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale1}'><em>&lt;source&gt;</em></a>
      </div>
    </td>
    <td dir='{$direction2}'>
      <span class='celltitle'>{$locale}</span>
      <div class='string'>{$target_string}</div>
      <div dir='ltr' class='result_meta_link'>
        <a class='source_link' href='{$path_locale2}'><em>&lt;source&gt;</em></a>
        {$file_bug}
      </div>
    </td>
    {$extra_column_rows}
  </tr>\n";
}

$table .= "</table>\n\n";
if ($entities) {
    print $table;
}
