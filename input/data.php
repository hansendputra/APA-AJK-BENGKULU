<?php
include "../param.php";

$qproduk = "SELECT * FROM ajkpolis WHERE del is null and reascode like '%".$_GET['produk']."%'";
$qproduk_ = mysql_query($qproduk);
while($row = mysql_fetch_assoc($qproduk_)){
  $produk[] = array(
    'id' => $row['id'],
    'produk' => $row['produk'],
  );
}

$return = array(
'produk'=> $produk,
'status'=> 'success',
);
echo json_encode($return);

?>
