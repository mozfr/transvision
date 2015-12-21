<?php

$output  = header('Content-type: application/xml; charset=UTF-8');
$output .= header('Access-Control-Allow-Origin: *');
$output .= '<?xml version="1.0" encoding="utf-8"?>' . "\n";
$output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
$output .= ' <channel>' . "\n";
$output .= '  <atom:link href="https://transvision.mozfr.org/rss" rel="self" type="application/rss+xml" />' . "\n";
$output .= '  <title>Transvision</title>' . "\n";
$output .= '  <description>Transvision Release Notes</description>' . "\n";
$output .= '  <lastBuildDate>' . date(DATE_RSS) . '</lastBuildDate>' . "\n";
$output .= '  <link>https://transvision.mozfr.org/news/</link>' . "\n";

foreach ($changelog as $release => $changes) {
    $section = '';
    $output .= '  <item>' . "\n";
    $output .= '  <guid isPermaLink="false">' . sha1($release . date('W')) . '</guid>' . "\n";
    $output .= '  <pubDate>' . date(DATE_RSS, strtotime($releases[$release])) . '</pubDate>' . "\n";
    $output .= '  <title>' . 'Version ' . $release . ' Release Notes</title>' . "\n";
    $output .= "  <link>https://transvision.mozfr.org/news/#v{$release}</link>\n";
    $output .= '  <description><![CDATA[ ';
    foreach ($changes as $change => $attributes) {
        if ($section != $attributes['section'][0]) {
            $section = $attributes['section'][0];
            $output .= '<h3>' . $get_sections($section) . '</h3>';
        }
        $output .= isset($attributes['type'])
                    ? '[' . $relnotes($attributes['type'][0]) . ']'
                    : '';

        $output .= isset($attributes['issues'])
                    ? ' ' . $issue($attributes['issues'])
                    : '';

        $output .= isset($attributes['commit'])
                    ? ' ' . $commit($attributes['commit'])
                    : '';

        $output .= ' ' . $attributes['message'][0];

        $output .= isset($attributes['authors'])
                    ? $authors($attributes['authors'])
                    : '';
    }
    $output .= $github_link($release);
    $output .= ' ]]></description>' . "\n";
    $output .= '  </item>' . "\n";
}

$output .= ' </channel>' . "\n";
$output .= '</rss>' . "\n";

print($output);
