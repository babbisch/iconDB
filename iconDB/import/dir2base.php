<html>
<head>
<title></title>
<meta name="description" content="">
<meta name="keywords" content="">
</head>
<body>
<?php
error_reporting(E_ALL);

require_once ('../cfg.php');
require_once ('../db_connect.php');
dbquery('DELETE FROM team');

function show_dir($dir, $pos = 2)
{
    if ($pos == 2) {
        echo '<hr><pre>';
    }
    $handle = opendir($dir);
    while ($file = readdir($handle)) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        if (is_dir($dir . $file)) {
            printf('% ' . $pos . "s <b>%s</b>\n", '|-', $file);
            show_dir($dir . $file . '/', $pos + 3);
        } else {
            $imgtype = explode(',', IMG_TYPES);
            if ($dir != './icons' && in_array(substr($file, -4), $imgtype)) {
                if (!file_exists("./icons/$file")) {
                    copy($dir . $file, "./icons/$file");
                }
                /* Heuristik zur Stadtbestimmung */
                $parts = explode(' ', substr($file, 0, -4));
                $city = '';
                $country = '';
                $anz = count($parts);
                switch ($anz) {
                    case 1:
                        $city = $parts[0];
                        $country = $parts[0];
                        break;
                    default:
                        if (strlen($parts[$anz - 1]) < 5 && strlen($parts[$anz - 2]) >= 4) {
                            $city = $parts[$anz - 2];
                        } else {
                            $city = $parts[$anz - 1];
                        }
                        break;
                }
                $query = mysql_query("INSERT INTO team (id,name,country,city) values (NULL,'" . substr($file, 0, -4) . "','$country','$city')");
                echo $error->getMessage();
                printf('% ' . $pos . "s %s\n", '|-', $file);
            }
        }
    }
    closedir($handle);

    if ($pos == 2) {
        echo '</pre><hr>';
    }
}

show_dir('../icons/');
?>
</body>
</html>