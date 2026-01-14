<?php
 /********************************************************************
 DESC  : Create by hansen;
 EMAIL : hansendputra@gmail.com;
 Create Date : 2018-02-22

 ********************************************************************/
 // echo ini_get('display_errors');
 // if (!ini_get('display_errors')) {
 //     ini_set('display_errors', '1');
 // }
 // echo ini_get('display_errors');

    include "../param.php";

    $path_upload ="../image/upload/";
    $now = date('YmdHis');
    $today = date('Y-m-d');

    $typeupload = AES::encrypt128CBC('uploadnonspk', ENCRYPTION_KEY);

    function insertpeserta($filname)
    {
        $sql = "INSERT INTO ajkpeserta (idC,idbroker,idclient,idpolicy,idpeserta,filename,nomorktp,nocif,nomorpk,nama,gender,tptlahir,tgllahir,usia,pekerjaan,plafond,tglakad,tenor,tglakhir,tgltransaksi,premirate,premirate_sys,premi,premi_sys,totalpremi,medical,aspremirate,aspremi,astotalpremi,alamatobjek,statusaktif,regional,area,cabang,nopinjaman,noreflunas,asuransi,input_by,input_time,approve_by,approve_time,statuslunas) SELECT idC,idbroker,idclient,idpolicy,idpeserta,filename,nomorktp,nocif,nomorpk,nama,gender,tptlahir,tgllahir,usia,pekerjaan,plafond,tglakad,tenor,tglakhir,tgltransaksi,premirate,premirate_sys,premi,premi_sys,totalpremi,medical,aspremirate,aspremi,astotalpremi,alamatobjek,statusaktif,regional,area,cabang,nopinjaman,noreflunas,asuransi,input_by,input_time,approve_by,approve_time,statuslunas FROM ajkpeserta_temp where filename = '$filname';";
        return $sql;
    }

    function insertcms($idpeserta)
    {
        $sql = "INSERT INTO CMS_ArAp_Transaction(fArAp_TransactionCode,
																		fArAp_TransactionDate,
																		fArAp_Status,
																		fArAp_No,
																		fArAp_Customer_Id,
																		fArAp_Customer_Nm,
																		fArAp_Asuransi_Id,
																		fArAp_Asuransi_Nm,
																		fArAp_Produk_Nm,
																		fArAp_StatusPeserta,
																		fArAp_DateStatus,
																		fArAp_CoreCode,
																		fArAp_BMaterialCode,
																		fArAp_RefMemberID,
																		fArAp_RefMemberNm,
																		fArAp_RefCabang,
																		fArAp_RefDescription,
																		fArAp_RefAmount,
																		fArAp_RefAmount2,
																		fArAp_RefDOB,
																		fArAp_AssDate,
																		fArAp_RefTenor,
																		fArAp_RefPlafond,
																		fArAp_Return_Status,
																		fArAp_Return_Date,
																		fArAp_Return_Amount,
																		fArAp_SourceDB,
																		input_by,
																		input_date)
  			SELECT
  				'AR-01' as fArAp_TransactionCode,
  				ajkdebitnote.tgldebitnote as fArAp_TransactionDate,
  				'A' as fArAp_Status,
  				ajkdebitnote.nomordebitnote as fArAp_No,
  				'JATIM' as fArAp_Customer_Id,
  				'PT Bank Pembangunan Daerah Jawa Timur Tbk' as fArAp_Customer_Nm,
  				ajkinsurance.name as fArAp_Asuransi_Id,
  				ajkinsurance.companyname as fArAp_Asuransi_Nm,
  				ajkpolis.produk as fArAp_Produk_Nm,
  				CONCAT(ajkpeserta.statusaktif,ajkpeserta.statuspeserta)as fArAp_StatusPeserta,
  				DATE_FORMAT(NOW(),'%Y-%m-%d')as fArAp_DateStatus,
  				'PRM' as fArAp_CoreCode,
  				'PRM' as fArAp_BMaterialCode,
  				ajkpeserta.idpeserta as fArAp_RefMemberID,
  				ajkpeserta.nama as fArAp_RefMemberNm,
  				ajkcabang.name as fArAp_RefCabang,
  				null as fArAp_RefDescription,
  				ajkpeserta.totalpremi as fArAp_RefAmount,
  				null as fArAp_RefAmount2,
  				ajkpeserta.tgllahir as fArAp_RefDOB,
  				DATE_FORMAT(NOW(),'%Y-%m-%d')as fArAp_AssDate,
  				ajkpeserta.tenor as fArAp_RefTenor,
  				ajkpeserta.plafond as fArAp_RefPlafond,
  				CASE WHEN ajkpeserta.tgllunas != '' THEN 'C' ELSE null END as fArAp_Return_Status,
  				ajkpeserta.tgllunas as fArAp_Return_Date,
  				ajkpeserta.totalpremi as fArAp_Return_Amount,
  				'BIOSJATIM' as fArAp_SourceDB,
  				ajkpeserta.input_by as input_by,
  				now()as input_date
  			FROM ajkpeserta
  			INNER JOIN ajkcabang
  			ON ajkcabang.er = ajkpeserta.cabang
  			INNER JOIN ajkdebitnote
  			ON ajkdebitnote.id = ajkpeserta.iddn
  			INNER JOIN ajkpolis
  			ON ajkpolis.id = ajkpeserta.idpolicy
  			INNER JOIN ajkinsurance
  			ON ajkinsurance.id = ajkpeserta.asuransi
  			WHERE ajkpeserta.del is null and
  			ajkpeserta.idpeserta = '".$idpeserta."';";
          return $sql;
    }

    function insertcadangan($idpeserta, $tahun, $nilai_cadangan_klaim, $nilai_cadangan_refund, $input_by, $input_time, $bungabank=0, $nilai_cicilan=0, $due,$plafond_cicilan)
    {
        $sql = "INSERT INTO ajkcadanganas (idpeserta,tahun,nilai_cadangan_klaim,nilai_cadangan_refund,bunga_bank,nilai_cicilan,input_by,input_time,duedate,plafond_cicilan)
				 		VALUES('$idpeserta',
				 						'$tahun',
				 						'$nilai_cadangan_klaim',
				 						'$nilai_cadangan_refund',
				 						'$bungabank',
				 						'$nilai_cicilan',
				 						'$input_by',
				 						'$input_time',
				 						'$due',
                    '$plafond_cicilan');";
        return $sql;
    }

    function updatednpeserta($cabang, $asuransi, $iddn, $filename)
    {
        $sql = "UPDATE ajkpeserta_temp SET iddn = '$iddn' WHERE cabang = '$cabang' and asuransi = '$asuransi' and filename= '$filename';";
        return $sql;
    }

    function insertpeserta_temp($idC,$idbro, $idclient, $idpolicy, $idpeserta, $filename, $nomorktp, $nocif, $nomorpk, $nama, $gender, $tptlahir, $tgllahir, $usia, $pekerjaan, $plafond, $tglakad, $tenor, $tglakhir,$tgltransaksi, $premirate, $premirate_sys, $premi, $premi_sys, $totalpremi,$medical, $aspremirate, $aspremi, $astotalpremi, $alamat,$status, $regional, $area, $cabang, $nopinjaman, $refpremi, $asuransi, $input_by, $input_time)
    {
        $nama = strtoupper($nama);
        $sql = "INSERT INTO ajkpeserta_temp (idC,idbroker,idclient,idpolicy,idpeserta,filename,nomorktp,nocif,nomorpk,nama,gender,tptlahir,tgllahir,usia,pekerjaan,plafond,tglakad,tenor,tglakhir,tgltransaksi,premirate,premirate_sys,premi,premi_sys,totalpremi,medical,aspremirate,aspremi,astotalpremi,alamatobjek,statusaktif,regional,area,cabang,nopinjaman,noreflunas,asuransi,input_by,input_time,approve_by,approve_time,statuslunas)
						VALUES ('$idC','$idbro','$idclient','$idpolicy','$idpeserta','$filename','$nomorktp','$nocif','$nomorpk','$nama','$gender','$tptlahir','$tgllahir','$usia','$pekerjaan','$plafond','$tglakad','$tenor','$tglakhir','$tgltransaksi','$premirate','$premirate_sys','$premi','$premi_sys','$premi','$medical','$aspremirate','$aspremi','$aspremi','$alamat','$status','$regional','$area','$cabang','$nopinjaman','$refpremi','$asuransi','$input_by','$input_time','$input_by','$input_time','0');";

        return $sql;
    }

    function insertpeserta_as($idpeserta,$idpolis,$asuransi,$tsi,$tglawal,$tglakhir,$tenor,$medical,$rate,$premi,$diskon,$em,$totalpremi,$keterangan, $input_by, $input_time)
    {
        $nama = strtoupper($nama);
        $sql = "INSERT INTO ajkpesertaas (idpeserta,idpolis,idas,tsi,tglawal,tglakhir,tenor,medical,rate,premi,diskon,em,totalpremi,keterangan,input_by,input_time,update_by,update_time)
						VALUES ('$idpeserta','$idpolis','$asuransi','$tsi','$tglawal','$tglakhir','$tenor','$medical','$rate','$premi','$diskon','$em','$totalpremi','$keterangan', '$input_by', '$input_time', '$input_by', '$input_time');";

        return $sql;
    }

    function insertdebitnote($idbro, $idclient, $idpolicy, $asuransi, $idaspolis, $iddn, $regional, $cabang, $nomordebitnote, $premiclient, $premiasuransi, $tgldebitnote, $input_by, $input_time)
    {
        $sql = "INSERT INTO ajkdebitnote (id,idbroker,idclient,idproduk,idas,idaspolis,iddn,idregional,idcabang,nomordebitnote,premiclient,premiasuransi,tgldebitnote,input_by,input_time,paidstatus,paidtanggal,premiclientdibayar)
						VALUES ($iddn,'$idbro','$idclient','$idpolicy','$asuransi','$idaspolis','$iddn','$regional','$cabang','$nomordebitnote','$premiclient','$premiasuransi','$tgldebitnote','$input_by','$input_time','Paid','$input_time',$premiclient);";

        return $sql;
    }

    function mysql_exec_batch($p_query, $p_transaction_safe = true)
    {
        if ($p_transaction_safe) {
            $p_query = 'START TRANSACTION;' . $p_query . '; COMMIT;';
        };
        $query_split = preg_split("/[;]+/", $p_query);
        foreach ($query_split as $command_line) {
            $command_line = trim($command_line);
            if ($command_line != '') {
                $query_result = mysql_query($command_line);
                if ($query_result == 0) {
                    break;
                };
            };
        };
        return $query_result;
    }


    switch ($_POST['han']) {
        case 'input':
            $idpolicy = $_POST['namaproduk'];
            $nama = $_POST['namatertanggung'];
            $gender = $_POST['jnsklmn'];
            $tgllahir = _convertDate2($_POST['tgllahir']);
            $nomorktp = $_POST['nomorktp'];
            $nomorpk = $_POST['nomorpk'];
            $alamat = $_POST['alamat'];
            $plafond = $_POST['plafon'];
            $tenor = $_POST['tenor'];
            $asuransi = $_POST['insurance'];

            $qpeserta = mysql_fetch_array(mysql_query("SELECT id+1 as id FROM ajkpeserta ORDER BY id DESC LIMIT 1 "));
            $qdn = mysql_fetch_array(mysql_query("SELECT ifnull(id,0)+1 as id FROM ajkdebitnote ORDER BY id DESC LIMIT 1 "));

            $metSetAuto = substr($metSetAutoNumber + $qpeserta['id'], 1);
            $idpeserta = $metSetAuto;

            $usia_diff = datediff($today, $tgllahir);
            $usia_ = explode(',', $usia_diff);

            if ($usia_[1] >= 6) {
                $usia = $usia_[0] + 1;
            } else {
                $usia = $usia_[0];
            }
            $tglakhir = date('Y-m-d', strtotime("+".$tenor." months", strtotime($today)));

            $qpolisas = mysql_fetch_array(mysql_query("SELECT *
																								FROM ajkpolisasuransi
																								WHERE idbroker = '".$idbro."' and
																											idcost = '".$idclient."' and
																											idproduk = '".$idpolicy."' and
																											idas = '".$asuransi."' and
																											del is null"));
            $nama = str_replace("'", "''", $nama);
            $plafond = str_replace(",", "", $plafond);

            $premirate = $qratebank['rate'];
            $premi = ($plafond * $premirate)/1000;
            $aspremirate = $qrateasuransi['rate'];
            $aspremi = ($plafond * $aspremirate)/1000;
            $idaspolis = $qpolisas['id'];

            if ($idbro < 9) {
                $kodeBroker = '0'.$idbro;
            } else {
                $kodeBroker = $idbro;
            }
            $fakcekdn = $qdn['id'];
            $idNumber = 100000000 + $fakcekdn;
            $autoNumber = substr($idNumber, 1);
            $nomordebitnote = "DN.".date(y)."".date(m).".".$kodeBroker.'.'.$autoNumber;
            $querypeserta = insertpeserta($idbro, $idclient, $idpolicy,  $idpeserta, "", $nomorktp, $nomorpk, $nama, $gender, $tgllahir, $usia, $plafond, $today, $tenor, $tglakhir, $premirate, $premi, $premi, $aspremirate, $aspremi, $aspremi, $regional, $area, $cabang, $iduser, $mamettoday);
            $querydebitnote =	insertdebitnote($idbro, $idclient, $idpolicy, $asuransi, $idaspolis, $regional, $cabang, $nomordebitnote, $premi, $aspremi, $today, $iduser, $mamettoday);
            $query= $querypeserta.$querydebitnote;
            if (mysql_exec_batch($query)) {
                echo "success";
            } else {
                echo mysql_error();
            }
        break;

        case 'upload':
            $query = '';
            $querydebitnote = '';
            $file_temp = $_SESSION['file_temp'];
            $file_name = $_SESSION['file_name'];

            $inputFileName = '../upload/temp/'.$file_temp;

            $query= "";
            try {
              PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
              $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
              $objReader = PHPExcel_IOFactory::createReader($inputFileType);
              $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
              die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
            }	

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
           
            $qpeserta = mysql_fetch_array(mysql_query("SELECT idC FROM ajkpeserta WHERE idbroker = '".$idbro."' and idclient='".$idclient."' ORDER BY idC DESC LIMIT 1 "));
            if($idbro != 1 ){
              $broker = $idbro;
            }else{
              $broker = "";
            }
            $baris = 0;
           
            for ($row = 9; $row <= $highestRow; $row++) {
             
                $baris++;
                $autonumber = ($qpeserta['idC']+$baris);
                $idpeserta = substr($metSetAutoNumber + $autonumber, 1);
                $idpeserta = $idclient.$idpeserta;
                //  Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true);
                $i = 0;

                foreach($rowData[0] as $k=>$v){
                  $data[$i] = $v;
                  $i++;
                }
               
                $today = date('Y-m-d');

                $no = $data[0]; //A
                $produk = $data[1]; //B
                $nama = $data[2]; //C
                $gender = $data[3]; //D
                $tptlahir = $data[4]; //E
                $tgllahir = $data[7]."-".$data[6]."-".$data[5]; // F G H
                $ktp = $data[8]; //I
                $alamat = $data[9]; //J
                $pekerjaan = $data[10]; //K
                $tujuan = $data[11]; //L
                $tglakad = $data[14]."-".$data[13]."-".$data[12]; //M N O
                $tenor = $data[15]; //P
                $nopinjaman = $data[16]; //Q
                $norekening = $data[17]; //R
                $plafond = $data[18]; //R

                $nama = str_replace("'", "''", $nama);
                $alamat = str_replace("'", "''", $alamat);
                $qproduk = mysql_fetch_array(mysql_query("SELECT * FROM ajkpolis WHERE idcost = '".$idclient."' AND (ref_mapping = '".$produk."')"));
                $qcabang = mysql_fetch_array(mysql_query("SELECT * FROM ajkcabang WHERE idclient = '".$idclient."' AND er = '".$cabang."'"));
                
                $idpolicy = $qproduk['id'];
                $usia = birthday($tgllahir, $tglakad);

                $regionalpeserta = $qcabang['idreg'];
                $cabangpeserta = $qcabang['er'];
                $areapeserta = $qcabang['idarea'];


                if ($idbro < 9) {
                    $kodeBroker = '0'.$idbro;
                } else {
                    $kodeBroker = $idbro;
                }

                $date=date_create($tglakad);
                date_add($date,date_interval_create_from_date_string($tenor." months"));
                $tglakhir = date_format($date,"Y-m-d");
                $asuransi = 2;

                if($idpolicy != 11){
                  $qpremi2 = mysql_fetch_array(mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$idpolicy."' and '".$tenor."' BETWEEN tenorfrom and tenorto and status = 'Aktif' and del is null"));
                  // $qpremi2 = mysql_fetch_array(mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = 11 and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' between agefrom and ageto and status = 'Aktif' and del is null"));
                  $qmedical2 = mysql_fetch_array(mysql_query("select * from ajkmedical where idproduk = '".$idpolicy."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'"));
                  // $qmedical2 = mysql_fetch_array(mysql_query("select * from ajkmedical where idproduk = 11 and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'"));

                //   $ratebank1 = $qpremi1['rate'];
                  $ratebank = $qpremi2['rate'];
                //   $ratebank = $ratebank1 + $ratebank2;

                //   $premibank1 = $plafond/$qproduk['calculatedrate'] * $ratebank1;
                  $premibank = $plafond/$qproduk['calculatedrate'] * $ratebank;

                //   $premibank = $premibank1 + $premibank2;

                  $rateasuransi   = $ratebank;
                  $premiasuransi  = $premibank;
                  $ratebank_sys = $ratebank;
                  $premibank_sys = $premibank; 

                //   $medical1        = $qmedical1['type'];
                  $medical        = $qmedical2['type'];

                //   $medical = $medical1.$medical2;

                  $queryas2 = insertpeserta_as($idpeserta,$idpolicy,$asuransi,$tsi,$tglawal,$tglakhir,$tenor,$medical,$ratebank,$premibank,0,0,$premibank,'ASURANSI JIWA', $iduser, $mamettoday);
                  // $queryas2 = insertpeserta_as($idpeserta,11,2,$tsi,$tglakad,$tglakhir,$tenor,$medical2,$ratebank2,$premibank2,0,0,$premibank2,'ASURANSI JIWA', $iduser, $mamettoday);

                //   mysql_query($queryas1);
                  mysql_query($queryas2);
                }else{
                  $qpremi = mysql_fetch_array(mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$idpolicy."' and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' between agefrom and ageto and status = 'Aktif' and del is null"));
                  $qmedical = mysql_fetch_array(mysql_query("select * from ajkmedical where idproduk = '".$idpolicy."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'"));
                  $ratebank = $qpremi['rate'];
                  $premibank = $plafond/$qproduk['calculatedrate'] * $ratebank;

                  $rateasuransi   = $ratebank;
                  $premiasuransi  = $premibank;

                  $ratebank_sys = $ratebank;
                  $premibank_sys = $premibank;     
                  $medical        = $qmedical['type'];                  

                  $queryas = insertpeserta_as($idpeserta,$idpolis,$asuransi,$tsi,$tglawal,$tglakhir,$tenor,$medical,$ratebank,$premibank,0,0,$premibank,'ASURANSI JIWA', $iduser, $mamettoday);
                  mysql_query($queryas);
                }

                

                
                // $asuransi = 2;
                $status = 'Pending';

                $querypeserta = insertpeserta_temp($autonumber,$idbro, $idclient, $idpolicy, $idpeserta, $file_temp, $ktp, $nocif, $norekening, $nama, $gender, $tptlahir, $tgllahir, $usia, $pekerjaan, $plafond, $tglakad, $tenor, $tglakhir, $tglakad,$ratebank, $ratebank_sys, $premibank, $premibank_sys, $premibank,$medical, $rateasuransi, $premiasuransi, $premiasuransi, $alamat,$status, $regionalpeserta, $areapeserta, $cabangpeserta, $nopinjaman, $refpremi, $asuransi, $iduser, $mamettoday);
                
                mysql_query($querypeserta);
                // echo $querypeserta.'<br>';
                
            }
            
            $insertpes = insertpeserta($file_temp);
            // echo $insertpes.'<br>';
            mysql_query($insertpes);
            
            header("location:../upload?xq=".$typeupload."&pesan=Berhasil di Simpan");
        break;
        
        case 'uploadcsf':
          $typeupload = AES::encrypt128CBC('uploadcsf', ENCRYPTION_KEY);
          $file_temp = $_SESSION['file_temp'];
          $file_name = $_SESSION['file_name'];
          $path = '../myFiles/_uploaddata/'.$foldername;

          if (!file_exists($path)) {
              mkdir($path, 0777);
              chmod($path, 0777);
          }

          $inputFileName = '../upload/temp/'.$file_temp;

          $newfilename = date('ymd_his').'_'.$file_name;
          copy($inputFileName, $path.$newfilename) or die("Could not upload file!");

          

          $handle = fopen($path.$newfilename, "r");
          if ($handle) {
              while (($line = fgets($handle)) !== false) {
               $data = explode('|', $line);
               //echo $data[0];
               $query = "UPDATE ajkpeserta SET noasuransi = '".$data[1]."' WHERE idpeserta = '".$data[0]."'";
               // echo $query.'<br>';
               mysql_query($query);
             }
           }

           header("location:../upload?xq=".$typeupload."&pesan=Berhasil di Simpan");
        break;

        case 'newresturno':
          $file_name = $_FILES['attachment']['name'];
	        $file_name_tmp = $_FILES['attachment']['tmp_name'];
          $path = '../myFiles/_uploaddata/'.$foldername;
          
          if (!file_exists($path)) {
              mkdir($path, 0777);
              chmod($path, 0777);
          }
          $newfilename = $foldername.date("Ymd").$file_name;
          
          move_uploaded_file($_FILES["attachment"]["tmp_name"], $path.date("Ymd").$file_name) or die( "Could not upload file!");
          $periode = $_POST['startdate'].'|'.$_POST['enddate'];
          $cabang = $_POST['cabang'];
          $tgl = _convertDate2($_POST['paiddate']);
          $nilai = str_replace(',','',$_POST['amount']);
          $keterangan = $_POST['keterangan'];
          $query = "INSERT INTO ajkhisresturno 
                    SET cabang = '".$cabang."',
                        nilai_bayar = '".$nilai."',
                        tgl_bayar = '".$tgl."',
                        periode = '".$periode."',
                        keterangan='".$keterangan."',
                        attachment='".$newfilename."',
                        input_by='".$iduser."',
                        input_date='".$mamettoday."'";
            //   echo $query;
          $result = mysql_query($query);
          header("location:../dashboard");
        break;

        
    }

    if($_REQUEST['han'] == 'delresturno'){
      $cabang = $_REQUEST['cab'];
      $periode = $_REQUEST['periode'];
      $query = "UPDATE ajkhisresturno
                SET update_by = '".$iduser."',
                    update_date = '".$mamettoday."',
                    del = 1
                WHERE cabang = '".$cabang."' and 
                      periode = '".$periode."'";
      // echo $query;
      $result = mysql_query($query);
      header("location:../dashboard");
    }
    
       
       
        
