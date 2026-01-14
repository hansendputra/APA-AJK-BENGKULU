<?php
// ----------------------------------------------------------------------------------
// Original Author Of File : Rahmad
// E-mail :penting_kaga@yahoo.com
// @ Copyright 2016
// ----------------------------------------------------------------------------------
include_once('ui.php');
include '../PHPExcel/IOFactory.php'; 

$today = date("Y-m-d G:i:s");
$day = date("Y-m-d");
	echo '
	<section id="main" role="main">
	<div class="container-fluid" style="min-height:1024px;"><!-- add min-height to simulate scrolling -->
	<div class="page-header page-header-block">';

switch ($_REQUEST['py']) {
	case "debitnote":
		echo '<div class="page-header-section"><h2 class="title semibold">Modul Agreement</h2></div>
				<div class="page-header-section"><div class="toolbar"><a href="ajk.php?re=arm">'.BTN_BACK.'</a></div></div>
			</div>';
		$metPay = mysql_fetch_array($database->doQuery('SELECT
		ajkdebitnote.id,
		ajkcobroker.`name` AS brokername,
		ajkcobroker.logo,
		ajkclient.`name` AS clientname,
		ajkpolis.produk,
		ajkcabang.`name` AS cabang,
		ajkdebitnote.nomordebitnote,
		ajkdebitnote.tgldebitnote,
		ajkdebitnote.paidstatus,
		ajkdebitnote.paidtanggal,
		ajkdebitnote.premiclient,
		ajkdebitnote.premiclientdibayar
		FROM ajkdebitnote
		INNER JOIN ajkcobroker ON ajkdebitnote.idbroker = ajkcobroker.id
		INNER JOIN ajkclient ON ajkdebitnote.idclient = ajkclient.id
		INNER JOIN ajkpolis ON ajkdebitnote.idproduk = ajkpolis.id
		INNER JOIN ajkcabang ON ajkdebitnote.idcabang = ajkcabang.er
		WHERE ajkdebitnote.id = "'.$thisEncrypter->decode($_REQUEST['idpay']).'"'));

		$_premioutstanding = $metPay['premiclient'] - $metPay['premiclientdibayar'];
		if ($_premioutstanding > 0 ) {
			$_premioutstanding_ = '<span class="label label-danger">'.duit($_premioutstanding).'</span>';
		}else{
			$_premioutstanding_ = '<span class="label label-success">'.duit($_premioutstanding).'</span>';
		}

		if ($_REQUEST['met']=="savepayment") {
			$totaldibayar = $metPay['premiclientdibayar'] + $_REQUEST['paymentpremium'];
			if ($totaldibayar > $metPay['premiclient']) {	$notifMetError ='<div class="alert alert-dismissable alert-danger"><strong>Error!</strong> Payment reject.</div>';	}
			if (_convertDateEng2($_REQUEST['datepay']) > $futoday) {	$notifMetError ='<div class="alert alert-dismissable alert-danger"><strong>Error!</strong> the payment date is not allowed the current date.</div>';	}
			if ($notifMetError) {

			}else{
			if ($totaldibayar == $metPay['premiclient']) {
				$paidstatus_ = "Paid";
				$metUpdatePeserta = $database->doQuery('UPDATE ajkpeserta SET statuslunas="1", tgllunas="'._convertDateEng2($_REQUEST['datepay']).'" WHERE iddn="'.$metPay['id'].'"');
			}else{
				$paidstatus_ = "Paid*";
			}
			$metPayment = $database->doQuery('UPDATE ajkdebitnote SET paidtanggal="'._convertDateEng2($_REQUEST['datepay']).'", premiclientdibayar="'.$totaldibayar.'", paidstatus="'.$paidstatus_.'", update_by="'.$q['id'].'", update_time="'.$futgl.'" WHERE id="'.$thisEncrypter->decode($_REQUEST['idpay']).'"');

		echo '<meta http-equiv="refresh" content="2; url=ajk.php?re=arm">
			  <div class="alert alert-dismissable alert-success">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
			  <strong>Success!</strong> Update payment Debit Note number '.$metPay['nomordebitnote'].'.
		      </div>';
			}
		}

		if ($metPay['logo']=="") {
			$logoclient = '<img class="img-circle img-bordered" src="../'.$PathPhoto.'logo.png" alt="" width="75px">';
		}else{
			$logoclient = '<img class="img-circle img-bordered" src="../'.$PathPhoto.''.$metPay['logo'].'" alt="" width="75px">';
		}
		echo '<div class="row">
				<div class="col-lg-12">
			        	<div class="tab-content">
			            	<div class="tab-pane active" id="profile">
			<form method="post" class="panel panel-color-top panel-default form-horizontal form-bordered"  action="#" data-parsley-validate enctype="multipart/form-data">
								<div class="panel-body pt0 pb0">
			                    	<div class="form-group header bgcolor-default">
			                        	<div class="col-md-12">
			            					<ul class="list-table">
			            					<li style="width:80px;">'.$logoclient.'</li>
											<li class="text-left"><h4 class="semibold ellipsis semibold text-primary mt0 mb5">'.$metPay['brokername'].'</h4></li>
											</ul>
										</div>
			                        </div>
									<div class="form-group">
			                            <div class="col-xs-12 col-sm-12 col-md-12">
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Partner &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'.$metPay['clientname'].'</a></p></div>
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Product &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'.$metPay['produk'].'</a></p></div>
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Debitote &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'.$metPay['nomordebitnote'].'</a></p></div>
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Date Debitote &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'._convertDate($metPay['tgldebitnote']).'</a></p></div>
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Nett Premium &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'.duit($metPay['premiclient']).'</a></p></div>
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Paid Payment &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'.duit($metPay['premiclientdibayar']).'</a></p></div>
		<div class="col-sm-2 text-right"><p class="meta nm text-left">Outstanding Premium &nbsp; </p></div>
		<div class="col-sm-10"><p class="meta nm"><a href="javascript:void(0);">'.$_premioutstanding_.'</a></p></div>
								        </div>
			                        </div>

									<div class="form-group header bgcolor-default">
		                            <div class="col-md-12"><h5 class="semibold text-primary nm">Update Payment</h5></div>
		                            </div>';
		if ($metPay['paidstatus']=="Paid") {
		echo '<div class="panel-body"><div class="row">
		        	<div class="alert alert-success fade in">
		            <h4 class="semibold">Payment has been paid!</h4>
					<p class="mb10">Debit Note have been paid on '._convertDate($metPay['paidtanggal']).'.</p>
		        </div>
		    </div></div>';
		}else{
			echo '<div class="form-group">
			      '.$notifMetError.'
				  <label class="control-label col-sm-2">Payment Premium</label>
		          	<div class="col-sm-10">
						<div class="row">
		                    <div class="col-md-12"><input type="text" name="paymentpremium" class="form-control" data-parsley-type="number" value="'.$_premioutstanding.'" placeholder="Payment Premium" required/></div>
						</div>
						</div>
		            </div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Date Payment</label>
						<input type="hidden" name="idpay" value="'.$thisEncrypter->encode($metPay['id']).'">
		                <div class="col-sm-10"><input type="text" name="datepay" class="form-control" id="datepicker1" placeholder="Date Payment Debit Note" required/></div>
			        </div>
				</div>
				<div class="panel-footer"><input type="hidden" name="met" value="savepayment">'.BTN_SUBMIT.'</div>
			    </div>
		';
		}
		echo '</form>
			</div>
		</div>';
		echo '<script type="text/javascript" src="templates/{template_name}/plugins/parsley/js/parsley.js"></script>';
				;
	break;

	case "setpayment":
		$idpeserta = $thisEncrypter->decode($_REQUEST['id']);
		$qpeserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."' and del is null"));
		$qbyr = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as bayar FROM ajkbayar WHERE idpeserta = '".$idpeserta."' and del is null"));
		$premi = $qpeserta['totalpremi'] - $qbyr['bayar'];
    $ilustrasi = mysql_query("
    SELECT (usiaperkiraan - difmonth) as aktif,
        tenor - (usiaperkiraan - difmonth) as pensiun,
        (((usiaperkiraan - difmonth)/12)*4)as rateaktif,
        (((tenor - (usiaperkiraan - difmonth))/12)*5)as ratepensiun,
        ((((usiaperkiraan - difmonth)/12)*4)+(((tenor - (usiaperkiraan - difmonth))/12)*5)) as ratepremi,
        (plafond/1000)*((((usiaperkiraan - difmonth)/12)*4)+(((tenor - (usiaperkiraan - difmonth))/12)*5)) as premi
    FROM
    (SELECT plafond,premirate,totalpremi,tenor,tgllahir,tglakad,TIMESTAMPDIFF(MONTH, tgllahir, tglakad)as difmonth,ajkilustrasiprapen.usiaperkiraan
    FROM ajkpeserta,ajkilustrasiprapen
    WHERE idpeserta = '".$idpeserta."'
    )as temp");
		
		if($_POST['btnpay']=="procpay"){
      $time = date("YmdHis");
      $buktibayar = $_FILES['txt_buktibayar']['tmp_name'];
      $sppaname =  $idpeserta.'_'.$time.'_'.$_FILES['txt_buktibayar']['name'];

    	$premi = str_replace(",","",$_POST['txtpremi']); 
    	$tgl = $_POST['txt_tglln'];
    	$noref = $_POST['txt_noreff']; 
    	$noloan = $qpeserta['nopinjaman'];
    	$premi_sys = $_POST['txt_premi'];
    	$ket = $_POST['txt_ket'];
      
      move_uploaded_file($buktibayar, '../myFiles/_pembayaran/'.$sppaname) or die( "Could not upload file!");

    	$query2 = " INSERT ajkbayar 
									SET nopinjaman ='".$noloan."',
											idpeserta = '".$idpeserta."',
											tipebayar = 'premibank',
											nilaibayar = '".$premi."',
											tglbayar = '".$tgl."',
											norefbayar = '".$noref."',
											keterangan = '".$ket."',
                      buktibayar_path = '".$sppaname."',
											input_by = '".$q['id']."',
											input_date = '".$today."';";

    	$res = mysql_query($query2);

    	echo '<meta http-equiv="refresh" content="2; url=ajk.php?re=arm&py=members">
			  <div class="alert alert-dismissable alert-success">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
			  <strong>Update Success!
			  </div>';
    }

		echo '
						<div class="page-header-section"><h2 class="title semibold">Payment '.$qpeserta['nama'].' - ['.$qpeserta['idpeserta'].']</h2></div>
						<div class="page-header-section"></div>
					</div>
					<div class="row">
						<div class="col-md-12">
              <div class="panel panel-default">
                '.$c_ilustrasi.'
								<form action="#" method="post" id="form2" autocomplete="off" enctype="multipart/form-data">
									<div class="form-group">
										<label class="col-sm-2 control-label">Nilai Bayar <span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="txtpremi" id="txtpremi" value="'.$premi.'" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Tgl Bayar <span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control datepicker" name="txt_tglln" value="'.$tgl.'" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">No Ref <span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="txt_noreff" value="'.$noref.'" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Keterangan <span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="txt_ket" value="'.$ket.'" required>
										</div>
									</div>
                  <div class="form-group">
										<label class="col-sm-2 control-label">Bukti Bayar </label>
										<div class="col-sm-10">
											<input type="file" class="form-control" name="txt_buktibayar">
										</div>
									</div>
									<input type="hidden" id="btnpay" name="btnpay" value="procpay">

								  <div class="panel-footer text-center">
										<button type="submit" class="btn btn-success text-center"><i class="ico-save"></i> Save</button>										
										<a href="ajk.php?re=arm&py=members" class="btn btn-danger" ><i class="ico-close"></i> Close</a>
								  </div>
							  </form>
							</div>
						</div>
					</div>
					<script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>
					<script type="text/javascript">  

						$("#txtpremi").inputmask("numeric", {
							radixPoint: ".",
							groupSeparator: ",",
							digits: 2,
							autoGroup: true,
							prefix: "", //No Space, this will truncate the first character
							rightAlign: false,
							oncleared: function () { self.Value(""); }
						});  
						$(function(){
						  $(".datepicker").datepicker({dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});
						});
				  </script>';
	break;

	case "setpaymentins":
		$idpeserta = $thisEncrypter->decode($_REQUEST['id']);
		
		$qpeserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."' and del is null"));
		$qas = mysql_fetch_array(mysql_query("SELECT * FROM ajkinsurance WHERE id = '".$qpeserta['asuransi']."'"));
		$qbyr = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as bayar FROM ajkbayar WHERE idpeserta = '".$idpeserta."' and tipebayar='premiasuransi' and del is null"));
		$qbyrcadklaim = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as bayar FROM ajkbayar WHERE idpeserta = '".$idpeserta."' and tipebayar='cadklaimasuransi' and del is null"));
		$qbyrcadpremi = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as bayar FROM ajkbayar WHERE idpeserta = '".$idpeserta."' and tipebayar='cadpremiasuransi' and del is null"));

		$premicadklaim = round($qpeserta['astotalpremi']*$qas['cad_klaim']/100 - $qbyrcadklaim['bayar'],2);
		$premicadpremi = round($qpeserta['astotalpremi']*$qas['cad_premi']/100 - $qbyrcadpremi['bayar'],2);

		if($premicadklaim > 0){
			$inputcadklaim = '									
			<div class="form-group">
				<label class="col-sm-2 control-label">Nilai Bayar Cad. Klaim<span class="text-danger">*</span></label>
				<div class="col-sm-10">
					<input type="text" class="form-control duit" name="txtpremicadklaim" id="txtpremicadklaim" value="'.$premicadklaim.'" required>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Tgl Bayar Cad. Klaim<span class="text-danger">*</span></label>
				<div class="col-sm-10">
					<input type="text" class="form-control datepicker" name="txt_tglpremicadklaim" value="'.$day.'" required>
				</div>
			</div>';			
		}else{
			$inputcadklaim = '';
		}

		if($premicadpremi > 0){
			$inputcadpremi = '									
				<div class="form-group">
					<label class="col-sm-2 control-label">Nilai Bayar Cad. Premi<span class="text-danger">*</span></label>
					<div class="col-sm-10">
						<input type="text" class="form-control duit" name="txtpremicadpremi" id="txtpremicadpremi" value="'.$premicadpremi.'" required>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Tgl Bayar Cad. Premi<span class="text-danger">*</span></label>
					<div class="col-sm-10">
						<input type="text" class="form-control datepicker" name="txt_tglpremicadpremi" value="'.$day.'" required>
					</div>
				</div>';			
		}else{
			$inputcadpremi = '';
		}

		$premi = $qpeserta['astotalpremi'] - $qbyr['bayar'] - round($qpeserta['astotalpremi']*$qas['cad_klaim']/100,2) - round($qpeserta['astotalpremi']*$qas['cad_premi']/100,2);
		
		if($_POST['btnpay']=="procpay"){
			$premi = str_replace(",","",$_POST['txtpremi']); 
			$tglpremi = $_POST['txt_tglpremi'];
			$premicadklaim = str_replace(",","",$_POST['txtpremicadklaim']);  
			$tglcadklaim = $_POST['txt_tglpremicadklaim'];
			$premicadpremi = str_replace(",","",$_POST['txtpremicadpremi']);  
			$tglcadpremi = $_POST['txt_tglpremicadpremi'];			
			$noloan = $qpeserta['nopinjaman'];

			$query2 = " INSERT ajkbayar 
									SET nopinjaman ='".$noloan."',
											idpeserta = '".$idpeserta."',
											tipebayar = 'premiasuransi',
											nilaibayar = '".$premi."',
											tglbayar = '".$tglpremi."',
											keterangan = 'Pembayaran Premi Asuransi',
											input_by = '".$q['id']."',
											input_date = '".$today."';";			
			mysql_query($query2);

			if($premicadklaim != ''){
				$query3 = " INSERT ajkbayar 
				SET nopinjaman ='".$qpeserta['nopinjaman']."',
						idpeserta = '".$idpeserta."',
						tipebayar = 'cadklaimasuransi',
						nilaibayar = '".$premicadklaim."',
						tglbayar = '".$tglcadklaim."',
						keterangan = 'Pembayaran Cadangan Klaim Asuransi',
						input_by = '".$q['id']."',
						input_date = '".$today."';";				
				mysql_query($query3);
			}
			if($premicadpremi != ''){
				$query4 = " INSERT ajkbayar 
				SET nopinjaman ='".$qpeserta['nopinjaman']."',
						idpeserta = '".$idpeserta."',
						tipebayar = 'cadpremiasuransi',
						nilaibayar = '".$premicadpremi."',
						tglbayar = '".$tglcadpremi."',
						keterangan = 'Pembayaran Cadangan Premi Asuransi',
						input_by = '".$q['id']."',
						input_date = '".$today."';";
				mysql_query($query4);
			}

			echo '<meta http-equiv="refresh" content="2; url=ajk.php?re=arm&py=ins">
				<div class="alert alert-dismissable alert-success">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
				<strong>Update Success!
				</div>';
		}

		echo '
						<div class="page-header-section"><h2 class="title semibold">Insurance Payment '.$qpeserta['nama'].' - ['.$qpeserta['idpeserta'].']</h2></div>
						<div class="page-header-section"></div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-default">
								<form action="#" method="post" id="form2">
									<div class="form-group">
										<label class="col-sm-2 control-label">Nilai Bayar Premi<span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control duit" name="txtpremi" id="txtpremi" value="'.$premi.'" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Tgl Bayar Premi<span class="text-danger">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control datepicker" name="txt_tglpremi" value="'.$day.'" required>
										</div>
									</div>
									'.$inputcadklaim.' '.$inputcadpremi.'
									<input type="hidden" id="btnpay" name="btnpay" value="procpay">
									<div class="panel-footer text-center">
										<button type="submit" class="btn btn-success text-center"><i class="ico-save"></i> Save</button>										
										<a href="ajk.php?re=arm&py=ins" class="btn btn-danger" ><i class="ico-close"></i> Close</a>
									</div>
								</form>
							</div>
						</div>
					</div>
					<script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>
					<script type="text/javascript">  

						$(".duit").inputmask("numeric", {
							radixPoint: ".",
							groupSeparator: ",",
							digits: 2,
							autoGroup: true,
							prefix: "", //No Space, this will truncate the first character
							rightAlign: false,
							oncleared: function () { self.Value(""); }
						});  

						$(function(){
							$(".datepicker").datepicker({dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});
						});
					</script>';
	break;

	case "members":
		$periode1 = $_REQUEST['periode1'];
		$periode2 = $_REQUEST['periode2'];

		if($periode1){
			$date1 = _convertDate($periode1);
			$date2 = _convertDate($periode2);
			$filter = " AND tgltransaksi BETWEEN '".$date1."' and '".$date2."'";
		}else{
			$periode1 = date('d-m-Y');
			$periode2 = date('d-m-Y');
			$filter = "";
		}
		// echo $filter;

		if ($_REQUEST['btnsubmit']=="submit") {
			$query = '';
			$tglbayar = _convertDate($_REQUEST['tglbayar']);
			// $tglbayar = $today;
			foreach($_REQUEST['idtemp'] as $k => $val){
				// echo "SELECT * FROM ajkpeserta WHERE idpeserta = '".$val."' and del is null";
				$qpes = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$val."' and del is null"));
				$qbyr = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as nilaibayar FROM ajkbayar WHERE idpeserta = '".$val."' and tipebayar='premibank' and del is null"));
				$premibayar = $qpes['premi'] - $qbyr['nilaibayar'];

	    	$query = "INSERT ajkbayar 
									SET nopinjaman ='".$qpes['nopinjaman']."',
											idpeserta = '".$val."',
											tipebayar = 'premibank',
											nilaibayar = '".$premibayar."',
											tglbayar = '".$tglbayar."',
											norefbayar = '".$qpes['noreflunas']."',
											keterangan = 'Approve',
											input_by = '".$q['id']."',
											input_date = '".$c."'";	
				// echo $query;
				mysql_query($query);
			}
			echo '<meta http-equiv="refresh" content="5; url=ajk.php?re=arm&py=members">
			  <div class="alert alert-dismissable alert-success">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
			  <strong>Approve Success!</div>';
		}
		
		echo '
		<script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>';
	
			   
		echo '
			<div class="page-header-section"><h2 class="title semibold">Payment Outstanding</h2></div>
			
		</div>
		<div class="row">
			<div class="col-md-12">
				<form method="post" action="#">
					<label>Tgl Transaksi</label>
					<input type="text" class="datepickers" name="periode1" value="'.$periode1.'" required autocomplete="off">
					<input type="text" class="datepickers" name="periode2" value="'.$periode2.'" required autocomplete="off">
					<input type="hidden" name="btncari" value="cari">
					'.BTN_SUBMIT.'
				</form>
			';
				
				if ($_REQUEST['btncari']=="cari"){
					$qmember = "SELECT idpeserta,
															nama,
															nomorpk,
															tglakad,
															tgltransaksi,
															plafond,
															tenor,
															premirate,
															premirate_sys,
															premi,
															premi_sys,
															totalpremi,
															astotalpremi,
															tgllunas,
															statuslunas,
															ajkpolis.produk,
															ajkcabang.name as nmcabang,
															nopinjaman,
                              nomorpk,
															nm_kategori_profesi,
															(SELECT sum(nilaibayar) 
																FROM ajkbayar 
															WHERE ajkbayar.idpeserta = ajkpeserta.idpeserta and 
																		tipebayar = 'premibank' and 
																		del is null)as nilaibayar
											FROM ajkpeserta
											INNER JOIN ajkpolis
											ON ajkpolis.id = ajkpeserta.idpolicy
											INNER JOIN ajkcabang
											ON ajkcabang.er = ajkpeserta.cabang
											LEFT JOIN ajkprofesi
											ON ajkprofesi.ref_mapping = ajkpeserta.pekerjaan
											LEFT JOIN ajkkategoriprofesi
											ON ajkkategoriprofesi.id = ajkprofesi.idkategoriprofesi
											WHERE statusaktif = 'Inforce' and statuslunas != 1 and ajkpeserta.del is null ".$filter." ";
										
										$_SESSION['lprmemberarm'] = $thisEncrypter->encode($qmember);

					echo'
					<div class="panel panel-default">
						<a href="ajk.php?re=dlExcel&Rxls=lprmemberarm" target="_blank"><img src="../image/excel.png" width="20"><br>Excel</a>

						<form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped" id="">
									<thead>
										<tr>
											<th width="2%"><input type="checkbox" id="selectall"/></th>					
											<th width="5%">Nomor Pinjaman</th>
                      <th width="5%">No. Rekening</th>
											<th width="5%">Nama</th>					
											<th width="5%">Tgl Akad</th>
											<th width="5%">Tgl Transaksi</th>
											<th width="2%">Plafond</th>
											<th width="1%">Tenor</th>
											<th width="1%">Pekerjaan</th>
											<th width="1%">Premi</th>
											<th width="1%">Bayar</th>
											<th width="1%">Selisih</th>
											<th width="5%">Cabang</th>		
											<th width="5%">Action</th>			
										</tr>
									</thead>
									<tbody>';
										$metMember = $database->doQuery($qmember);
										while ($metMember_ = mysql_fetch_array($metMember)) {			
											$pes = $thisEncrypter->encode($metMember_['idpeserta']);
											$selisih = $metMember_['premi'] - $metMember_['nilaibayar'];
											$dataceklist = '<input type="checkbox" class="case" name="idtemp[]" value="'.$metMember_['idpeserta'].'">';
											echo '
											<tr>
												<td align="center">'.$dataceklist.' '.++$no.'</td>
												<td align="center">'.$metMember_['nopinjaman'].'</td>
                        <td align="center">'.$metMember_['nomorpk'].'</td>
												<td>'.$metMember_['nama'].'</td>
												<td align="center">'._convertDate($metMember_['tglakad']).'</td>
												<td align="center">'._convertDate($metMember_['tgltransaksi']).'</td>
												<td align="right">'.duit($metMember_['plafond']).'</td>
												<td align="center">'.$metMember_['tenor'].'</td>
												<td align="center">'.$metMember_['nm_kategori_profesi'].'</td>
												<td align="center">'.duit($metMember_['premi']).'</td>
												<td align="center">'.duit($metMember_['nilaibayar']).'</td>
												<td align="center">'.duit($selisih).'</td>
												<td>'.$metMember_['nmcabang'].'</td>
												<td><a href="ajk.php?re=arm&py=setpayment&id='.$pes.'" class="btn btn-primary">Payment</a></td>
											</tr>';  
														
										}
										echo '
									</tbody>
								</table>
							</div>
							<br>
							<label>Tgl Bayar</label>
							<input type="text" class="datepickers" name="tglbayar" required autocomplete="off">

							<div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>						 
						</form>
					</div>';
				}
		    echo '				
			</div>
		</div>
		</div>
		</div> 

		<script language="javascript">
			$(function(){
			    $("#selectall").click(function () {	$(\'.case\').attr(\'checked\', this.checked);	});			    // add multiple select / deselect functionality
			    $(".case").click(function(){																	// if all checkbox are selected, check the selectall checkbox	// and viceversa
			        if($(".case").length == $(".case:checked").length) {
			            $("#selectall").attr("checked", "checked");
			        } else {
			            $("#selectall").removeAttr("checked");
			        }

			    });
			});
		</script>';	
  break;
  
	case "uploadmembers":		
    echo '
    <script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>
    <style>
      blink {
        -webkit-animation: 2s linear infinite condemned_blink_effect; // for Safari 4.0 - 8.0
        animation: 2s linear infinite condemned_blink_effect;
      }
      @-webkit-keyframes condemned_blink_effect { // for Safari 4.0 - 8.0
        0% {
          visibility: hidden;
        }
        50% {
          visibility: hidden;
        }
        100% {
          visibility: visible;
        }
      }
      @keyframes condemned_blink_effect {
        0% {
          visibility: hidden;
        }
        50% {
          visibility: hidden;
        }
        100% {
          visibility: visible;
        }
      }
    </style>
			<div class="page-header-section"><h2 class="title semibold">Payment Outstanding</h2></div>
			
		</div>
		<div class="row">
			<div class="col-md-12">
        <form action="ajk.php?re=arm&py=uploadmembers" method="post" enctype="multipart/form-data">
					<label>Upload File Rekening Koran </label>
					<input type="file" name="fileupload" id="fileupload" class="form-control">					
					<input type="hidden" name="btnupload" value="upload">
					'.BTN_SUBMIT.'
				</form>';
				
				if ($_REQUEST['btnupload']=="upload"){		
          if(isset($_FILES['fileupload']['name'])){
            $file_name = $_FILES['fileupload']['name'];                  
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_name = $_FILES['fileupload']['tmp_name'];
            $file_info = pathinfo($file_name);
            $file_extension = $file_info["extension"];
            $namefile = $file_info["filename"].'.'.$file_extension;
            $inputFileName = $file_name;
            // echo $inputFileName;
            $_SESSION['file_temp'] = $namefile;
            $_SESSION['file_name'] = $_FILES['fileupload']['name'];											
                

            try {
              PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
              $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
              $objReader = PHPExcel_IOFactory::createReader($inputFileType);
              $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
              die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
            }	
            
            // //Table used to display the contents of the file
            // //Get worksheet dimensions

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $error=0;
            
            $detail = "";
            for ($row = 9; $row <= $highestRow; $row++) {
              //  Read a row of data into an array
              $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
              
              $i = 0;

              foreach($rowData[0] as $k=>$v){
                $data[$i] = $v;
                $i++;
              }
              
              $today = date('Y-m-d');
              $idpeserta = $data[0];
              $tglbayar = $data[1];
              $nilaibayar = $data[2];
              $keterangan = $data[3];
              $noref = $data[4];
				
              //validasi
                // 1. Validasi cabang
                $qcabang = "SELECT * FROM ajkcabang WHERE ref_mapping = '".$kdcabang."'";
                $rescabang = mysql_query($qcabang);

                if(mysql_num_rows($rescabang)>0){
                  $errorcabang = "";
                  $rowcabang = mysql_fetch_array($rescabang);
                  $kdcabang = $rowcabang['name'];
                }else{
                  $errorcabang = '<span class="label label-danger">Cabang Tidak terdapat di database</span>';                  
                  $error=1;
                }

                // 2. Validasi No Pinjaman
                $qpeserta = "SELECT *,(select sum(nilaibayar) from ajkbayar where ajkbayar.idpeserta = ajkpeserta.idpeserta and tipebayar = 'premibank')as bayar FROM ajkpeserta WHERE nopinjaman = '".$nopinjaman."' and del is null";
                $respeserta = mysql_query($qpeserta);

                if(mysql_num_rows($respeserta)>0){
                  $errornopinjaman = "";
                  // $rownopinjaman = mysql_fetch_array($respeserta);                  
                }else{
                  $errornopinjaman = '<span class="label label-danger">No Pinjaman Tidak terdapat di database</span>';                  
                  $error=1;
                }

                // 3. Validasi Nama
                $qpesertanama = "SELECT * FROM ajkpeserta WHERE nopinjaman = '".$nopinjaman."' and nama like '%".$nama."%'";
                $respesertanama = mysql_query($qpesertanama);

                if(mysql_num_rows($respesertanama)>0){
                  $errornama = "";
                  $rowpeserta = mysql_fetch_array($respeserta);
                }else{
                  $errornama = '<span class="label label-danger">Nama Tidak sesuai</span>';                  
                  $error=1;
                }

                // 4. Validasi Nama
                $qasuransi = "SELECT * FROM ajkinsurance WHERE name like '%".$asuransi."%'";
                $resasuransi = mysql_query($qasuransi);

                if(mysql_num_rows($resasuransi)>0){
                  $errorasuransi = "";
                  // $rownopinjaman = mysql_fetch_array($respeserta);                  
                }else{
                  $errorasuransi = '<span class="label label-danger">Nama Tidak sesuai</span>';                  
                  $error=1;
                }
              // end validasi              
              $detail .= '
              <tr>
                <td>'.++$no.'</td>
								<td>'.$kdcabang.' '.$errorcabang.'</td>								
                <td>'.$nopinjaman.' '.$errornopinjaman.'</td>
                <td>'.$nama.' '.$errornama.'</td>
								<td>'.$asuransi.' '.$errorasuransi.'</td>								
								<td>'.duit($rowpeserta['totalpremi']).'</td>
								<td>'.duit($premi).'</td>
								<td>'.duit($rowpeserta['bayar']).'</td>
								<td>'._convertDate($tglbayar).'</td>
                <td>'.$keterangan.'</td>
              </tr>';
            }
            if($error==0){
              move_uploaded_file($file_name, realpath(dirname(__FILE__)).'/temp/'.$namefile) or die( "Could not upload file!");
              
              $button = '<div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>';
            }else{
              $texterror = '<blink><h2 class="text-center text-danger">Harap Lengkapi yang error !!!</h2></blink>';
              $button = '';
            }
          }

					echo'
					<div class="panel panel-default">

						<form method="post" class="panel panel-color-top panel-default form-horizontal" action="ajk.php?re=arm&py=actionuploadmembers" data-parsley-validate enctype="multipart/form-data">
              <div class="table-responsive">
              '.$texterror.'
								<table class="table table-hover table-bordered table-striped" id="">
									<thead>
										<tr>
                      <!--<th width="2%"><input type="checkbox" id="selectall"/></th>-->
                      <th width="5%">No</th>
											<th width="5%">Cabang</th>
											<th width="5%">No Pinjaman</th>
                      <th width="5%">Nama</th>
											<th width="5%">Asuransi</th>
											<th width="5%">Premi System</th>
											<th width="5%">Premi yg diterima</th>
											<th width="5%">Nilai Bayar</th>
											<th width="5%">Tgl Bayar</th>
                      <th width="5%">Keterangan</th>
										</tr>
									</thead>
                  <tbody>
                  '.$detail.'
									</tbody>
								</table>
							</div>
              '.$button.'
              '.$texterror.'
						</form>
					</div>';
				}
		    echo '				
			</div>
		</div>
		</div>
		</div> 

		<script language="javascript">
			$(function(){
			    $("#selectall").click(function () {	$(\'.case\').attr(\'checked\', this.checked);	});			    // add multiple select / deselect functionality
			    $(".case").click(function(){																	// if all checkbox are selected, check the selectall checkbox	// and viceversa
			        if($(".case").length == $(".case:checked").length) {
			            $("#selectall").attr("checked", "checked");
			        } else {
			            $("#selectall").removeAttr("checked");
			        }

			    });
			});
		</script>';	
	break;

  case "uploadmembersnew":		
    echo '
    <script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>
    <style>
      blink {
        -webkit-animation: 2s linear infinite condemned_blink_effect; // for Safari 4.0 - 8.0
        animation: 2s linear infinite condemned_blink_effect;
      }
      @-webkit-keyframes condemned_blink_effect { // for Safari 4.0 - 8.0
        0% {
          visibility: hidden;
        }
        50% {
          visibility: hidden;
        }
        100% {
          visibility: visible;
        }
      }
      @keyframes condemned_blink_effect {
        0% {
          visibility: hidden;
        }
        50% {
          visibility: hidden;
        }
        100% {
          visibility: visible;
        }
      }
    </style>
			<div class="page-header-section"><h2 class="title semibold">Payment Outstanding</h2></div>
			
		</div>
		<div class="row">
			<div class="col-md-12">
        <form action="ajk.php?re=arm&py=uploadmembersnew" method="post" enctype="multipart/form-data">
					<label>Upload File Rekening Koran </label>
					<br>
          <a href="../myFiles/Pembayaran Premi Bank.xlsx" target="_blank"><img src="../image/excel.png" width="20"><br>Download File Upload</a>
					<input type="file" name="fileupload" id="fileupload" class="form-control">					
					<input type="hidden" name="btnupload" value="upload">
					'.BTN_SUBMIT.'
				</form>';
				
				if ($_REQUEST['btnupload']=="upload"){		
          if(isset($_FILES['fileupload']['name'])){
            $file_name = $_FILES['fileupload']['name'];                  
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_name = $_FILES['fileupload']['tmp_name'];
            $file_info = pathinfo($file_name);
            $file_extension = $file_info["extension"];
            $namefile = $file_info["filename"].'.'.$file_extension;
            $inputFileName = $file_name;
            // echo $inputFileName;
            $_SESSION['file_temp'] = $namefile;
            $_SESSION['file_name'] = $_FILES['fileupload']['name'];											
                

            try {
              PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
              $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
              $objReader = PHPExcel_IOFactory::createReader($inputFileType);
              $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
              die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
            }	
            
            // //Table used to display the contents of the file
            // //Get worksheet dimensions

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $error=0;
            
            $detail = "";
            for ($row = 2; $row <= $highestRow; $row++) {
              //  Read a row of data into an array
              $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
              
              $i = 0;

              foreach($rowData[0] as $k=>$v){
                $data[$i] = $v;
                $i++;
              }
              
              $today = date('Y-m-d');
              $idpeserta = $data[0];
              $tglbayar = $data[1];
              $nilaibayar = $data[2];
              $keterangan = $data[3];					

              //validasi
                // 1. Validasi No Pinjaman
                $qpeserta = "
                SELECT ajkcabang.name as nmcabang,ajkpeserta.nama,ajkpeserta.totalpremi,ifnull((select sum(nilaibayar) from ajkbayar where ajkbayar.idpeserta = ajkpeserta.idpeserta and tipebayar = 'premibank'),0)as bayar 
                FROM ajkpeserta 
                INNER JOIN ajkcabang ON ajkcabang.er = ajkpeserta.cabang
                WHERE idpeserta = '".$idpeserta."' and ajkpeserta.del is null";
                
                $respeserta = mysql_query($qpeserta);
                $_peserta = mysql_fetch_array($respeserta);
                $kdcabang = $_peserta['nmcabang'];
                $nama = $_peserta['nama'];
                $totalpremi = $_peserta['totalpremi'];
                $nilaisettle = $_peserta['bayar'];

                if(mysql_num_rows($respeserta)>0){
                  $erroridpeserta = "";
                  // $rownopinjaman = mysql_fetch_array($respeserta);                  
                }else{
                  $erroridpeserta = '<span class="label label-danger">Id Peserta Tidak terdapat di database</span>';                  
                  $error=1;
                }
                
                if($totalpremi != $nilaibayar){
                  // $errorpremi = '<span class="label label-danger">Premi Berbeda dengan sistem</span>';
                  // $error=1;
                }else{
                  $errorpremi = "";
                }

                if($nilaibayar == $totalpremi){
                  $errorbayar = '<span class="label label-danger">Premi telah dibayar</span>';
                  $error=1;
                }else{
                  $errorbayar = "";
                }

                if($nilaibayar+$nilaisettle >= $totalpremi){
                  $errorpremi = '<span class="label label-danger">Pembayaran Melebihi tagihan Premi</span>';
                }else{
                  $errorpremi = "";
                }
                
              // end validasi              
              $detail .= '
              <tr>
                <td>'.++$no.'</td>
                <td>'.$idpeserta.' '.$erroridpeserta.'</td>
								<td>'.$kdcabang.'</td>								                
                <td>'.$nama.'</td>
								<td>'.duitkoma($totalpremi).'</td>
								<td>'.duitkoma($nilaibayar).' '.$errorpremi.'</td>
								<td>'.duitkoma($nilaisettle).'  '.$errorbayar.'</td>
								<td>'._convertDate($tglbayar).'</td>
                <td>'.$keterangan.'</td>
              </tr>';
            }

            if($error==0){
              move_uploaded_file($file_name, realpath(dirname(__FILE__)).'/temp/'.$namefile) or die( "Could not upload file!");
              
              $button = '<div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>';
            }else{
              $texterror = '<blink><h2 class="text-center text-danger">Harap Lengkapi yang error !!!</h2></blink>';
              $button = '';
            }
          }

					echo'
					<div class="panel panel-default">

						<form method="post" class="panel panel-color-top panel-default form-horizontal" action="ajk.php?re=arm&py=actionuploadmembersnew" data-parsley-validate enctype="multipart/form-data">
              <div class="table-responsive">
              '.$texterror.'
								<table class="table table-hover table-bordered table-striped" id="">
									<thead>
										<tr>
                      <!--<th width="2%"><input type="checkbox" id="selectall"/></th>-->
                      <th width="5%">No</th>
                      <th width="5%">Idpeserta</th>
											<th width="5%">Cabang</th>											
                      <th width="5%">Nama</th>
											<th width="5%">Premi System</th>
											<th width="5%">Premi yg diterima</th>
											<th width="5%">Nilai Bayar</th>
											<th width="5%">Tgl Bayar</th>
                      <th width="5%">Keterangan</th>
										</tr>
									</thead>
                  <tbody>
                  '.$detail.'
									</tbody>
								</table>
							</div>
              '.$button.'
              '.$texterror.'
						</form>
					</div>';
				}
		    echo '				
			</div>
		</div>
		</div>
		</div> 

		<script language="javascript">
			$(function(){
			    $("#selectall").click(function () {	$(\'.case\').attr(\'checked\', this.checked);	});			    // add multiple select / deselect functionality
			    $(".case").click(function(){																	// if all checkbox are selected, check the selectall checkbox	// and viceversa
			        if($(".case").length == $(".case:checked").length) {
			            $("#selectall").attr("checked", "checked");
			        } else {
			            $("#selectall").removeAttr("checked");
			        }

			    });
			});
		</script>';	
	break;

  case "actionuploadmembers":
  
    $file = $_SESSION['file_temp'];
    $inputFileName = realpath(dirname(__FILE__)).'/temp/'.$file;
      
    try {
      PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
      $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($inputFileName);
    } catch (Exception $e) {
      die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
    }	
    
    // //Table used to display the contents of the file
    // //Get worksheet dimensions

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // mysql_query('START TRANSACTION');
    for ($row = 9; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
      
      $i = 0;

      foreach($rowData[0] as $k=>$v){
        $data[$i] = $v;
        $i++;
      }
      
      $today = date('Y-m-d');
      $keterangan = $data[3];
			$premi = $data[5];
			$tglbayar = $data[1];
      $arr = (explode("/",$keterangan));
      $kdcabang = $arr[0];
      $nopinjaman = $arr[1];
      $nama = $arr[2];
      $asuransi = $arr[3];  

      $peserta = mysql_fetch_array(mysql_query("select * from ajkpeserta where nopinjaman = '".$nopinjaman."'"));

      $query = "
      INSERT INTO ajkbayar 
      SET nopinjaman = '".$nopinjaman."', 
          idpeserta = '".$peserta['idpeserta']."', 
          tipebayar = 'premibank',
          nilaibayar='".$premi."',
          tglbayar = '"._convertDate2($tglbayar)."',
          input_by='".$q['id']."',
          input_date = '.$today.'";
      // echo $query.'<br>';
      mysql_query($query);      
    }  
    echo '<meta http-equiv="refresh" content="5; url=ajk.php?re=arm&py=uploadmembers">
    <div class="alert alert-dismissable alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
    <strong>Upload Success!</div>';

  break;

  case "actionuploadmembersnew":
  
    $file = $_SESSION['file_temp'];
    $inputFileName = realpath(dirname(__FILE__)).'/temp/'.$file;
      
    try {
      PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
      $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($inputFileName);
    } catch (Exception $e) {
      die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
    }	
    
    // //Table used to display the contents of the file
    // //Get worksheet dimensions

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // mysql_query('START TRANSACTION');
    for ($row = 2; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
      
      $i = 0;

      foreach($rowData[0] as $k=>$v){
        $data[$i] = $v;
        $i++;
      }
      
      $today = date('Y-m-d');
      
      $idpeserta = $data[0];
      $tglbayar = $data[1];
      $nilaibayar = $data[2];
      $keterangan = $data[3];
      $tipe = 'Premi';

      if($tipe=="Premi"){
        $tipebayar = 'premibank';
        $premi = $data[5];
      }elseif($tipe=="Asuransi"){
        $tipebayar = 'premiasuransi';
        $premi = $data[4];
      }elseif($tipe=="Restorno"){
        $tipebayar = 'restornobank';
        $premi = $data[4];
      }else{
        continue;
      }
      $peserta = mysql_fetch_array(mysql_query("select * from ajkpeserta where idpeserta = '".$idpeserta."'"));

      $query = "
      INSERT INTO ajkbayar 
      SET idpeserta = '".$idpeserta."', 
          tipebayar = '".$tipebayar."',
          nilaibayar='".$nilaibayar."',
          tglbayar = '".$tglbayar."',
          keterangan = '".$keterangan."',
          input_by='".$q['id']."',
          input_date = '".$today."'";
      // echo $query.'<br>';
      mysql_query($query);      
      unlink(realpath(dirname(__FILE__)).'/temp/'.$file);
    }  
    echo '<meta http-equiv="refresh" content="5; url=ajk.php?re=arm&py=uploadmembersnew">
    <div class="alert alert-dismissable alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
    <strong>Upload Success!</div>';

	break;
	
	case "uploadins":		
    echo '
    <script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>
    <style>
      blink {
        -webkit-animation: 2s linear infinite condemned_blink_effect; // for Safari 4.0 - 8.0
        animation: 2s linear infinite condemned_blink_effect;
      }
      @-webkit-keyframes condemned_blink_effect { // for Safari 4.0 - 8.0
        0% {
          visibility: hidden;
        }
        50% {
          visibility: hidden;
        }
        100% {
          visibility: visible;
        }
      }
      @keyframes condemned_blink_effect {
        0% {
          visibility: hidden;
        }
        50% {
          visibility: hidden;
        }
        100% {
          visibility: visible;
        }
      }
    </style>
			<div class="page-header-section"><h2 class="title semibold">Payment Insurance</h2></div>
			
		</div>
		<div class="row">
			<div class="col-md-12">
        <form action="ajk.php?re=arm&py=uploadins" method="post" enctype="multipart/form-data">
          <label>Upload File Pembayaran Asuransi</label>
          <br>
          <a href="../myFiles/Upload Pembayaran Premi Asuransi.xlsx" target="_blank"><img src="../image/excel.png" width="20"><br>Download File Upload</a>
					<input type="file" name="fileupload" id="fileupload" class="form-control">					
					<input type="hidden" name="btnupload" value="upload">
					'.BTN_SUBMIT.'
				</form>';
				
				if ($_REQUEST['btnupload']=="upload"){		
          if(isset($_FILES['fileupload']['name'])){
            $file_name = $_FILES['fileupload']['name'];                  
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_name = $_FILES['fileupload']['tmp_name'];
            $file_info = pathinfo($file_name);
            $file_extension = $file_info["extension"];
            $namefile = $file_info["filename"].'.'.$file_extension;
            $inputFileName = $file_name;
            // echo $inputFileName;
            $_SESSION['file_temp'] = $namefile;
            $_SESSION['file_name'] = $_FILES['fileupload']['name'];											
                

            try {
              PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
              $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
              $objReader = PHPExcel_IOFactory::createReader($inputFileType);
              $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
              die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
            }	
            
            // //Table used to display the contents of the file
            // //Get worksheet dimensions

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $error=0;
            
            $detail = "";
            for ($row = 2; $row <= $highestRow; $row++) {
              //  Read a row of data into an array
              $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
              
              $i = 0;

              foreach($rowData[0] as $k=>$v){
                $data[$i] = $v;
                $i++;
              }
              
              $today = date('Y-m-d');
              $idpeserta = $data[0];
              $tglbayar = $data[1];
							$bayar = $data[2];
							// $keterangan = $data[4];

              //validasi
                // 1. Validasi cabang
                $qpeserta = "SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."' and del is null and statusaktif = 'Inforce'";
                $respeserta = mysql_query($qpeserta);

                if(mysql_num_rows($respeserta)>0){
                  $errorpeserta = "";
                  $rowpeserta = mysql_fetch_array($respeserta);
                  $nmpeserta = $rowpeserta['nama'];
                }else{
                  $errorpeserta = '<span class="label label-danger">Id Peserta Tidak terdapat di database</span>';                  
                  $error=1;
                }

              
              
              // end validasi              
              $detail .= '
              <tr>
                <td>'.++$no.'</td>
                <td>'.$idpeserta.' '.$errorpeserta.'</td>                
                <td>'.$nmpeserta.'</td>
                <td>'.$tglbayar.'</td>
                <td>'.duit($bayar).'</td>                
              </tr>';
            }
            if($error==0){
              move_uploaded_file($file_name, realpath(dirname(__FILE__)).'/temp/'.$namefile) or die( "Could not upload file!");
              
              $button = '<div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>';
            }else{
              $texterror = '<blink><h2 class="text-center text-danger">Harap Lengkapi yang error !!!</h2></blink>';
              $button = '';
            }
          }

					echo'
					<div class="panel panel-default">

						<form method="post" class="panel panel-color-top panel-default form-horizontal" action="ajk.php?re=arm&py=actionuploadins" data-parsley-validate enctype="multipart/form-data">
              <div class="table-responsive">
              '.$texterror.'
								<table class="table table-hover table-bordered table-striped" id="">
									<thead>
										<tr>
                      <th width="5%">No</th>
											<th width="5%">Id Peserta</th>
											<th width="5%">Nama</th>
                      <th width="5%">Tgl Bayar</th>
											<th width="5%">Nilai Bayar</th>
										</tr>
									</thead>
                  <tbody>
                  '.$detail.'
									</tbody>
								</table>
							</div>
              '.$button.'
              '.$texterror.'
						</form>
					</div>';
				}
		    echo '				
			</div>
		</div>
		</div>
		</div> 

		<script language="javascript">
			$(function(){
			    $("#selectall").click(function () {	$(\'.case\').attr(\'checked\', this.checked);	});			    // add multiple select / deselect functionality
			    $(".case").click(function(){																	// if all checkbox are selected, check the selectall checkbox	// and viceversa
			        if($(".case").length == $(".case:checked").length) {
			            $("#selectall").attr("checked", "checked");
			        } else {
			            $("#selectall").removeAttr("checked");
			        }

			    });
			});
		</script>';	
	break;

  case "actionuploadins":
  
    $file = $_SESSION['file_temp'];
    $inputFileName = realpath(dirname(__FILE__)).'/temp/'.$file;
      
    try {
      PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
      $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($inputFileName);
    } catch (Exception $e) {
      die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME).'":'.$e->getMessage());
    }	
    
    // //Table used to display the contents of the file
    // //Get worksheet dimensions

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // mysql_query('START TRANSACTION');
    for ($row = 2; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
      
      $i = 0;

      foreach($rowData[0] as $k=>$v){
        $data[$i] = $v;
        $i++;
      }
      
      $today = date('Y-m-d');
      $idpeserta = $data[0];
      $tglbayar = $data[1];
      $bayar = $data[2];

      // $peserta = mysql_fetch_array(mysql_query("select * from ajkpeserta where nopinjaman = '".$nopinjaman."'"));

      $query = "
      UPDATE ajkpeserta 
      SET stsbayaras = 1,
          tglbayaras = '".$tglbayar."',
          nilaibayaras = '".$bayar."',
      WHERE idpeserta = '".$idpeserta."' and 
      del is null and 
      statusaktif = 'Inforce'";

      // $query = "
      // INSERT INTO ajkbayar 
      // SET nopinjaman = '".$nopinjaman."', 
      //     idpeserta = '".$peserta['idpeserta']."', 
      //     tipebayar = 'premibank',
      //     nilaibayar='".$premi."',
      //     tglbayar = '"._convertDate2($tglbayar)."',
      //     input_by='".$q['id']."',
      //     input_date = '.$today.'";
      // echo $query.'<br>';
      mysql_query($query);      
    }  
    echo '<meta http-equiv="refresh" content="5; url=ajk.php?re=arm&py=uploadins">
    <div class="alert alert-dismissable alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
    <strong>Upload Success!</div>';

  break;

	case "ins":
		$qmember = "SELECT idpeserta,
											 nama,
											 nomorpk,
											 tglakad,
											 plafond,
											 tenor,
											 premirate,
											 premirate_sys,
											 premi,
											 premi_sys,
											 totalpremi,
											 astotalpremi,
											 tgllunas,
											 statuslunas,
											 ajkpolis.produk,
											 ajkcabang.name as nmcabang,
											 nopinjaman,
											 nm_kategori_profesi,
											 (SELECT sum(nilaibayar) 
											 	FROM ajkbayar 
												WHERE ajkbayar.idpeserta = ajkpeserta.idpeserta and 
															tipebayar !='premibank' and del is null)as nilaibayar
								FROM ajkpeserta
								INNER JOIN ajkpolis
								ON ajkpolis.id = ajkpeserta.idpolicy
								INNER JOIN ajkcabang
								ON ajkcabang.er = ajkpeserta.cabang
								LEFT JOIN ajkprofesi
								ON ajkprofesi.ref_mapping = ajkpeserta.pekerjaan
								LEFT JOIN ajkkategoriprofesi
								ON ajkkategoriprofesi.id = ajkprofesi.idkategoriprofesi
								WHERE stsbayaras is null and approve_by is not null and ajkpeserta.del is null and ajkpeserta.iddn is not null";;
		$_SESSION['lprmemberarm'] = $thisEncrypter->encode($qmember);

		if ($_REQUEST['btnsubmit']=="submit") {
			$query = '';
			foreach($_REQUEST['idtemp'] as $k => $val){				
				$qpes = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$val."' and del is null"));
				$qas = mysql_fetch_array(mysql_query("SELECT * FROM ajkinsurance WHERE id = '".$qpes['asuransi']."'"));
				$qbyr = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as nilaibayar FROM ajkbayar WHERE idpeserta = '".$val."' and tipebayar='premiasuransi' and del is null"));
				$qbyrcadklaim = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as nilaibayar FROM ajkbayar WHERE idpeserta = '".$val."' and tipebayar='cadklaimasuransi' and del is null"));
				$qbyrcadpremi = mysql_fetch_array(mysql_query("SELECT sum(nilaibayar)as nilaibayar FROM ajkbayar WHERE idpeserta = '".$val."' and tipebayar='cadpremiasuransi' and del is null"));

				$premibayarcadklaim = round($qpes['astotalpremi']*$qas['cad_klaim']/100 - $qbyrcadklaim['nilaibayar'],2);
				$premibayarcadpremi = round($qpes['astotalpremi']*$qas['cad_premi']/100 - $qbyrcadklaim['nilaibayar'],2);
				
				$premibayar = round($qpes['astotalpremi'] - $premibayarcadklaim - $premibayarcadpremi - $qbyr['nilaibayar'],2);
				
	    	$query2 = " INSERT ajkbayar 
										SET nopinjaman ='".$qpes['nopinjaman']."',
												idpeserta = '".$val."',
												tipebayar = 'premiasuransi',
												nilaibayar = '".$premibayar."',
												tglbayar = '".$today."',
												keterangan = 'Pembayaran Premi Asuransi',
												input_by = '".$q['id']."',
												input_date = '".$today."'";	

				$query3 = " INSERT ajkbayar 
										SET nopinjaman ='".$qpes['nopinjaman']."',
												idpeserta = '".$val."',
												tipebayar = 'cadklaimasuransi',
												nilaibayar = '".$premibayarcadklaim."',
												tglbayar = '".$today."',
												keterangan = 'Pembayaran Cadangan Klaim Asuransi',
												input_by = '".$q['id']."',
												input_date = '".$today."'";	

				$query4 = " INSERT ajkbayar 
										SET nopinjaman ='".$qpes['nopinjaman']."',
												idpeserta = '".$val."',
												tipebayar = 'cadpremiasuransi',
												nilaibayar = '".$premibayarcadpremi."',
												tglbayar = '".$today."',
												keterangan = 'Pembayaran Cadangan Premi Asuransi',
												input_by = '".$q['id']."',
												input_date = '".$today."'";	

				mysql_query($query2);
				mysql_query($query3);
				mysql_query($query4);
			}
			echo '<meta http-equiv="refresh" content="2; url=ajk.php?re=arm&py=ins">
			  <div class="alert alert-dismissable alert-success">
			  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
			  <strong>Insurance Payment Success!</div>';
		}
		
		echo '<script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>';
		echo '<script>
				$(function(){
				  $(".datepicker").datepicker({dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});
				});
			  </script>';
			   
		echo '
			<div class="page-header-section"><h2 class="title semibold">Payment To Insurance</h2></div>
			<div class="page-header-section"></div>
		</div>
		<div class="row">
		<div class="col-md-12">
		<div class="panel panel-default">
		

		<form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
		<div class="table-responsive">
		<table class="table table-hover table-bordered table-striped" id="">
			<thead>
				<tr>
					<th width="2%"><input type="checkbox" id="selectall"/></th>					
					<th width="5%">ID Peserta</th>
					<th width="5%">Nomor Pinjaman</th>
					<th width="5%">Nama</th>					
					<th width="10%">Tgl Akad</th>
					<th width="2%">Plafond</th>
					<th width="1%">Tenor</th>
					<th width="1%">Pekerjaan</th>
					<th width="1%">Premi</th>
					<th width="1%">Premi As</th>
					<th width="1%">Nilai Bayar As</th>
					<th width="5%">Cabang</th>
					<th width="5%">Action</th>
				</tr>
			</thead>
			<tbody>';

		$metMember = $database->doQuery($qmember);
		while ($metMember_ = mysql_fetch_array($metMember)) {			
			$pes = $thisEncrypter->encode($metMember_['idpeserta']);
			$dataceklist = '<input type="checkbox" class="case" name="idtemp[]" value="'.$metMember_['idpeserta'].'">';
			echo '
			<tr>
				<td align="center">'.$dataceklist.' '.++$no.'</td>
				<td align="center">'.$metMember_['idpeserta'].'</td>
		   	<td align="center">'.$metMember_['nopinjaman'].'</td>
		   	<td>'.$metMember_['nama'].'</td>
		   	<td align="center">'._convertDate($metMember_['tglakad']).'</td>
		   	<td align="right">'.duit($metMember_['plafond']).'</td>
		   	<td align="center">'.$metMember_['tenor'].'</td>
				<td align="center">'.$metMember_['nm_kategori_profesi'].'</td>
				<td align="center">'.duit($metMember_['totalpremi']).'</td>
				<td align="center">'.duit($metMember_['astotalpremi']).'</td>
				<td align="center">'.duit($metMember_['nilaibayar']).'</td>
				<td>'.$metMember_['nmcabang'].'</td>
				<td><a href="ajk.php?re=arm&py=setpaymentins&id='.$pes.'" class="btn btn-primary">Payment</a></td>
	    </tr>';  
						
		}
				echo '
							</tbody>
						 </table>
						 </div>
		       	<div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>
		       	</form>
		    	</div>
		    				
			    </div>

				</div>
		  </div>
		</div> 

		<script language="javascript">
			$(function(){
			    $("#selectall").click(function () {	$(\'.case\').attr(\'checked\', this.checked);	});			    // add multiple select / deselect functionality
			    $(".case").click(function(){																	// if all checkbox are selected, check the selectall checkbox	// and viceversa
			        if($(".case").length == $(".case:checked").length) {
			            $("#selectall").attr("checked", "checked");
			        } else {
			            $("#selectall").removeAttr("checked");
			        }

			    });
			});
		</script>';	
	break;

	default:
		echo '
		<div class="page-header-section"><h2 class="title semibold">Payment Debit Note</h2></div>
		<div class="page-header-section"></div>
		</div>
		<div class="row">
		<div class="col-md-12">
		<div class="panel panel-default">

		<table class="table table-hover table-bordered table-striped table-responsive" id="column-filtering">
			<thead>
				<tr>
				<th width="1%">No</th>
					<th>Partner</th>
					<th>Product</th>
					<th width="1%">Date DN</th>
					<th>Debit Note</th>
					<th width="1%">Members</th>
					<th width="1%">Nett Premium</th>
					<th width="10%">Status</th>
					<th width="10%">Date Paid</th>
					<th width="10%">Branch</th>
				</tr>
			</thead>
			<tbody>';

		$metDebitnote = $database->doQuery('SELECT
		Count(ajkpeserta.nama) AS jData,
		ajkcobroker.`name` AS namebroker,
		ajkclient.`name` AS nameclient,
		ajkpolis.produk,
		ajkcabang.`name` AS cabang,
		ajkdebitnote.id,
		ajkdebitnote.nomordebitnote,
		ajkdebitnote.premiclient,
		ajkdebitnote.premiasuransi,
		ajkdebitnote.paidstatus,
		ajkdebitnote.paidtanggal,
		ajkdebitnote.tgldebitnote
		FROM ajkdebitnote
		INNER JOIN ajkpeserta ON ajkdebitnote.id = ajkpeserta.iddn
		INNER JOIN ajkcobroker ON ajkdebitnote.idbroker = ajkcobroker.id
		INNER JOIN ajkclient ON ajkdebitnote.idclient = ajkclient.id
		INNER JOIN ajkpolis ON ajkdebitnote.idproduk = ajkpolis.id
		INNER JOIN ajkcabang ON ajkdebitnote.idcabang = ajkcabang.er
		WHERE ajkdebitnote.del IS NULL '.$q___3.'
		GROUP BY ajkdebitnote.id
		ORDER BY ajkdebitnote.id DESC');
		while ($metDebitnote_ = mysql_fetch_array($metDebitnote)) {
			if ($metDebitnote_['paidstatus']=="Unpaid") {
				$metPaid_ = '<span class="label label-inverse">'.$metDebitnote_['paidstatus'].'</span>';
			}elseif ($metDebitnote_['paidstatus']=="Paid*") {
				$metPaid_ = '<span class="label label-danger">'.$metDebitnote_['paidstatus'].'</span>';
			}else{
				$metPaid_ = '<span class="label label-success">'.$metDebitnote_['paidstatus'].'</span>';
			}
			echo '<tr>
			   	<td align="center">'.++$no.'</td>
			   	<td>'.$metDebitnote_['nameclient'].'</td>
			   	<td align="center">'.$metDebitnote_['produk'].'</td>
			   	<td align="center">'._convertDate($metDebitnote_['tgldebitnote']).'</td>
			   	<td><a href="ajk.php?re=dlPdf&pID='.$thisEncrypter->encode($metDebitnote_['nomordebitnote']).'&idd='.$thisEncrypter->encode($metDebitnote_['id']).'" target="_blank">'.$metDebitnote_['nomordebitnote'].'</a></td>
			   	<td align="center"><a href="ajk.php?re=dlPdf&pdf=member&pID='.$thisEncrypter->encode($metDebitnote_['nomordebitnote']).'&idd='.$thisEncrypter->encode($metDebitnote_['id']).'" target="_blank">'.$metDebitnote_['jData'].'</a></td>
			   	<td align="right">'.duit($metDebitnote_['premiclient']).'</td>
			   	<td align="center"><a href="ajk.php?re=arm&py=debitnote&idpay='.$thisEncrypter->encode($metDebitnote_['id']).'">'.$metPaid_.'</a></td>
			   	<td align="center">'._convertDate($metDebitnote_['paidtanggal']).'</td>
			   	<td>'.$metDebitnote_['cabang'].'</td>
			    </tr>';
		}
				echo '
							</tbody>
							<tfoot>
				        <tr>
					        <th><input type="hidden" class="form-control" name="search_engine"></th>
			            <th><input type="search" class="form-control" name="search_engine" placeholder="Partner"></th>
			            <th><input type="search" class="form-control" name="search_engine" placeholder="Product"></th>
			            <th><input type="hidden" class="form-control" name="search_engine"></th>
			            <th><input type="search" class="form-control" name="search_engine" placeholder="Debit Note"></th>
			            <th><input type="hidden" class="form-control" name="search_engine"></th>
			            <th><input type="hidden" class="form-control" name="search_engine"></th>
			            <th><input type="search" class="form-control" name="search_engine" placeholder="Status"></th>
			            <th><input type="hidden" class="form-control" name="search_engine"></th>
			            <th><input type="search" class="form-control" name="search_engine"></th>
				        </tr>
			        </tfoot>
		       	</table>
		    	</div>
				</div>
		  </div>
		</div>';
} // switch
echo '</div>
		<a href="#" class="totop animation" data-toggle="waypoints totop" data-showanim="bounceIn" data-hideanim="bounceOut" data-offset="50%"><i class="ico-angle-up"></i></a>
    </section>';
?>