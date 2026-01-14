<?php
include "../param.php";
include_once "../includes/functions.php";

//ini_set('display_errors', 1);
//error_reporting(E_ALL);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<?php
_head($user,$namauser,$photo,$logo);
?>

<body>
	<!-- begin #page-loader -->
	<div id="page-loader" class="page-loader fade in"><span class="spinner">Loading...</span></div>
	<!-- end #page-loader -->

	<!-- begin #page-container -->
	<div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-two-sidebar page-with-footer page-with-top-menu page-without-sidebar">
		<?php
		_header($user,$namauser,$photo,$logo,$logoklient);
		_sidebar($user,$namauser,'','');
		?>
		<!-- begin #content -->
		<div id="content" class="content">
			<div class="panel p-30">
				<h4 class="m-t-0">Upload Certificate</h4>
				<div class="section-container section-with-top-border">			    
			    <form action="../api/apijatim.php" id="form-upload" name="form-upload" class="form-horizontal" method="post" enctype="multipart/form-data">
			    	<input type="hidden" name="han" value="upload">
			    	<div class="panel-body">
							<table id="data-pesertatemp" class="table table-bordered table-hover" width="100%">
								<thead >
									<tr class="primary">
										<th class="text-center">No</th>
										<th class="text-center">Produk</th>
										<th class="text-center">Nama</th>
										<th class="text-center">Nomor KTP</th>										
										<th class="text-center">Gender</th>
										<th class="text-center">Tanggal Lahir</th>
										<th class="text-center">Usia</th>
										<th class="text-center">Plafond</th>
										<th class="text-center">Tanggal Akad</th>
										<th class="text-center">Tenor</th>
										<th class="text-center">Tanggal Akhir</th>										
										<th class="text-center">Premi</th>
										<th class="text-center">No Pinjaman</th>
										<th class="text-center">Cabang</th>										
										<th class="text-center">Asuransi</th>
									</tr>
								</thead>
								<tbody>									
									<?php

											$file = fopen($_FILES['fileupload']['name'], "r");
													//$line =  fgets($file);
													echo fread($myfile);
											   
									?>
								</tbody>
							</table>
							<div class="form-group m-b-0">
								<label class="control-label col-sm-12"></label>
								<div class="col-sm-6">
									<input type="submit" name="sub" class="btn btn-success width-xs" value="Submit" <?php echo $disabledbtn ?>>
									<a href="../upload?xq=<?php echo AES::encrypt128CBC($_REQUEST['xq'],ENCRYPTION_KEY)?>" class="btn btn-danger width-xs">Cancel</a>
								</div>
							</div>
						</div>
					</form>
        </div>
        <!-- end section-container -->
	    </div>
      <?php
      _footer();
      ?>
		</div>
		<!-- end #content -->
	</div>
	<!-- end page container -->

	<?php
	_javascript();
	?>

	<script>
		$(document).ready(function() {
	    App.init();
	    Demo.init();

			$(".active").removeClass("active");
			document.getElementById("has_upload").classList.add("active");
			document.getElementById("idhas_uploadcsf").classList.add("active");
		});

	</script>
</body>
</html>
