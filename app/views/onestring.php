<?php

// Error management
if (isset($error)) {
	if ($error == 1) {
		print "<p>No entity asked for, goodbye.</p>";
	} elseif ($error == 2) {
		print "<p>Entity does not exist for this repo, goodbye.</p>";
	}

	return;
}

// We have no error, display results
$page_descr = $entity;

?>

<table>
	<tr>
		<th>Locale</th>
		<th>Translation</th>
	</tr>
	<?php foreach($translations as $k => $v): ?>
    <tr>
    	<th><?=$k?></th>
    	<td lang="<?=$k?>"><?=$v?></td>
    </tr>
	<?php endforeach; ?>
</table>
