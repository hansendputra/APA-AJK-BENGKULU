<?php
 /********************************************************************
 DESC  : Create by hansen;
 EMAIL : hansendputra@gmail.com;
 Create Date : 2018-01-02

 ********************************************************************/
	include "../param.php";

	$path_upload ="../image/upload/";
	$today = date('YmdHis');
	
	if (!file_exists($path_upload)) {
	  mkdir($path_upload, 0777, true);
	  chmod($path, 0777);
	}

	$produk = $_POST['namaproduk'];
	$nama = $_POST['namatertanggung'];
	$gender = $_POST['jnsklmn'];
	$tgllahir = _convertDate2($_POST['tgllahir']);
	$ktp = $_POST['nomorktp'];
	$pekerjaan = $_POST['pekerjaan'];
	$alamat = $_POST['alamat'];
	$plafond = $_POST['plafon'];
	$tenor = $_POST['tenor'];
  $filektp = $_POST['filektp'];
  $filesppa = $_POST['filesppa'];
  $nopinjaman = $_POST['nopinjaman'];
  $npk =$_POST['nomorpk'];
  $medical = $_POST['medical'];
  $tglakad = date('Y-m-d');
  

	$nama = str_replace("'", "''", $nama);
  $plafond = str_replace(",", "", $plafond);
  $usia = birthday($tgllahir,$today);
  $tglakhir = date('Y-m-d', strtotime("+".$tenor." months", strtotime($tglakad)));

// //VALIDASI 
  // Validasi Produk
  $qproduk = mysql_query("SELECT * FROM ajkpolis WHERE id = '".$produk."' and del is null");

  if(mysql_num_rows($qproduk) > 0){                            
    $produk_ = mysql_fetch_array($qproduk);
    $nmproduk = $produk_['produk'];																
    $errorproduk = null;
  }else{
    $msg[] = 'Produk tidak terdapat di database';
    $error = 1;
  }
  
  //---------------------------------------Validasi No Pinjaman--------------------------------------------//
  if($nopinjaman != ""){
    $qproduk = mysql_query("SELECT * FROM ajkpeserta WHERE idclient = '".$idclient."' AND nopinjaman = '".$nopinjaman."' and del is null");

    if(mysql_num_rows($qproduk) > 0){
      $msg[] = 'No Pinjaman sudah terdapat di database';
      $error = 1;
    }else{
      if($nopinjaman == $nopinjaman_temp){
        $msg[] = 'No Pinjaman Double';
        $error = 1;
      }else{
        $nopinjaman_temp = $nopinjaman;																
        $errornopinjaman = null;
      }
    }
  }else{
    $msg[] = 'No Pinjaman tidak Boleh Kosong';
    $error = 1;
  }

  //-------------------------------------------Validasi Nama-------------------------------------------//
  if($nama != ""){
    $errornama = null;
  }else{
    $msg[] = 'Nama Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Nama----------------------------------------//

  //-------------------------------------------Validasi Tptlahir-------------------------------------------//
  // if($tptlahir != ""){
  //   $errortptlahir = null;
  // }else{
  //   $msg[] = 'Tempat Lahir Tidak Boleh Kosong';
  //   $error = 1;
  // }
  //---------------------------------------- End Validasi Tptlahir----------------------------------------//


  //-------------------------------------------Validasi Pekerjaan-------------------------------------------//
  if($pekerjaan != ""){
    $errorpekerjaan = null;
  }else{
    $msg[] = 'Pekerjaan Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Pekerjaan----------------------------------------//

  //-------------------------------------------Validasi Tujuan-------------------------------------------//
  // if($tujuan != ""){
  //   $errortujuan = null;
  // }else{
  //   $msg[] = 'Tujuan Tidak Boleh Kosong';
  //   $error = 1;
  // }
  //---------------------------------------- End Validasi Tujuan----------------------------------------//

  //-------------------------------------------Validasi Gender-------------------------------------------//
  if($gender != ""){
    // if($gender == "L" or $gender == "P"){
    //   if($gender == "L"){
    //     $gender = 'Laki - laki';
    //   }else{
    //     $gender = 'Perempuan';
    //   }
    //   $errorgender = null;	
    // }else{															
    //   $errorgender = '<span class="label label-danger">Jenis Kelamin hanya bisa diisi L (Laki - laki)atau P (Perempuan)</span>';
    //   $error = 1;
    // }														
  }else{
    $msg[] = 'Jenis Kelamin Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Gender----------------------------------------//

  //-------------------------------------------Validasi KTP-------------------------------------------//
  if($ktp != ""){
    $ktp = str_replace(" ","",$ktp);
    $ktp = str_replace("'","",$ktp);
    $ktp = str_replace(".","",$ktp);
    $ktp = str_replace("-","",$ktp);
    $ktp = str_replace(",","",$ktp);

    if(strlen($ktp) != 16){
      $msg[] = 'KTP Harus 16 Digit';
      $nomorktp = $ktp;
      $error = 1;
    }else{
      $pes = mysql_query("SELECT * FROM ajkpeserta inner join ajkpolis on ajkpolis.id = ajkpeserta.idpolicy WHERE nomorktp = '".$ktp."' and ajkpeserta.del is null");
      
      if(mysql_num_rows($pes) > 0){
        $result = '';
        $totalpinjaman = 0;
        
        $nomorktp = '<a href="#modal-'.$ktp.'" data-toggle="modal">'.$ktp.'</a>'.$modal;															
      }else{
        $nomorktp = $ktp;
      }
      $errorktp = null;
    }														
  }else{
    $msg[] = 'KTP Tidak Boleh Kosong';
    $error = 1;
  }
  
  //---------------------------------------- End Validasi KTP----------------------------------------//

  //-------------------------------------------Validasi Tanggal Lahir-------------------------------------------//
  if($tgllahir != ""){														
    $errortgllahir = null;
  }else{
    $msg[] = 'Tanggal Lahir Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Tanggal Lahir----------------------------------------//

  if($npk != ""){														
    $errornpk = null;
  }else{
    $msg[] = 'Nomor Rekening Tidak Boleh Kosong';
    $error = 1;
  }

  //-------------------------------------------Validasi Tanggal Akad-------------------------------------------//
  if($tglakad != ""){
    $errortglakad = null;
  }else{
    $msg[] = 'Tanggal Akad Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Tanggal Akad----------------------------------------//
  
  //-------------------------------------------Validasi Usia-------------------------------------------//
  $usia = birthday($tgllahir,$tglakad);

  $usiaawal = $produk_['agestart'];
  $usiaakhir = $produk_['ageend'];
  
  if(!isset($medical)){
    if($usia < $usiaawal or $usia > $usiaakhir){
      $msg[] = 'Usia tidak sesuai ketentuan polis';
      $error = 1;
    }else{
      $errorusia = null;
    }
  }

  
  //---------------------------------------- End Validasi Usia----------------------------------------//

  //-------------------------------------------Validasi Tenor-------------------------------------------//
  // $tenor = datediffmonth($tglakad,$tglakhir);
  $tenorawal = $produk_['tenormin'];
  $tenorakhir = $produk_['tenormax'];

  if(!isset($medical)){

    if(isset($tenorawal)){
      if($tenor < $tenorawal or $tenor > $tenorakhir){
        $msg[] = 'Tenor tidak sesuai ketentuan polis';
        $error = 1;														
      }else{
        $errortenor = null;
      }
    }else{
      $msg[] = 'Tenor belum di setting di Polis';
      $error = 1;
    }
  }
  //-------------------------------------------End Validasi Tenor-------------------------------------------//

  //-------------------------------------------Validasi Plafond-------------------------------------------//
  
  if($plafond != ""){
    // $plafond_a = $plafond + $totalpinjaman;
    // $plafondawal = $produk_['plafondstart'];
    // $plafondakhir = $produk_['plafondend'];
    // if($plafond < $plafondawal or $plafond > $plafondakhir){
    //   $errorplafond = '<span class="label label-danger">Plafond tidak sesuai polis</span>';
    //   $error = 1;
    // }else{															
    //   if($plafond_a < $plafondawal or $plafond_a > $plafondakhir){
    //     $errorplafond = '<span class="label label-danger">Plafond Total ['.duit($plafond_a).'] tidak sesuai polis</span>';
    //     $error = 1;	
    //   }else{
    //     $errorplafond = null;	
    //   }
      
    // }														
  }else{
    $msg[] = 'Plafond Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Plafond----------------------------------------//

  if($produk_['id'] != 11 and $produk_['id'] != 12){
    $qpremi1 = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and idas = 1 and '".$tenor."' BETWEEN tenorfrom and tenorto and status = 'Aktif' and del is null");
    $qpremi2 = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and idas = 2 and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' BETWEEN agefrom and ageto and status = 'Aktif' and del is null");  

    if(mysql_num_rows($qpremi1) > 0 or mysql_num_rows($qpremi2) > 0){															
      $qpremi1_ = mysql_fetch_array($qpremi1);
      $qpremi2_ = mysql_fetch_array($qpremi2);
      $rate1 = $qpremi1_['rate'];
      $rate2 = $qpremi2_['rate'];
      $premi1 = $plafond/$produk_['calculatedrate'] * $rate1;
      $premi2 = $plafond/$produk_['calculatedrate'] * $rate2;
  
      $rate = $rate1 + $rate2;
      $premi = $premi1 + $premi2;
      $errorrate = null;
    }else{
      if($medical == ""){
        $rate = 0;
        $msg[] = 'Rate belum tersedia di database';
        $error = 1;
      }else{
        $rate = 0;
        $premi = 0;
        $errorrate = null;
      }
      
    }
  }else{
    $qpremi = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' BETWEEN agefrom and ageto and status = 'Aktif' and del is null");
    if(mysql_num_rows($qpremi) > 0){
      $qpremi_ = mysql_fetch_array($qpremi);
      $rate = $qpremi_['rate'];
      $premi = $plafond/$produk_['calculatedrate'] * $rate;

      $errorrate = null;
    }else{
      if($medical == ""){
        $rate = 0;
        $msg[] = 'Rate belum tersedia di database';
        $error = 1;
      }else{
        $rate = 0;
        $premi = 0;
        $errorrate = null;
      }
    }
  }
  
  


  //-------------------------------------------Validasi Premi-------------------------------------------//
  // if($premi != ""){
    // $premi = $plafond/$produk_['calculatedrate'] * $rate;
  //   if(round($premi,0) != round($premisys,0)){
  //     $errorpremi = '<span class="label label-danger">Premi Berbeda, Seharusnya '.duit($premisys).'</span>';	
  //     $error = 1;
  //   }else{
  //     $errorpremi = null;
  //   }														 	
  // }else{
  // 	$errorpremi = '<span class="label label-danger">Premi Tidak Boleh Kosong</span>';
  // 	$error = 1;
  // }
  //---------------------------------------- End Validasi Premi----------------------------------------//


  if($medical == ""){
    if($produk_['id'] != 11){    
      $qmedical2 = mysql_query("select * from ajkmedical where idproduk = '".$produk."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
    
      if(mysql_num_rows($qmedical2) > 0){
        $qmedical2_ = mysql_fetch_array($qmedical2);
        $medical2 = $qmedical2_['type'];
        $medical1 = 'FCL';
    
        $medical = $medical1.$medical2;
    
        // if($medical != 'GOA'){
          $status = 'Pending';
        // }else{
        //   $status = 'Approve';
        // }
        $errormedical = null;
      }else{
        $msg[] = 'Medical tidak ditemukan';
        $error = 1;
      }
    }else{
      $qmedical = mysql_query("select * from ajkmedical where idproduk = '".$produk."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
      if(mysql_num_rows($qmedical) > 0){
        $qmedical_ = mysql_fetch_array($qmedical);
        $medical = $qmedical_['type'];
        
        $status = 'Pending';      
        $errormedical = null;
      }else{
        $msg[] = 'Medical tidak ditemukan';
        $error = 1;
      }
    }
  }else{
    $status = 'Pending';
    $errormedical = null;
  }
  if($_FILES['filesppa']['name'] == ""){
    $msg[] = 'File SPPA harus diisi';
    $error = 1; 
  }
  
  if($_FILES['filektp']['name'] == ""){
    $msg[] = 'File KTP harus diisi';
    $error = 1;
  }

  if($_FILES['filesppa']['size'] > 2000000){
    $msg[] = 'File SPPA tidak boleh lebih dari 2MB';
    $error = 1;
  }
  if($_FILES['filektp']['size'] > 2000000){
    $msg[] = 'File KTP tidak boleh lebih dari 2MB';
    $error = 1;
  }
//END VALIDASI
  if($error > 0){
    $json = $msg;
    echo json_encode($json);exit;
  }

  $qpeserta = mysql_fetch_array(mysql_query("SELECT idC FROM ajkpeserta WHERE idbroker = '".$idbro."' and idclient='".$idclient."' ORDER BY idC DESC LIMIT 1 "));
  $autonumber = ($qpeserta['idC']+1);
  $idpeserta = substr($metSetAutoNumber + $autonumber, 1);
  $idpeserta = $idclient.$idpeserta;

  $time = date("YmdHis");
  $PathUpload= "../myFiles/_peserta/".$idpeserta;

  if (!file_exists($PathUpload)) {
    mkdir($PathUpload, 0777);
    chmod($PathUpload, 0777);
    fopen($PathUpload.'index.html','r');
  }

  $sppaname =  str_replace(" ", "_","SPPA_".$time.'_'.$_FILES['filesppa']['name']);
  $ktpname =  str_replace(" ", "_","KTP_".$time.'_'.$_FILES['filektp']['name']);

  $query = "INSERT INTO ajkpeserta 
  SET   idC = '".$autonumber."',
        idbroker='".$idbro."',
				idclient='".$idclient."',
				idpolicy='".$produk."',
        idpeserta = '".$idpeserta."',
				gender='".$gender."',
				nomorktp='".$ktp."',
				nomorpk='".$npk."',
				nomorspk='".$nomorformulir."',
				nama='".strtoupper(trim($nama))."',
				tgllahir='".$tgllahir."',
				usia='".$usia."',
				plafond='".$plafond."',
				tglakad='".$tglakad."',
        tgltransaksi='".date('Y-m-d')."',
				tenor='".$tenor."',
				tglakhir='".$tglakhir."',
				statusaktif='".$status."',
				cabang='".$branchid."',
				area='".$area."',
				regional='".$regional."',
				premirateid='".$rate_id."',
				premirate='".$rate."',
				premi='".$premi."',
				diskonpremi='".$discountpremi."',
				biayaadmin='".$adminfee."',
				extrapremi='".$extpremi."',
				totalpremi='".$premi."',
				medical='".$medical."',
        nopinjaman='".$nopinjaman."',
        asuransi = '2',
        ktp_file = '".$ktpname."',
        sppa_file = '".$sppaname."',
				input_by='".$iduser."',
				input_time='".$mamettoday."',
        approve_by='".$iduser."',
				approve_time='".$mamettoday."'";
  
  if($produk_['id'] != 11){
    $pesertaas1 = "INSERT INTO ajkpesertaas
    SET idpeserta='".$idpeserta."',
        idpolis='".$produk."',
        idas=1,
        tsi ='".$plafond."',
        tglawal='".$tglakad."',
        tglakhir='".$tglakhir."',
        tenor='".$tenor."',
        medical='".$medical1."',
        rate='".$rate1."',
        premi='".$premi1."',
        totalpremi='".$premi1."',
        keterangan='ASURANSI KREDIT',
        input_by='".$iduser."',
        input_time='".$mamettoday."'";
  
    $pesertaas2 = "INSERT INTO ajkpesertaas
    SET idpeserta='".$idpeserta."',
        idpolis='".$idbro."',
        idas=2,
        tsi ='".$plafond."',
        tglawal='".$tglakad."',
        tglakhir='".$tglakhir."',
        tenor='".$tenor."',
        medical='".$medical2."',
        rate='".$rate2."',
        premi='".$premi2."',
        totalpremi='".$premi2."',
        keterangan='ASURANSI JIWA',
        input_by='".$iduser."',
        input_time='".$mamettoday."'";  
  }else{
    $pesertaas2 = "INSERT INTO ajkpesertaas
    SET idpeserta='".$idpeserta."',
        idpolis='".$idbro."',
        idas=2,
        tsi ='".$plafond."',
        tglawal='".$tglakad."',
        tglakhir='".$tglakhir."',
        tenor='".$tenor."',
        medical='".$medical."',
        rate='".$rate."',
        premi='".$premi."',
        totalpremi='".$premi."',
        keterangan='ASURANSI JIWA',
        input_by='".$iduser."',
        input_time='".$mamettoday."'";  
  }
 
  try{
    mysql_query("START TRANSACTION");

    if (!mysql_query($query)){
        throw new Exception("simpan gagal");
    }

    
    if($produk_['id'] != 11){
      
      mysql_query($pesertaas1);
      mysql_query($pesertaas2);
    }else{
      mysql_query($pesertaas2);
    }
    
    $sppa_tmp = $_FILES['filesppa']['tmp_name'];
    $ktp_tmp = $_FILES['filektp']['tmp_name'];
   
    if (!move_uploaded_file($sppa_tmp, $PathUpload.'/'.$sppaname)) {
      throw new Exception('Could not move file');
    }

    if (!move_uploaded_file($ktp_tmp, $PathUpload.'/'.$ktpname)) {
      throw new Exception('Could not move file');
    }
    mysql_query("COMMIT");
    echo "success";

  }catch(Exception $e){
    mysql_query("ROLLBACK");
    echo $e;
  }
	

	

	// function uploaded($data,$file){
	// 	global $path_upload;
	// 	global $today;
	// 	global $nama;
	// 	$nama = str_replace(" ","_", $nama);
	// 	$data = substr($data,strpos($data,",")+1);
	// 	$data = base64_decode($data);
	// 	$image = $path_upload.$nama.uniqid().'_D'.$file;
	// 	file_put_contents($image, $data);	
	// 	return $image;
	// }

	function formatSPAKNo($input, $idprod){
		if(strlen($idprod<10)){
			$noprod = '0'.$idprod;
		}else{
			$noprod = $idprod;
		}

		$year = date("y");

		if(strlen($input)<5){
			$cur_length = strlen($input);
			$dif = 4-$cur_length;
			$zero = "";
			for($i=0;$i<$dif;$i++){
				$zero .= "0" ;
			}
			return "M".$year.$noprod.$zero.$input;
		}else{
			return "M".$year.$noprod.$input;
		}
	}
		
	function generateRandomNumber($length){
		$token = "";
		for($i = 0 ; $i<$length;$i++){
			$token.=rand(0,9);
		}
		return $token;
	}		
	
?>