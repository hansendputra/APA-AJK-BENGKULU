<?php
/*
   ----------------------------------------------------------------------------------
   Copyright (C) 2020 APLIKASI AJK
   Original Author Of File : Hansen
   E-mail :hansen@adonai.co.id
   ----------------------------------------------------------------------------------
*/

define("BASE_URL", "/clibios/");
// define("hostname", "localhost:3361");
// define("username", "jatimsql");
// define("password", 'ved+-18bios');
// define("dbname", "biosjatim");

define("hostname", "192.168.17.5:3306");
define("username", "bengkulu");
define("password", 'a9947cb01c514faee5fef7259b17fb0f');
define("dbname", "bengkulu");

define("Utheme", "themeUser");
define("Atheme", "themeAdmin");
//$pdo = new PDO("mysql:host=hostname;dbname=dbname", username, password);
$conn = @mysql_connect( hostname, username, password ) or die( mysql_error( ) );
mysql_select_db( dbname, $conn ) or die( mysql_error( $conn ) );
?>
