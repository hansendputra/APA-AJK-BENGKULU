<?php
/*
   ----------------------------------------------------------------------------------
   Copyright (C) 2020 APLIKASI AJK
   Original Author Of File : Hansen
   E-mail :hansen@adonai.co.id
   ----------------------------------------------------------------------------------
*/

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

define("BASE_URL", getenv('BASE_URL') ?: "/clibios/");
define("hostname", getenv('DB_HOST') . ':' . getenv('DB_PORT'));
define("username", getenv('DB_USER'));
define("password", getenv('DB_PASSWORD'));
define("dbname", getenv('DB_NAME'));

define("Utheme", "themeUser");
define("Atheme", "themeAdmin");

$conn = @mysql_connect( hostname, username, password ) or die( mysql_error( ) );
mysql_select_db( dbname, $conn ) or die( mysql_error( $conn ) );
?>
