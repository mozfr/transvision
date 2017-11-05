<?php
namespace Transvision;

$output = '';
$output .= $latest_undocumented_release();
foreach ($changelog as $release => $changes) {
    // Add release title and initialize variables
    $output .= $release_title($release, $releases);
    $empty_release = true;
    $section = '';
    foreach ($changes as $change => $attributes) {
        if ($section != $attributes['section'][0]) {
            $section = $attributes['section'][0];

            if (! $empty_release) {
                $output .= "</ul>\n";
            }
            $output .= '<h3>' . $get_sections($section) . "</h3>\n<ul>\n";
            $empty_release = false;
        }

        $output .= '  <li>';
        $output .= isset($attributes['type']) ? $relnotes($attributes['type'][0]) . ' ' : '';
        $output .= '<div>';
        $output .= isset($attributes['issues']) ? $issue($attributes['issues']) : '';
        $output .= isset($attributes['commit']) ? $commit($attributes['commit']) : '';
        $output .= $attributes['message'][0];
        $output .= isset($attributes['authors']) ? $authors($attributes['authors']) : '';
        $output .= "</div></li>\n";
    }
    $output .= "</ul>\n";
    $output .= $github_link($release, $releases);
}

print($output);
