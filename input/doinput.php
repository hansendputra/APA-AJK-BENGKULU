<?php
 /********************************************************************
 DESC  : Create by hansen;
 EMAIL : hansendputra@gmail.com;
 Create Date : 2018-01-02

 ********************************************************************/
	include "../param.php";

	$path_upload ="../image/upload/";
	$today = date('YmdHis');
	
  if(isset($_POST['idpeserta'])){
    $peserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$_POST['idpeserta']."'"));
  }

  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'submit'){
    mysql_query("update ajkpeserta set del = null where idpeserta = '".$_REQUEST['idpeserta']."'");
    $return = array(
      'status' => 'success',
    );
    echo json_encode($return); exit;
  }

  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
     $return = array(
      'status' => 'success',
    );
    echo json_encode($return); exit;
  }

  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'hapusfile'){
    $idpeserta = $_REQUEST['idpeserta'];
    $type = $_REQUEST['jenis'];

    $query = '';
    if($type == 'ktp'){
      
      $query = "update ajkpeserta set ktp_file = null where idpeserta = '".$idpeserta."'";
      mysql_query($query);
    }elseif($type == 'sppa'){
      
      $query = "update ajkpeserta set sppa_file = null where idpeserta = '".$idpeserta."'";
      mysql_query($query);
    }

    $return = array(
      'status' => 'success',
    );
    echo json_encode($return); exit;
  }

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
  $tptlahir = $_POST['tptlahir'];
  $email = $_POST['email'];
  $kota = $_POST['kota'];
  $kodepos = $_POST['kodepos'];
  $notelp = $_POST['notelp'];
	$alamat = $_POST['alamat'];
	$plafond = $_POST['plafon'];
	$tenor = $_POST['tenor'];
  $filektp = $_POST['filektp'];
  $filesppa = $_POST['filesppa'];
  $nopinjaman = $_POST['nopinjaman'];
  $npk =$_POST['nomorpk'];
  $medical = $_POST['medical'];
  $tglakad = _convertDate2($_POST['tglakad']);
  $macet = $_POST['macet'];
  $jiwa = $_POST['jiwa'];
  $typedata = $_POST['typedata'];

	$nama = str_replace("'", "''", $nama);
  $plafond = str_replace(",", "", $plafond);
  
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
    $nopinjaman_temp = $nopinjaman;																
    $errornopinjaman = null;
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

  //-------------------------------------------Validasi Pekerjaan-------------------------------------------//
  if($pekerjaan != ""){
    $errorpekerjaan = null;
  }else{
    $msg[] = 'Pekerjaan Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Pekerjaan----------------------------------------//

  //-------------------------------------------Validasi Gender-------------------------------------------//
  if($gender != ""){
  											
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
    if(!isset($_POST['idpeserta'])){
      $qproduk = mysql_query("SELECT * FROM ajkpeserta WHERE idclient = '".$idclient."' AND nomorpk = '".$npk."' and statusaktif in ('Inforce','Pending','Analisa','Approve') and del is null");												
      if(mysql_num_rows($qproduk) > 0){
        $msg[] = 'Nomor Rekening sudah terdapat di database';
        $error = 1;
      }else{
        if($npk == $npk_temp){
          $msg[] = 'Nomor Rekening Double';
          $error = 1;
        }else{
          $npk_temp = $npk;
          $errornpk = null;
        }
      }    
    }
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
  $usia = usia($tglakad,$tgllahir);

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
								
  }else{
    $msg[] = 'Plafond Tidak Boleh Kosong';
    $error = 1;
  }
  //---------------------------------------- End Validasi Plafond----------------------------------------//
  
  if($macet == 'T' && $jiwa == 'T'){
    $asuransi = 0;
  }else if($macet == 'T'){
    $asuransi = 3;
  }else if($jiwa == 'T'){
    $asuransi = 2; 
  }else if($produk_['id'] == 11 or $produk_['id'] == 12){
    $asuransi = 2; 
  }

  if($medical == ""){
    $medical = '';
    $idasjiwa = 2;
    if($produk_['id'] != 11 and $produk_['id'] != 12){    
      $qmedical2 = mysql_query("select * from ajkmedical where idas = '".$idasjiwa."' and idproduk = '".$produk."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
    
      if(mysql_num_rows($qmedical2) > 0){
        
        if($macet == 'T'){
          $medical1 = 'FCL';
          $medical .= $medical1;
        }
        if($jiwa == 'T'){
          $qmedical2_ = mysql_fetch_array($qmedical2);
          $medical2 = $qmedical2_['type'];
          if($medical2 != 'GIO'){
            $asuransi = 5;
            $idasjiwa = 5;

            $qmedical2 = mysql_query("select * from ajkmedical where idas = '".$idasjiwa."' and idproduk = '".$produk."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");

            $medical2 = 'FCL, '.$medical2;
          }
          $medical .= $medical2;
        }
       
        $status = 'Pending';
        $errormedical = null;
      }else{
        $msg[] = 'Medical tidak ditemukan';
        $error = 1;
      }
    }else{
      $qmedical = mysql_query("select * from ajkmedical where idas = '".$idasjiwa."' and idproduk = '".$produk."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
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

  if($produk_['id'] != 11 and $produk_['id'] != 12){
    if($tenor > 60){
      $tenormacet = 60;
    }else{
      $tenormacet = $tenor;
    }
    $qpremi1 = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and idas = 3 and '".$tenormacet."' BETWEEN tenorfrom and tenorto and status = 'Aktif' and del is null");
    $qpremi2 = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and idas = '".$idasjiwa."' and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' BETWEEN agefrom and ageto and status = 'Aktif' and del is null");  
    $rate = 0;
    $premi = 0;
    $medical = '';

    if(mysql_num_rows($qpremi1) > 0 and mysql_num_rows($qpremi2) > 0){															
      if($macet == 'T'){
        $qpremi1_ = mysql_fetch_array($qpremi1);
        $rate1 = $qpremi1_['rate'];
        $rate1 = ($rate1/12)*$tenormacet;
        $rate += $rate1;
        $premi1 = $plafond/$produk_['calculatedrate'] * $rate1;
        $premi += $premi1;
      }

      if($jiwa == 'T'){
        $qpremi2_ = mysql_fetch_array($qpremi2);
        $rate2 = $qpremi2_['rate'];
        $rate += $rate2;
        $premi2 = $plafond/$produk_['calculatedrate'] * $rate2;
        $premi += $premi2;
      }
     
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
    $qpremi = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and idas = 2 and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' BETWEEN agefrom and ageto and status = 'Aktif' and del is null");
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
  
  $time = date("YmdHis");

  $ktpname_ = '';

  if($peserta['ktp_file'] != ""){
    $ktpname = '';
  }else{
    if($_FILES['filektp']['name'] == ""){
        $msg[] = 'File KTP harus diisi';
        $error = 1;
      }else{
        $ktpname = str_replace(" ", "_","KTP_".$time.'_'.$_FILES['filektp']['name']);
        $ktpname_ = " ktp_file = '".$ktpname."',";
      }
  }


  $sppaname_ = '';
  if($peserta['sppa_file'] != ""){
    $sppaname = '';
  }else{
    if($_FILES['filesppa']['name'] == ""){
      $msg[] = 'File SPPA harus diisi';
      $error = 1;
    }else{
      $sppaname = str_replace(" ", "_","SPPA_".$time.'_'.$_FILES['filesppa']['name']);
      $sppaname_ = " sppa_file = '".$sppaname."',";
      
    }
  }

  if($_FILES['filektp']['size'] > 2000000){
    $msg[] = 'File KTP tidak boleh lebih dari 2MB';
    $error = 1;
  }

  if($_FILES['filesppa']['size'] > 2000000){
    $msg[] = 'File SPPA tidak boleh lebih dari 2MB';
    $error = 1;
  }
//END VALIDASI
  if($error > 0){
    $json = $msg;
    echo json_encode($json);exit;
  }




  $discountpremi = 0;
  $adminfee = 0;
  $extpremi = 0;

  if(isset($_POST['idpeserta'])){
    $idpeserta = $_POST['idpeserta'];
    $peserta = mysql_fetch_array(mysql_query("select * from ajkpeserta where idpeserta = '".$idpeserta."'"));
    $query = "UPDATE ajkpeserta 
    SET ".$ktpname_."
        ".$sppaname_."
        update_by='".$iduser."',
        update_time='".$mamettoday."'
    WHERE idpeserta = '".$idpeserta."'";
  }else{
    $qpeserta = mysql_fetch_array(mysql_query("SELECT idC FROM ajkpeserta WHERE idbroker = '".$idbro."' and idclient='".$idclient."' ORDER BY idC DESC LIMIT 1 "));
    $autonumber = ($qpeserta['idC']+1);
    $idpeserta = substr($metSetAutoNumber + $autonumber, 1);
    $idpeserta = $idclient.$idpeserta;

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
          typedata = '".$typedata."',
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
          pekerjaan='".$pekerjaan."',
          regional='".$regional."',
          premirate='".$rate."',
          premi='".$premi."',
          alamatobjek='".$alamat."',
          diskonpremi='".$discountpremi."',
          biayaadmin='".$adminfee."',
          extrapremi='".$extpremi."',
          totalpremi='".$premi."',
          medical='".$medical."',
          nopinjaman='".$nopinjaman."',
          asuransi = '".$asuransi."',
          ".$ktpname_."
          ".$sppaname_."
          tptlahir = '".$tptlahir."',
          email = '".$email."',
          notelp = '".$notelp."',
          kota = '".$kota."',
          kodepos = '".$kodepos."',        
          input_by='".$iduser."',
          input_time='".$mamettoday."',
          approve_by='".$iduser."',
          approve_time='".$mamettoday."',
          del= 1";
    
    if($produk_['id'] != 11 && $produk_['id'] != 12){

      if($macet == 'T'){
        $tglakhirmacet = date('Y-m-d', strtotime("+".$tenormacet." months", strtotime($tglakad)));
        $pesertaas1 = "INSERT INTO ajkpesertaas
        SET idpeserta='".$idpeserta."',
            idpolis='".$produk."',
            idas=3,
            tsi ='".$plafond."',
            tglawal='".$tglakad."',
            tglakhir='".$tglakhirmacet."',
            tenor='".$tenormacet."',
            medical='".$medical1."',
            rate='".$rate1."',
            premi='".$premi1."',
            totalpremi='".$premi1."',
            keterangan='ASURANSI KREDIT',
            input_by='".$iduser."',
            input_time='".$mamettoday."'";
      }
      
      if($jiwa == 'T'){
        $pesertaas2 = "INSERT INTO ajkpesertaas
        SET idpeserta='".$idpeserta."',
            idpolis='".$produk."',
            idas='".$idasjiwa."',
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
      }
      
    }else{
      $pesertaas2 = "INSERT INTO ajkpesertaas
      SET idpeserta='".$idpeserta."',
          idpolis='".$produk."',
          idas='".$idasjiwa."',
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
  }

  $PathUpload= "../myFiles/_peserta/".$idpeserta;

  if (!file_exists($PathUpload)) {
    mkdir($PathUpload, 0777);
    chmod($PathUpload, 0777);
    fopen($PathUpload.'index.html','r');
  }

//  echo $query;exit;
  try{
    mysql_query("START TRANSACTION");
    
    if (!mysql_query($query)){
        throw new Exception("simpan gagal");
    }

    
    if($produk_['id'] != 11 && $produk_['id'] != 12){
      if($macet == 'T'){
        mysql_query($pesertaas1);
      }
      if($jiwa == 'T'){
        mysql_query($pesertaas2);
      }
    }else{
      mysql_query($pesertaas2);
    }
    
    $sppa_tmp = $_FILES['filesppa']['tmp_name'];
    $ktp_tmp = $_FILES['filektp']['tmp_name'];
   
    // if (!move_uploaded_file($sppa_tmp, $PathUpload.'/'.$sppaname)) {
    //   throw new Exception('Could not move file');
    // }

    // if (!move_uploaded_file($ktp_tmp, $PathUpload.'/'.$ktpname)) {
    //   throw new Exception('Could not move file');
    // }
    
    if(isset($_FILES['filesppa'])){
      // echo 'ada file sppa'.$PathUpload.'/'.$sppaname;
      if (!move_uploaded_file($sppa_tmp, $PathUpload.'/'.$sppaname)) {
        throw new Exception('Could not move sppa file');
      }
    }
    if(isset($_FILES['filektp'])){
      // echo 'ada file filektp'.$PathUpload.'/'.$ktpname;
      if (!move_uploaded_file($ktp_tmp, $PathUpload.'/'.$ktpname)) {
        throw new Exception('Could not move ktp file');
      }
    }
    mysql_query("COMMIT");

    // Notify external simulation endpoint with the new participant ID using cURL.
    $simulationUrl = 'https://apiajk.adonai.co.id/mnc/Simulation/'.$idpeserta;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $simulationUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    @curl_exec($ch);
    curl_close($ch);

    $return = array(
      'idpeserta' => $idpeserta,
      'medical' => $medical,
      'status' => 'success'
    );
    echo json_encode($return); 

  }catch(Exception $e){
    mysql_query("ROLLBACK");

    $return = array(
      'status' => 'failed',
      'message' => $e->getMessage()
    );
    echo json_encode($return); 
  }

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