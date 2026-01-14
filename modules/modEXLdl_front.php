<?php
// ----------------------------------------------------------------------------------
// Original Author Of File : Rahmad
// E-mail :kepodank@gmail.com
// @ Copyright 2016
// ----------------------------------------------------------------------------------
error_reporting(0);
require_once('../includes/metPHPXLS/Worksheet.php');
require_once('../includes/metPHPXLS/Workbook.php');
include_once('../includes/fu6106.php');
include_once('../includes/functions.php');
include_once('../koneksi.php');
session_start();
$type = 'query'.$_REQUEST['Rxls'];
switch ($_REQUEST['Rxls']) {
    case "ExlDL":
        $filename = "FILE_UPLOAD";
        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }

        $colField = mysql_fetch_array(mysql_query('SELECT ajkexcelupload.id, Count(ajkexcelupload.idxls) AS jumField, ajkclient.`name`, ajkpolis.policyauto FROM ajkexcelupload INNER JOIN ajkclient ON ajkexcelupload.idc = ajkclient.id INNER JOIN ajkpolis ON ajkexcelupload.idp = ajkpolis.id WHERE ajkexcelupload.idb="'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'" AND ajkexcelupload.idc="'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'" AND ajkexcelupload.idp="'.AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY).'" GROUP BY ajkexcelupload.idp'));
        $jumlahFieldDataUplaod = $colField['jumField'];
        HeaderingExcel(str_replace(" ", "_", strtoupper($colField['name'])).'_'.$filename.'.xls');
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
								 WHERE ajkexcelupload.idb = "'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'" AND
								 	   ajkexcelupload.idc = "'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'" AND
								 	   ajkexcelupload.idp = "'.AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY).'"
								 ORDER BY ajkexcelupload.id ASC');
        $worksheet1->write_string($Databaris, 0, "No", $format);
        while ($metDLExl_ = mysql_fetch_array($metDLExl)) {
            if ($metDLExl_['valempty']=="Y" or $metDLExl_['valdate']=="Y" or $metDLExl_['valsamedata']=="Y") {
                $metKolomVal = $metDLExl_['fieldname'].'*';
            } else {
                $metKolomVal = $metDLExl_['fieldname'];
            }
            $worksheet1->write_string($Databaris, $Datakolom, $metKolomVal, $format);
            $Datakolom++;
        }

            $workbook->close();
                ;
    break;

    case "lprmember":
        $title = date("Ymd").'_MEMBER_BANK.xls';
        header("Content-type: application/vnd-ms-excel");                   
        header("Content-Disposition: attachment; filename=$title");
        $merge = 'colspan="20"';
    
        echo '
        <style>
        .judul{background-color:tomato;color:white;}
        .str{ mso-number-format:\@; }
        </style>
        <table>
          <tr>
            <!--<th style="text-align:left">Report Name :</th>-->
            <th '.$merge.'>'.strtoupper($_SESSION['lprproduk']).'</th>
          </tr>
          <tr>
            <th '.$merge.'>'.strtoupper($_SESSION['lprstatus']).'</th>
          </tr>
          <tr>
            <th '.$merge.'>'.strtoupper($_SESSION['lprcabang']).'</th>
          </tr>
          <tr>
            <th '.$merge.'>'.strtoupper($_SESSION['lprperiode']).'</th>
          </tr>
        </table>
        <table border="1">                     
        <tr>
          <th class="judul">No</th>
          <th class="judul">Produk</th>
          <th class="judul">Pekerjaan</th>
          <th class="judul">No Perjanjian Kredit</th>
          <th class="judul">Id Peserta</th>
          <th class="judul">Sertifikat</th>
          <th class="judul">Nama</th>
          <th class="judul">Tgl.Lahir</th>
          <th class="judul">Umur</th>
          <th class="judul">Plafond</th>
          <th class="judul">Tgl.Transaksi</th>
          <th class="judul">Tgl.Akad</th>
          <th class="judul">Tenor</th>
          <th class="judul">Tgl.Akhir</th>
          <th class="judul">Cabang</th>
          <th class="judul">Wilayah</th>
          <th class="judul">Asuransi</th>
          <th class="judul">Total Premi</th>
          <th class="judul">Status</th>
          <th class="judul">Feebase</th>
          <th class="judul">Tgl.Bayar Premi</th>          
        </tr>';

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
          echo '
          <tr>
          <td>'.++$no.'</td>
          <td>'.$metCOB_['typedata'].'</td>
          <td>'.$metCOB_['produk'].'</td>
          <td class="str">'.$metCOB_['nopinjaman'].'</td>
          <td class="str">'.$metCOB_['idpeserta'].'</td>
          <td>'.$metCOB_['noasuransi'].'</td>
          <td>'.$metCOB_['nama'].'</td>
          <td class="str">'._convertDate($metCOB_['tgllahir']).'</td>
          <td>'.$metCOB_['usia'].'</td>
          <td>'.$metCOB_['plafond'].'</td>
          <td class="str">'._convertDate($metCOB_['tgltransaksi']).'</td>
          <td class="str">'._convertDate($metCOB_['tglakad']).'</td>
          <td>'.$metCOB_['tenor'].'</td>
          <td class="str">'._convertDate($metCOB_['tglakhir']).'</td>
          <td>'.$metCOB_['cabang'].'</td>
          <td>'.$metCOB_['wilayah'].'</td>
          <td>'.$metCOB_['asuransi'].'</td>
          <td>'.$metCOB_['totalpremi'].'</td>
          <td>'.$metCOB_['statusaktif'].'</td>
          <td>'.$metCOB_['resturno'].'</td>
          <td class="str">'._convertDate($metCOB_['tglbayaras']).'</td>
          </tr>';
          $Tpremi += $metCOB_['totalpremi'];
          $Tresturno += $metCOB_['resturno'];
        }     
        
        echo '
        <tr>
            <td colspan="15">Total</td>
            <td>'.$Tpremi.'</td>
        </tr>
        </table>';
    break;

    case "lprmemberS":
        $filename = "MEMBER_BANK";
        // $filename1 = "MEMBER_INSURANCE";
        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
        //HeaderingExcel(_convertDate(_convertDateEng2(AES::decrypt128CBC($_REQUEST['dtfrom'], ENCRYPTION_KEY))).'_'._convertDate(_convertDateEng2(AES::decrypt128CBC($_REQUEST['dtto'], ENCRYPTION_KEY))).'_'.$filename.'.xls');
        HeaderingExcel(date("Ymd").'_'.$filename.'.xls');
        $workbook = new Workbook("");
        $worksheet1 =& $workbook->add_worksheet($filename);
        // $worksheet2 =& $workbook->add_worksheet($filename1);

        $format =& $workbook->add_format();   $format->set_align('center'); $format->set_color('white');  $format->set_bold();  $format->set_pattern(); $format->set_fg_color('orange');
        $fjudul =& $workbook->add_format();   $fjudul->set_align('center'); $fjudul->set_bold();
        $ftotal =& $workbook->add_format();   $ftotal->set_bold();        

        $met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual, ajkpolis.byrate
                        FROM ajkcobroker
                        INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
                        INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
                        WHERE ajkcobroker.id = "'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'" AND ajkclient.id = "'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'" AND ajkpolis.id = "'.AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY).'" '));

        if ($_REQUEST['idb']=="") {
            $_metbroker = '';
        } else {
            $_metbroker = $met_['brokername'];
        }
        if ($_REQUEST['idc']=="") {
            $_metclient = 'ALL CLIENT';
        } else {
            $_metclient = $met_['clientname'];
        }
        if ($_REQUEST['idp']=="") {
            $_metproduk = 'ALL PRODUCT';
        } else {
            $_metproduk = $met_['produk'];
        }
        $worksheet1->write_string(0, 0, "MEMBERSHIP DATA REPORT", $fjudul); $worksheet1->merge_cells(0, 0, 0, 19);
        $worksheet1->write_string(1, 0, strtoupper($_SESSION['lprproduk']), $fjudul); $worksheet1->merge_cells(1, 0, 1, 19);
        $worksheet1->write_string(2, 0, strtoupper($_SESSION['lprstatus']), $fjudul); $worksheet1->merge_cells(2, 0, 2, 19);
        $worksheet1->write_string(3, 0, strtoupper($_SESSION['lprcabang']), $fjudul); $worksheet1->merge_cells(3, 0, 3, 19);
        $worksheet1->write_string(4, 0, strtoupper($_SESSION['lprperiode']), $fjudul);  $worksheet1->merge_cells(4, 0, 4, 19);
        // $worksheet2->write_string(0, 0, "MEMBERSHIP DATA REPORT", $fjudul);  $worksheet2->merge_cells(0, 0, 0, 15);
        // $worksheet2->write_string(1, 0, strtoupper($_SESSION['lprproduk']), $fjudul);  $worksheet2->merge_cells(1, 0, 1, 15);
        // $worksheet2->write_string(2, 0, strtoupper($_SESSION['lprstatus']), $fjudul);  $worksheet2->merge_cells(2, 0, 2, 15);
        // $worksheet2->write_string(3, 0, strtoupper($_SESSION['lprcabang']), $fjudul);  $worksheet2->merge_cells(3, 0, 3, 15);
        // $worksheet2->write_string(3, 0, strtoupper($_SESSION['lprperiode']), $fjudul); $worksheet2->merge_cells(3, 0, 3, 15);

        $worksheet1->set_row(6, 15);
        $worksheet1->set_column(5, 0, 1); $worksheet1->write_string(5, 0, "NO", $format);
        $worksheet1->set_column(5, 1, 15);  $worksheet1->write_string(5, 1, "Produk", $format);
        $worksheet1->set_column(5, 2, 15);  $worksheet1->write_string(5, 2, "No Pinjaman", $format);
        $worksheet1->set_column(5, 3, 15);  $worksheet1->write_string(5, 3, "ID Peserta", $format);
        $worksheet1->set_column(5, 4, 15);  $worksheet1->write_string(5, 4, "Sertifikat", $format);
        $worksheet1->set_column(5, 5, 50);  $worksheet1->write_string(5, 5, "Nama", $format);
        $worksheet1->set_column(5, 6, 10);  $worksheet1->write_string(5, 6, "Tgl.Lahir", $format);
        $worksheet1->set_column(5, 7, 5); $worksheet1->write_string(5, 7, "Umur", $format);
        $worksheet1->set_column(5, 8, 10);  $worksheet1->write_string(5, 8, "Plafond", $format);
        $worksheet1->set_column(5, 9, 10);  $worksheet1->write_string(5, 9, "Tgl.Transaksi", $format);
        $worksheet1->set_column(5, 10, 10);  $worksheet1->write_string(5, 10, "Tgl.Akad", $format);
        $worksheet1->set_column(5, 11, 5); $worksheet1->write_string(5, 11, "Tenor", $format);
        $worksheet1->set_column(5, 12, 10);  $worksheet1->write_string(5, 12, "Tgl.Akhir", $format);
        $worksheet1->set_column(5, 13, 20);  $worksheet1->write_string(5, 13, "Cabang", $format);
        $worksheet1->set_column(5, 14, 20);  $worksheet1->write_string(5, 14, "Wilayah", $format);
        $worksheet1->set_column(5, 15, 20); $worksheet1->write_string(5, 15, "Asuransi", $format);
        $worksheet1->set_column(5, 16, 15); $worksheet1->write_string(5, 16, "Total Premi", $format);
        $worksheet1->set_column(5, 17, 15); $worksheet1->write_string(5, 17, "Resturno", $format);
        $worksheet1->set_column(5, 18, 15); $worksheet1->write_string(5, 18, "Tgl Bayar Premi", $format);
        $worksheet1->set_column(5, 19, 15); $worksheet1->write_string(5, 19, "Tgl Bayar Resturno", $format);

        // $worksheet2->set_row(5, 13);
        // $worksheet2->set_column(5, 0, 1);  $worksheet2->write_string(5, 0, "NO", $format);
        // $worksheet2->set_column(5, 1, 15);  $worksheet2->write_string(5, 1, "No Pinjaman", $format);
        // $worksheet2->set_column(5, 1, 15); $worksheet2->write_string(5, 2, "ID Peserta", $format);
        // $worksheet2->set_column(5, 2, 30); $worksheet2->write_string(5, 3, "Nama", $format);
        // $worksheet2->set_column(5, 3, 10); $worksheet2->write_string(5, 4, "Tgl.Lahir", $format);
        // $worksheet2->set_column(5, 4, 5);  $worksheet2->write_string(5, 5, "Umur", $format);
        // $worksheet2->set_column(5, 5, 10); $worksheet2->write_string(5, 6, "Plafond", $format);
        // $worksheet2->set_column(5, 6, 10); $worksheet2->write_string(5, 7, "Tgl.Akad", $format);
        // $worksheet2->set_column(5, 7, 5);  $worksheet2->write_string(5, 8, "Tenor", $format);
        // $worksheet2->set_column(5, 8, 10); $worksheet2->write_string(5, 9, "Tgl.Akhir", $format);
        // $worksheet2->set_column(5, 9, 20); $worksheet2->write_string(5, 10, "Cabang", $format);
        // $worksheet2->set_column(5, 10, 10);  $worksheet2->write_string(5, 11, "Asuransi", $format);
        // $worksheet2->set_column(5, 11, 15);  $worksheet2->write_string(5, 12, "Total Premi", $format);

        $baris = 6;

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
            $worksheet1->write_string($baris, 0, ++$no, 'C');
            $worksheet1->write_string($baris, 1, $metCOB_['produk']);
            $worksheet1->write_string($baris, 2, $metCOB_['nopinjaman']);
            $worksheet1->write_string($baris, 3, $metCOB_['idpeserta']);
            $worksheet1->write_string($baris, 4, $metCOB_['noasuransi']);
            $worksheet1->write_string($baris, 5, $metCOB_['nama']);
            $worksheet1->write_string($baris, 6, $metCOB_['tgllahir']);
            $worksheet1->write_number($baris, 7, $metCOB_['usia']);
            $worksheet1->write_number($baris, 8, $metCOB_['plafond']);
            $worksheet1->write_string($baris, 9, _convertDate($metCOB_['tgltransaksi']));
            $worksheet1->write_string($baris, 10, _convertDate($metCOB_['tglakad']));
            $worksheet1->write_number($baris, 11, $metCOB_['tenor']);
            $worksheet1->write_string($baris, 12, _convertDate($metCOB_['tglakhir']));
            $worksheet1->write_string($baris, 13, $metCOB_['cabang']);
            $worksheet1->write_string($baris, 14, $metCOB_['wilayah']);
            $worksheet1->write_string($baris, 15, $metCOB_['asuransi']);
            $worksheet1->write_number($baris, 16, $metCOB_['totalpremi']);
            $worksheet1->write_number($baris, 17, $metCOB_['resturno']);
            $worksheet1->write_string($baris, 18, ($metCOB_['tglbayaras']));
            $worksheet1->write_string($baris, 19, ($metCOB_['tgl_bayar']));
            


            // $worksheet2->write_string($baris, 0, ++$no1, 'C');
            // $worksheet2->write_string($baris, 1, $metCOB_['nopinjaman']);
            // $worksheet2->write_string($baris, 2, $metCOB_['idpeserta']);
            // $worksheet2->write_string($baris, 3, $metCOB_['nama']);
            // $worksheet2->write_string($baris, 4, $metCOB_['tgllahir']);
            // $worksheet2->write_number($baris, 5, $metCOB_['usia']);
            // $worksheet2->write_number($baris, 6, $metCOB_['plafond']);
            // $worksheet2->write_string($baris, 7, _convertDate($metCOB_['tglakad']));
            // $worksheet2->write_number($baris, 8, $metCOB_['tenor']);
            // $worksheet2->write_string($baris, 9, _convertDate($metCOB_['tglakhir']));
            // $worksheet2->write_string($baris, 10, $metCOB_['cabang']);
            // $worksheet2->write_string($baris, 11, $metCOB_['asuransi']);
            // $worksheet2->write_number($baris, 12, $metCOB_['astotalpremi']);
            $baris++;
            $tPremi += $metCOB_['totalpremi'];
            $tPremias += $metCOB_['astotalpremi'];
            $tResturno += $metCOB_['resturno'];
        }
        $worksheet1->write_string($baris, 0, "TOTAL", $ftotal); $worksheet1->merge_cells($baris, 0, $baris, 15);
        $worksheet1->write_number($baris, 16, $tPremi, $ftotal);
        $worksheet1->write_number($baris, 17, $tResturno, $ftotal);        
        // $worksheet2->write_string($baris, 0, "TOTAL", $fjudul);  $worksheet2->merge_cells($baris, 0, $baris, 10);
        // $worksheet2->write_number($baris, 11, $tPremias, $ftotal);


        $workbook->close();
                ;
    break;

    case "rptdebitnote":
        $filename = "DETBINOTE";
        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
        HeaderingExcel(date("Ymd").'_'.$_SESSION['lprperiode'].'_'.$filename.'.xls');
        $workbook = new Workbook("");
        $worksheet1 =& $workbook->add_worksheet($filename);

        $format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
        $fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
        $ftotal =& $workbook->add_format();		$ftotal->set_bold();

        if ($_REQUEST['idb']) {
            $satu ='AND ajkcobroker.id = "'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'"';
        }
        if ($_REQUEST['idc']) {
            $dua ='AND ajkclient.id = "'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'"';
        }
        $met_idproduk = explode("_", AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY));
        if ($_REQUEST['idp']) {
            $tiga ='AND ajkpolis.id = "'.$met_idproduk[0].'"';
        }

        $met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual, ajkpolis.byrate
											  FROM ajkcobroker
											  INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
											  INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
											  WHERE ajkcobroker.del IS NULL '.$satu.' '.$dua.'  '.$tiga.''));

        if (AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY)=="") {
            $_metbroker = '';
        } else {
            $_metbroker = $met_['brokername'];
        }
        if (AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY)=="") {
            $_metclient = 'ALL CLIENT';
        } else {
            $_metclient = $met_['clientname'];
        }
        if (AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY)=="") {
            $_metproduk = 'ALL PRODUCT';
        } else {
            $_metproduk = $met_['produk'];
        }

        $worksheet1->write_string(0, 0, "REPORT PAYMENTS", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 7);
        $worksheet1->write_string(1, 0, strtoupper($_SESSION['lprproduk']), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 7);
        $worksheet1->write_string(2, 0, strtoupper($_SESSION['lprstatus']), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 7);
        $worksheet1->write_string(3, 0, strtoupper($_SESSION['lprcabang']), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 7);
        $worksheet1->write_string(4, 0, strtoupper($_SESSION['lprperiode']), $fjudul);	$worksheet1->merge_cells(4, 0, 4, 7);

        $worksheet1->set_row(5, 15);
        $worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "NO", $format);
        $worksheet1->set_column(5, 1, 40);	$worksheet1->write_string(5, 1, "Nota Debit", $format);
        $worksheet1->set_column(5, 2, 10);	$worksheet1->write_string(5, 2, "Tgl.DN", $format);
        $worksheet1->set_column(5, 3, 15);	$worksheet1->write_string(5, 3, "Peserta", $format);
        $worksheet1->set_column(5, 4, 15);	$worksheet1->write_string(5, 4, "Premium", $format);
        $worksheet1->set_column(5, 5, 15);	$worksheet1->write_string(5, 5, "Status", $format);
        $worksheet1->set_column(5, 6, 10);	$worksheet1->write_string(5, 6, "Tgl.Bayar", $format);
        $worksheet1->set_column(5, 7, 10);	$worksheet1->write_string(5, 7, "Cabang", $format);
        $baris = 6;
        if (AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY)) {
            $satu = 'AND ajkdebitnote.idbroker="'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'"';
        }
        if (AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY)) {
            $dua = 'AND ajkdebitnote.idclient="'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'"';
        }
        if (AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY)) {
            $tiga = 'AND ajkdebitnote.idproduk="'.$met_idproduk[0].'"';
        }
                if (AES::decrypt128CBC($_REQUEST['st'], ENCRYPTION_KEY)=="1") {
                    $_datapaid="Paid";
                } elseif (AES::decrypt128CBC($_REQUEST['st'], ENCRYPTION_KEY)=="2") {
                    $_datapaid="Paid*";
                } elseif (AES::decrypt128CBC($_REQUEST['st'], ENCRYPTION_KEY)=="") {
                    $_datapaid="%";
                } else {
                    $_datapaid="Unpaid";
                }
                if ($_REQUEST['st']) {
                    $empat = 'AND ajkdebitnote.paidstatus like "'.$_datapaid.'"';
                }

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
            if ($metCOB_['paidtanggal']=="" or $metCOB_['paidtanggal']=="0000-00-00") {
                $tgllunas = '';
            } else {
                $tgllunas = _convertDate($metCOB_['paidtanggal']);
            }

            $worksheet1->write_string($baris, 0, ++$no, 'C');
            $worksheet1->write_string($baris, 1, $metCOB_['nomordebitnote']);
            $worksheet1->write_string($baris, 2, $metCOB_['tgldebitnote']);
            $worksheet1->write_number($baris, 3, $metCOB_['jmember']);
            $worksheet1->write_number($baris, 4, $metCOB_['premiclient']);
            $worksheet1->write_string($baris, 5, $metCOB_['paidstatus']);
            $worksheet1->write_string($baris, 6, $tgllunas);
            $worksheet1->write_string($baris, 7, $metCOB_['cabang']);
            $baris++;
            $tPremi += $metCOB_['premiclient'];
        }
        $worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 3);
        $worksheet1->write_number($baris, 4, $tPremi, $ftotal);
        $workbook->close();
            ;
    break;

    case "rptcnbatal":
        $filename = "MEMBER_BATAL";
        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
        HeaderingExcel(date("Ymd").'_'.$_SESSION['lprperiode'].'_'.$filename.'.xls');
        $workbook = new Workbook("");
        $worksheet1 =& $workbook->add_worksheet($filename);

        $format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
        $fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
        $ftotal =& $workbook->add_format();		$ftotal->set_bold();

        $worksheet1->write_string(0, 0, "MEMBERSHIP DATA BATAL REPORT", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 15);
        $worksheet1->write_string(1, 0, strtoupper($_SESSION['lprproduk']), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 15);
        $worksheet1->write_string(2, 0, strtoupper($_SESSION['lprstatus']), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 15);
        $worksheet1->write_string(3, 0, strtoupper($_SESSION['lprcabang']), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 15);
        $worksheet1->write_string(4, 0, strtoupper($_SESSION['lprperiode']), $fjudul);	$worksheet1->merge_cells(4, 0, 3, 15);

        $worksheet1->set_row(6, 15);
        $worksheet1->set_column(5, 0, 1);	$worksheet1->write_string(5, 0, "NO", $format);
        $worksheet1->set_column(5, 1, 15);	$worksheet1->write_string(5, 1, "ID Peserta", $format);
        $worksheet1->set_column(5, 2, 30);	$worksheet1->write_string(5, 2, "Nama", $format);
        $worksheet1->set_column(5, 3, 10);	$worksheet1->write_string(5, 3, "Tgl.Lahir", $format);
        $worksheet1->set_column(5, 4, 5);	$worksheet1->write_string(5, 4, "Umur", $format);
        $worksheet1->set_column(5, 5, 10);	$worksheet1->write_string(5, 5, "Plafond", $format);
        $worksheet1->set_column(5, 6, 10);	$worksheet1->write_string(5, 6, "Tgl.Akad", $format);
        $worksheet1->set_column(5, 7, 5);	$worksheet1->write_string(5, 7, "Tenor", $format);
        $worksheet1->set_column(5, 8, 10);	$worksheet1->write_string(5, 8, "Tgl.Akhir", $format);
        $worksheet1->set_column(5, 9, 20);	$worksheet1->write_string(5, 9, "Branch", $format);
        $worksheet1->set_column(5, 10, 20);	$worksheet1->write_string(5, 10, "Asuransi", $format);
        $worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 11, "Total Premi", $format);

        $baris = 6;

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
            $worksheet1->write_string($baris, 0, ++$no, 'C');
            $worksheet1->write_string($baris, 1, $metCOB_['idpeserta']);
            $worksheet1->write_string($baris, 2, $metCOB_['nama']);
            $worksheet1->write_string($baris, 3, $metCOB_['tgllahir']);
            $worksheet1->write_number($baris, 4, $metCOB_['usia']);
            $worksheet1->write_number($baris, 5, duit($metCOB_['plafond']));
            $worksheet1->write_string($baris, 6, _convertDate($metCOB_['tglakad']));
            $worksheet1->write_number($baris, 7, $metCOB_['tenor']);
            $worksheet1->write_string($baris, 8, _convertDate($metCOB_['tglakhir']));
            $worksheet1->write_string($baris, 9, $metCOB_['cabang']);
            $worksheet1->write_string($baris, 10, $metCOB_['asuransi']);
            $worksheet1->write_number($baris, 11, $metCOB_['totalpremi']);

            $baris++;
            $tPremi += $metCOB_['totalpremi'];
            $tPremias += $metCOB_['astotalpremi'];
        }
        $worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 10);
        $worksheet1->write_number($baris, 11, $tPremi, $ftotal);


        $workbook->close();
                ;
    break;

    case "rptcnrefund":
        $filename = "MEMBER_REFUND";
        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
        HeaderingExcel(date("Ymd").'_'.$_SESSION['lprperiode'].'_'.$filename.'.xls');
        $workbook = new Workbook("");
        $worksheet1 =& $workbook->add_worksheet($filename);

        $format =& $workbook->add_format();		$format->set_align('center');	$format->set_color('white');	$format->set_bold();	$format->set_pattern();	$format->set_fg_color('orange');
        $fjudul =& $workbook->add_format();		$fjudul->set_align('center');	$fjudul->set_bold();
        $ftotal =& $workbook->add_format();		$ftotal->set_bold();

        $worksheet1->write_string(0, 0, "MEMBERSHIP DATA REFUND REPORT", $fjudul);	$worksheet1->merge_cells(0, 0, 0, 15);
        $worksheet1->write_string(1, 0, strtoupper($_SESSION['lprproduk']), $fjudul);	$worksheet1->merge_cells(1, 0, 1, 15);
        $worksheet1->write_string(2, 0, strtoupper($_SESSION['lprstatus']), $fjudul);	$worksheet1->merge_cells(2, 0, 2, 15);
        $worksheet1->write_string(3, 0, strtoupper($_SESSION['lprcabang']), $fjudul);	$worksheet1->merge_cells(3, 0, 3, 15);
        $worksheet1->write_string(4, 0, strtoupper($_SESSION['lprperiode']), $fjudul);	$worksheet1->merge_cells(4, 0, 3, 15);

        $worksheet1->set_row(6, 15);
        $worksheet1->set_column(5, 0, 1);		$worksheet1->write_string(5, 0, "NO", $format);
        $worksheet1->set_column(5, 1, 15);	$worksheet1->write_string(5, 1, "ID Peserta", $format);
        $worksheet1->set_column(5, 2, 30);	$worksheet1->write_string(5, 2, "Nama", $format);
        $worksheet1->set_column(5, 3, 10);	$worksheet1->write_string(5, 3, "Tgl.Lahir", $format);
        $worksheet1->set_column(5, 4, 5);		$worksheet1->write_string(5, 4, "Umur", $format);
        $worksheet1->set_column(5, 6, 10);	$worksheet1->write_string(5, 5, "Tgl.Akad", $format);
        $worksheet1->set_column(5, 7, 5);		$worksheet1->write_string(5, 6, "Tenor", $format);
        $worksheet1->set_column(5, 8, 10);	$worksheet1->write_string(5, 7, "Tgl.Akhir", $format);
        $worksheet1->set_column(5, 5, 10);	$worksheet1->write_string(5, 8, "Plafond", $format);
        $worksheet1->set_column(5, 9, 20);	$worksheet1->write_string(5, 9, "Cabang", $format);
        $worksheet1->set_column(5, 10, 20);	$worksheet1->write_string(5, 10, "Asuransi", $format);
        $worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 11, "Total Premi", $format);
        $worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 12, "Tgl Refund", $format);
        $worksheet1->set_column(5, 11, 15);	$worksheet1->write_string(5, 13, "Nilai Refund", $format);

        $baris = 6;

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
            $worksheet1->write_string($baris, 0, ++$no, 'C');
            $worksheet1->write_string($baris, 1, $metCOB_['idpeserta']);
            $worksheet1->write_string($baris, 2, $metCOB_['nama']);
            $worksheet1->write_string($baris, 3, $metCOB_['tgllahir']);
            $worksheet1->write_number($baris, 4, $metCOB_['usia']);
            $worksheet1->write_string($baris, 5, _convertDate($metCOB_['tglakad']));
            $worksheet1->write_number($baris, 6, $metCOB_['tenor']);
            $worksheet1->write_string($baris, 7, _convertDate($metCOB_['tglakhir']));
            $worksheet1->write_number($baris, 8, duit($metCOB_['plafond']));
            $worksheet1->write_string($baris, 9, $metCOB_['cabang']);
            $worksheet1->write_string($baris, 10, $metCOB_['asuransi']);
            $worksheet1->write_number($baris, 11, $metCOB_['totalpremi']);
            $worksheet1->write_string($baris, 12, _convertDate($metCOB_['tglklaim']));
            $worksheet1->write_number($baris, 13, $metCOB_['nilaiclaimclient']);

            $baris++;
            $tPremi += $metCOB_['totalpremi'];
            $tPremias += $metCOB_['astotalpremi'];
        }
        $worksheet1->write_string($baris, 0, "TOTAL", $fjudul);	$worksheet1->merge_cells($baris, 0, $baris, 10);
        $worksheet1->write_number($baris, 11, $tPremi, $ftotal);


        $workbook->close();
                ;
    break;

    case "lapmemins":
        $filename = "MEMBER_INSURANCE";
        
        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
        
        HeaderingExcel(date("Ymd").'_'.$_SESSION['lprperiode'].'_'.$filename.'.xls');
        $workbook = new Workbook("");
        $worksheet1 =& $workbook->add_worksheet($filename);

        $format =& $workbook->add_format();   $format->set_align('center'); $format->set_color('white');  $format->set_bold();  $format->set_pattern(); $format->set_fg_color('orange');
        $fjudul =& $workbook->add_format();   $fjudul->set_align('center'); $fjudul->set_bold();
        $ftotal =& $workbook->add_format();   $ftotal->set_bold();

        $met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual, ajkpolis.byrate
                        FROM ajkcobroker
                        INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
                        INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
                        WHERE ajkcobroker.id = "'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'" AND ajkclient.id = "'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'" AND ajkpolis.id = "'.AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY).'" '));

        if ($_REQUEST['idb']=="") {
            $_metbroker = '';
        } else {
            $_metbroker = $met_['brokername'];
        }
        if ($_REQUEST['idc']=="") {
            $_metclient = 'ALL CLIENT';
        } else {
            $_metclient = $met_['clientname'];
        }
        if ($_REQUEST['idp']=="") {
            $_metproduk = 'ALL PRODUCT';
        } else {
            $_metproduk = $met_['produk'];
        }
        $worksheet1->write_string(0, 0, "MEMBERSHIP DATA REPORT", $fjudul); $worksheet1->merge_cells(0, 0, 0, 17);
        $worksheet1->write_string(1, 0, strtoupper($_SESSION['lprperiode']), $fjudul);  $worksheet1->merge_cells(1, 0, 1, 17);
        $worksheet1->write_string(2, 0, strtoupper($_SESSION['lprasuransi']), $fjudul);  $worksheet1->merge_cells(2, 0, 2, 17);

        $worksheet1->set_row(6, 15);
        $worksheet1->set_column(5, 0, 1); $worksheet1->write_string(5, 0, "NO", $format);
        $worksheet1->set_column(5, 1, 20);  $worksheet1->write_string(5, 1, "ID Peserta", $format);
        $worksheet1->set_column(5, 2, 20);  $worksheet1->write_string(5, 2, "Sertifikat", $format);
        $worksheet1->set_column(5, 3, 20);  $worksheet1->write_string(5, 3, "No Pinjaman", $format);        
        $worksheet1->set_column(5, 4, 20);  $worksheet1->write_string(5, 4, "Cabang", $format);
        $worksheet1->set_column(5, 5, 50);  $worksheet1->write_string(5, 5, "Nama Debitur", $format);
        $worksheet1->set_column(5, 6, 15);   $worksheet1->write_string(5, 6, "Instansi", $format);
        $worksheet1->set_column(5, 7, 15);  $worksheet1->write_string(5, 7, "Tgl.Lahir", $format);
        $worksheet1->set_column(5, 8, 20);   $worksheet1->write_string(5, 8, "Usia (Tahun)", $format);
        $worksheet1->set_column(5, 9, 20);   $worksheet1->write_string(5, 9, "Penggunaan Kredit", $format);
        $worksheet1->set_column(5, 10, 20);  $worksheet1->write_string(5, 10, "Pokok Kredit", $format);
        $worksheet1->set_column(5, 11, 10);   $worksheet1->write_string(5, 11, "JK. Waktu", $format);
        $worksheet1->set_column(5, 12, 15);  $worksheet1->write_string(5, 12, "Tgl.Realisasi", $format);
        $worksheet1->set_column(5, 13, 15);  $worksheet1->write_string(5, 13, "Tgl.Jatuh Tempo", $format);        
        $worksheet1->set_column(5, 14, 15);  $worksheet1->write_string(5, 14, "Premi", $format);        
        $worksheet1->set_column(5, 15, 10); $worksheet1->write_string(5, 15, "Tipe Penjaminan", $format);
        $worksheet1->set_column(5, 16, 10); $worksheet1->write_string(5, 16, "Status", $format);
        $worksheet1->set_column(5, 17, 10); $worksheet1->write_string(5, 17, "Keterangan", $format);

        $baris = 6;

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
            $worksheet1->write_string($baris, 0, ++$no, 'C');
            $worksheet1->write_string($baris, 1, $metCOB_['idpeserta']);
            $worksheet1->write_string($baris, 2, $metCOB_['noasuransi']);
            $worksheet1->write_string($baris, 3, $metCOB_['nopinjaman']);
            $worksheet1->write_string($baris, 4, $metCOB_['nmcabang']);
            $worksheet1->write_string($baris, 5, $metCOB_['nama']);
            $worksheet1->write_string($baris, 6, $metCOB_['nm_profesi']);
            $worksheet1->write_string($baris, 7, _convertDate($metCOB_['tgllahir']));
            $worksheet1->write_string($baris, 8, $metCOB_['usia']);
            $worksheet1->write_string($baris, 9, $metCOB_['produk']);
            $worksheet1->write_number($baris, 10, $metCOB_['plafond']);
            $worksheet1->write_string($baris, 11, $metCOB_['tenor']);
            $worksheet1->write_string($baris, 12, _convertDate($metCOB_['tglakad']));
            $worksheet1->write_string($baris, 13, _convertDate($metCOB_['tglakhir']));
            $worksheet1->write_number($baris, 14, $metCOB_['totalpremi']);
            $worksheet1->write_string($baris, 15, $metCOB_['tipe_pinjaman']);
            $worksheet1->write_string($baris, 16, $metCOB_['status']);
            $worksheet1->write_string($baris, 17, $metCOB_['keterangan']);

            $baris++;
            $tPremi += $metCOB_['totalpremi'];
            $tPremias += $metCOB_['astotalpremi'];
        }
        
        $worksheet1->write_string($baris+2, 0, '*Note : Status "Proses Pengecekan Adonai" masih dalam proses penutupan adonai yang belum final',$ftotal);
        $workbook->close();
                ;
    break;

    case "lapmeminsjamkrindo":
        $filename = "MEMBER_INSURANCE";
        $periodeawal = date_create($_SESSION['lprstartdate']);

        $month = date_format($periodeawal,"F");
        $year = date_format($periodeawal,"Y");


        function HeaderingExcel($filename)
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
        
        HeaderingExcel(date("Ymd").'_'.$_SESSION['lprperiode'].'_'.$filename.'.xls');
        $workbook = new Workbook("");
        $worksheet1 =& $workbook->add_worksheet($filename);

        $format =& $workbook->add_format();   $format->set_align('center'); $format->set_color('white');  $format->set_bold();  $format->set_pattern(); $format->set_fg_color('orange');
        $fjudul =& $workbook->add_format();   $fjudul->set_align('center'); $fjudul->set_bold();
        $ftotal =& $workbook->add_format();   $ftotal->set_bold();

        $met_ = mysql_fetch_array(mysql_query('SELECT ajkcobroker.id, ajkcobroker.logo, ajkcobroker.`name` AS brokername, ajkclient.`name` AS clientname, ajkclient.logo AS logoclient, ajkpolis.produk, ajkpolis.policymanual, ajkpolis.byrate
                        FROM ajkcobroker
                        INNER JOIN ajkclient ON ajkcobroker.id = ajkclient.idc
                        INNER JOIN ajkpolis ON ajkclient.id = ajkpolis.idcost
                        WHERE ajkcobroker.id = "'.AES::decrypt128CBC($_REQUEST['idb'], ENCRYPTION_KEY).'" AND ajkclient.id = "'.AES::decrypt128CBC($_REQUEST['idc'], ENCRYPTION_KEY).'" AND ajkpolis.id = "'.AES::decrypt128CBC($_REQUEST['idp'], ENCRYPTION_KEY).'" '));

        if ($_REQUEST['idb']=="") {
            $_metbroker = '';
        } else {
            $_metbroker = $met_['brokername'];
        }
        if ($_REQUEST['idc']=="") {
            $_metclient = 'ALL CLIENT';
        } else {
            $_metclient = $met_['clientname'];
        }
        if ($_REQUEST['idp']=="") {
            $_metproduk = 'ALL PRODUCT';
        } else {
            $_metproduk = $met_['produk'];
        }
        $worksheet1->write_string(0, 0, "Daftar Nominatif Pengajuan Penjaminan Kredit Konsumtif PT. BPD Jatim", $fjudul); $worksheet1->merge_cells(0, 0, 0, 15);
        $worksheet1->write_string(1, 0, "PT. Adonai Pialang Asuransi Cabang Surabaya", $fjudul); $worksheet1->merge_cells(1, 0, 1, 15);
        $worksheet1->write_string(2, 0, "Untuk Bulan $month Tahun $year", $fjudul); $worksheet1->merge_cells(2, 0, 2, 15);
        
        $worksheet1->set_row(7, 15);
        $worksheet1->set_column(5, 0, 1); $worksheet1->write_string(5, 0, "NO", $format);
        $worksheet1->set_column(5, 1, 50);  $worksheet1->write_string(5, 1, "Nama", $format);
        $worksheet1->set_column(5, 2, 50);  $worksheet1->write_string(5, 2, "Alamat", $format);        
        $worksheet1->set_column(5, 3, 20);  $worksheet1->write_string(5, 3, "Umur", $format);
        $worksheet1->set_column(5, 4, 50);  $worksheet1->write_string(5, 4, "Tanggal Lahir", $format);
        $worksheet1->set_column(5, 5, 15);   $worksheet1->write_string(5, 5, "Instansi", $format);
        $worksheet1->set_column(5, 6, 15);  $worksheet1->write_string(5, 6, "Plafond", $format);
        $worksheet1->set_column(5, 7, 20);   $worksheet1->write_string(5, 7, "Status Kepegawaian", $format);
        $worksheet1->set_column(5, 8, 20);   $worksheet1->write_string(5, 8, "Tingkat Suku Bunga", $format);
        $worksheet1->set_column(5, 9, 20);  $worksheet1->write_string(5, 9, "No PK", $format);
        $worksheet1->set_column(5, 10, 10);   $worksheet1->write_string(5, 10, "Tgl PK", $format);
        $worksheet1->set_column(5, 11, 15);  $worksheet1->write_string(5, 11, "Tanggal Realisasi", $format);
        $worksheet1->set_column(5, 12, 15);  $worksheet1->write_string(5, 12, "Tanggal Jatuh Tempo", $format);        
        $worksheet1->set_column(5, 13, 15);  $worksheet1->write_string(5, 13, "Jangka Waktu Kredit (Bln)", $format);        
        $worksheet1->set_column(5, 14, 10); $worksheet1->write_string(5, 14, "Imbal Jasa Penjaminan (IJP)", $format);
        $worksheet1->set_column(5, 15, 10); $worksheet1->write_string(5, 15, "Keterangan (Kredit Baru/Sedang Berjalan/ Perpanjangan/Suplesi", $format);

        $baris = 6;

        $metCOB = mysql_query(AES::decrypt128CBC($_SESSION[$type], ENCRYPTION_KEY));

        while ($metCOB_ = mysql_fetch_array($metCOB)) {
            $worksheet1->write_string($baris, 0, ++$no, 'C');
            $worksheet1->write_string($baris, 1, $metCOB_['nama']);
            $worksheet1->write_string($baris, 2, $metCOB_['alamatobjek']);
            $worksheet1->write_string($baris, 3, $metCOB_['usia']);
            $worksheet1->write_string($baris, 4, _convertDate($metCOB_['tgllahir']));
            $worksheet1->write_string($baris, 5, $metCOB_['nm_profesi']);
            $worksheet1->write_number($baris, 6, $metCOB_['plafond']);
            $worksheet1->write_string($baris, 7, $metCOB_['nm_kategori_profesi']);
            $worksheet1->write_string($baris, 8, $metCOB_['aspremirate']);
            $worksheet1->write_string($baris, 9, $metCOB_['nomorpk']);
            $worksheet1->write_string($baris, 10, _convertDate($metCOB_['tglakad']));
            $worksheet1->write_string($baris, 11, _convertDate($metCOB_['tglakad']));
            $worksheet1->write_string($baris, 12, _convertDate($metCOB_['tglakhir']));
            $worksheet1->write_number($baris, 13, $metCOB_['tenor']);
            $worksheet1->write_number($baris, 14, $metCOB_['astotalpremi']);
            $worksheet1->write_string($baris, 15, $metCOB_['tipe_pinjaman']);
            
            $baris++;
        }

        $workbook->close();
                ;
    break;

    case "klaimprapialang":
        $filename = "klaim_prapialang".date("YmdHis").'.xls';
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename='.$filename);

        $sql = mysql_query("SELECT * FROM ajkklaimprapialang");
        $detail = "";
        
        while($row = mysql_fetch_array($sql)){
        $detail .='<tr>';
        $detail .='<td>'.$row['nourut'].'</td>';
        $detail .='<td>'.$row['nama'].'</td>';
        $detail .='<td>'._convertDate($row['tgllahir']).'</td>';
        $detail .='<td>'._convertDate($row['tglakad']).'</td>';
        $detail .='<td>'.$row['tenor'].'</td>';
        $detail .='<td>'.$row['plafond'].'</td>';
        $detail .='<td>'.$row['cabang'].'</td>';
        $detail .='<td>'.$row['nopolis'].'</td>';
        $detail .='<td>'.$row['nosuratclient'].'</td>';
        $detail .='<td>'.$row['nopengajuan'].'</td>';
        $detail .='<td>'._convertDate($row['tglklaim']).'</td>';
        $detail .='<td>'.$row['jenis_klaim'].'</td>';
        $detail .='<td>'.$row['tempat_kejadian'].'</td>';
        $detail .='<td>'.$row['nilaiklaimdiajukan'].'</td>';
        $detail .='<td>'.$row['asuransi'].'</td>';
        $detail .='<td>'._convertDate($row['tglpengajuan']).'</td>';
        $detail .='<td>'._convertDate($row['tglterimadokumen']).'</td>';
        $detail .='<td>'._convertDate($row['tgldokumenlengkap']).'</td>';
        $detail .='<td>'._convertDate($row['tgllaporas']).'</td>';
        $detail .='<td>'.$row['keteranganklaim'].'</td>';
        $detail .='<td>'.$row['kelengkapandokumen'].'</td>';
        $detail .='<td>'.$row['statusklaim'].'</td>';
        $detail .='<td>'.$row['nilaibayaras'].'</td>';
        $detail .='<td>'._convertDate($row['tglbayaras']).'</td>';
        $detail .='<td>'.$row['nilaibayarclient'].'</td>';
        $detail .='<td>'._convertDate($row['tglbayarclient']).'</td>';
        $detail .='</tr>';
        }
        
        echo '
        <table border="1">
          <thead>
          <tr><th colspan="26" style="text-align:center">KLAIM PRA PIALANG</th></tr>
          <tr><th colspan="26"></th></tr>
          <tr>
              <th>No Urut</th>
              <th>Nama</th>
              <th>Tgl Lahir</th>
              <th>Tgl Akad</th>
              <th>Tenor</th>
              <th>Plafond</th>
              <th>Cabang</th>
              <th>No Polis</th>
              <th>No Surat Client</th>
              <th>No Pengajuan</th>
              <th>Tgl Klaim</th>
              <th>Jenis Klaim</th>
              <th>Tempat Kejadian</th>
              <th>Nilai Klaim diajukan</th>
              <th>Asuransi</th>
              <th>Tgl Pengajuan</th>
              <th>Tgl Terima Dokumen</th>
              <th>Tgl Dokumen Lengkap</th>
              <th>Tgl Lapor Asuransi</th>
              <th>Keterangan</th>
              <th>Kelengkapan Dokumen</th>
              <th>Status Klaim</th>
              <th>Nilai Bayar Asuransi</th>
              <th>Tgl Bayar Asuransi</th>
              <th>Nilai Bayar ke Bank</th>
              <th>Tgl Bayar ke Bank</th>
          </tr>
          </thead>
          <tbody>'.$detail.'</tbody>
        </table>';
        // exit;
    break;


    default:
        ;
} // switch
