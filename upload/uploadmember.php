<?php

include "../param.php";
include_once "../includes/functions.php";

if(isset($_FILES['fileupload']['name'])){
  $ext = pathinfo($_FILES['fileupload']['name'], PATHINFO_EXTENSION);
  $excel_extension = Array('xls','xlsx','csv');
  if(!in_array($ext, $excel_extension)){
    $typeupload = AES::encrypt128CBC($_REQUEST['xq'],ENCRYPTION_KEY);                        
    header("location:../upload?xq=".$typeupload."&pesan=File harus Excel");
  }
}
// ini_set('display_errors', 1);
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
			    	<input type="hidden" name="han" value="upload">
			    	<div class="panel-body" style="overflow-x: auto;">
							<table id="data-pesertatemp" class="table table-bordered table-hover" width="100%">
								<thead >
									<tr class="primary">
                    <th class="text-center">No</th>
                    <th class="text-center">Produk</th>
										<th class="text-center">Nama</th>
										<th class="text-center">Jenis Kelamin</th>
                    <th class="text-center">Tempat Lahir</th>
										<th class="text-center">Tgl. Lahir</th>		
                    <th class="text-center">Usia</th>												
										<th class="text-center">No KTP</th>										
                    <th class="text-center">Pekerjaan</th>		
                    <th class="text-center">Tujuan</th>
										<th class="text-center">Tanggal Akad</th>
										<th class="text-center">Tenor</th>										
                    <th class="text-center">No. Pinjaman</th>
										<th class="text-center">Plafond</th>
                    <th class="text-center">Medical</th>
                    <th class="text-center">Status</th>
										<th class="text-center">Rate</th>
										<th class="text-center">Premi</th>                                  
									</tr>
								</thead>
								<tbody>									
									<?php
                  
										if(isset($_FILES['fileupload']['name'])){
                      
											$file_name = $_FILES['fileupload']['name'];
											$ext = pathinfo($file_name, PATHINFO_EXTENSION);
											$file_temp_name = $_FILES['fileupload']['tmp_name'];
											$file_info = pathinfo($file_name);
											$file_extension = $file_info["extension"];
											$namefile = $file_info["filename"].'_'.date('YmdHis').'.'.$file_extension;
											$inputFileName = $file_temp_name;

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
											// $no=1;
                    
											for ($row = 9; $row <= $highestRow; $row++) {

												//  Read a row of data into an array
                        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL, TRUE, FALSE);                        
												echo "<tr>";
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
												
                        $tgllahir = date("Y-m-d",strtotime($tgllahir));
                        $tglakad = date("Y-m-d",strtotime($tglakad));

												// //VALIDASI 
                          // Validasi Produk
                          $qproduk = mysql_query("SELECT * FROM ajkpolis WHERE ref_mapping = '".$produk."' and del is null");
                       
                          if(mysql_num_rows($qproduk) > 0){                            
                            $produk_ = mysql_fetch_array($qproduk);
                            $nmproduk = $produk_['produk'];																
                            $errorproduk = null;
                          }else{
                            $errorproduk = '<span class="label label-danger">Produk tidak terdapat di database</span>';
                            $error = 1;
                          }
                          
													//---------------------------------------Validasi No Pinjaman--------------------------------------------//
													if($nopinjaman != ""){
														$qproduk = mysql_query("SELECT * FROM ajkpeserta WHERE idclient = '".$idclient."' AND nopinjaman = '".$nopinjaman."' and del is null");

														if(mysql_num_rows($qproduk) > 0){
															$errornopinjaman = '<span class="label label-danger">No Pinjaman sudah terdapat di database</span>';
															$error = 1;
														}else{
															if($nopinjaman == $nopinjaman_temp){
																$errornopinjaman = '<span class="label label-danger">No Pinjaman Double</span>';
																$error = 1;
															}else{
																$nopinjaman_temp = $nopinjaman;																
																$errornopinjaman = null;
															}
														}
													}else{
														$errornopinjaman = '<span class="label label-danger">No Pinjaman tidak Boleh Kosong</span>';
														$error = 1;
													}

													//-------------------------------------------Validasi Nama-------------------------------------------//
													if($nama != ""){
														$errornama = null;
													}else{
														$errornama = '<span class="label label-danger">Nama Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Nama----------------------------------------//

                          //-------------------------------------------Validasi Tptlahir-------------------------------------------//
													if($tptlahir != ""){
														$errortptlahir = null;
													}else{
														$errortptlahir = '<span class="label label-danger">Tempat Lahir Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Tptlahir----------------------------------------//


                          //-------------------------------------------Validasi Pekerjaan-------------------------------------------//
													if($pekerjaan != ""){
														$errorpekerjaan = null;
													}else{
														$errorpekerjaan = '<span class="label label-danger">Pekerjaan Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Pekerjaan----------------------------------------//

                          //-------------------------------------------Validasi Tujuan-------------------------------------------//
                          if($tujuan != ""){
                            $errortujuan = null;
                          }else{
                            $errortujuan = '<span class="label label-danger">Tujuan Tidak Boleh Kosong</span>';
                            $error = 1;
                          }
                          //---------------------------------------- End Validasi Tujuan----------------------------------------//

													//-------------------------------------------Validasi Gender-------------------------------------------//
													if($gender != ""){
														if($gender == "L" or $gender == "P"){
                              if($gender == "L"){
                                $gender = 'Laki - laki';
                              }else{
                                $gender = 'Perempuan';
                              }
															$errorgender = null;	
														}else{															
															$errorgender = '<span class="label label-danger">Jenis Kelamin hanya bisa diisi L (Laki - laki)atau P (Perempuan)</span>';
															$error = 1;
														}														
													}else{
														$errorgender = '<span class="label label-danger">Jenis Kelamin Tidak Boleh Kosong</span>';
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
															$errorktp = '<span class="label label-danger">KTP Harus 16 Digit</span>';
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
														$errorktp = '<span class="label label-danger">KTP Tidak Boleh Kosong</span>';
														$error = 1;
													}
													
													//---------------------------------------- End Validasi KTP----------------------------------------//

													//-------------------------------------------Validasi Tanggal Lahir-------------------------------------------//
													if($tgllahir != ""){														
														$errortgllahir = null;
													}else{
														$errortgllahir = '<span class="label label-danger">Tanggal Lahir Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Tanggal Lahir----------------------------------------//

													//-------------------------------------------Validasi Tanggal Akad-------------------------------------------//
													if($tglakad != ""){
														$errortglakad = null;
													}else{
														$errortglakad = '<span class="label label-danger">Tanggal Akad Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Tanggal Akad----------------------------------------//
													
													//-------------------------------------------Validasi Usia-------------------------------------------//
													$usia = usia($tglakad,$tgllahir);

													$usiaawal = $produk_['agestart'];
													$usiaakhir = $produk_['ageend'];
													
													if($usia < $usiaawal or $usia > $usiaakhir){
														$errorusia = '<span class="label label-danger">Usia tidak sesuai ketentuan polis</span>';
														$error = 1;
													}else{
														$errorusia = null;
													}
													//---------------------------------------- End Validasi Usia----------------------------------------//

													//-------------------------------------------Validasi Tenor-------------------------------------------//
													// $tenor = datediffmonth($tglakad,$tglakhir);
													$tenorawal = $produk_['tenormin'];
													$tenorakhir = $produk_['tenormax'];
													if(isset($tenorawal)){
														if($tenor < $tenorawal or $tenor > $tenorakhir){
															$errortenor = '<span class="label label-danger">Tenor tidak sesuai ketentuan polis</span>';
															$error = 1;														
														}else{
															$errortenor = null;
														}
													}else{
														$errortenor = '<span class="label label-danger">Tenor belum di setting di Polis</span>';
														$error = 1;
													}
												  //-------------------------------------------End Validasi Tenor-------------------------------------------//

													//-------------------------------------------Validasi Plafond-------------------------------------------//
													
													if($plafond != ""){
														$plafond_a = $plafond + $totalpinjaman;
														$plafondawal = $produk_['plafondstart'];
														$plafondakhir = $produk_['plafondend'];
														if($plafond < $plafondawal or $plafond > $plafondakhir){
															$errorplafond = '<span class="label label-danger">Plafond tidak sesuai polis</span>';
															$error = 1;
														}else{															
															if($plafond_a < $plafondawal or $plafond_a > $plafondakhir){
																$errorplafond = '<span class="label label-danger">Plafond Total ['.duit($plafond_a).'] tidak sesuai polis</span>';
																$error = 1;	
															}else{
																$errorplafond = null;	
															}
															
														}														
													}else{
														$errorplafond = '<span class="label label-danger">Plafond Tidak Boleh Kosong</span>';
														$error = 1;
													}
													//---------------------------------------- End Validasi Plafond----------------------------------------//

                          if($produk_['id'] != 11){
                            // $qpremi1 = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and '".$tenor."' BETWEEN tenorfrom and tenorto and status = 'Aktif' and del is null");
                            $qpremi2 = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = 11 and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' BETWEEN agefrom and ageto and status = 'Aktif' and del is null");
                            if(mysql_num_rows($qpremi2) > 0){
                              // $qpremi1_ = mysql_fetch_array($qpremi1);
                              $qpremi2_ = mysql_fetch_array($qpremi2);
                              // $rate1 = $qpremi1_['rate'];
                              $rate2 = $qpremi2_['rate'];
                              // $premi1 = $plafond/$produk_['calculatedrate'] * $rate1;
                              $premi2 = $plafond/$produk_['calculatedrate'] * $rate2;
                              $rate = $rate2;
                              $premi = $premi2;

                              $errorrate = null;
                            }else{
                              $rate = 0;
                              $errorrate = '<span class="label label-danger">Rate belum tersedia di database</span>';
                              $error = 1;
                            }
                          }else{
                            // $qpremi = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = '".$produk_['id']."' and '".$tenor."' BETWEEN tenorfrom and tenorto and status = 'Aktif' and del is null");
                            $qpremi = mysql_query("SELECT * FROM ajkratepremi WHERE idbroker = '".$idbro."' and idclient = '".$idclient."' and idpolis = 11 and '".$tenor."' BETWEEN tenorfrom and tenorto and '".$usia."' BETWEEN agefrom and ageto and status = 'Aktif' and del is null");
                            if(mysql_num_rows($qpremi) > 0){															
                              $qpremi_ = mysql_fetch_array($qpremi);
                              $rate = $qpremi_['rate'];
                              $premi = $plafond/$produk_['calculatedrate'] * $rate;
                              $errorrate = null;
                            }else{
                              $rate = 0;
                              $errorrate = '<span class="label label-danger">Rate belum tersedia di database</span>';
                              $error = 1;
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

                          if($produk_['id'] != 11){
                            $qmedical1 = mysql_query("select * from ajkmedical where idproduk = '".$produk_['id']."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
                            // $qmedical2 = mysql_query("select * from ajkmedical where idproduk = 11 and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
                            if(mysql_num_rows($qmedical1) > 0 && mysql_num_rows($qmedical2) > 0){
                              $qmedical1_ = mysql_fetch_array($qmedical1);
                              // $qmedical2_ = mysql_fetch_array($qmedical2);
                              $medical1 = $qmedical1_['type'];
                              // $medical2 = $qmedical2_['type'];
                              $medical = $medical1.$medical2;
                              // if($medical != 'GOA'){
                                $status = 'Pending';
                              // }else{
                              //   $status = 'Approve';
                              // }
                              $errormedical = null;
                            }else{
                              $errormedical = '<span class="label label-danger">Medical tidak ditemukan</span>';
                              $error = 1;
                            }
                          }else{
                            $qmedical = mysql_query("select * from ajkmedical where idproduk = '".$produk_['id']."' and '".$usia."' between agefrom and ageto and '".$plafond."' between upfrom and upto and status = 'Aktif'");
                            if(mysql_num_rows($qmedical) > 0){															
                              $qmedical_ = mysql_fetch_array($qmedical);
                              $medical = $qmedical_['type'];
                              // if($medical != 'GOA'){
                                $status = 'Pending';
                              // }else{
                                // $status = 'Approve';
                              // }
                              $errormedical = null;
                            }else{
                              $errormedical = '<span class="label label-danger">Medical tidak ditemukan</span>';
                              $error = 1;
                            }
                          }
                          
                        //END VALIDASI
                        
												echo "<td>".$no." </td>";
                        echo "<td>".$nmproduk." $errorproduk</td>";
												echo "<td>".$nama." $errornama</td>";
												echo "<td>".$gender." $errorgender</td>";
                        echo "<td>".$tptlahir." $errortptlahir</td>";
												echo "<td>"._convertDate($tgllahir)." $errortgllahir</td>";
                        echo "<td>".$usia." $errorusia</td>";
												echo "<td>".$nomorktp." $errorktp</td>";
                        echo "<td>".$pekerjaan." $errorpekerjaan</td>";
                        echo "<td>".$tujuan." $errortujuan</td>";
												echo "<td>"._convertDate($tglakad)." $errortglakad</td>";
												echo "<td>".$tenor." $errortenor</td>";
												echo "<td>".$nopinjaman." $errornopinjaman</td>";
                        echo "<td>".$norekening."</td>";                        
                        echo "<td class='text-right'>".number_format($plafond,0,".",",")." $errorplafond</td>";
                        echo "<td>".$medical." $errormedical</td>";
                        echo "<td>".$status." $errorstatus</td>";
												echo "<td>".$rate."% $errorrate</td>";
												echo "<td class='text-right'>".number_format($premi,0,".",",")." $errorpremi</td>";
												echo "</tr>";                        
												// $no++;
											}
											$path = 'temp/';
											if($error == 0){
                        if (!file_exists($path)) {
                          mkdir($path, 0777);
                          chmod($path, 0777);
                      }
                        move_uploaded_file($file_temp_name, $path.'/'.$namefile) or die("Could not upload file!");
                       
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
