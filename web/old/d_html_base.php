<?php
if (!$valid) {
    die("File can't be called directly");
}

require_once '../PAGES/function_clean.php';

$unique     = secureText($_POST['unique']);
$primaire   = secureText($_POST['primaire']);
$secondaire = secureText($_POST['secondaire']);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$locale?>" lang="<?=$locale?>" dir="ltr">
  <head>
    <title>Duplicates</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="../styles/glossary.css" type="text/css" media="all" />

  </head>
  <body>
    <h1>Duplicates</h1>

    <form method="post" action="doublons.php">
    <fieldset>
      <legend>First Directory to Check</legend>
      <p>
        <input type="radio" name="primaire" value="browser" /> browser
        <input type="radio" name="primaire" value="calendar" /> calendar
        <input type="radio" name="primaire" value="dom" /> dom
        <input type="radio" name="primaire" value="editor" /> editor
        <input type="radio" name="primaire" value="extensions" /> extensions
        <input type="radio" name="primaire" value="mail" /> mail
        <input type="radio" name="primaire" value="netwerk" /> netwerk
        <input type="radio" name="primaire" value="other-licenses" /> other-licenses
        <input type="radio" name="primaire" value="security" /> security
        <input type="radio" name="primaire" value="suite" /> suite
        <input type="radio" name="primaire" value="toolkit" /> toolkit
        <input type="radio" name="primaire" value="mobile" /> mobile
        <input type="radio" name="primaire" value="tout" /> All
      </p>
    </fieldset>
    <fieldset>
      <legend>Second Directory to Check</legend>
      <p>
        <input type="radio" name="secondaire" value="browser" /> browser
        <input type="radio" name="secondaire" value="calendar" <?=$select2?> /> calendar
        <input type="radio" name="secondaire" value="dom" <?=$select2?> /> dom
        <input type="radio" name="secondaire" value="editor" /> editor
        <input type="radio" name="secondaire" value="extensions" /> extensions
        <input type="radio" name="secondaire" value="mail" /> mail
        <input type="radio" name="secondaire" value="netwerk" /> netwerk
        <input type="radio" name="secondaire" value="other-licenses" /> other-licenses
        <input type="radio" name="secondaire" value="security" /> security
        <input type="radio" name="secondaire" value="suite" /> suite
        <input type="radio" name="secondaire" value="toolkit" /> toolkit
        <input type="radio" name="secondaire" value="mobile" /> mobile
        <input type="radio" name="secondaire" value="tout" /> All
      </p>
    </fieldset>
      <p>
        <input type="checkbox" name="unique" value="1" checked="checked" /> Searched on Same Entity Name
        <input type="submit" value="Submit&hellip;" />
      </p>
    </form>


  <h2>Comparison <span class="searchedTerm"><?=$primaire?></span> with <span class="searchedTerm"><?=$secondaire?></span></h2>


  <table>
      <tr>
      <th>Entity</th>
      <th>en-US</th>
      <th><?=$locale?> 1</th>
      <th><?=$locale?> 2</th>
      <th>Entity 2</th>
    </tr>
