<?php
include "../param.php";
include_once('../includes/functions.php');
if (isset($_REQUEST['type'])) {
    $typedata = $_REQUEST['type'];
    $typedata = AES::decrypt128CBC($typedata, ENCRYPTION_KEY);
} else {
    header("location:../dashboard");
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<?php
_head($user, $namauser, $photo, $logo);
?>
<style type="text/css">

.modal-backdrop {
    position: fixed;
    z-index: 1000000000 !important;
}

.modal {
    position: fixed !important;
    z-index: 20000000000000 !important;
}

#myModal .modal-dialog{
    overflow-y: initial !important
}
#myModal .modal-body{
    height: 350px;
    overflow-y: auto;
}
</style>

<body>
	<!-- begin #page-loader -->
	<div id="page-loader" class="page-loader fade in"><span class="spinner">Loading...</span></div>
	<!-- end #page-loader -->

	<!-- begin #page-container -->
	<div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-two-sidebar page-with-footer page-with-top-menu page-without-sidebar">
		<?php
        _header($user, $namauser, $photo, $logo, $logoklient);
        _sidebar($user, $namauser, '', '');
        ?>
		<!-- begin #content -->
		<div id="content" class="content">
				<?php
      if ($typedata == 'peserta') {
          ?>
					<div class="panel p-30">
						<h4 class="m-t-0">Data Peserta</h4>
						<div class="section-container section-with-top-border">
					    <form action="#" id="form-peserta" class="form-horizontal" method="post" enctype="multipart/form-data">
		            <table id="data-peserta" class="table table-bordered table-hover" width="100%">
		              <thead>
										<tr class="primary">
											<th>No</th>
                      <th>Asuransi</th>
											<th>Produk</th>
                      <th>Pekerjaan</th>
                      <th>Cover</th>
											<th>No Perjanjian Kredit</th>
											<th>ID Pesserta</th>
											<th>Sertifikat</th>
											<th>Nama</th>
											<th>Tgl. Lahir</th>
											<th>Umur</th>
											<th>Plafond</th>
											<th>Tgl. Akad</th>
											<th>Tenor</th>
											<th>Tgl. Akhir</th>
											<th>Premi</th>
                      <th>Medical</th>
											<th>Status</th>
											<th>Cabang</th>
                      <th>SPPA</th>
                      <th>KTP</th>
                      <th>Batal</th>
                      <th>Tgl. Input</th>
										</tr>
									</thead>
		              <tbody>

		              </tbody>
		            </table>
			        </form>
		        </div>

		          <!-- end section-container -->
		      </div>
      	<?php
      } elseif ($typedata == 'debitnote') {
      	?>
				<div class="panel p-30">
					<h4 class="m-t-0">Data Nota Debit</h4>
					<div class="section-container section-with-top-border">
						<form action="#" id="form-debitnote" class="form-horizontal" method="post" enctype="multipart/form-data">
							<table id="data-debitnote" class="table table-bordered table-hover" width="100%">
								<thead>
									<tr class="warning">
										<th>No</th>
										<th>Produk</th>
										<th>Tgl. DN</th>
										<th>Nota Debit</th>
										<th>Peserta</th>
                    <th>Feebase</th>
										<th>Premi</th>
										<th>Status</th>
										<th>Tgl. Bayar</th>
										<th>Cabang</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</form>
					</div>
					<!-- end section-container -->
		    </div>

				<?php
      } elseif ($typedata=="pesertaSPK") {
        ?>
				<div class="panel p-30">
				<h4 class="m-t-0">Data Peserta SPK</h4>
				<div class="section-container section-with-top-border">
				    <form action="#" id="form-debitnote" class="form-horizontal" method="post" enctype="multipart/form-data">
	                    <table id="data-pesertaspk" class="table table-bordered table-hover" width="100%">
                        <thead>
							<tr class="warning">
								<th>No</th>
								<th>Produk</th>
								<th>Status</th>
								<th>Partner</th>
								<th>SPK</th>
								<th>Nama</th>
								<th>KTP</th>
								<th>Tgl Lahir</th>
								<th>Usia</th>
								<th>Alamat</th>
								<th>Awal Asuransi</th>
								<th>Tenor (bln)</th>
								<th>Akhir Asuransi</th>
								<th>Plafond</th>
								<th>Premi</th>
								<th>EM(%)</th>
								<th>Premi EM</th>
								<th>Total Premi</th>
								<th>Grace Period</th>
								<th>Cabang</th>
								<th>Staff</th>
								<th>Tgl Input</th>
								<th>Tgl Approve</th>
							</tr>
						</thead>
           	<tbody>
                        <?php

							$queryspk = mysql_query('SELECT
							ajkcobroker.`name` AS broker,
							ajkclient.`name` AS perusahaan,
							ajkpolis.produk AS produk,
							ajkspk.id,
							ajkspk.idbroker,
							ajkspk.idpartner,
							ajkspk.idproduk,
							ajkspk.nomorspk,
							ajkspk.statusspk,
							ajkspk.statusnote,
							ajkspk.nomorktp,
							ajkspk.nama,
							IF(ajkspk.jeniskelamin="M", "Laki-laki","Perempuan") AS jnskelamin,
							ajkspk.dob,
							ajkspk.usia,
							ajkspk.alamat,
							ajkspk.pekerjaan,
							ajkspk.plafond,
							ajkspk.tglakad,
							ajkspk.tenor,
							ajkspk.tglakhir,
							ajkspk.mppbln,
							ajkspk.em,
							ajkspk.premi,
							ajkspk.premiem,
							IF(ajkspk.nettpremi IS NULL, ajkspk.premi, ajkspk.nettpremi) AS totalpremiSPK,
							ajkspk.photodebitur1,
							ajkspk.photodebitur2,
							ajkspk.photoktp,
							ajkspk.photosk,
							ajkspk.ttddebitur,
							ajkspk.ttdmarketing,
							ajkcabang.`name` AS cabang,
							userinput.firstname AS userinput,
							DATE_FORMAT(ajkspk.input_date, "%Y-%m-%d") AS tglinput,
							userapprove.firstname AS userapprove,
							DATE_FORMAT(ajkspk.approve_date, "%Y-%m-%d") AS tglapprove
							FROM ajkspk
							INNER JOIN ajkcobroker ON ajkspk.idbroker = ajkcobroker.id
							INNER JOIN ajkclient ON ajkspk.idpartner = ajkclient.id
							INNER JOIN ajkpolis ON ajkspk.idproduk = ajkpolis.id
							INNER JOIN ajkcabang ON ajkspk.cabang = ajkcabang.er
							INNER JOIN useraccess AS userinput ON ajkspk.input_by = userinput.id
							LEFT JOIN useraccess AS userapprove ON ajkspk.approve_by = userapprove.id
							WHERE ajkspk.idbroker = "'.$idbro.'" AND
								  ajkspk.idpartner = "'.$idclient.'" AND
								  ajkspk.cabang = "'.$cabang.'" AND
								  ajkspk.del IS NULL
							ORDER BY ajkspk.approve_date DESC');
              $li_row = 1;
              while ($rowspk = mysql_fetch_array($queryspk)) {
                  $input_date_format = date('d-m-Y', strtotime($input_date));
                  $approve_date = $rowspk['approve_date'];
                  $approve_date_format = date('d-m-Y', strtotime($approve_date));
                  if ($rowspk['statusspk']!="Request" and $rowspk['statusspk']!="Pending" and $rowspk['statusspk']!="Batal") {
                      $linknama = '<a href="../modules/modPdfdl_front.php?pdf=_spk&ids='.AES::encrypt128CBC($rowspk['nomorspk'], ENCRYPTION_KEY).'&idp='.AES::encrypt128CBC($rowspk['idproduk'], ENCRYPTION_KEY).'&idc='.AES::encrypt128CBC($rowspk['idpartner'], ENCRYPTION_KEY).'&idb='.AES::encrypt128CBC($rowspk['idbroker'], ENCRYPTION_KEY).'" target="_blank">'.$rowspk['nama'].'</a>';
                  } else {
                      $linknama = $rowspk['nama'];
                  }

                  if ($rowspk['statusspk']=="Aktif") {
                      $statusspk = '<span class="label label-success">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Approve") {
                      $statusspk = '<span class="label label-info">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="PreApproval") {
                      $statusspk = '<span class="label label-warning">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Proses") {
                      $statusspk = '<span class="label label-primary">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Request") {
                      $statusspk = '<span class="label label-lime">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Pending") {
                      $statusspk = '<span class="label label-grey">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Batal") {
                      $statusspk = '<span class="label label-danger">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Tolak") {
                      $statusspk = '<span class="label label-inverse">'.$rowspk['statusspk'].'</span>';
                  } elseif ($rowspk['statusspk']=="Realisasi") {
                      $statusspk = '<span class="label label-success">'.$rowspk['statusspk'].'</span>';
                  }


                  if ($rowspk['premiem']==null) {
                      $metPremiem = '';
                  } else {
                      $metPremiem = duit($rowspk['premiem']);
                  }
                  if ($rowspk['mppbln']==null) {
                      $metMPPbln = '';
                  } else {
                      $metMPPbln = $rowspk['mppbln'].' bulan';
                  }
                  echo '<tr class="odd gradeX">
		        <td>'.$li_row.'</td>
				<td>'.$rowspk['produk'].'</td>
				<td>'.$statusspk.'</td>
				<td>'.$rowspk['perusahaan'].'</td>
				<td>'.$rowspk['nomorspk'].'</td>
				<td>'.$rowspk['nama'].'</td>
				<td>'.$rowspk['nomorktp'].'</td>
				<td>'._convertDate($rowspk['dob']).'</td>
				<td>'.$rowspk['usia'].'</td>
				<td>'.$rowspk['alamat'].'</td>
				<td>'._convertDate($rowspk['tglakad']).'</td>
				<td>'.$rowspk['tenor'].'</td>
				<td>'._convertDate($rowspk['tglakhir']).'</td>
				<td>'.duit($rowspk['plafond']).'</td>
				<td><span class="label label-success">'.duit($rowspk['premi']).'</span></td>
				<td>'.$rowspk['em'].'</td>
				<td>'.$metPremiem.'</td>
				<td><span class="label label-success">'.duit($rowspk['totalpremiSPK']).'</span></td>
				<td>'.$metMPPbln.'</td>
				<td>'.$rowspk['cabang'].'</td>
				<td>'.$rowspk['userinput'].'</td>
				<td>'._convertDate($rowspk['tglinput']).'</td>
				<td>'._convertDate($rowspk['tglapprove']).'</td>
            </tr>';
                  $li_row++;
              } ?>

                        </tbody>
                    </table>

	                </form>
	            </div>
	            <!-- end section-container -->
	        </div>
				<?php
      }
       	?>

        <?php
      _footer();
        ?>
	</div>
		<!-- end #content -->
	</div>
	<!-- end page container -->

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="color:#fff">Cicilan</h4>
        </div>
        <div id="tbl-cicilan" class="modal-body">
          <table class="table table-striped table-bordered table-hover" width="100%">
          <thead>
            <tr>
            	<th>Tahun</td>
              <th>Plafond</td>
              <th>Nilai Cicilan</td>
              <th>Tgl Renewal</td>
            </tr>
          </thead>
          <tbody></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="certModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="color:#fff">Sertifikat</h4>
        </div>
        <form id="certForm">
          <div class="modal-body">
              <div class="form-group">
                <label for="exampleInputEmail1">ID Peserta</label>
                <input id="idper" type="text" class="form-control" readonly>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">No.Sertifikat</label>
                <input id="nocert" type="text" class="form-control" placeholder="Contoh: 123456789" required>
              </div>
              <div class="form-group">
                <label for="exampleInputFile">File input</label>
                <input id="filecert" type="file" id="exampleInputFile" accept="image/x-png,image/jpeg"  >
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="sppaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="color:#fff">SPPA</h4>
        </div>
        <form id="sppaForm">
          <div class="modal-body">
              <div class="form-group">
                <label for="exampleInputEmail1">ID Peserta</label>
                <input id="idpersppa" type="text" class="form-control" readonly>
              </div>
              <div class="form-group">
                <label for="exampleInputFile">File</label>
                <input id="filesppa" type="file" accept="image/x-png,image/jpeg,application/pdf"  class="form-control">
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="ktpModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="color:#fff">KTP</h4>
        </div>
        <form id="ktpForm">
          <div class="modal-body">
              <div class="form-group">
                <label for="exampleInputEmail1">ID Peserta</label>
                <input id="idperktp" type="text" class="form-control" readonly>
              </div>
              <div class="form-group">
                <label for="exampleInputFile">File</label>
                <input id="filektp" type="file" accept="image/x-png,image/jpeg" class="form-control" >
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="batalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" style="color:#fff">Batal</h4>
        </div>
        <form id="batalForm">
          <div class="modal-body">
              <div class="form-group">
                <label for="exampleInputEmail1">ID Peserta</label>
                <input id="idperbatal" type="text" class="form-control" readonly>
              </div>
              <div class="form-group">
                <label for="exampleInputFile">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

	<?php
    _javascript();
    ?>

	<script>
		$(document).ready(function() {
		    App.init();
		    Demo.init();
					<?php
        if ($typedata == 'peserta') {
          ?>
					$(".active").removeClass("active");
					document.getElementById("has_master").classList.add("active");
					document.getElementById("idsub_master").classList.add("active");
					document.getElementById("idsub_peserta").classList.add("active");

					t = $("#data-peserta").DataTable({
						"scrollX":true,
						"bProcessing": true,
						"bServerSide": true,
						"order": [[ 8, "desc" ]],
						"ajax":{
							url :"data.php?action=datapeserta", // json datasource
							type: "post",  // method  , by default get
							error: function(aa){  // error handling
								$(".data-peserta-error").html("");
								$("#data-peserta").append('<tbody class="data-peserta-error"><tr><th colspan="16">Data tidak tersedia</th></tr></tbody>');
							}
						}
					})

          $('#ktpForm').submit(function(event){
            var formData = new FormData();
            formData.append('filektp', $('#filektp')[0].files[0]);
            formData.append('idper', $('#idperktp').val());
            formData.append('action', 'uploadktp');

            $.ajax({
                url   : 'certificate.php',
                type : 'POST',
                data : formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success : function(data) {
                  if(data.url!=''){
                    $('#ktpModal').modal('hide');
                    swal({
                      title: "Message",
                      text: "Sesi anda telah berakhir!",
                      icon: "warning",
                      buttons: true,
                      dangerMode: false,
                    }).then((confirm) => {
                      if (confirm) {
                        window.location=data.url;
                      }
                    });
                  }else{
                    if(data.rs==true){
                      $('#ktpForm').trigger("reset");
                      $('#ktpModal').modal('hide');
                      t.ajax.reload();
                      swal("Success!", data.msg, "success");
                    }else{
                      $('#ktpForm').trigger("reset");
                      $('#ktpModal').modal('hide');
                      swal("Error!", data.msg, "error");
                    }

                  }
                },beforeSend: function( xhr ) {
                    $(".loading").show();
                },complete: function( xhr ) {
                    $(".loading").hide();
                }
            });

            event.preventDefault();
          })

          $('#sppaForm').submit(function(event){
            var formData = new FormData();
            formData.append('filesppa', $('#filesppa')[0].files[0]);
            formData.append('idper', $('#idpersppa').val());
            formData.append('action', 'uploadsppa');

            $.ajax({
                url   : 'certificate.php',
                type : 'POST',
                data : formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success : function(data) {
                  if(data.url!=''){
                    $('#ktpModal').modal('hide');
                    swal({
                      title: "Message",
                      text: "Sesi anda telah berakhir!",
                      icon: "warning",
                      buttons: true,
                      dangerMode: false,
                    }).then((confirm) => {
                      if (confirm) {
                        window.location=data.url;
                      }
                    });
                  }else{
                    if(data.rs==true){
                      $('#ktpForm').trigger("reset");
                      $('#ktpModal').modal('hide');
                      t.ajax.reload();
                      swal("Success!", data.msg, "success");
                    }else{
                      $('#ktpForm').trigger("reset");
                      $('#ktpModal').modal('hide');
                      swal("Error!", data.msg, "error");
                    }

                  }
                },beforeSend: function( xhr ) {
                    $(".loading").show();
                },complete: function( xhr ) {
                    $(".loading").hide();
                }
            });

            event.preventDefault();
          })
          
          $('#batalForm').submit(function(event){
            var formData = new FormData();
            formData.append('keterangan', $('#keterangan').val());
            formData.append('idper', $('#idperbatal').val());
            formData.append('action', 'batal');

            $.ajax({
                url   : 'certificate.php',
                type : 'POST',
                data : formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success : function(data) {
                  if(data.url!=''){
                    $('#batalModal').modal('hide');
                    swal({
                      title: "Message",
                      text: "Sesi anda telah berakhir!",
                      icon: "warning",
                      buttons: true,
                      dangerMode: false,
                    }).then((confirm) => {
                      if (confirm) {
                        window.location=data.url;
                      }
                    });
                  }else{
                    if(data.rs==true){
                      $('#batalForm').trigger("reset");
                      $('#batalModal').modal('hide');
                      t.ajax.reload();
                      swal("Success!", data.msg, "success");
                    }else{
                      $('#batalForm').trigger("reset");
                      $('#batalModal').modal('hide');
                      swal("Error!", data.msg, "error");
                    }

                  }
                },beforeSend: function( xhr ) {
                    $(".loading").show();
                },complete: function( xhr ) {
                    $(".loading").hide();
                }
            });

            event.preventDefault();
          })

					<?php
        } elseif ($typedata == 'debitnote') {
          ?>
					$(".active").removeClass("active");
					document.getElementById("has_master").classList.add("active");
					document.getElementById("idsub_master").classList.add("active");
					document.getElementById("idsub_debitnote").classList.add("active");

					$("#data-debitnote").DataTable({
						responsive: true,
						"bProcessing": true,
						"bServerSide": true,
						"order": [[ 4, "desc" ]],
						"ajax":{
							url :"data.php?action=datadebitnote", // json datasource
							type: "post",  // method  , by default get
							error: function(aa){  // error handling
								$(".data-debitnote-error").html("");
								$("#data-debitnote").append('<tbody class="data-debitnote-error"><tr><th colspan="16">Data tidak tersedia</th></tr></tbody>');
							}
						}
					})
					<?php
        } elseif ($typedata == 'pesertaSPK') {
          ?>
					$(".active").removeClass("active");
					document.getElementById("has_master").classList.add("active");
					document.getElementById("idsub_master").classList.add("active");
					document.getElementById("idsub_pesertaspk").classList.add("active");

					$("#data-pesertaspk").DataTable({
						responsive: true
					})
					<?php
        }
          ?>

          $('#certForm').submit(function(event){
            var formData = new FormData();
            formData.append('filecert', $('#filecert')[0].files[0]);
            formData.append('idper', $('#idper').val());
            formData.append('nocert', $('#nocert').val());
            formData.append('action', 'upload');

            $.ajax({
                url   : 'certificate.php',
                type : 'POST',
                data : formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success : function(data) {
                  console.log(data)
                  if(data.url!=''){
                    $('#certModal').modal('hide');
                    swal({
                      title: "Message",
                      text: "Sesi anda telah berakhir!",
                      icon: "warning",
                      buttons: true,
                      dangerMode: false,
                    }).then((confirm) => {
                      if (confirm) {
                        window.location=data.url;
                      }
                    });
                  }else{
                    if(data.rs==true){
                      $('#certForm').trigger("reset");
                      $('#certModal').modal('hide');
                      t.ajax.reload();
                      swal("Success!", data.msg, "success");
                    }else{
                      $('#certForm').trigger("reset");
                      $('#certModal').modal('hide');
                      swal("Error!", data.msg, "error");
                    }

                  }
                },beforeSend: function( xhr ) {
                    $(".loading").show();
                },complete: function( xhr ) {
                    $(".loading").hide();
                }
            });

            event.preventDefault();
          })

		});

function toggle(source) {
	var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(:disabled)');
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i] != source)
		checkboxes[i].checked = source.checked;
	}
}


function formSertifikat(id_peserta,no_cert)
{
    $('#certModal').modal('show');
    $('#idper').val(id_peserta);
    $('#nocert').val(no_cert);
}

function loadCicilan(id_peserta,data)
{
    $('#tbl-cicilan tbody').empty();
    $('#myModal').modal('show');
    $('#myModal .modal-title').text('Cicilan ('+id_peserta+')');
    $('#tbl-cicilan tbody').html(data);
}

function loadSppa(id_peserta)
{
  $('#sppaModal').modal('show');
  $('#idpersppa').val(id_peserta);
}

function loadKtp(id_peserta)
{
  $('#ktpModal').modal('show');
  $('#idperktp').val(id_peserta);
}

function removeCert(idper,nocert)
{
  swal({
    title: "Message",
    text: "Apakah anda yakin ingin menghapus sertifikat "+nocert+"?",
    icon: "warning",
    showCancelButton: true,
    buttons: true,
    dangerMode: false,
  })
  .then((willDelete) => {
    if (willDelete) {
      $.ajax({
          url   : 'certificate.php',
          type : 'POST',
          dataType: 'json',
          data : {idper:idper,nocert:nocert,action:'remove'},
          success : function(data) {
            console.log(data)
            if(data.url!=''){
              swal({
                title: "Message",
                text: "Sesi anda telah berakhir!",
                icon: "warning",
                buttons: true,
                dangerMode: false,
              }).then((confirm) => {
                if (confirm) {
                  window.location=data.url;
                }
              });
            }else{
              t.ajax.reload();
            }

          },beforeSend: function( xhr ) {
              $(".loading").show();
          },complete: function( xhr ) {
              $(".loading").hide();
          }
      });
    } else {
      swal("Your imaginary file is safe!");
    }
  });

}

function batal(idper)
{
  $('#batalModal').modal('show');
  $('#idperbatal').val(idper);
}
	</script>

</body>

</html>
