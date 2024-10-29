<?php
session_start();
if (!file_exists('cfg.php'))
    header('Location:setup.php');
require_once ('cfg.php');
$_SESSION['ok'] = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="de-DE">
<head>
  <title>IconDatabase <?php echo VERSION ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<frameset cols="75%,25%">
  <frame name="main" src="main.php" frameborder="0">
  <frame name="result" src="result.php" frameborder="0">
</frameset>
<noframes>
  <h1>IconDatabase</h1>
  <a href="main.php" target="main">Open the Selection Window</a>
  <a href="result.php" target="result">Open the File Window</a>
</html>