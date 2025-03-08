<?php

session_start();
include ('ini.php');

$_SESSION['userlang'] = isset($_GET['userlang']) ? $_GET['userlang'] : (isset($_SESSION['userlang']) ? $_SESSION['userlang'] : 'de');
$userlang = strtolower($_SESSION['userlang']);

$lang = array('de' => array('SETUP_HEAD' => 'Installationsroutine für IconDatabase',
        'SETUP_DB_HOST' => 'DB-Host (meistens "localhost")',
        'SETUP_DB_USER' => 'DB-User',
        'SETUP_DB_PASS' => 'DB-Passwort',
        'SETUP_DB_NAME' => 'DB-Name',
        'SETUP_IMG_TYPES' => 'Dateiendungen',
        'SETUP_SUCCESS' => 'Installation erfolgreich',
        'NEXT' => 'weiter'),
    'en' => array('SETUP_HEAD' => 'Installation for IconDatabase',
        'SETUP_DB_HOST' => 'DB-Host (mostly "localhost")',
        'SETUP_DB_USER' => 'DB-User',
        'SETUP_DB_PASS' => 'DB-Password',
        'SETUP_DB_NAME' => 'DB-Name',
        'SETUP_IMG_TYPES' => 'file extensions',
        'SETUP_SUCCESS' => 'Installation successfull',
        'NEXT' => 'next'));

// Exit, if config-file exists
if (file_exists('cfg.php'))
    exit("bereits installiert / already installed");

$step = (isset($_GET['step']) ? $_GET['step'] : '0');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="<?php echo $userlang ?>">
  <head>
    <title><?php echo $lang[$userlang]['SETUP_HEAD'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
<?php

// initial startpage
if ($step == '0') {
?>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?step=1">
    <div class="container">
      <div class="jumbotron jumbotron-fluid">
        <h3><?php echo $lang[$userlang]['SETUP_HEAD'] ?></h3>
      </div>
      <div class="row p-1">
        <div class="col-3"><?php echo $lang[$userlang]['SETUP_DB_HOST'] ?></div>
        <div class="col-2"><input class="form-control" type="text" name="dbhost" placeholder="localhost"></div>
      </div>
      <div class="row p-1">
        <div class="col-3"><?php echo $lang[$userlang]['SETUP_DB_USER'] ?></div>
        <div class="col-2"><input class="form-control" type="text" name="dbuser"></div>
      </div>
      <div class="row p-1">
        <div class="col-3"><?php echo $lang[$userlang]['SETUP_DB_PASS'] ?></div>
        <div class="col-2"><input class="form-control" type="password" name="dbpass"></div>
      </div>
      <div class="row p-1">
        <div class="col-3"><?php echo $lang[$userlang]['SETUP_DB_NAME'] ?></div>
        <div class="col-2"><input class="form-control" type="text" name="dbname"></div>
      </div>
      <div class="row p-1">
        <div class="col-3"><?php echo $lang[$userlang]['SETUP_IMG_TYPES'] ?></div>
        <div class="col-2"><input class="form-control" type="text" name="imgtypes" placeholder=".svg,.png"></div>
      </div>
      <div class="row">
        <div class="col"><input class="btn btn-primary btn-sm" type="submit" value="<?php echo $lang[$userlang]['NEXT'] ?>"></div>
      </div>
    </div>
  </form>
  <?php
    foreach ($lang as $arr => $keys) {
        echo "<a href='" . $_SERVER['PHP_SELF'] . "?userlang=$arr'>$arr</a> ";
    }
}

// save information in cfg.php
if ($step == '1') {
    $config = "<?php
//database settings
define('LMOID_DB_HOST', '" . $_POST['dbhost'] . "');
define('LMOID_DB_USER', '" . $_POST['dbuser'] . "');
define('LMOID_DB_PASS', '" . $_POST['dbpass'] . "');
define('LMOID_DB', '" . $_POST['dbname'] . "');

// URL settings
define('ICON_IMG', '".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."');
define('ICON_URL', ICON_IMG.'icons/');
define('ICON_PATH', str_replace('\\\\','/',dirname(__FILE__)));
define('IMG_TYPES', '" . $_POST['imgtypes'] . "');

// other stuff
define('MAX_RESULTS_PER_PAGE',40);  //Max Icons/Zip-Datei
define('MAXIMUM_ICONS_PER_ZIP',40);  //Max Icons/Zip-Datei
define('MAXIMUM_SEARCH_RESULTS',500);  //Max Suchergebnisse

// needed files
require_once('functions/html_output.php');
require_once('ini.php');
require_once('lang/$userlang.php');

?>";

    $temp = fopen('cfg.php', 'w');
    if (!fwrite($temp, $config)) {
        echo 'ERROR!! CHMOD current directory to 666';
        fclose($temp);
        exit;
    }
    fclose($temp);
    require_once ('cfg.php');
    require_once('db_connect.php');
    $dbconnect = dbconnect(LMOID_DB_HOST, LMOID_DB_USER, LMOID_DB_PASS, LMOID_DB);

    $delDB = dbquery('DROP TABLE IF EXISTS team ');
    if (!$delDB) {
        echo mysql_error();
        exit;
    }
    
    $insDB = dbquery("CREATE TABLE team (
    id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL DEFAULT '',
    country VARCHAR(255) DEFAULT NULL,
    region VARCHAR(255) DEFAULT NULL,
    city VARCHAR(255) DEFAULT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (Id),
    KEY name_idx (name)
  ) ENGINE=MyISAM;");
    if (!$insDB) {
        echo mysql_error();
        exit;
    }
?>
  <h3><?php echo $lang[$userlang]['SETUP_SUCCESS'] ?></h3>
<?php
}
?>
  <script src="//cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>