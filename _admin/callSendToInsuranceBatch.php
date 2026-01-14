<?php
error_reporting(0);
session_start();
include_once('includes/jjt1502.php');
include_once('includes/db.php');
include_once('includes/Encrypter.class.php');
$database = new db();
$thisEncrypter = new textEncrypter();

// Accept either encrypted ids via `ids` (comma separated) or raw numeric ids via `raw` param
$idsParam = isset($_REQUEST['ids']) ? trim($_REQUEST['ids']) : '';
$rawParam = isset($_REQUEST['raw']) ? trim($_REQUEST['raw']) : '';

$ids = array();
if ($idsParam != '') {
    $parts = explode(',', $idsParam);
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p != '') $ids[] = $p;
    }
} elseif ($rawParam != '') {
    $parts = explode(',', $rawParam);
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p != '') {
            // encode raw id when possible
            if (isset($thisEncrypter) && method_exists($thisEncrypter, 'encode')) {
                $ids[] = $thisEncrypter->encode($p);
            } else {
                $ids[] = $p;
            }
        }
    }
} else {
    // if no params provided, default: all peserta with iddn not null and del is null
    $q = $database->doQuery('SELECT idpeserta FROM ajkpeserta WHERE iddn IS NOT NULL AND del IS NULL');
    while ($r = mysql_fetch_array($q)) {
        if (!empty($r['idpeserta'])) {
            if (isset($thisEncrypter) && method_exists($thisEncrypter, 'encode')) {
                $ids[] = $thisEncrypter->encode($r['idpeserta']);
            } else {
                $ids[] = $r['idpeserta'];
            }
        }
    }
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$base = $protocol . '://' . $host . '/_admin/sendToInsurance.php';

$results = array();
foreach ($ids as $enc) {
    $url = $base . '?i=' . urlencode($enc);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $results[] = array('id_sent' => $enc, 'url' => $url, 'http_code' => $code, 'error' => $err, 'response' => $resp);
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array('count' => count($results), 'results' => $results), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
