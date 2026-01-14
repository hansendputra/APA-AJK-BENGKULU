<?php 
include "../param.php";
include_once('../includes/functions.php');

   
	$lines = file('data_bjtm_pusat.txt'); 
	echo "<h3>list peserta</h3><hr>";
	foreach ($lines as $line_num => $line){
	
    $sql = 'SELECT nopinjaman, idpeserta, nama FROM ajkpeserta where nopinjaman = '.trim($line);
    $result = $conn->query($sql);
    print $line ."<br>"; 
    }
    while ($baris = mysql_fetch_array($querygw)) {
        print $line;
    }
?>