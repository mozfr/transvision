<?php
namespace Transvision;

// Results - define error/success messages
if ($empty_TMX) {
    $results .= '<span id="error">Oops, none of the requested strings are available for this locale.</span>';
} elseif ($missing_repos_count > 0) {
    $results .= '<span id="error">';
    if ($missing_repos_count == 1) {
        $results .= 'The following element couldn\'t be included in the TMX file:';
    } else {
        $results .= 'The following ' . $missing_repos_count . ' elements couldn\'t be included in the TMX file:';
    }
    $results .= $missing_repos . '</span>'
             . '<span id="success">However, you can download the TMX file containing the available strings:'
             . $available_repos . '</span>';
} else {
    $results .= '<span id="success">Your TMX file is ready for download, with all the requested strings.</span>';
}

?>

<p id="TMX_results">
    <?=$results?><br>
    <span id="download">
<?php
    if (!$empty_TMX) {
        echo '<a class="button" href="/download/' . $target_file_name . '">Download</a> or ';
    }
?>
        <a href="/downloads">Go back to TMX creation page</a>
    </span>
</p>
