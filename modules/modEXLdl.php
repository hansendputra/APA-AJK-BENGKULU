<?php
// ----------------------------------------------------------------------------------
// Original Author Of File : Rahmad
// E-mail :kepodank@gmail.com
// @ Copyright 2016
// ----------------------------------------------------------------------------------
error_reporting(0);
session_start();
require_once('../includes/metPHPXLS/Worksheet.php');
require_once('../includes/metPHPXLS/Workbook.php');
include_once('../includes/fu6106.php');
include_once('../includes/Encrypter.class.php');
$thisEncrypter = new textEncrypter();
include_once('../includes/functions.php');
$today = date("YmdHis");
switch ($_REQUEST['Rxls']) {
	case "ExlDL":
		$filename = "FILE_UPLOAD";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}

		$colField = mysql_fetch_array(mysql_query('SELECT ajkexcelupload.id, Count(ajkexcelupload.idxls) AS jumField, ajkclient.`name`, ajkpolis.policyauto FROM ajkexcelupload INNER JOIN ajkclient ON ajkexcelupload.idc = ajkclient.id INNER JOIN ajkpolis ON ajkexcelupload.idp = ajkpolis.id WHERE ajkexcelupload.idb="'.$thisEncrypter->decode($_REQUEST['idb']).'" AND ajkexcelupload.idc="'.$thisEncrypter->decode($_REQUEST['idc']).'" AND ajkexcelupload.idp="'.$thisEncrypter->decode($_REQUEST['idp']).'" GROUP BY ajkexcelupload.idp'));
		$jumlahFieldDataUplaod = $colField['jumField'];
		HeaderingExcel(str_replace(" ","_", strtoupper($colField['name'])).'_'.$filename.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('vcenter');	$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('green');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('vcenter');	$fjudul->set_align('center');	$fjudul->set_bold();
		$fdate =& $workbook->add_format();		$fdate->set_color('white');

		$worksheet1->write_string(0, $jumlahFieldDataUplaod + 1, date("Y-m-d"), $fdate);	//cek data asli file excel

		$worksheet1->merge_cells(0, 0, 0, $jumlahFieldDataUplaod);	$worksheet1->write_string(0, 0, "DATA UPLOAD PESERTA", $fjudul, 0, $jumlahFieldDataUplaod);
		$worksheet1->merge_cells(1, 0, 1, $jumlahFieldDataUplaod);	$worksheet1->write_string(1, 0, strtoupper($colField['name']), $fjudul);
		$worksheet1->merge_cells(2, 0, 2, $jumlahFieldDataUplaod);	$worksheet1->write_string(2, 0, strtoupper($colField['policyauto']), $fjudul);

		$Databaris = 4;
		$Datakolom = 1;
		$metDLExl = mysql_query('SELECT ajkexcel.fieldname, ajkexcelupload.valempty, ajkexcelupload.valdate, ajkexcelupload.valsamedata
						 FROM ajkexcelupload
						 INNER JOIN ajkexcel ON ajkexcelupload.idxls = ajkexcel.id
						 WHERE ajkexcelupload.idb = "'.$thisEncrypter->decode($_REQUEST['idb']).'" AND
						 	   ajkexcelupload.idc = "'.$thisEncrypter->decode($_REQUEST['idc']).'" AND
						 	   ajkexcelupload.idp = "'.$thisEncrypter->decode($_REQUEST['idp']).'"
						 ORDER BY ajkexcelupload.id ASC');
		$worksheet1->write_string($Databaris, 0, "No",$format);
		while ($metDLExl_ = mysql_fetch_array($metDLExl)) {
			if ($metDLExl_['valempty']=="Y" OR $metDLExl_['valdate']=="Y" OR $metDLExl_['valsamedata']=="Y") {
				$metKolomVal = $metDLExl_['fieldname'].'*';
			}else{
				$metKolomVal = $metDLExl_['fieldname'];
			}
			$worksheet1->write_string($Databaris, $Datakolom, $metKolomVal, $format);
			$Datakolom++;
		}

		$workbook->close();
		;
		break;

	case "lprmember":
    
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel(date('YmdHis').'_MEMBER_BANK.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();

		if ($thisEncrypter->decode($_REQUEST['idb'])) {	$satu ='AND ajkcobroker.id = "'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc'])) {	$dua ='AND ajkclient.id = "'.$thisEncrypter->decode($_REQUEST['idc']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idp'])) {
			$metProduk = explode("_", $thisEncrypter->decode($_REQUEST['idp']));
			$tiga ='AND ajkpolis.id = "'.$metProduk[0].'"';	}

		$met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient
									  FROM ajkcobroker
									  INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
									  WHERE ajkcobroker.del IS NULL '.$satu.' '.$dua.''));

		if ($_REQUEST['idb']==""){	$_metbroker = '';	}else{	$_metbroker = $met_['brokername'];	}
		if ($thisEncrypter->decode($_REQUEST['idc'])) {	$_metclient = $met_['clientname'];	}else{	$_metclient = 'ALL CLIENT';	}
		if ($thisEncrypter->decode($_REQUEST['idp'])) {	
      $metproduk = $thisEncrypter->decode($_REQUEST['idp']);
      // if($metproduk == 'KPR'){
      //   $tiga = 'AND ajkpeserta.idpolicy="11"';
      //   $_metproduk = 'KPR';
      // }elseif($metproduk == 'THT'){
      //   $tiga = 'AND ajkpeserta.idpolicy="12"';
      //   $_metproduk = 'THT';	
      // }else{
      //   $tiga = 'AND ajkpeserta.idpolicy not in (11,12)';
      //   $_metproduk = 'Multiguna';	
      // }
      $tiga = 'AND ajkpeserta.typedata ="'.$metproduk.'"';
      $_metproduk = $metproduk;	
    }else{	
      $_metproduk = 'ALL PRODUCT';	
    }

    $periode = '';
    $periodetrans = '';

    $start = $thisEncrypter->decode($_REQUEST['dtfrom']);
    $end = $thisEncrypter->decode($_REQUEST['dtto']);
    $starttrans = $thisEncrypter->decode($_REQUEST['dtfromtrans']);
    $endtrans = $thisEncrypter->decode($_REQUEST['dttotrans']);

    if($start != ""){
      $start = $thisEncrypter->decode($_REQUEST['dtfrom']);
      $periode = 'Tgl. Akad : '.$start.' to '.$end.'';
    }
    if($starttrans != ""){
      $periodetrans = 'Tgl. Transaksi : '.$starttrans.' to '.$endtrans.'';
    }    

    $kolom = 0;
    
   
    $baris = 6;
		$worksheet1->set_row($baris, 15);
		$worksheet1->set_column($baris, $kolom, 1);	$worksheet1->write_string($baris, $kolom, "No", $format);
		$worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Perusahaan", $format);
		$worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Produk", $format);
    $worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Pekerjaan", $format);
		$worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Debitnote", $format);
		$worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "Tanggal DN", $format);
		$worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "KTP", $format);
		$worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "No Pinjaman", $format);
    $worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "No Rekening", $format);
		$worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "ID Debitur", $format);
		$worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "No. Sertifikat", $format);
		$worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Nama Debitur", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Tanggal Lahir", $format);
		$worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Jenis Kelamin", $format);
		$worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Pekerjaan", $format);
    $worksheet1->set_column($baris, ++$kolom, 30);	$worksheet1->write_string($baris, $kolom, "Alamat", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Mulai Asuransi", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Akhir Asuransi", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Tgl Transaksi", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Nm Asuransi", $format);
		$worksheet1->set_column($baris, ++$kolom, 5);	$worksheet1->write_string($baris, $kolom, "JWP (Bulan)", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Harga Pertanggungan", $format);
		$worksheet1->set_column($baris, ++$kolom, 5);	$worksheet1->write_string($baris, $kolom, "Usia", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Usia + JWP", $format);
    $worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Medical", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Rate", $format);
		$worksheet1->set_column($baris, ++$kolom, 15);	$worksheet1->write_string($baris, $kolom, "Premi", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Diskon", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Feebase", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Brokerage", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "DPP", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "PPn", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "PPh", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Komisi Broker After Tax", $format);
    $worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Nett Premi Asuransi", $format);
    $worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Nilai Bayar", $format);
    $worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Tgl Bayar", $format);
    $worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Selisih Bayar", $format);
		$worksheet1->set_column($baris, ++$kolom, 10);	$worksheet1->write_string($baris, $kolom, "Status", $format);
		$worksheet1->set_column($baris, ++$kolom, 20);	$worksheet1->write_string($baris, $kolom, "Cabang", $format);
		$worksheet1->set_column($baris, ++$kolom, 20);	$worksheet1->write_string($baris, $kolom, "Keterangan", $format);

    $worksheet1->write_string(0, 0, "LAPORAN DATA DEBITUR PERUSAHAAN", $fjudul);	$worksheet1->merge_cells(0, 0, 0, $kolom);
		$worksheet1->write_string(1, 0, strtoupper($_metbroker), $fjudul);	$worksheet1->merge_cells(1, 0, 1, $kolom);
		$worksheet1->write_string(2, 0, strtoupper($_metclient), $fjudul);	$worksheet1->merge_cells(2, 0, 2, $kolom);
		$worksheet1->write_string(3, 0, strtoupper($_metproduk), $fjudul);	$worksheet1->merge_cells(3, 0, 3, $kolom);
    
    if($periode != ''){
      $worksheet1->write_string(4, 0, strtoupper($periode), $fjudul);	$worksheet1->merge_cells(4, 0, 4, $kolom);
      $baris = $baris+1;
    }

    if($periodetrans != ''){
      $worksheet1->write_string(4, 0, strtoupper($periodetrans), $fjudul);	$worksheet1->merge_cells(4, 0, 4, $kolom);
      $baris = $baris+1;
    }
    
    // $baris -= 1;
		$metCOB = mysql_query($thisEncrypter->decode($_SESSION['lprmember']));

		while ($metCOB_ = mysql_fetch_array($metCOB)) {
			if($metCOB_['nomordebitnote']!=''){
					
				$qdiskon = $metCOB_['rumus_diskon'];
				$qfeebase = $metCOB_['rumus_feebase'];
				$qbrokerage = $metCOB_['rumus_brokerage'];
				$qdpp = $metCOB_['rumus_dpp'];
				$qppn = $metCOB_['rumus_ppn'];
				$qpph = $metCOB_['rumus_pph'];
				$qpremi = $metCOB_['rumus_premi'];
				$qcad_klaim = $metCOB_['rumus_cad_klaim'];
				$qcad_premi = $metCOB_['rumus_cad_premi'];
				$qnettpremi = $metCOB_['rumus_nettpremi'];

				$querya = "
				SELECT $qdiskon as diskon,
							 $qfeebase as feebase,
							 $qbrokerage as brokerage,
							 $qdpp as dpp,
							 $qppn as ppn,
							 $qpph as pph,
							 $qpremi as premi,
							 $qnettpremi as nettpremi
				FROM ajkpeserta
				INNER JOIN ajkinsurance on ajkinsurance.id = ajkpeserta.asuransi
				WHERE idpeserta = '".$metCOB_['idpeserta']."' ";
				
				$res = mysql_fetch_array(mysql_query($querya));
				$diskon = $res['diskon'];
				$feebase = $res['feebase'];
				$brokerage = $res['brokerage'];
				$dpp = $res['dpp'];
				$ppn = $res['ppn'];
				$pph = $res['pph'];
				$premi = $res['premi'];
				$cad_klaim = $res['cad_klaim'];
				$cad_premi = $res['cad_premi'];
				$nettpremi = $res['nettpremi'];
			}else{					
				$diskon = 0;
				$feebase = 0;
				$brokerage = 0;
				$dpp = 0;
				$ppn = 0;
				$pph = 0;
				$premi = 0;
				$cad_klaim = 0;
				$cad_premi = 0;
				$nettpremi = 0;
			}

			$usiatenor = round($metCOB_['tenor'] / 12) + $metCOB_['usia'];
			
    $kolom = 0;
			$worksheet1->write_string($baris, $kolom, ++$no, 'C');
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['perusahaan']);
      $worksheet1->write_string($baris, ++$kolom, $metCOB_['typedata']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['produk']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['nomordebitnote']);
			$worksheet1->write_string($baris, ++$kolom, _convertDate($metCOB_['tgldebitnote']));
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['nomorktp']);			
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['nopinjaman']);
      $worksheet1->write_string($baris, ++$kolom, $metCOB_['nomorpk']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['idpeserta']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['noasuransi']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['nama']);
			$worksheet1->write_string($baris, ++$kolom, _convertDate($metCOB_['tgllahir']));
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['jnskelamin']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['pekerjaan']);
      $worksheet1->write_string($baris, ++$kolom, $metCOB_['alamatobjek']);
			$worksheet1->write_string($baris, ++$kolom, _convertDate($metCOB_['tglakad']));
			$worksheet1->write_string($baris, ++$kolom, _convertDate($metCOB_['tglakhir']));
			$worksheet1->write_string($baris, ++$kolom, _convertDate($metCOB_['tgltransaksi']));
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['nmasuransi']);
			$worksheet1->write_number($baris, ++$kolom, $metCOB_['tenor']);
			$worksheet1->write_number($baris, ++$kolom, $metCOB_['plafond']);
			$worksheet1->write_number($baris, ++$kolom, $metCOB_['usia']);
			$worksheet1->write_string($baris, ++$kolom, $usiatenor);
      $worksheet1->write_string($baris, ++$kolom, $metCOB_['medical']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['premirate']);
			$worksheet1->write_number($baris, ++$kolom, $metCOB_['totalpremi']);
			// $worksheet1->write_number($baris, 26, $metCOB_['resturno']);
			$worksheet1->write_number($baris, ++$kolom, $diskon);
			$worksheet1->write_number($baris, ++$kolom, $feebase);
			$worksheet1->write_number($baris, ++$kolom, $brokerage);
			$worksheet1->write_number($baris, ++$kolom, $dpp);
			$worksheet1->write_number($baris, ++$kolom, $ppn);
			$worksheet1->write_number($baris, ++$kolom, $pph);
			$worksheet1->write_number($baris, ++$kolom, $premi);
      $worksheet1->write_number($baris, ++$kolom, $nettpremi);
      $worksheet1->write_number($baris, ++$kolom, $metCOB_['bayar']);
      $worksheet1->write_string($baris, ++$kolom, _convertDate($metCOB_['tglbayar']));
      $worksheet1->write_number($baris, ++$kolom, $metCOB_['totalpremi'] - $metCOB_['bayar']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['statusaktif']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['cabang']);
			$worksheet1->write_string($baris, ++$kolom, $metCOB_['keterangan']);

			$baris++;
			$tPremi += $metCOB_['totalpremi'];
			$tPremias += $metCOB_['astotalpremi'];
		}
		// $worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 27);
		// $worksheet1->write_number($baris, 28, $tPremi, $ftotal);

		$workbook->close();
		;
	break;

	case "lprmemberarm":
		$filename = "MEMBER_BANK_ARM";

		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.date('YmdHis').'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();

		$worksheet1->write_string(0, 0, "LIST OUTSTANDING PEMBAYARAN", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 13);
		$worksheet1->write_string(1, 0, strtoupper($_metbroker), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 13);
		$worksheet1->write_string(2, 0, strtoupper($_metclient), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 13);
		$worksheet1->write_string(3, 0, strtoupper($_metproduk), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 13);

		$worksheet1->set_row(5, 15);
		$worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "No", $format);
		$worksheet1->set_column(5, 1, 30);	$worksheet1->write_string(5, 1, "ID Peserta", $format);
		$worksheet1->set_column(5, 2, 30);	$worksheet1->write_string(5, 2, "No Pinjaman", $format);
		$worksheet1->set_column(5, 3, 30);	$worksheet1->write_string(5, 3, "Nama", $format);
		$worksheet1->set_column(5, 4, 15);	$worksheet1->write_string(5, 4, "Tgl Akad", $format);
		$worksheet1->set_column(5, 5, 15);	$worksheet1->write_string(5, 5, "Plafond", $format);
		$worksheet1->set_column(5, 6, 15);	$worksheet1->write_string(5, 6, "Tenor", $format);
		$worksheet1->set_column(5, 7, 15);	$worksheet1->write_string(5, 7, "Pekerjaan", $format);
		$worksheet1->set_column(5, 8, 15);	$worksheet1->write_string(5, 8, "Rate", $format);
		$worksheet1->set_column(5, 9, 15);	$worksheet1->write_string(5, 9, " Nilai Premi", $format);
		$worksheet1->set_column(5, 10, 15);	$worksheet1->write_string(5, 10, "Nilai Bayar", $format);
		$worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 11, "Selisih Bayar", $format);
		$worksheet1->set_column(5, 12, 15);	$worksheet1->write_string(5, 12, "Cabang", $format);
		$baris = 6;
				
		$metCOB = mysql_query($thisEncrypter->decode($_SESSION['lprmemberarm']));
		
		while ($metCOB_ = mysql_fetch_array($metCOB)) {
			$usiatenor = round($metCOB_['tenor'] / 12) + $metCOB_['usia'];
			
			$worksheet1->write_string($baris, 0, ++$no, 'C');
			$worksheet1->write_string($baris, 1, $metCOB_['idpeserta']);
			$worksheet1->write_string($baris, 2, $metCOB_['nopinjaman']);
			$worksheet1->write_string($baris, 3, $metCOB_['nama']);
			$worksheet1->write_string($baris, 4, $metCOB_['tglakad']);
			$worksheet1->write_string($baris, 5, $metCOB_['plafond']);			
			$worksheet1->write_string($baris, 6, $metCOB_['tenor']);
			$worksheet1->write_string($baris, 7, $metCOB_['nm_kategori_profesi']);
			$worksheet1->write_string($baris, 8, $metCOB_['premirate']);
			$worksheet1->write_string($baris, 9, $metCOB_['premi']);
			$worksheet1->write_string($baris, 10, $metCOB_['nilaibayar']);			
			$worksheet1->write_string($baris, 11, $metCOB_['premi'] - $metCOB_['nilaibayar']);
			$worksheet1->write_string($baris, 12, $metCOB_['nmcabang']);

			$baris++;
			$tPremi += $metCOB_['totalpremi'];
			$tPremias += $metCOB_['astotalpremi'];
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 16);
		$worksheet1->write_number($baris, 15, $tPremi, $ftotal);

		$workbook->close();
		;
	break;

	case "lprviewappins":
		$filename = "MEMBER_VIEW_INS";

		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.date('YmdHis').'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();

		$worksheet1->write_string(0, 0, "LAPORAN DATA DEBITUR ASURANSI", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 20);
		$worksheet1->write_string(1, 0, strtoupper($_metbroker), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 20);
		$worksheet1->write_string(2, 0, strtoupper($_metclient), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 20);
		$worksheet1->write_string(3, 0, strtoupper($_metproduk), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 20);

		$worksheet1->set_row(5, 15);
		$worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "No", $format);
		$worksheet1->set_column(5, 1, 15);	$worksheet1->write_string(5, 1, "Asuransi", $format);
		$worksheet1->set_column(5, 2, 30);	$worksheet1->write_string(5, 2, "ID Peserta", $format);
		$worksheet1->set_column(5, 3, 30);	$worksheet1->write_string(5, 3, "No Pinjaman", $format);
		$worksheet1->set_column(5, 4, 50);	$worksheet1->write_string(5, 4, "Nama", $format);
		$worksheet1->set_column(5, 5, 10);	$worksheet1->write_string(5, 5, "Tgl Lahir", $format);
		$worksheet1->set_column(5, 6, 15);	$worksheet1->write_string(5, 6, "Tgl Akad", $format);
		$worksheet1->set_column(5, 7, 15);	$worksheet1->write_string(5, 7, "Tgl Akhir", $format);
		$worksheet1->set_column(5, 8, 15);	$worksheet1->write_string(5, 8, "Plafond", $format);
		$worksheet1->set_column(5, 9, 5);	$worksheet1->write_string(5, 9, "Usia", $format);
		$worksheet1->set_column(5, 10, 5);	$worksheet1->write_string(5, 10, "Tenor", $format);
		$worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 11, "Pekerjaan", $format);
		$worksheet1->set_column(5, 12, 15);	$worksheet1->write_string(5, 12, "Cabang", $format);
		$worksheet1->set_column(5, 13, 15);	$worksheet1->write_string(5, 13, "Rate Bank", $format);
		$worksheet1->set_column(5, 14, 15);	$worksheet1->write_string(5, 14, "Premi Bank", $format);
		$worksheet1->set_column(5, 15, 15);	$worksheet1->write_string(5, 15, "Rate AS", $format);
		$worksheet1->set_column(5, 16, 15);	$worksheet1->write_string(5, 16, "Premi AS", $format);
		$worksheet1->set_column(5, 17, 15);	$worksheet1->write_string(5, 17, "B/F", $format);
		$worksheet1->set_column(5, 18, 15);	$worksheet1->write_string(5, 18, "Cad. Klaim", $format);
		$worksheet1->set_column(5, 19, 15);	$worksheet1->write_string(5, 19, "Cad. Premi", $format);
		$worksheet1->set_column(5, 20, 15);	$worksheet1->write_string(5, 20, "Nett Premi", $format);
		

		$baris = 6;
				
		$metCOB = mysql_query($thisEncrypter->decode($_SESSION['lprmemberasviewapp']));
		
		while ($metCOB_ = mysql_fetch_array($metCOB)) {

			$usiatenor = round($metCOB_['tenor'] / 12) + $metCOB_['usia'];			
			$worksheet1->write_string($baris, 0, ++$no, 'C');			
			$worksheet1->write_string($baris, 1, $metCOB_['nmasuransi']);
			$worksheet1->write_string($baris, 2, $metCOB_['idpeserta']);
			$worksheet1->write_string($baris, 3, $metCOB_['nopinjaman']);
			$worksheet1->write_string($baris, 4, $metCOB_['nama']);			
			$worksheet1->write_string($baris, 5, $metCOB_['tgllahir']);
			$worksheet1->write_string($baris, 6, $metCOB_['tglakad']);
			$worksheet1->write_string($baris, 7, $metCOB_['tglakhir']);
			$worksheet1->write_string($baris, 8, $metCOB_['plafond']);
			$worksheet1->write_string($baris, 9, $metCOB_['usia']);			
			$worksheet1->write_string($baris, 10, $metCOB_['tenor']);
			$worksheet1->write_string($baris, 11, $metCOB_['nm_kategori_profesi']);
			$worksheet1->write_string($baris, 12, $metCOB_['nmcabang']);
			$worksheet1->write_string($baris, 13, $metCOB_['premirate']);
			$worksheet1->write_string($baris, 14, $metCOB_['premi']);
			$worksheet1->write_string($baris, 15, $metCOB_['aspremirate']);
			$worksheet1->write_string($baris, 16, $metCOB_['aspremi']);
			$worksheet1->write_string($baris, 17, $metCOB_['bf']);			
			$worksheet1->write_string($baris, 18, $metCOB_['cad_klaim']);
			$worksheet1->write_string($baris, 19, $metCOB_['cad_premi']);
			$worksheet1->write_string($baris, 20, $metCOB_['premi'] - $metCOB_['bf'] - $metCOB_['cad_klaim'] - $metCOB_['cad_premi']);

			$baris++;
			$tPremi += $metCOB_['totalpremi'];
			$tPremias += $metCOB_['astotalpremi'];
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 16);
		$worksheet1->write_number($baris, 15, $tPremi, $ftotal);

		$workbook->close();
		;
	break;

	case "lprmemberIns":
		$filename = "INSURANCE";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.$today.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();

		if ($thisEncrypter->decode($_REQUEST['idb'])) {	$satu ='AND ajkcobroker.id = "'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc'])) {	$dua ='AND ajkclient.id = "'.$thisEncrypter->decode($_REQUEST['idc']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idp'])) {	$tiga ='AND ajkpolis.id = "'.$thisEncrypter->decode($_REQUEST['idp']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['ida'])) {	$empat ='AND ajkinsurance.id = "'.$thisEncrypter->decode($_REQUEST['ida']).'"';	}

		$met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id,
																								  ajkcobroker.logo,
																								  ajkcobroker.`name` AS brokername,
																								  ajkclient.`name` AS clientname,
																								  ajkclient.logo AS logoclient,
																								  ajkpolis.produk,
																								  ajkpolis.policymanual,
																								  ajkpolis.byrate,
																								  ajkinsurance.`name` AS insurancename
																						  FROM ajkcobroker
																						  INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
																						  INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
																						  INNER JOIN ajkinsurance ON ajkcobroker.id = ajkinsurance.idc
																						  WHERE ajkcobroker.del IS NULL '.$satu.' '.$dua.' '.$tiga.' '.$empat.''));

		if ($thisEncrypter->decode($_REQUEST['idb']))	{	$_metbroker 	= $met_['brokername'];	}else{	$_metbroker = 'ALL BROKER';	}
		if ($thisEncrypter->decode($_REQUEST['idc']))	{	$_metclient 	= $met_['clientname'];		}else{	$_metclient = 'ALL CLIENT';	}
		if ($thisEncrypter->decode($_REQUEST['idp']))	{	$_metproduk 	= $met_['produk'];			}else{	$_metproduk = 'ALL PRODUCT';	}
		if ($thisEncrypter->decode($_REQUEST['ida']))	{	$_metinsurance 	= $met_['insurancename'];	}else{	$_metproduk = 'ALL INSURANCE';	}
		if ($thisEncrypter->decode($_REQUEST['dtfrom']))	{	
			$tglakad 	= 'PERIODE AKAD : '.$thisEncrypter->decode($_REQUEST['dtfrom']).' s/d '.$thisEncrypter->decode($_REQUEST['dtto']);
		}
		if ($thisEncrypter->decode($_REQUEST['dtfromtrans']))	{	
			$tgltransaksi 	= 'PERIODE TRANSAKSI : '.$thisEncrypter->decode($_REQUEST['dtfromtrans']).' s/d '.$thisEncrypter->decode($_REQUEST['dttotrans']);
		}
    if ($thisEncrypter->decode($_REQUEST['dm']))	{	
			$_metmedical 	= 'MEDICAL : '.$thisEncrypter->decode($_REQUEST['dm']);
		}

    $row1= 0;
		$worksheet1->write_string($row1, 0, "LAPORAN DATA DEBITUR ASURANSI ".strtoupper($_metinsurance), $fjudul);	$worksheet1->merge_cells($row1, 0, 0, 21);
		$worksheet1->write_string(++$row1, 0, strtoupper($_metbroker), $fjudul);		$worksheet1->merge_cells($row1, 0, 1, 24);
		$worksheet1->write_string(++$row1, 0, strtoupper($_metclient), $fjudul);		$worksheet1->merge_cells($row1, 0, 2, 24);
		$worksheet1->write_string(++$row1, 0, strtoupper($_metproduk), $fjudul);		$worksheet1->merge_cells($row1, 0, 3, 24);
    $worksheet1->write_string(++$row1, 0, strtoupper($_metmedical), $fjudul);		$worksheet1->merge_cells($row1, 0, 4, 24);
    if(isset($tglakad)){
      $worksheet1->write_string(++$row1, 0,$tglakad, $fjudul);	$worksheet1->merge_cells($row1, 0, 5, 24);
    }
		
    if(isset($tgltransaksi)){
      $worksheet1->write_string(++$row1, 0,$tgltransaksi, $fjudul);	$worksheet1->merge_cells($row1, 0, 5, 24);
    }

		$worksheet1->set_row(7, 15);
		$worksheet1->set_column(7, 0, 1);	  $worksheet1->write_string(6, 0, "No", $format);
    $worksheet1->set_column(7, 1, 30);	$worksheet1->write_string(6, 1, "Asuransi", $format);
		$worksheet1->set_column(7, 2, 30);	$worksheet1->write_string(6, 2, "Produk", $format);
    $worksheet1->set_column(7, 3, 30);	$worksheet1->write_string(6, 3, "Pekerjaan", $format);
		$worksheet1->set_column(7, 4, 20);	$worksheet1->write_string(6, 4, "ID Debitur", $format);
		$worksheet1->set_column(7, 5, 15);	$worksheet1->write_string(6, 5, "Nama Debitur", $format);
    $worksheet1->set_column(7, 6, 15);	$worksheet1->write_string(6, 6, "No. KTP", $format);
    $worksheet1->set_column(7, 7, 15);	$worksheet1->write_string(6, 7, "No. Rekening", $format);
		$worksheet1->set_column(7, 8, 15);	$worksheet1->write_string(6, 8, "Tanggal Lahir", $format);
		$worksheet1->set_column(7, 9, 30);	$worksheet1->write_string(6, 9, "Jenis Kelamin", $format);
		$worksheet1->set_column(7, 10, 10);	$worksheet1->write_string(6, 10, "Mulai Asuransi", $format);
		$worksheet1->set_column(7, 11, 10);	$worksheet1->write_string(6, 11, "JWP (Bulan)", $format);
		$worksheet1->set_column(7, 12, 10);	$worksheet1->write_string(6, 12, "Harga Pertanggungan", $format);
		$worksheet1->set_column(7, 13, 5);	$worksheet1->write_string(6, 13, "Usia Masuk", $format);
		$worksheet1->set_column(7, 14, 10);	$worksheet1->write_string(6, 14, "Akhir Asuransi", $format);
		$worksheet1->set_column(7, 15, 5);	$worksheet1->write_string(6, 15, "Usia + JWP", $format);
    $worksheet1->set_column(7, 16, 5);	$worksheet1->write_string(6, 16, "Medical", $format);
		$worksheet1->set_column(7, 17, 10);	$worksheet1->write_string(6, 17, "Rate", $format);
		$worksheet1->set_column(7, 18, 10);	$worksheet1->write_string(6, 18, "Premi", $format);
    $worksheet1->set_column(7, 19, 10);	$worksheet1->write_string(6, 19, "Brokerage", $format);
    $worksheet1->set_column(7, 20, 10);	$worksheet1->write_string(6, 20, "Total Premi", $format);
		$worksheet1->set_column(7, 21, 10);	$worksheet1->write_string(6, 21, "Tgl Bayar", $format);
		$worksheet1->set_column(7, 22, 10);	$worksheet1->write_string(6, 22, "Nilai Bayar", $format);
		$worksheet1->set_column(7, 23, 10);	$worksheet1->write_string(6, 23, "Status", $format);
		$worksheet1->set_column(7, 24, 20);	$worksheet1->write_string(6, 24, "Cabang", $format);


		$baris = 7;
				
		$metCOB = mysql_query($thisEncrypter->decode($_SESSION['lprmemberIns']));

		while ($metCOB_ = mysql_fetch_array($metCOB)) {
			$usiatenor = round($metCOB_['tenor'] / 12) + $metCOB_['usia'];

			$worksheet1->write_string($baris, 0, ++$no, 'C');
      $worksheet1->write_string($baris, 1, $metCOB_['nmcompasuransi']);
			$worksheet1->write_string($baris, 2, $metCOB_['typedata']);
      $worksheet1->write_string($baris, 3, $metCOB_['nmproduk']);
			$worksheet1->write_string($baris, 4, $metCOB_['idpeserta']);
			$worksheet1->write_string($baris, 5, $metCOB_['nama']);
      $worksheet1->write_string($baris, 6, $metCOB_['nomorktp']);
      $worksheet1->write_string($baris, 7, $metCOB_['nomorpk']);
			$worksheet1->write_string($baris, 8, $metCOB_['tgllahir']);
			$worksheet1->write_string($baris, 9, $metCOB_['gender']);
			$worksheet1->write_string($baris, 10, _convertDate($metCOB_['tglawal']));
			$worksheet1->write_number($baris, 11, $metCOB_['tenor']);
			$worksheet1->write_number($baris, 12, $metCOB_['tsi']);
			$worksheet1->write_number($baris, 13, $metCOB_['usia']);
			$worksheet1->write_string($baris, 14, _convertDate($metCOB_['tglakhir']));
			$worksheet1->write_number($baris, 15, $usiatenor);
      $worksheet1->write_string($baris, 16, $metCOB_['medical']);
			$worksheet1->write_string($baris, 17, $metCOB_['rate']);
      $worksheet1->write_number($baris, 18, $metCOB_['totalpremi']);
      $worksheet1->write_number($baris, 19, $metCOB_['brokerage_sys']);
			$worksheet1->write_number($baris, 20, $metCOB_['astotalpremi_sys']);
			$worksheet1->write_string($baris, 21, $metCOB_['tglbayaras']);
			$worksheet1->write_number($baris, 22, $metCOB_['nilaibayaras']);
			$worksheet1->write_string($baris, 23, $metCOB_['statusaktif']);
			$worksheet1->write_string($baris, 24, $metCOB_['nmcabang']);
      


			$baris++;
			$tPremias += $metCOB_['astotalpremi_sys'];
			$tPlafondas += $metCOB_['plafond'];
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 8);
		// $worksheet1->write_number($baris, 8, $tPlafondas, $ftotal);
		$worksheet1->write_number($baris, 20, $tPremias, $ftotal);

		$workbook->close();
	break;

	case "mKlm":
		$filename = "Master Klaim";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		$today = date("YmdHis");
		HeaderingExcel($today.$filename.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();
		
		$worksheet1->write_string(0, 0, "MASTER KLAIM", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 28);

		$worksheet1->set_row(2, 15);
		$worksheet1->set_column(2, 0, 1);		$worksheet1->write_string(2, 0, "No", $format);
		$worksheet1->set_column(2, 1, 30);	$worksheet1->write_string(2, 1, "Urut", $format);
		$worksheet1->set_column(2, 2, 30);	$worksheet1->write_string(2, 2, "Cabang", $format);
		$worksheet1->set_column(2, 3, 15);	$worksheet1->write_string(2, 3, "Asuransi", $format);
		$worksheet1->set_column(2, 4, 30);	$worksheet1->write_string(2, 4, "Produk", $format);
		$worksheet1->set_column(2, 5, 15);	$worksheet1->write_string(2, 5, "ID Peserta", $format);
		$worksheet1->set_column(2, 6, 30);	$worksheet1->write_string(2, 6, "Nama", $format);
		$worksheet1->set_column(2, 7, 10);	$worksheet1->write_string(2, 7, "Tgl Lahir", $format);
		$worksheet1->set_column(2, 8, 15);	$worksheet1->write_string(2, 8, "Usia", $format);
		$worksheet1->set_column(2, 9, 10);	$worksheet1->write_string(2, 9, "Plafond", $format);
		$worksheet1->set_column(2, 10, 10);	$worksheet1->write_string(2, 10, "Klaim Diajukan", $format);
		$worksheet1->set_column(2, 11, 10);	$worksheet1->write_string(2, 11, "Tgl Akad", $format);
		$worksheet1->set_column(2, 12, 5);	$worksheet1->write_string(2, 12, "Tenor", $format);
		$worksheet1->set_column(2, 13, 10);	$worksheet1->write_string(2, 13, "Tgl Meninggal", $format);
		$worksheet1->set_column(2, 14, 5);	$worksheet1->write_string(2, 14, "Akad vs Dol", $format);
		$worksheet1->set_column(2, 15, 10);	$worksheet1->write_string(2, 15, "Tgl Terima Laporan", $format);		
		$worksheet1->set_column(2, 16, 5);	$worksheet1->write_string(2, 16, "Dur", $format);
		$worksheet1->set_column(2, 17, 10);	$worksheet1->write_string(2, 17, "Tgl Lapor Asuransi", $format);
		$worksheet1->set_column(2, 18, 30);	$worksheet1->write_string(2, 18, "Keterangan", $format);
		$worksheet1->set_column(2, 19, 10);	$worksheet1->write_string(2, 19, "Tgl Dokumen Lengkap", $format);
		$worksheet1->set_column(2, 20, 20);	$worksheet1->write_string(2, 20, "Tempat Meninggal", $format);
		$worksheet1->set_column(2, 21, 20);	$worksheet1->write_string(2, 21, "Penyebab Meninggal", $format);
		$worksheet1->set_column(2, 22, 20);	$worksheet1->write_string(2, 22, "Status Klaim", $format);
		$worksheet1->set_column(2, 23, 10);	$worksheet1->write_string(2, 23, "Paid AS", $format);
		$worksheet1->set_column(2, 24, 10);	$worksheet1->write_string(2, 24, "Tgl Paid AS", $format);
		$worksheet1->set_column(2, 25, 10);	$worksheet1->write_string(2, 25, "Paid C", $format);
		$worksheet1->set_column(2, 26, 10);	$worksheet1->write_string(2, 26, "Tgl Paid C", $format);
		$worksheet1->set_column(2, 27, 5);	$worksheet1->write_string(2, 27, "Kol", $format);
		$worksheet1->set_column(2, 28, 10);	$worksheet1->write_string(2, 28, "Batas Kadaluarsa", $format);


		$baris = 3;
				
		$metCOB = mysql_query("SELECT * FROM vklaim");

		while ($metCOB_ = mysql_fetch_array($metCOB)) {			
			$worksheet1->write_string($baris, 0, ++$no, 'C');
			$worksheet1->write_string($baris, 1, $metCOB_['urut'],'C');
			$worksheet1->write_string($baris, 2, $metCOB_['nmcabang']);
			$worksheet1->write_string($baris, 3, $metCOB_['nmasuransi']);
			$worksheet1->write_string($baris, 4, $metCOB_['nmproduk']);
			$worksheet1->write_string($baris, 5, $metCOB_['idpeserta']);
			$worksheet1->write_string($baris, 6, $metCOB_['nama']);
			$worksheet1->write_string($baris, 7, $metCOB_['tgllahir']);
			$worksheet1->write_string($baris, 8, $metCOB_['usia'],'C');
			$worksheet1->write_string($baris, 9, $metCOB_['plafond'],'R');
			$worksheet1->write_string($baris, 10, $metCOB_['nilaiklaimdiajukan'],'R');
			$worksheet1->write_string($baris, 11, $metCOB_['tglakad'],'C');
			$worksheet1->write_string($baris, 12, $metCOB_['tenor'],'C');
			$worksheet1->write_string($baris, 13, $metCOB_['tglklaim'],'C');
			$worksheet1->write_string($baris, 14, $metCOB_['akadvsdol'],'C');
			$worksheet1->write_string($baris, 15, $metCOB_['tglterimalaporan'],'C');			
			$worksheet1->write_string($baris, 16, $metCOB_['dur'],'C');
			$worksheet1->write_string($baris, 17, $metCOB_['tglinfoasuransi'],'C');
			$worksheet1->write_string($baris, 18, $metCOB_['keterangan']);
			$worksheet1->write_string($baris, 19, $metCOB_['tgllengkapdokumen'],'C');
			$worksheet1->write_string($baris, 20, $metCOB_['tempatmeninggal']);
			$worksheet1->write_string($baris, 21, $metCOB_['penyebabmeninggal']);
			$worksheet1->write_string($baris, 22, $metCOB_['statusklaim']);
			$worksheet1->write_string($baris, 23, $metCOB_['nilaiclaimasuransi'],'R');
			$worksheet1->write_string($baris, 24, $metCOB_['tglbayarasuransi'],'C');
			$worksheet1->write_string($baris, 25, $metCOB_['nilaiclaimdibayar'],'R');
			$worksheet1->write_string($baris, 26, $metCOB_['tglbayar'],'C');
			$worksheet1->write_string($baris, 27, $metCOB_['kol'],'C');
			$worksheet1->write_string($baris, 28, $metCOB_['date_exp'],'C');
			
			$baris++;
		}
		$workbook->close();
	break;

	case "armpayment":
		$filename = "PAYMENT";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel(_convertDate(_convertDateEng2($thisEncrypter->decode($_REQUEST['dtfrom']))).'_'._convertDate(_convertDateEng2($thisEncrypter->decode($_REQUEST['dtto']))).'_'.$filename.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();

		if ($thisEncrypter->decode($_REQUEST['idb'])) {	$satu ='AND ajkcobroker.id = "'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc'])) {	$dua ='AND ajkclient.id = "'.$thisEncrypter->decode($_REQUEST['idc']).'"';	}
		$met_idproduk = explode("_", $thisEncrypter->decode($_REQUEST['idp']));
		if ($thisEncrypter->decode($_REQUEST['idp'])) {	$tiga ='AND ajkpolis.id = "'.$met_idproduk[0].'"';	}

		$met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual, ajkpolis.byrate
									  FROM ajkcobroker
									  INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
									  INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
									  WHERE ajkcobroker.del IS NULL '.$satu.' '.$dua.'  '.$tiga.''));

		if ($_REQUEST['idb']==""){	$_metbroker = '';	}else{	$_metbroker = $met_['brokername'];	}
		if ($_REQUEST['idc']==""){	$_metclient = 'ALL CLIENT';	}else{	$_metclient = $met_['clientname'];	}
		if ($_REQUEST['idp']==""){	$_metproduk = 'ALL PRODUCT';	}else{	$_metproduk = $met_['produk'];	}

		$worksheet1->write_string(0, 0, "REPORT PAYMENTS", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 6);
		$worksheet1->write_string(1, 0, strtoupper($_metbroker), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 6);
		$worksheet1->write_string(2, 0, strtoupper($_metclient), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 6);
		$worksheet1->write_string(3, 0, strtoupper($_metproduk), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 6);

		$worksheet1->set_row(5, 15);
		$worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "NO", $format);
		$worksheet1->set_column(5, 1, 40);	$worksheet1->write_string(5, 1, "Debitnote", $format);
		$worksheet1->set_column(5, 2, 10);	$worksheet1->write_string(5, 2, "Date DN", $format);
		$worksheet1->set_column(5, 3, 15);	$worksheet1->write_string(5, 3, "Premium", $format);
		$worksheet1->set_column(5, 4, 15);	$worksheet1->write_string(5, 4, "Status", $format);
		$worksheet1->set_column(5, 5, 10);	$worksheet1->write_string(5, 5, "Date Payment", $format);
		$worksheet1->set_column(5, 6, 10);	$worksheet1->write_string(5, 6, "Branch", $format);

		$baris = 6;
		if ($thisEncrypter->decode($_REQUEST['idb']))	{	$satu = 'AND ajkdebitnote.idbroker="'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc']))	{	$dua = 'AND ajkdebitnote.idclient="'.$thisEncrypter->decode($_REQUEST['idc']).'"';		}
		/*
		   if ($_REQUEST['idp'])	{	$tiga = 'AND ajkdebitnote.idproduk="'.$thisEncrypter->decode($_REQUEST['idp']).'"';	}
		   if ($_REQUEST['st'])	{	$empat = 'AND ajkdebitnote.paidstatus="'.$thisEncrypter->decode($_REQUEST['st']).'"';	}
		*/

		if ($thisEncrypter->decode($_REQUEST['st'])=="1") 		{	$_datapaid="Paid";
		}elseif ($thisEncrypter->decode($_REQUEST['st'])=="2")	{	$_datapaid="Paid*";
		}else{	$_datapaid="Unpaid";	}
		if ($thisEncrypter->decode($_REQUEST['idp']))	{	$tiga = 'AND ajkdebitnote.idproduk="'.$met_idproduk[0].'"';	}
		if ($thisEncrypter->decode($_REQUEST['st']))	{	$empat = 'AND ajkdebitnote.paidstatus="'.$_datapaid.'"';	}

		$metCOB = mysql_query('SELECT
		ajkdebitnote.id,
		ajkdebitnote.idbroker,
		ajkdebitnote.idclient,
		ajkdebitnote.idproduk,
		ajkdebitnote.idas,
		ajkdebitnote.idaspolis,
		ajkcabang.`name` AS cabang,
		ajkdebitnote.tgldebitnote,
		ajkdebitnote.nomordebitnote,
		ajkdebitnote.premiclient,
		ajkdebitnote.paidstatus,
		ajkdebitnote.paidtanggal
		FROM ajkdebitnote
		INNER JOIN ajkcabang ON ajkdebitnote.idcabang = ajkcabang.er
		WHERE ajkdebitnote.del IS NULL '.$satu.' '.$dua.' '.$tiga.' '.$empat.' AND ajkdebitnote.tgldebitnote BETWEEN "'._convertDateEng2($thisEncrypter->decode($_REQUEST['dtfrom'])).'" AND "'._convertDateEng2($thisEncrypter->decode($_REQUEST['dtto'])).'"');
		while ($metCOB_ = mysql_fetch_array($metCOB)) {
			if ($metCOB_['paidtanggal']=="" OR $metCOB_['paidtanggal']=="0000-00-00") {
				$tgllunas = '';
			}else{
				$tgllunas = _convertDate($metCOB_['paidtanggal']);
			}

			$worksheet1->write_string($baris, 0, ++$no, 'C');
			$worksheet1->write_string($baris, 1, $metCOB_['nomordebitnote']);
			$worksheet1->write_string($baris, 2, $metCOB_['tgldebitnote']);
			$worksheet1->write_number($baris, 3, $metCOB_['premiclient']);
			$worksheet1->write_string($baris, 4, $metCOB_['paidstatus']);
			$worksheet1->write_string($baris, 5, $tgllunas);
			$worksheet1->write_string($baris, 6, $metCOB_['cabang']);

			$baris++;
			$tPremi += $metCOB_['premiclient'];
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 2);
		$worksheet1->write_number($baris, 3, $tPremi, $ftotal);
		$workbook->close();
		;
	break;

	case "rptdebitnote":
		$filename = "DEBITNOTE BANK";
		$filename1 = "CREDITNOTE INS";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel(_convertDate(_convertDateEng2($thisEncrypter->decode($_REQUEST['dtfrom']))).'_'._convertDate(_convertDateEng2($thisEncrypter->decode($_REQUEST['dtto']))).'_'.$filename.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);
		$worksheet2 =& $workbook->add_worksheet($filename1);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();

		if ($thisEncrypter->decode($_REQUEST['idb'])) {	$satu ='AND ajkcobroker.id = "'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc'])) {	$dua ='AND ajkclient.id = "'.$thisEncrypter->decode($_REQUEST['idc']).'"';	}
		$met_idproduk = explode("_", $thisEncrypter->decode($_REQUEST['idp']));
		if ($thisEncrypter->decode($_REQUEST['idp'])) {	$tiga ='AND ajkpolis.id = "'.$met_idproduk[0].'"';	}

		$met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual, ajkpolis.byrate
									  FROM ajkcobroker
									  INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
									  INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
									  WHERE ajkcobroker.del IS NULL '.$satu.' '.$dua.'  '.$tiga.''));

		if ($_REQUEST['idb']==""){	$_metbroker = '';	}else{	$_metbroker = $met_['brokername'];	}
		if ($thisEncrypter->decode($_REQUEST['idc'])==""){	$_metclient = 'ALL CLIENT';	}else{	$_metclient = $met_['clientname'];	}
		if ($thisEncrypter->decode($_REQUEST['idp'])==""){	$_metproduk = 'ALL PRODUCT';	}else{	$_metproduk = $met_['produk'];	}

		$worksheet1->write_string(0, 0, "REPORT PAYMENTS BANK", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 9);
		$worksheet1->write_string(1, 0, strtoupper($_metbroker), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 9);
		$worksheet1->write_string(2, 0, strtoupper($_metclient), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 9);
		$worksheet1->write_string(3, 0, strtoupper($_metproduk), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 9);

		$worksheet2->write_string(0, 0, "REPORT PAYMENTS INSURANCE", $fjudul);	$worksheet2->merge_cells(0, 0, 0, 11);
		$worksheet2->write_string(1, 0, strtoupper($_metbroker), $fjudul);		$worksheet2->merge_cells(1, 0, 1, 11);
		$worksheet2->write_string(2, 0, strtoupper($_metclient), $fjudul);		$worksheet2->merge_cells(2, 0, 2, 11);
		$worksheet2->write_string(3, 0, strtoupper($_metproduk), $fjudul);		$worksheet2->merge_cells(3, 0, 3, 11);

		$worksheet1->set_row(5, 15);
		$worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "NO", $format);
		$worksheet1->set_column(5, 1, 40);	$worksheet1->write_string(5, 1, "Perusahaan", $format);
		$worksheet1->set_column(5, 2, 40);	$worksheet1->write_string(5, 2, "produk", $format);
		$worksheet1->set_column(5, 3, 40);	$worksheet1->write_string(5, 3, "Debitnote", $format);
		$worksheet1->set_column(5, 4, 10);	$worksheet1->write_string(5, 4, "Date DN", $format);
		$worksheet1->set_column(5, 5, 15);	$worksheet1->write_string(5, 5, "Member", $format);
		$worksheet1->set_column(5, 6, 15);	$worksheet1->write_string(5, 6, "Premium", $format);
		$worksheet1->set_column(5, 7, 15);	$worksheet1->write_string(5, 7, "Status", $format);
		$worksheet1->set_column(5, 8, 10);	$worksheet1->write_string(5, 8, "Date Payment", $format);
		$worksheet1->set_column(5, 9, 10);	$worksheet1->write_string(5, 9, "Branch", $format);

		$worksheet2->set_row(5, 15);
		$worksheet2->set_column(5, 0, 1);	$worksheet2->write_string(5, 0, "NO", $format);
		$worksheet2->set_column(5, 1, 40);	$worksheet2->write_string(5, 1, "Perusahaan", $format);
		$worksheet2->set_column(5, 2, 40);	$worksheet2->write_string(5, 2, "produk", $format);
		$worksheet2->set_column(5, 3, 40);	$worksheet2->write_string(5, 3, "Debitnote", $format);
		$worksheet2->set_column(5, 4, 10);	$worksheet2->write_string(5, 4, "Date DN", $format);
		$worksheet2->set_column(5, 5, 15);	$worksheet2->write_string(5, 5, "Member", $format);
		$worksheet2->set_column(5, 6, 15);	$worksheet2->write_string(5, 6, "Premium", $format);
		$worksheet2->set_column(5, 7, 15);	$worksheet2->write_string(5, 7, "Status", $format);
		$worksheet2->set_column(5, 8, 10);	$worksheet2->write_string(5, 8, "Date Payment", $format);
		$worksheet2->set_column(5, 9, 10);	$worksheet2->write_string(5, 9, "Branch", $format);
		$worksheet2->set_column(5, 10, 10);	$worksheet2->write_string(5, 10, "Insurance", $format);
		$worksheet2->set_column(5, 11, 10);	$worksheet2->write_string(5, 11, "Policy Insurance", $format);

		$baris = 6;
		if ($thisEncrypter->decode($_REQUEST['idb']))	{	$satu = 'AND ajkdebitnote.idbroker="'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc']))	{	$dua = 'AND ajkdebitnote.idclient="'.$thisEncrypter->decode($_REQUEST['idc']).'"';		}
		if ($thisEncrypter->decode($_REQUEST['idp']))	{	$tiga = 'AND ajkdebitnote.idproduk="'.$met_idproduk[0].'"';	}
		if ($thisEncrypter->decode($_REQUEST['st'])=="1") 		{	$_datapaid="Paid";
		}elseif ($thisEncrypter->decode($_REQUEST['st'])=="2")	{	$_datapaid="Paid*";
		}else{	$_datapaid="Unpaid";	}
		if ($thisEncrypter->decode($_REQUEST['st']))	{	$empat = 'AND ajkdebitnote.paidstatus="'.$_datapaid.'"';	}

		$metCOB = mysql_query('SELECT
		ajkdebitnote.id,
		ajkdebitnote.idbroker,
		ajkdebitnote.idclient,
		ajkdebitnote.idproduk,
		ajkdebitnote.idas,
		ajkdebitnote.idaspolis,
		ajkcabang.name AS cabang,
		ajkdebitnote.tgldebitnote,
		ajkdebitnote.nomordebitnote,
		ajkdebitnote.premiclient,
		ajkdebitnote.paidstatus,
		ajkdebitnote.paidtanggal,
		ajkdebitnote.premiasuransi,
		ajkdebitnote.as_paidstatus,
		ajkdebitnote.as_paidtgl,
		Count(ajkpeserta.nama) AS jmember,
		ajkinsurance.name AS asuransi,
		ajkclient.name AS perusahaan,
		ajkpolis.produk AS produk,
		ajkpolisasuransi.policymanual AS asuransipolis
		FROM ajkdebitnote
		INNER JOIN ajkcabang ON ajkdebitnote.idcabang = ajkcabang.er
		INNER JOIN ajkclient ON ajkdebitnote.idclient = ajkclient.id
		INNER JOIN ajkpolis ON ajkdebitnote.idproduk = ajkpolis.id
		INNER JOIN ajkpeserta ON ajkdebitnote.id = ajkpeserta.iddn
		INNER JOIN ajkinsurance ON ajkdebitnote.idas = ajkinsurance.id
		LEFT JOIN ajkpolisasuransi ON ajkdebitnote.idaspolis = ajkpolisasuransi.id
		WHERE ajkdebitnote.del IS NULL '.$satu.' '.$dua.' '.$tiga.' '.$empat.' AND ajkdebitnote.tgldebitnote BETWEEN "'._convertDateEng2($thisEncrypter->decode($_REQUEST['dtfrom'])).'" AND "'._convertDateEng2($thisEncrypter->decode($_REQUEST['dtto'])).'"
		GROUP BY ajkdebitnote.id');
		while ($metCOB_ = mysql_fetch_array($metCOB)) {
			if ($metCOB_['paidtanggal']=="" OR $metCOB_['paidtanggal']=="0000-00-00") {	$tgllunas = '';	}else{	$tgllunas = _convertDate($metCOB_['paidtanggal']);	}
			if ($metCOB_['as_paidtgl']=="" OR $metCOB_['as_paidtgl']=="0000-00-00") {	$tgllunasAS = '';	}else{	$tgllunasAS = _convertDate($metCOB_['as_paidtgl']);	}

			$worksheet1->write_string($baris, 0, ++$no, 'C');
			$worksheet1->write_string($baris, 1, $metCOB_['perusahaan']);
			$worksheet1->write_string($baris, 2, $metCOB_['produk']);
			$worksheet1->write_string($baris, 3, $metCOB_['nomordebitnote']);
			$worksheet1->write_string($baris, 4, $metCOB_['tgldebitnote']);
			$worksheet1->write_number($baris, 5, $metCOB_['jmember']);
			$worksheet1->write_number($baris, 6, $metCOB_['premiclient']);
			$worksheet1->write_string($baris, 7, $metCOB_['paidstatus']);
			$worksheet1->write_string($baris, 8, $tgllunas);
			$worksheet1->write_string($baris, 9, $metCOB_['cabang']);

			$worksheet2->write_string($baris, 0, ++$no1, 'C');
			$worksheet2->write_string($baris, 1, $metCOB_['perusahaan']);
			$worksheet2->write_string($baris, 2, $metCOB_['produk']);
			$worksheet2->write_string($baris, 3, $metCOB_['nomordebitnote']);
			$worksheet2->write_string($baris, 4, $metCOB_['tgldebitnote']);
			$worksheet2->write_number($baris, 5, $metCOB_['jmember']);
			$worksheet2->write_number($baris, 6, $metCOB_['premiasuransi']);
			$worksheet2->write_string($baris, 7, $metCOB_['as_paidstatus']);
			$worksheet2->write_string($baris, 8, $tgllunasAS);
			$worksheet2->write_string($baris, 9, $metCOB_['cabang']);
			$worksheet2->write_string($baris, 10, $metCOB_['asuransi']);
			$worksheet2->write_string($baris, 11, $metCOB_['asuransipolis']);

			$baris++;
			$tPremi += $metCOB_['premiclient'];
			$tPremiAs += $metCOB_['premiasuransi'];
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 5);	$worksheet1->write_number($baris, 6, $tPremi, $ftotal);
		$worksheet2->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet2->merge_cells($baris, 0, $baris, 5);	$worksheet2->write_number($baris, 6, $tPremiAs, $ftotal);

		$workbook->close();
		;
	break;

	case "rptrestitusi":
		$filename = "REKAPITULASI RESTITUSI";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.'_'.$today.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();


		$worksheet1->write_string(0, 0, "REKAPITULASI RESTITUSI", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 13);
		$baris = 2;
		$worksheet1->set_row($baris, 15);
		$worksheet1->set_column($baris, 0, 1);	$worksheet1->write_string($baris, 0, "NO", $format);
		$worksheet1->set_column($baris, 1, 40);	$worksheet1->write_string($baris, 1, "NAMA DEBITUR", $format);
		$worksheet1->set_column($baris, 2, 15);	$worksheet1->write_string($baris, 2, "TGL LAHIR", $format);
		$worksheet1->set_column($baris, 3, 15);	$worksheet1->write_string($baris, 3, "TGL MULAI AKAD", $format);
		$worksheet1->set_column($baris, 4, 15);	$worksheet1->write_string($baris, 4, "TGL JATUH TEMPO", $format);
		$worksheet1->set_column($baris, 5, 15);	$worksheet1->write_string($baris, 5, "TENOR (BULAN)", $format);
		$worksheet1->set_column($baris, 6, 30);	$worksheet1->write_string($baris, 6, "PLAFOND AWAL", $format);
		$worksheet1->set_column($baris, 7, 30);	$worksheet1->write_string($baris, 7, "CABANG / CAPEM", $format);
		$worksheet1->set_column($baris, 8, 10);	$worksheet1->write_string($baris, 8, "NO POLIS LAMA", $format);
		$worksheet1->set_column($baris, 9, 20);	$worksheet1->write_string($baris, 9, "PREMI AWAL", $format);
		$worksheet1->set_column($baris, 10, 15);	$worksheet1->write_string($baris, 10, "TGL PELUNASAN / TGL REALISASI BARU", $format);
		$worksheet1->set_column($baris, 11, 20);	$worksheet1->write_string($baris, 11, "NILAI RESTITUSI", $format);
		$worksheet1->set_column($baris, 12, 20);	$worksheet1->write_string($baris, 12, "ASURANSI", $format);
		$worksheet1->set_column($baris, 13, 10);	$worksheet1->write_string($baris, 13, "SISA MASA ASURANSI (BULAN)", $format);

		$baris = $baris+1;
		
		$metCOB = mysql_query($thisEncrypter->decode($_SESSION['lprrestitusi']));

		while ($metCOB_ = mysql_fetch_array($metCOB)) {
			$worksheet1->write_string($baris, 0, ++$no, 'C');
			$worksheet1->write_string($baris, 1, $metCOB_['nama']);
			$worksheet1->write_string($baris, 2, _convertDate($metCOB_['tgllahir']));
			$worksheet1->write_string($baris, 3, _convertDate($metCOB_['tglakad']));
			$worksheet1->write_string($baris, 4, _convertDate($metCOB_['tglakhir']));
			$worksheet1->write_number($baris, 5, $metCOB_['tenor']);
			$worksheet1->write_number($baris, 6, $metCOB_['plafond']);
			$worksheet1->write_string($baris, 7, $metCOB_['nmcabang']);
			$worksheet1->write_string($baris, 8, "-");
			$worksheet1->write_number($baris, 9, $metCOB_['premi']);
			$worksheet1->write_string($baris, 10, _convertDate($metCOB_['tglklaim']));
			$worksheet1->write_number($baris, 11, $metCOB_['nilaiclaimclient']);
			$worksheet1->write_string($baris, 12, $metCOB_['nm_asuransi']);
			$worksheet1->write_string($baris, 13, $metCOB_['sisa']);

			$baris++;
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 5);	$worksheet1->write_number($baris, 6, $tPremi, $ftotal);

		$workbook->close();
	break;

	case "rategeneral":
		$filename1 = "RATE_COMPREHENSIVE";
		$filename2 = "RATE_TOTALLOSONLY";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		$fRateGen = mysql_fetch_array(mysql_query('SELECT ajkclient.name, ajkpolis.produk
										   FROM ajkclient
										   INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
										   WHERE ajkclient.id = "'.$thisEncrypter->decode($_REQUEST['idc']).'" AND ajkpolis.id = "'.$thisEncrypter->decode($_REQUEST['idp']).'" '));
		HeaderingExcel('FORMAT_RATE_'.str_replace(" ","_" ,$fRateGen['name']).'_'.str_replace(" ","_" ,$fRateGen['produk']).'.xls');
		$workbook = new Workbook("");

		$worksheet1 =& $workbook->add_worksheet($filename1);
		$worksheet1->set_column(0, 0, 10);	$worksheet1->write_string(0, 0, "No", $format);
		$worksheet1->set_column(0, 1, 10);	$worksheet1->write_string(0, 1, "TenorStart", $format);
		$worksheet1->set_column(0, 2, 10);	$worksheet1->write_string(0, 2, "TenorEnd", $format);
		$worksheet1->set_column(0, 3, 10);	$worksheet1->write_string(0, 3, "PlafondStart", $format);
		$worksheet1->set_column(0, 4, 10);	$worksheet1->write_string(0, 4, "PlafondEnd", $format);
		$worksheet1->set_column(0, 5, 10);	$worksheet1->write_string(0, 5, "KodeLokasi", $format);
		$worksheet1->set_column(0, 6, 10);	$worksheet1->write_string(0, 6, "KodePertanggungan", $format);
		$worksheet1->set_column(0, 7, 10);	$worksheet1->write_string(0, 7, "KodeKelas", $format);
		$worksheet1->set_column(0, 8, 10);	$worksheet1->write_string(0, 8, "Rate", $format);


		$worksheet2 =& $workbook->add_worksheet($filename2);
		$worksheet2->set_column(0, 0, 10);	$worksheet2->write_string(0, 0, "No", $format);
		$worksheet2->set_column(0, 1, 10);	$worksheet2->write_string(0, 1, "TenorStart", $format);
		$worksheet2->set_column(0, 2, 10);	$worksheet2->write_string(0, 2, "TenorEnd", $format);
		$worksheet2->set_column(0, 3, 10);	$worksheet2->write_string(0, 3, "PlafondStart", $format);
		$worksheet2->set_column(0, 4, 10);	$worksheet2->write_string(0, 4, "PlafondEnd", $format);
		$worksheet2->set_column(0, 5, 10);	$worksheet2->write_string(0, 5, "KodeLokasi", $format);
		$worksheet2->set_column(0, 6, 10);	$worksheet2->write_string(0, 6, "KodePertanggungan", $format);
		$worksheet2->set_column(0, 7, 10);	$worksheet2->write_string(0, 7, "KodeKelas", $format);
		$worksheet2->set_column(0, 8, 10);	$worksheet2->write_string(0, 8, "Rate", $format);

		$workbook->close();
		;
	break;

	case "lprdataspk":
		$filename = "DATA_SPK";
		function HeaderingExcel($filename) {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$filename" );
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		}
		HeaderingExcel(_convertDate(_convertDateEng2($thisEncrypter->decode($_REQUEST['dtfrom']))).'_'._convertDate(_convertDateEng2($thisEncrypter->decode($_REQUEST['dtto']))).'_'.$filename.'.xls');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);
		$format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
		$fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
		$ftotal =& $workbook->add_format();		$ftotal->set_bold();
		if ($thisEncrypter->decode($_REQUEST['idb'])!="") {	$satu ='AND ajkcobroker.id = "'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc'])!="") {	$dua ='AND ajkclient.id = "'.$thisEncrypter->decode($_REQUEST['idc']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idp'])!="") {
			//$tiga ='AND ajkpolis.id = "'.$thisEncrypter->decode($_REQUEST['idp']).'"';
			$metProduk = explode("_", $thisEncrypter->decode($_REQUEST['idp']));
			$tiga ='AND ajkpolis.id = "'.$metProduk[0].'"';
		}

		$metDataSPK = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual
											  FROM ajkcobroker
											  INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
											  INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
											  WHERE ajkcobroker.del IS NULL '.$satu.' '.$dua.'  '.$tiga.''));

		if ($_REQUEST['idb']==""){	$_metbroker = '';	}else{	$_metbroker = $metDataSPK['brokername'];	}
		if ($thisEncrypter->decode($_REQUEST['idc'])) {	$_metclient = $metDataSPK['clientname'];	}else{	$_metclient = 'ALL CLIENT';	}
		if ($thisEncrypter->decode($_REQUEST['idp'])) {	$_metproduk = $metDataSPK['produk'];		}else{	$_metproduk = 'ALL PRODUCT';	}

		$worksheet1->write_string(0, 0, "LAPORAN DATA DEBITUR SPK", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 16);
		$worksheet1->write_string(1, 0, strtoupper($_metbroker), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 16);
		$worksheet1->write_string(2, 0, strtoupper($_metclient), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 16);
		$worksheet1->write_string(3, 0, strtoupper($_metproduk), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 16);

		$worksheet1->set_row(5, 15);
		$worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "No", $format);
		$worksheet1->set_column(5, 1, 40);	$worksheet1->write_string(5, 1, "Perusahaan", $format);
		$worksheet1->set_column(5, 2, 40);	$worksheet1->write_string(5, 2, "Produk", $format);
		$worksheet1->set_column(5, 3, 20);	$worksheet1->write_string(5, 3, "SPK", $format);
		$worksheet1->set_column(5, 4, 15);	$worksheet1->write_string(5, 4, "Status", $format);
		$worksheet1->set_column(5, 5, 25);	$worksheet1->write_string(5, 5, "Nama", $format);
		$worksheet1->set_column(5, 6, 15);	$worksheet1->write_string(5, 6, "Tgl Lahir", $format);
		$worksheet1->set_column(5, 7, 5);	$worksheet1->write_string(5, 7, "Usia", $format);
		$worksheet1->set_column(5, 8, 10);	$worksheet1->write_string(5, 8, "Mulai Asuransi", $format);
		$worksheet1->set_column(5, 9, 5);	$worksheet1->write_string(5, 9, "Tenor", $format);
		$worksheet1->set_column(5, 10, 10);	$worksheet1->write_string(5, 10, "Akhir Asuransi", $format);
		$worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 11, "Plafond", $format);
		$worksheet1->set_column(5, 12, 10);	$worksheet1->write_string(5, 12, "Premi", $format);
		$worksheet1->set_column(5, 13, 5);	$worksheet1->write_string(5, 13, "EM", $format);
		$worksheet1->set_column(5, 14, 10);	$worksheet1->write_string(5, 14, "Nett Premi", $format);
		$worksheet1->set_column(5, 15, 20);	$worksheet1->write_string(5, 15, "Cabang", $format);
		$worksheet1->set_column(5, 16, 10);	$worksheet1->write_string(5, 16, "Tgl Input", $format);

		$baris = 6;
		if ($thisEncrypter->decode($_REQUEST['idb']))	{	$spksatu = 'AND ajkspk.idbroker="'.$thisEncrypter->decode($_REQUEST['idb']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idc']))	{	$spkdua = 'AND ajkspk.idpartner="'.$thisEncrypter->decode($_REQUEST['idc']).'"';	}
		if ($thisEncrypter->decode($_REQUEST['idp']))	{	$metEx = explode("_",$thisEncrypter->decode($_REQUEST['idp']));
															$spktiga = 'AND ajkspk.idproduk="'.$metEx[0].'"';
															}
		if ($thisEncrypter->decode($_REQUEST['st']))	{	$spkempat = 'AND ajkspk.statusspk="'.$thisEncrypter->decode($_REQUEST['st']).'"';	}

		$metSPK = mysql_query('SELECT
		ajkspk.id,
		ajkspk.idbroker,
		ajkspk.idpartner,
		ajkspk.idproduk,
		ajkcobroker.`name` AS namabroker,
		ajkclient.`name` AS namaperusahaan,
		ajkpolis.produk AS namaproduk,
		ajkratepremi.rate,
		ajkspk.nomorspk,
		ajkspk.statusspk,
		ajkspk.nama,
		ajkspk.dob,
		ajkspk.usia,
		ajkspk.tglakad,
		ajkspk.tenor,
		ajkspk.tglakhir,
		ajkspk.mppbln,
		ajkspk.plafond,
		ajkspk.premi,
		ajkspk.em,
		ajkspk.premiem,
		ajkspk.nettpremi,
		ajkspk.cabang,
		ajkcabang.`name` AS namacabang,
		DATE_FORMAT(ajkspk.input_date,"%Y-%m-%d") AS tglinput
		FROM ajkspk
		INNER JOIN ajkcobroker ON ajkspk.idbroker = ajkcobroker.id
		INNER JOIN ajkclient ON ajkspk.idpartner = ajkclient.id
		INNER JOIN ajkpolis ON ajkspk.idproduk = ajkpolis.id
		LEFT JOIN ajkratepremi ON ajkspk.idrate = ajkratepremi.id
		INNER JOIN ajkcabang ON ajkspk.cabang = ajkcabang.er
		WHERE
		ajkspk.del IS NULL '.$spksatu.' '.$spkdua.' '.$spktiga.' '.$spkempat.' AND DATE_FORMAT(ajkspk.input_date,"%Y-%m-%d") BETWEEN "'._convertDateEng2($thisEncrypter->decode($_REQUEST['dtfrom'])).'" AND "'._convertDateEng2($thisEncrypter->decode($_REQUEST['dtto'])).'"
		ORDER BY ajkspk.input_date DESC');
		while ($metSPK_ = mysql_fetch_array($metSPK)) {
			$usiatenor = round($metCOB_['tenor'] / 12) + $metCOB_['usia'];

			$worksheet1->write_number($baris, 0, ++$no, 'C');
			$worksheet1->write_string($baris, 1, $metSPK_['namaperusahaan']);
			$worksheet1->write_string($baris, 2, $metSPK_['namaproduk']);
			$worksheet1->write_string($baris, 3, $metSPK_['nomorspk']);
			$worksheet1->write_string($baris, 4, $metSPK_['statusspk']);
			$worksheet1->write_string($baris, 5, $metSPK_['nama']);
			$worksheet1->write_string($baris, 6, _convertDate($metSPK_['dob']));
			$worksheet1->write_number($baris, 7, $metSPK_['usia']);
			$worksheet1->write_string($baris, 8, _convertDate($metSPK_['tglakad']));
			$worksheet1->write_number($baris, 9, $metSPK_['tenor']);
			$worksheet1->write_string($baris, 10, _convertDate($metSPK_['tglakhir']));
			$worksheet1->write_number($baris, 11, $metSPK_['plafond']);
			$worksheet1->write_number($baris, 12, $metSPK_['premi']);
			$worksheet1->write_number($baris, 13, $metSPK_['em']);
			$worksheet1->write_number($baris, 14, $metSPK_['nettpremi']);
			$worksheet1->write_string($baris, 15, $metSPK_['namacabang']);
			$worksheet1->write_string($baris, 16, _convertDate($metSPK_['tglinput']));

			$baris++;
			$tPremi += $metSPK_['premi'];
			$tEM += $metSPK_['em'];
			$tNettPremi += $metSPK_['nettpremi'];
		}
		$worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 11);
		$worksheet1->write_number($baris, 12, $tPremi, $ftotal);
		$worksheet1->write_number($baris, 13, $tEM, $ftotal);
		$worksheet1->write_number($baris, 14, $tNettPremi, $ftotal);

		$workbook->close();		
	break;

	case "putjatim03":
		$filename = "adonai";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.date("Ymd").'-03.csv');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$query = "SELECT nopinjaman,idpeserta 
							FROM ajkpeserta 
							WHERE DATE_FORMAT(input_time,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')";
		$put03 = mysql_query($query);

		while ($put03_ = mysql_fetch_array($put03)) {

			$worksheet1->write_string($baris, 0, $put03_['nopinjaman'].'|'.$put03_['idpeserta']);
			//$worksheet1->write_string($baris, 1, );
			$baris++;
		}
	
		$workbook->close();		
	break;

	case "putjatim04":
		$filename = "adonai";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.date("Ymd").'-04.csv');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$query = "SELECT nopinjaman,
											nilaiclaimclient 
							FROM ajkcreditnote 
									 INNER JOIN ajkpeserta
									 ON ajkpeserta.id = ajkcreditnote.idpeserta
							WHERE tipeklaim = 'Restitusi' /*and 
										DATE_FORMAT(ajkcreditnote.input_time,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')*/";
		$put04 = mysql_query($query);

		while ($put04_ = mysql_fetch_array($put04)) {

			$worksheet1->write_string($baris, 0, $put04_['nopinjaman'].'|'.$put04_['nilaiclaimclient']);
			//$worksheet1->write_string($baris, 1, );
			$baris++;
		}

		$workbook->close();
	break;

	case "putjatim05":
		$filename = "adonai";
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.date("Ymd").'-05.csv');
		$workbook = new Workbook("");
		$worksheet1 =& $workbook->add_worksheet($filename);

		$query = "SELECT nopinjaman,
											status 
							FROM ajkcreditnote 
									 INNER JOIN ajkpeserta
									 ON ajkpeserta.id = ajkcreditnote.idpeserta
							WHERE tipeklaim = 'Claim' /*and 
										DATE_FORMAT(ajkcreditnote.input_time,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')*/";
		$put04 = mysql_query($query);

		while ($put04_ = mysql_fetch_array($put04)) {

			$worksheet1->write_string($baris, 0, $put04_['nopinjaman'].'|'.$put04_['status']);
			//$worksheet1->write_string($baris, 1, );
			$baris++;
		}

		$workbook->close();
	break;
	 
	case "drefundxls":
	  $fileName = 'ExportExcel_'.date('Y-m-d').'.xls';
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		
		echo '
				<table border="1">
				<thead>
				<tr><th width="1%">No</th>
					<th width="1%">Broker</th>
					<th>Partner</th>
					<th>Product</th>
					<th width="1%">Credit Note</th>
					<th width="1%">ID Member</th>
					<th width="1%">Name</th>
					<th width="1%">Plafond</th>
					<th width="1%">Start Insurance</th>
					<th width="1%">Tenor</th>
					<th width="1%">Last Insurance</th>
					<th width="1%">Date Claim</th>
					<th width="10%">Payment Claim</th>
					<th width="10%">Status</th>
					<th width="10%">Branch</th>
				</tr>
				</thead>
				<tbody>';
                $query_sql = 'SELECT
		ajkcreditnote.id,
		ajkcobroker.`name` AS namebroker,
		ajkclient.`name` AS nameclient,
		ajkpolis.produk,
		ajkpeserta.idpeserta,
		ajkpeserta.nomorktp,
		ajkpeserta.nama,
		ajkpeserta.plafond,
		ajkpeserta.tglakad,
		ajkpeserta.tenor,
		ajkpeserta.tglakhir,
		ajkpeserta.totalpremi,
		ajkcabang.`name` AS cabang,
		ajkcreditnote.tempatmeninggal,
		ajkcreditnote.penyebabmeninggal,
		ajkcreditnote.nomorcreditnote,
		ajkcreditnote.`status`,
		ajkcreditnote.tglbayar,
		ajkcreditnote.tglklaim,
		ajkcreditnote.status,
		ajkcreditnote.nilaiclaimclient
		FROM ajkcreditnote
		INNER JOIN ajkcobroker ON ajkcreditnote.idbroker = ajkcobroker.id
		INNER JOIN ajkclient ON ajkcreditnote.idclient = ajkclient.id
		INNER JOIN ajkpolis ON ajkcreditnote.idproduk = ajkpolis.id
		INNER JOIN ajkpeserta ON ajkcreditnote.idpeserta = ajkpeserta.id
		INNER JOIN ajkcabang ON ajkcreditnote.idcabang = ajkcabang.er
		WHERE ajkcreditnote.status != "Request" AND ajkcreditnote.tipeklaim IN ("Refund","Topup") AND ajkcreditnote.del IS NULL '.$q___1.'
		ORDER BY ajkcreditnote.id DESC';
		$metCreditnote = mysql_query($query_sql);
                while ($metCreditnote_ = mysql_fetch_array($metCreditnote)) {
                    if ($metCreditnote_['status']=="Process") {
                        $metglow = 'info';
                    } elseif ($metCreditnote_['status']=="Batal" or $metCreditnote_['status']=="Cancel") {
                        $metglow = 'danger';
                    } elseif ($metCreditnote_['status']=="Investigation") {
                        $metglow = 'warning';
                    } elseif ($metCreditnote_['status']=="Approve Unpaid") {
                        $metglow = 'primary';
                    } elseif ($metCreditnote_['status']=="Approve Paid") {
                        $metglow = 'success';
                    } else {
                        $metglow = 'warning';
                    }
                    echo '<tr>
				   	<td align="center">'.++$no.'</td>
				   	<td>'.$metCreditnote_['namebroker'].'</td>
				   	<td>'.$metCreditnote_['nameclient'].'</td>
				   	<td align="center">'.$metCreditnote_['produk'].'</td>
				   	<td align="center"><a href="ajk.php?re=dlPdf&pdf=dlPdfcn&cID='.$thisEncrypter->encode($metCreditnote_['nomorcreditnote']).'&idc='.$thisEncrypter->encode($metCreditnote_['id']).'" target="blank">'.$metCreditnote_['nomorcreditnote'].'</a></td>
				   	<td align="center">'.$metCreditnote_['idpeserta'].'</td>
				   	<td align="center">'.$metCreditnote_['nama'].'</td>
				   	<td align="right">'.duit($metCreditnote_['plafond']).'</td>
				   	<td align="center">'._convertDate($metCreditnote_['tglakad']).'</td>
				   	<td align="center">'.$metCreditnote_['tenor'].'</td>
				   	<td align="center">'._convertDate($metCreditnote_['tglakhir']).'</td>
				   	<td align="center">'._convertDate($metCreditnote_['tglklaim']).'</td>
				   	<td align="right">'.duit($metCreditnote_['nilaiclaimclient']).'</td>
				   	<td align="center">'.$metCreditnote_['status'].'</td>
				   	<td>'.$metCreditnote_['cabang'].'</td>
				    </tr>';
                }
                echo '</tbody> </table>';
	break;
	
	case "dclaimxls":
	  $fileName = 'ExportExcel_'.date('Y-m-d').'.xls';
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		
		echo '<table border="1">
		<thead>
		<tr><th width="1%">No</th>
			<th width="1%">Broker</th>
			<th>Partner</th>
			<th>Product</th>
			<th width="1%">Credit Note</th>
			<th width="1%">ID Member</th>
			<th width="1%">Name</th>
			<th width="1%">Plafond</th>
			<th width="1%">Tipe Klaim</th>
			<th width="1%">Start Insurance</th>
			<th width="1%">Tenor</th>
			<th width="1%">Last Insurance</th>
			<th width="1%">Date Claim</th>
			<th width="10%">Payment Claim</th>
			<th width="10%">Status</th>
			<th width="10%">Branch</th>
		</tr>
		</thead>
		<tbody>';
        $query_sql = 'SELECT
		ajkcreditnote.id,
		ajkcobroker.`name` AS namebroker,
		ajkclient.`name` AS nameclient,
		ajkpolis.produk,
		ajkpeserta.idpeserta,
		ajkpeserta.nomorktp,
		ajkpeserta.nama,
		ajkpeserta.plafond,
		ajkpeserta.tglakad,
		ajkpeserta.tenor,
		ajkpeserta.tglakhir,
		ajkpeserta.totalpremi,
		ajkcabang.`name` AS cabang,
		ajkcreditnote.tempatmeninggal,
		ajkcreditnote.penyebabmeninggal,
		ajkcreditnote.nomorcreditnote,
		ajkcreditnote.`status`,
		ajkcreditnote.tglbayar,
		ajkcreditnote.tglklaim,
		ajkcreditnote.status,
		ajkcreditnote.nilaiclaimclient,
		ajkcreditnote.nilaiclaimdibayar,
		ajkcreditnote.tipeklaim
		FROM ajkcreditnote
		INNER JOIN ajkcobroker ON ajkcreditnote.idbroker = ajkcobroker.id
		INNER JOIN ajkclient ON ajkcreditnote.idclient = ajkclient.id
		INNER JOIN ajkpolis ON ajkcreditnote.idproduk = ajkpolis.id
		INNER JOIN ajkpeserta ON ajkcreditnote.idpeserta = ajkpeserta.id
		INNER JOIN ajkcabang ON ajkcreditnote.idcabang = ajkcabang.er
		WHERE ajkcreditnote.status NOT IN ("Request", "Process") AND ajkcreditnote.tipeklaim in ("Death","PHK","PAW","Kredit Macet") AND ajkcreditnote.del IS NULL '.$q___1.'
		ORDER BY ajkcreditnote.id DESC';
		
		$metCreditnote = mysql_query($query_sql); 
        while ($metCreditnote_ = mysql_fetch_array($metCreditnote)) {
            if ($metCreditnote_['status']=="Process") {
                $metglow = 'info';
            } elseif ($metCreditnote_['status']=="Batal" or $metCreditnote_['status']=="Cancel") {
                $metglow = 'danger';
            } elseif ($metCreditnote_['status']=="Investigation") {
                $metglow = 'warning';
            } elseif ($metCreditnote_['status']=="Approve Unpaid") {
                $metglow = 'primary';
            } elseif ($metCreditnote_['status']=="Approve Paid") {
                $metglow = 'success';
            } else {
                $metglow = 'warning';
            }
            echo '<tr>
		   	<td align="center">'.++$no.'</td>
		   	<td>'.$metCreditnote_['namebroker'].'</td>
		   	<td>'.$metCreditnote_['nameclient'].'</td>
		   	<td align="center">'.$metCreditnote_['produk'].'</td>
		   	<td align="center">'.$metCreditnote_['nomorcreditnote'].'</td>
		   	<td align="center">'.$metCreditnote_['idpeserta'].'</td>
		   	<td align="center">'.$metCreditnote_['nama'].'</td>
		   	<td align="right">'.duit($metCreditnote_['plafond']).'</td>
			<td align="right">'.$metCreditnote_['tipeklaim'].'</td>
		   	<td align="center">'._convertDate($metCreditnote_['tglakad']).'</td>
		   	<td align="center">'.$metCreditnote_['tenor'].'</td>
		   	<td align="center">'._convertDate($metCreditnote_['tglakhir']).'</td>
		   	<td align="center">'._convertDate($metCreditnote_['tglklaim']).'</td>
		   	<td align="right">'.duit($metCreditnote_['nilaiclaimdibayar']).'</td>
		   	<td align="center">'.$metCreditnote_['status'].'</td>
		   	<td>'.$metCreditnote_['cabang'].'</td>
		    </tr>';
        }
        echo '</tbody> </table> ';
	break;
	 
	case "rekapjatuhtempo":
		$filename = "JATUH_TEMPO".$DatePolis1;
		$query = $_SESSION['rekapjatuhtempo'];
		
		//echo $query;
		function HeaderingExcel($filename) {
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
		}
		HeaderingExcel($filename.'.xls');
		$workbook = new Workbook("");
		
		$format =& $workbook->add_format();		
		$format->set_align('center');	
		$format->set_color('white');	
		$format->set_bold();	
		$format->set_pattern();	
		$format->set_fg_color('orange');

		$group = "SELECT nmcabang,count(*)as cnt
							FROM($query)as temp GROUP BY nmcabang";
		
		$result = mysql_query($group);
		
		$worksheet =& $workbook->add_worksheet('All');	
		$worksheet->set_row(0, 15);
		$worksheet->set_column(0, 0, 5);	$worksheet->write_string(0, 0, "No", $format);
		$worksheet->set_column(0, 1, 20);	$worksheet->write_string(0, 1, "No Pinjaman", $format);
		$worksheet->set_column(0, 2, 30);	$worksheet->write_string(0, 2, "Nama", $format);
		$worksheet->set_column(0, 3, 20);	$worksheet->write_string(0, 3, "Cabang", $format);
		$worksheet->set_column(0, 4, 10);	$worksheet->write_string(0, 4, "Tgl J.Tempo", $format);
		$worksheet->set_column(0, 5, 5);	$worksheet->write_string(0, 5, "Hari", $format);
		$baris = 1;

		$result2 = mysql_query($query);
		$no = 1;
		while($row2 = mysql_fetch_array($result2)){
			// echo $query2;
			$worksheet->write_string($baris, 0,$no, 'C');
			$worksheet->write_string($baris, 1,$row2['nopinjaman'] , 'C');	
			$worksheet->write_string($baris, 2,$row2['nama'] , 'C');	
			$worksheet->write_string($baris, 3,$row2['nmcabang'] , 'C');	
			$worksheet->write_string($baris, 4,$row2['duedate'] , 'C');	
			$worksheet->write_string($baris, 5,$row2['hari'] , 'C');	
			$baris++;
			$no++;
		}
		while ($row = mysql_fetch_array($result)) {
			
			$worksheet =& $workbook->add_worksheet($row['nmcabang'].' ('.$row['cnt'].')');	
			$worksheet->set_row(0, 15);
			$worksheet->set_column(0, 0, 5);	$worksheet->write_string(0, 0, "No", $format);
			$worksheet->set_column(0, 1, 20);	$worksheet->write_string(0, 1, "No Pinjaman", $format);
			$worksheet->set_column(0, 2, 30);	$worksheet->write_string(0, 2, "Nama", $format);
			$worksheet->set_column(0, 3, 20);	$worksheet->write_string(0, 3, "Cabang", $format);
			$worksheet->set_column(0, 4, 10);	$worksheet->write_string(0, 4, "Tgl J.Tempo", $format);
			$worksheet->set_column(0, 5, 5);	$worksheet->write_string(0, 5, "Hari", $format);
			$baris = 1;

			$query2 = "SELECT *
								 FROM($query)as temp 
								 WHERE nmcabang = '".$row['nmcabang']."'";

			$result2 = mysql_query($query2);
			$no = 1;
			while($row2 = mysql_fetch_array($result2)){
				// echo $query2;
				$worksheet->write_string($baris, 0,$no, 'C');
				$worksheet->write_string($baris, 1,$row2['nopinjaman'] , 'C');	
				$worksheet->write_string($baris, 2,$row2['nama'] , 'C');	
				$worksheet->write_string($baris, 3,$row2['nmcabang'] , 'C');	
				$worksheet->write_string($baris, 4,$row2['duedate'] , 'C');	
				$worksheet->write_string($baris, 5,$row2['hari'] , 'C');	
				$baris++;
				$no++;
			}
			
		}		
		$workbook->close();
	break;

	default:
	;
} // switch

?>