<?php
    // ----------------------------------------------------------------------------------
    // Original Author Of File : Rahmad
    // E-mail :penting_kaga@yahoo.com
    // ----------------------------------------------------------------------------------

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

    $host = getenv('DB_HOST') . ':' . getenv('DB_PORT');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASSWORD');
    $db   = getenv('DB_NAME');

    $conn = @mysql_connect($host, $user, $pass) or die(mysql_error());
    mysql_select_db($db, $conn) or die(mysql_error($conn));

    $datelog = date("Y-m-d");
    $timelog = date("G:i:s");
    $alamat_ip = $_SERVER['REMOTE_ADDR'];
    //$nama_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $useragent = $_SERVER ['HTTP_USER_AGENT'];
    $referrer = getenv('HTTP_REFERER');
