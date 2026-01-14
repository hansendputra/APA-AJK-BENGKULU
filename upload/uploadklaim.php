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
<style>
.modal {
   position: absolute;
   top: 10px;
   right: 100px;
   bottom: 0;
   left: 0;
   z-index: 10040;
   overflow: auto;
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
		_header($user,$namauser,$photo,$logo,$logoklient);
    _sidebar($user,$namauser,'','');
		?>
		<!-- begin #content -->
		<div id="content" class="content">
			<div class="panel p-30">
				<h4 class="m-t-0">Upload Data Deklarasi</h4>
				<div class="section-container section-with-top-border">			    
			    <form action="../api/api.php" id="form-upload" name="form-upload" class="form-horizontal" method="post" enctype="multipart/form-data">
			    	<input type="hidden" name="han" value="klaimupload">
			    	<div class="panel-body">
							<table id="data-pesertatemp" class="table table-bordered table-hover" width="100%">
								<thead >
									<tr class="primary">
                    <th class="text-center">No</th>
										<th class="text-center">No Pinjaman</th>
										<th class="text-center">Nama</th>
										<th class="text-center">Produk</th>
										<th class="text-center">Tanggal Lahir</th>	
										<th class="text-center">Tenor</th>										
										<th class="text-center">Tanggal Akad</th>										
										<th class="text-center">Tanggal Akhir</th>
										<th class="text-center">Tgl Macet</th>
                    <th class="text-center">Nilai Klaim</th>
										<th class="text-center">Penyebab Macet</th>
									</tr>
								</thead>
								<tbody>									
									<?php
                  
										if(isset($_FILES['fileupload']['name'])){
											$file_name = $_FILES['fileupload']['name'];
											$ext = pathinfo($file_name, PATHINFO_EXTENSION);
											$file_name = $_FILES['fileupload']['tmp_name'];
											$file_info = pathinfo($file_name);
											$file_extension = $file_info["extension"];
											$namefile = $file_info["filename"].'.'.$file_extension;
											$inputFileName = $file_name;
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
											
											//Table used to display the contents of the file
											//Get worksheet dimensions

											$sheet = $objPHPExcel->getSheet(0);
											$highestRow = $sheet->getHighestRow();
                      $highestColumn = $sheet->getHighestColumn();
											$error=0;
											$no=1;

											for ($row = 5; $row <= $highestRow; $row++) {
												//  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);
                        // print_r($rowData[0][0]);
												echo "<tr>";
												$i = 0;

												foreach($rowData[0] as $k=>$v){
													$data[$i] = $v;
													$i++;
												}
												
												$today = date('Y-m-d');
                        
                        $no = $data[0];
												$nopinjaman = $data[1];
												$tglmacet = $data[2];
												$nilaiklaim = $data[3];
												$penyebab = $data[4];

												
												// //VALIDASI 

                          //---------------------------------------Validasi No Pinjaman--------------------------------------------//
													if($nopinjaman != ""){
                            
														$qmember = mysql_query("SELECT ajkpeserta.*,ajkpolis.produk FROM ajkpeserta INNER JOIN ajkpolis on ajkpolis.id = ajkpeserta.idpolicy WHERE idclient = '".$idclient."' AND nopinjaman = '".$nopinjaman."' ");

														if(mysql_num_rows($qmember) > 0){
                              $row = mysql_fetch_array($qmember);
                              if($row['statusaktif'] != 'Inforce'){
                                $errornopinjaman = '<span class="label label-danger">No Pinjaman tidak terdapat di database atau sudah dilakukan proses klaim</span>';
                                $error = 1;  
                              }else{
                                if($row['statuslunas'] != 1){
                                  $errornopinjaman = '<span class="label label-danger">Status Harus Aktif</span>';
                                  $error = 1;
                                }else{
                                  $errortglmacet = null;
                                }
                              }
														}
													}else{
														$errornopinjaman = '<span class="label label-danger">No Pinjaman tidak Boleh Kosong</span>';
														$error = 1;
													}

													//---------------------------------------End Validasi No Pinjaman----------------------------------------//

													//-------------------------------------------Validasi Tanggal Macet-------------------------------------------//
													if($tglmacet != ""){														
														$tglmacet = substr($tglmacet,0,4).'-'.substr($tglmacet,-4,2).'-'.substr($tglmacet,-2,2);														
														$errortglmacet = null;
													}else{
														$errortglmacet = '<span class="label label-danger">Tanggal Lahir Tidak Boleh Kosong</span>';
														$error = 1;
													}
                          //---------------------------------------- End Validasi Tanggal Macet----------------------------------------//
                          
													//-------------------------------------------Validasi Nilai Klaim-------------------------------------------//
													if($nilaiklaim > 0){														
														$errornilaiklaim = null;
													}else{
                            $errornilaiklaim = '<span class="label label-danger">Nilai Klaim harus lebih besar dari 0</span>';
														$error = 1;
													}
                          //---------------------------------------- End Validasi Tanggal Lahir----------------------------------------//
                          
													//-------------------------------------------Validasi Penyebab-------------------------------------------//
													if($penyebab != ""){																												
														$errorpenyebab = null;
													}else{
														$errorpenyebab = '<span class="label label-danger">Penyebab Klaim Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Tanggal Lahir----------------------------------------//

												// //END VALIDASI

											
												echo "<td>".$no." </td>";
												echo "<td>".$nopinjaman." $errornopinjaman</td>";
												echo "<td>".$row['nama']."</td>";
												echo "<td>".strtoupper($row['produk'])."</td>";
												echo "<td>"._convertDate($row['tgllahir'])."</td>";                        
												echo "<td>".$row['tenor']."</td>";
												echo "<td>"._convertDate($row['tglakad'])." </td>";
                        echo "<td>"._convertDate($row['tglakhir'])." </td>";
                        echo "<td>"._convertDate($tglmacet)." $errortglmacet</td>";
                        echo "<td class='text-right'>".duit($nilaiklaim)." $errornilaiklaim</td>";
                        echo "<td>".$penyebab." $errorpenyebab</td>";
												echo "</tr>";                        
												$no++;
											}
											
											if($error == 0){
												move_uploaded_file($file_name,'temp/'.$namefile) or die( "Could not upload file!");
												$disabledbtn = '';
											}else{
												$disabledbtn = 'disabled';
											}
										}
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
			document.getElementById("has_input").classList.add("active");
			document.getElementById("idhas_input").classList.add("active");
		});

		$("#data-pesertatemp").DataTable({	responsive: false,scrollX:true,paging:false	});

	</script>
</body>
</html>
