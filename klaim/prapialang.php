<?php
include "../param.php";
$setproduk = '';
?>
<!DOCTYPE html>
<html lang="en">

<?php
	_head($user,$namauser,$photo,$logo);
	
	function duitret($a){
    return str_replace(',','',$a);
  }
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
    $opt = AES::decrypt128CBC($_REQUEST['opt'], ENCRYPTION_KEY);
    $list = AES::encrypt128CBC('list', ENCRYPTION_KEY);
    $edit = AES::encrypt128CBC('edit', ENCRYPTION_KEY);
    $new = AES::encrypt128CBC('new', ENCRYPTION_KEY);
    $delete = AES::encrypt128CBC('delete', ENCRYPTION_KEY);
    $deldok = AES::encrypt128CBC('deldok', ENCRYPTION_KEY);

    
    switch($opt){
      case "edit":        
        $id = AES::decrypt128CBC($_REQUEST['ab'], ENCRYPTION_KEY);

        $query = "SELECT * FROM ajkklaimprapialang WHERE id = '".$id."'";
        $row = mysql_fetch_array(mysql_query($query));
        // print_r($row);
        $nourut = $row['nourut'];
        $nama = $row['nama'];
        $nmcabang = $row['cabang'];
        $tgllahir = $row['tgllahir'];
        $tglakad = $row['tglakad'];
        $tenor = $row['tenor'];
        $plafond = duit($row['plafond']);
        $nopolis = $row['nopolis'];
        $nosuratclient = $row['nosuratclient'];
        $nopengajuan = $row['nopengajuan'];
        $tglklaim = $row['tglklaim'];
        $jenis_klaim = $row['jenis_klaim'];
        $nilaiklaimdiajukan = $row['nilaiklaimdiajukan'];
        $asuransi = $row['asuransi'];
        $tglpengajuan = $row['tglpengajuan'];
        $tglterimadokumen = $row['tglterimadokumen'];
        $tgldokumenlengkap = $row['tgldokumenlengkap'];
        $tgllaporas = $row['tgllaporas'];
        $keteranganklaim = $row['keteranganklaim'];
        $kelengkapandokumen = $row['kelengkapandokumen'];
        $statusklaim = $row['statusklaim'];
        $nilaibayaras = $row['nilaibayaras'];
        $tglbayaras = $row['tglbayaras'];
        $nilaibayarclient = $row['nilaibayarclient'];
        $tglbayarclient = $row['tglbayarclient'];

      break;

      case "delete":
        $id = AES::decrypt128CBC($_REQUEST['ab'], ENCRYPTION_KEY);
        $query = "UPDATE ajkklaimprapialang SET del = 1 WHERE id = '".$id."'";
      break;

      case "deldok":
      
        $idpra = $_REQUEST['goiagadva'];
        $iddoc = $_REQUEST['adadgasf'];
        $query = "
        UPDATE ajkdocumentclaimmemberpra SET fileklaim = NULL
        WHERE idmemberpra = '".$idpra."' and 
        iddoc = '".$iddoc."'";
        $res = mysql_query($query);
        if($res){                    
          echo '<meta http-equiv="refresh" content="0; url=../klaim/prapialang.php?opt='.$edit.'&id='.$idpra.'">';
        }else{
          // echo 'test';
        }
      break;
    }
    
    if($_POST['act']=="save"){
      if($_REQUEST['id']){
        $process = "UPDATE ";
        $where = " WHERE id = '".$_REQUEST['id']."'";
        $idpeserta = "";
        $jenis_klaim = $_POST['jenis_klaim'];
        $tempatkejadian = $_POST['tempat_kejadian'];
        $idpra = $_REQUEST['id'];
      }else{
        $process = "INSERT INTO ";
        $where = "";
        $q = "select id+1 as id from ajkklaimprapialang order by id desc limit 1";
        $res = mysql_fetch_array(mysql_query($q));
        $idpra = $res['id'];
        $idpialang = " id = '".$idpra."',";

        $jenis_klaim = $_POST['jenis_klaim'];
        $tempatkejadian = $_POST['tempat_kejadian'];

        if($jenis_klaim == "Meninggal Dunia"){
          $jenis_klaim = "DEATH";
        }
  
        if($tempatkejadian){
          $tempat = " and opsional in ('','$tempatkejadian')";
        }else{
          $tempat = "";
        }
        $querydokBAK = "
        INSERT INTO ajkdocumentclaimmemberpra (iddoc,nmdoc,idmemberpra,inputby,inputdate)
        SELECT id,namadokumen,'$idpra','$user','$mamettoday'
        FROM ajkdocumentclaim WHERE type in ('AJK','$jenis_klaim') $tempat";        
        $querydokumen = "
        INSERT INTO ajkdocumentclaimmemberpra SET nmdoc='DOKUMEN PRAPIALANG',idmemberpra='".$idpra."',inputby='".$user."',inputdate='".$mamettoday."'";
      }
      

      $nourut = $_POST['nourut'];
      $nama = $_POST['nama'];
      $nmcabang = $_POST['nmcabang'];
      $tgllahir = $_POST['tgllahir'];
      $tglakad = $_POST['tglakad'];
      $tenor = $_POST['tenor'];
      $plafond = $_POST['plafond'];
      $nopolis = $_POST['nopolis'];
      $nosuratclient = $_POST['nosuratclient'];
      $nopengajuan = $_POST['nopengajuan'];
      $tglklaim = $_POST['tglklaim'];
      $nilaiklaimdiajukan = $_POST['nilaiklaimdiajukan'];
      $asuransi = $_POST['asuransi'];
      $tglpengajuan = $_POST['tglpengajuan'];
      $tglterimadokumen = $_POST['tglterimadokumen'];
      $tgldokumenlengkap = $_POST['tgldokumenlengkap'];
      $tgllaporas = $_POST['tgllaporas'];
      $keteranganklaim = $_POST['keteranganklaim'];
      $kelengkapandokumen = $_POST['kelengkapandokumen'];
      $statusklaim = $_POST['statusklaim'];
      $nilaibayaras = $_POST['nilaibayaras'];
      $tglbayaras = $_POST['tglbayaras'];
      $nilaibayarclient = $_POST['nilaibayarclient'];
      $tglbayarclient = $_POST['tglbayarclient'];

      ($_POST['nourut'])?$qnourut = " nourut = '".$nourut."',":$qnourut = "";
      ($_POST['nama'])?$qnama = " nama = '".$nama."',":$qnama = "";
      ($_POST['nmcabang'])?$qnmcabang = " cabang = '".$nmcabang."',":$qnmcabang = "";      
      ($_POST['tgllahir'])?$qtgllahir = " tgllahir = '"._convertDate2($tgllahir)."',":$qtgllahir = "";
      ($_POST['tglakad'])?$qtglakad = " tglakad = '"._convertDate2($tglakad)."',":$qtglakad = "";
      ($_POST['tenor'])?$qtenor = " tenor = '".$tenor."',":$qtenor = "";      
      ($_POST['plafond'])?$qplafond = " plafond = '".duitret($plafond)."',":$qplafond = "";            
      ($_POST['nopolis'])?$qnopolis = " nopolis = '".$nopolis."',":$qnopolis = "";  
      ($_POST['nosuratclient'])?$qnosuratclient = " nosuratclient = '".$nosuratclient."',":$qnosuratclient = "";  
      ($_POST['nopengajuan'])?$qnopengajuan = " nopengajuan = '".$nopengajuan."',":$qnopengajuan = "";  
      ($_POST['tglklaim'])?$qtglklaim = " tglklaim = '"._convertDate2($tglklaim)."',":$qtglklaim = "";
      ($_POST['nilaiklaimdiajukan'])?$qnilaiklaimdiajukan = "nilaiklaimdiajukan = '".duitret($nilaiklaimdiajukan)."',":$qnilaiklaimdiajukan = "";            
      ($_POST['asuransi'])?$qasuransi = " asuransi = '".$asuransi."',":$qasuransi = "";  
      ($_POST['jenis_klaim'])?$qjenis_klaim = " jenis_klaim = '".$jenis_klaim."',":$qasuransi = "";  
      ($_POST['tglpengajuan'])?$qtglpengajuan = " tglpengajuan = '"._convertDate2($tglpengajuan)."',":$qtglpengajuan = "";
      ($_POST['tglterimadokumen'])?$qtglterimadokumen = " tglterimadokumen = '"._convertDate2($tglterimadokumen)."',":$qtglterimadokumen = "";
      ($_POST['tgldokumenlengkap'])?$qtgldokumenlengkap = " tgldokumenlengkap = '"._convertDate2($tgldokumenlengkap)."',":$qtgldokumenlengkap = "";
      ($_POST['tgllaporas'])?$qtgllaporas = " tgllaporas = '"._convertDate2($tgllaporas)."',":$qtgllaporas = "";
      ($_POST['keteranganklaim'])?$qketeranganklaim = " keteranganklaim = '".$keteranganklaim."',":$qketeranganklaim = "";  
      ($_POST['kelengkapandokumen'])?$qkelengkapandokumen = " kelengkapandokumen = '".$kelengkapandokumen."',":$qkelengkapandokumen = "";  
      ($_POST['statusklaim'])?$qstatusklaim = " statusklaim = '".$statusklaim."',":$qstatusklaim = "";        
      ($_POST['nilaibayaras'])?$qnilaibayaras = " nilaibayaras = '".duitret($nilaibayaras)."',":$qnilaibayaras = "";  
      ($_POST['tglbayaras'])?$qtglbayaras = " tglbayaras = '"._convertDate2($tglbayaras)."',":$qtglbayaras = "";
      ($_POST['nilaibayarclient'])?$qnilaibayarclient = " nilaibayarclient = '".duitret($nilaibayarclient)."',":$qnilaibayarclient = "";  
      ($_POST['tglbayarclient'])?$qtglbayarclient = " tglbayarclient = '"._convertDate2($tglbayarclient)."',":$qtglbayarclient = "";


      $query = "$process ajkklaimprapialang 
      SET $idpialang 
          idbroker= 2, 
          idclient = 1, 
          $qnourut
          $qnama
          $qtgllahir
          $qtglakad
          $qtenor
          $qplafond
          $qnmcabang
          $qnopolis
          $qnosuratclient
          $qnopengajuan
          $qtglklaim
          $qjenis_klaim
          $qtempatkejadian
          $qnilaiklaimdiajukan
          $qasuransi
          $qtglpengajuan
          $qtglterimadokumen
          $qtgldokumenlengkap
          $qtgllaporas
          $qketeranganklaim
          $qkelengkapandokumen
          $qstatusklaim
          $qnilaibayaras
          $qtglbayaras
          $qnilaibayarclient
          $qtglbayarclient
          input_by = '".$user."', 
          input_date = '".$mamettoday."', 
          update_by = '".$user."', 
          update_date = '".$mamettoday."' $where";
            // $query = "test";
            
      // echo $query;
      // echo $querydokumen;
      mysql_query("START TRANSACTION");

      $a1 = mysql_query($query);
      if($querydokumen){
        $a2 = mysql_query($querydokumen);
      }else{
        $a2 = true;
      }
      
      
      if ($a1 and $a2) {
          mysql_query("COMMIT");       
          echo '<h4>Data Berhasil Disimpan</h4><meta http-equiv="refresh" content="5; url=../klaim/prapialang.php?opt='.$edit.'&id='.$idpra.'">';
      } else {        
          mysql_query("ROLLBACK");
          echo '<h4>Data Gagal Disimpan</h4><!--<meta http-equiv="refresh" content="5; url=../klaim/prapialang.php?opt='.$list.'">-->';
      }
    }

    if($_POST['dokumen']=="save"){
      $idpra = $_REQUEST['id'];
      print_r($_FILES);
      $total = count($_FILES['fileklaim']['name']);

      for( $i=0 ; $i < $total ; $i++ ) {
        //Get the temp file path
        $tmpFilePath = $_FILES['fileklaim']['tmp_name'][$i];
        // echo $tmpFilePath.'<br>';
        if ($tmpFilePath != ""){
          //Setup our new file path
          $Newpath = '../'.$PathKlaimPrapialang.$foldername;
          $newfilename = str_replace(" ","_","CLAIM_".$_FILES['fileklaim']['name'][$i]);          
          $Newpathfile = $Newpath.$newfilename;
          $Newdbfile = $foldername.$newfilename;

          if (!file_exists($path)) {
            mkdir($path, 0777);
            chmod($path, 0777);
          }
                    
          //Upload the file into the temp dir
          if(move_uploaded_file($tmpFilePath, $Newpathfile)) {
            $qupdate = "UPDATE ajkdocumentclaimmemberpra 
                        SET fileklaim='$Newdbfile'
                        WHERE idmemberpra = $idpra and 
                              iddoc = ".$_POST['goiagadva'][$i];
            // echo $qupdate;
            $res = mysql_query($qupdate);
            if($res){
              echo 'upload success'.'<br>';
              echo '<h4>Data Berhasil Disimpan</h4><meta http-equiv="refresh" content="5; url=../klaim/prapialang.php?opt='.$edit.'&id='.$idpra.'">';
            }else{
              echo 'upload gagal'.'<br>';
              echo '<h4>Data Gagal Disimpan</h4><meta http-equiv="refresh" content="10; url=../klaim/prapialang.php?opt='.$edit.'&id='.$idpra.'">';
            }
          }else{
            echo "error ".$_FILES["fileklaim"]["error"][$i];
            $result[$i] = false;
          }
        }
      }     

      $totalpost = count($_POST['tgl_terima']);
      for( $b=0 ; $b < $totalpost ; $b++ ) {
       
        if($_POST['tgl_terima'][$b]){
          $tglterima = " tgl_terima = '"._convertDate2($_POST['tgl_terima'][$b])."' ";
        }else{
          $tglterima = " tgl_terima = NULL";
        }
        $catatan = $_POST['catatan'][$b];

        $query = "
        UPDATE ajkdocumentclaimmemberpra 
        SET ".$tglterima.",
            catatan = '".$catatan."'
        WHERE idmemberpra = $idpra and 
              iddoc = ".$_POST['goiagadva'][$b];
        
        if(mysql_query($query)){
          // echo 'update dokumen success'.'<br>';
        }else{
          // echo 'update dokumen gagal'.'<br>';
        }

      }
      
    }
		?>
    
		<!-- begin #content -->
		<div id="content" class="content">
			<div class="panel p-30"><h4 class="m-t-0">Klaim Pra Pialang</h4>
        <div class="section-container section-with-top-border">
				
			    <?php if($opt=='list'){?>
          
          <a href="prapialang.php?opt=<?= $new ?>">
            <span class="fa-stack fa-2x text-primary">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-plus fa-stack-1x fa-inverse"></i>
						</span>
          </a>
          <!-- <a href="prapialang.php?opt=<?= $export ?>">
            <span class="fa-stack fa-2x text-primary">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-plus fa-stack-1x fa-inverse"></i>
						</span>
          </a> -->
					<a title="Download Excel"  href="../modules/modEXLdl_front.php?Rxls=klaimprapialang" target='_blank'">
						<span class="fa-stack fa-2x text-success">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-file-excel-o fa-stack-1x fa-inverse"></i>
						</span>
					</a>          
          <table id="table-prapialang" class="table table-striped table-bordered">
            <thead>
              <tr class="primary">
                <th class="text-center">No</th>
                <th class="text-center">Option</th>
                <th class="text-center">Cabang</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Jenis Klaim</th>
                <th class="text-center">Asuransi</th>
                <th class="text-center">Klaim Diajukan</th>
                <th class="text-center">Tanggal Pengajuan</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
            <?php 
              $query = "SELECT * FROM ajkklaimprapialang";
              $ret = mysql_query($query);
              while($row = mysql_fetch_array($ret)){
                $idpeserta = AES::encrypt128CBC($row['id'], ENCRYPTION_KEY);
            ?>
              <tr>
                
                <td class="text-center"><?= ++$no; ?></td>
                <td class="text-center"><a href="prapialang.php?opt=<?= $edit ?>&ab=<?= $idpeserta; ?>" class="btn btn-xs btn-success">Edit</a> <a href="prapialang.php?opt=<?= $delete ?>&ab=<?= $idpeserta; ?>" class="btn btn-xs btn-danger">Delete</a></td>
                <td class="text-center"><?= $row['cabang']; ?></td>
                <td><?= $row['nama']; ?></td>
                <td class="text-center"><?= $row['jenis_klaim']; ?></td>
                <td class="text-center"><?= $row['asuransi']; ?></td>
                <td class="text-right"><?= duit($row['nilaiklaimdiajukan']); ?></td>
                <td class="text-center"><?= _convertDate($row['tglpengajuan']); ?></td>
                <td class="text-center"><?= $row['statusklaim']; ?></td>
              </tr>
            <?php
              }
            ?>
              
            </tbody>
          </table>
          
          <?php }elseif($opt=="new" or $opt=="edit"){?>
          <ul class="nav nav-tabs">
            <li class="nav-item active"><a class="nav-link active" href="#data-klaim" aria-expanded="true" data-toggle="tab">Data Klaim</a></li>
            <?php if($opt!="new"){?>
            <li class="nav-item"><a class="nav-link" href="#data-dokumen" data-toggle="tab">Data Dokumen</a></li>
            <li class="nav-item"><a class="nav-link" href="#data-surat" data-toggle="tab">Data Surat</a></li>
            <?php } ?>
          </ul>
          <div class="tab-content m-b-0">
            <div class="tab-pane fade active in" id="data-klaim">            
              <form action="" id="inputmember" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="act" value="save">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="m-t-5">
                        <dl class="dl-horizontal">
                          <h4 class="text-center">DATA DEBITUR</h4>
                          <hr style="height:1px;border:none;color:#333;background-color:#333;">
                          <dt><label class="control-label"><strong>No Urut </label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="nourut" id="nourut" class="form-control" placeholder="No Urut" value="<?= $nourut; ?>" type="text" autocomplete="off"></div>
                            </div>
                          </dd>
                          <dt><label class="control-label"><strong>Cabang <span class="text-danger">*</span></label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12">
                                <?php if($opt=="new"){?>
                                  <select name="cabang" id="cabang" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    <?php $q = "select * from ajkcabang where del is null";
                                      $res = mysql_query($q);
                                      while($row = mysql_fetch_array($res)){                                    
                                    ?>
                                    <option value="<?= $row['name'] ?>" <?= $nmcabang==$row['name']? 'selected':''; ?> ><?= $row['name'] ?></option>
                                    <?php } ?>
                                  </select>
                                <?php }else{?>
                                  <input name="nmcabang" id="nmcabang" class="form-control" placeholder="Cabang" type="text" value="<?= $nmcabang; ?>" autocomplete="off" required>
                                <?php }?>
                              </div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>Asuransi <span class="text-danger">*</span></label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="asuransi" id="asuransi" class="form-control" placeholder="Asuransi" type="text" value="<?= $asuransi; ?>" autocomplete="off" required></div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>No Polis </label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="nopolis" id="nopolis" class="form-control" placeholder="No Polis" type="text" value="<?= $nopolis; ?>" autocomplete="off"></div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>Nama <span class="text-danger">*</span></label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="nama" id="nama" class="form-control" placeholder="Nama" type="text" value="<?= $nama; ?>" autocomplete="off" required></div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>Tgl Lahir </label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="tgllahir" id="tgllahir" class="form-control mydatepicker" placeholder="Tanggal Lahir" value="<?= _convertDate3($tgllahir); ?>" type="text" autocomplete="off"></div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>Tgl Akad </label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="tglakad" id="tglakad" class="form-control mydatepicker" placeholder="Tanggal Akad" type="text" value="<?= _convertDate3($tglakad); ?>" autocomplete="off"></div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>Tenor </label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="tenor" id="tenor" class="form-control" placeholder="Tenor" type="text" value="<?= $tenor; ?>" autocomplete="off"></div>
                            </div>
                          </dd>

                          <dt><label class="control-label"><strong>Plafond </label> :</strong></dt>
                          <dd>
                            <div class="form-group">
                              <div class="col-sm-12"><input name="plafond" id="plafond" class="form-control duit" placeholder="Plafond" type="text" value="<?= $plafond; ?>" autocomplete="off"></div>
                            </div>
                          </dd>

                        </dl>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <h4 class="text-center">Data Klaim</h4>
                      <hr style="height:1px;border:none;color:#333;background-color:#333;">
                      <dl class="dl-horizontal">
                        <dt><label class="control-label"><strong>Jenis Klaim <span class="text-danger">*</span></label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12">
                              <!-- <input name="jenis_klaim" id="jenis_klaim" class="form-control" placeholder="Jenis Klaim" type="text" value="<?= $jenis_klaim; ?>" autocomplete="off"> -->
                              <select name="jenis_klaim" id="jenis_klaim" class="form-control" required>
                                <option value="">- Pilih -</option>
                                <option value="PHK" <?= $jenis_klaim=="PHK"? 'selected':''; ?> >PHK</option>
                                <option value="Meninggal Dunia" <?= $jenis_klaim=="Meninggal Dunia"? 'selected':''; ?>>Meninggal Dunia</option>
                                <option value="PAW" <?= $jenis_klaim=="PAW"? 'selected':''; ?>>PAW</option>
                                <option value="Macet" <?= $jenis_klaim=="Macet"? 'selected':''; ?>>Macet</option>
                              </select>
                            </div>
                          </div>
                        </dd>
                        <div id="tempatmeninggal" <?= $jenis_klaim=="Meninggal Dunia"? '' : 'style="display:none"' ; ?>>
                        <dt><label class="control-label"><strong>Tempat Meninggal <span class="text-danger">*</span></label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12">
                              <select name="tempat_kejadian" id="tempat_kejadian" class="form-control" <?= $jenis_klaim=="Meninggal Dunia"? 'required' : '' ; ?>>
                                <option value="">- Pilih -</option>
                                <option value="RUMAH" <?= $tempatkejadian=="RUMAH"? 'selected':''; ?> >RUMAH</option>
                                <option value="RUMAH SAKIT" <?= $tempatkejadian=="RUMAH SAKIT"? 'selected':''; ?>>RUMAH SAKIT</option>
                                <option value="LUAR NEGRI" <?= $tempatkejadian=="Macet"? 'selected':''; ?>>LUAR NEGRI</option>
                                <option value="OTHER" <?= $tempatkejadian=="OTHER"? 'selected':''; ?>>OTHER</option>                                
                              </select>
                            </div>
                          </div>
                        </dd>
                        </div>
                        <dt><label class="control-label"><strong>Tgl Klaim <span class="text-danger">*</span></label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="tglklaim" id="tglklaim" class="form-control mydatepicker" placeholder="Tgl Klaim" type="text"  value="<?= _convertDate3($tglklaim); ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Status Klaim <span class="text-danger">*</span></label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12">
                              <select name="statusklaim" id="statusklaim" class="form-control" >
                                <option value="">- Pilih -</option>
                                <option value="PROSES ADONAI" <?= $statusklaim=="PROSES ADONAI"? 'selected':''; ?> >PROSES ADONAI</option>
                                <option value="PROSES ASURANSI" <?= $statusklaim=="PROSES ASURANSI"? 'selected':''; ?>>PROSES ASURANSI</option>
                                <option value="DITOLAK" <?= $statusklaim=="DITOLAK"? 'selected':''; ?>>DITOLAK</option>
                                <option value="CLOSE FILE" <?= $statusklaim=="CLOSE FILE"? 'selected':''; ?>>CLOSE FILE</option>                                
                              </select>                            
                            </div>
                          </div>
                        </dd>

                        <dt><label class="control-label"><strong>Tgl Pengajuan </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="tglpengajuan" id="tglpengajuan" class="form-control mydatepicker" placeholder="Tgl Pengajuan" type="text" value="<?= _convertDate3($tglpengajuan); ?>" autocomplete="off"></div>
                          </div>
                        </dd>

                        <dt><label class="control-label"><strong>Nilai Pengajuan </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="nilaiklaimdiajukan" id="nilaiklaimdiajukan" class="form-control duit" placeholder="Nilai Pengajuan" type="text" value="<?= $nilaiklaimdiajukan; ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Tgl Terima Dokumen </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12">
                            <input name="tglterimadokumen" id="tglterimadokumen" class="form-control mydatepicker" placeholder="Tgl Terima Dokumen" type="text" value="<?= _convertDate3($tglterimadokumen); ?>" autocomplete="off">
                            </div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Tgl Lapor Asuransi </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="tgllaporas" id="tgllaporas" class="form-control mydatepicker" placeholder="Tgl Lapor Asuransi" type="text" value="<?= _convertDate3($tgllaporas); ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Tgl Dokumen Lengkap </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="tgldokumenlengkap" id="tgldokumenlengkap" class="form-control mydatepicker" placeholder="Tgl Dokumen Lengkap" type="text" value="<?= _convertDate3($tgldokumenlengkap); ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Tgl dibayar Asuransi </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="tglbayaras" id="tglbayaras" class="form-control mydatepicker" placeholder="Tgl dibayar Asuransi" type="text" value="<?= _convertDate3($tglbayaras); ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Nilai dibayar Asuransi </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="nilaibayaras" id="nilaibayaras" class="form-control duit" placeholder="Nilai dibayar Asuransi" type="text" value="<?= $nilaibayaras; ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Tgl bayar ke client </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="tglbayarclient" id="tglbayarclient" class="form-control mydatepicker" placeholder="Tgl Bayar ke client" type="text" value="<?= _convertDate3($tglbayarclient); ?>" autocomplete="off"></div>
                          </div>
                        </dd>
                        <dt><label class="control-label"><strong>Nilai bayar ke client </label> :</strong></dt>
                        <dd>
                          <div class="form-group">
                            <div class="col-sm-12"><input name="nilaibayarclient" id="nilaibayarclient" class="form-control duit" placeholder="Nilai dibayar Asuransi" type="text" value="<?= $nilaibayarclient; ?>" autocomplete="off"></div>
                          </div>
                        </dd>

                      </dl>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <h4 class="text-center">KETERANGAN KLAIM</h4>
                      <div class="form-group">
                        <div class="col-sm-12">
                          <textarea name="keteranganklaim" id="keteranganklaim" class="form-control" rows="5" autocomplete="off"><?= $keteranganklaim; ?></textarea>
                        </div>
                      </div>
                    </div>                    
                  </div>
                </div>
                <div class="form-group m-b-0">
                  <div class="col-sm-12 text-center">
                    <a href="prapialang.php?opt=<?= $list ?>" class="btn btn-danger width-xs">Cancel</a>
                    <button type="submit" id="load" class="btn btn-success width-xs" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">Submit</button>
                  </div> 
                </div>
              </form>            
            </div>
            <?php if($opt!="new"){?>
            <div class="tab-pane fade" id="data-dokumen">            
              <form action="" id="dokumenklaim" class="form-horizontal" method="post" enctype="multipart/form-data">
                <input type="hidden" name="dokumen" value="save">
                <div class="panel-body">
                  <table class="table table-striped table-bordered">
                    <thead>
                      <tr class="primary">
                        <th class="text-center" width="1%">No.</th>
                        <th class="text-center" width="30%">Nama Dokumen</th>
                        <th class="text-center" width="15%">Tgl Terima</th>
                        <th class="text-center" width="40%">Keterangan</th>
                        <th class="text-center" width="14%">File</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
                      $query = "
                      SELECT * 
                      FROM ajkdocumentclaimmemberpra
                      WHERE idmemberpra = '".$id."'";

                      $res = mysql_query($query);
                      while($row = mysql_fetch_array($res)){
                    ?>
                    <tr>
                      <td class="text-center"><?= ++$no; ?></td>
                      <td class="text-center"><label><?= $row['nmdoc']; ?></label></td>
                      <td class="text-center"><input type="text" class="form-control mydatepicker" name="tgl_terima[]" value="<?= _convertDate3($row['tgl_terima']) ?>" autocomplete="off"></td>
                      <td class="text-left"><input type="text" name="catatan[]" class="form-control" value="<?= $row['catatan'] ?>" autocomplete="off"></td>
                      <td class="text-center">
                      <input type="hidden" name="goiagadva[]" value="<?= $row['iddoc'] ?>"> <?= ($row['fileklaim']) ? '<a href="../'.$PathKlaimPrapialang.$row['fileklaim'].'" class="btn btn-warning" target="_blank">View</a> <a href="?opt='.$deldok.'&goiagadva='.$id.'&adadgasf='.$row['iddoc'].'" class="btn btn-danger">Delete</a>' : '<input type="file" name="fileklaim['.$row['iddoc'].']">'; ?>
                      </td>
                    </tr>
                      <?php }?>
                    </tbody>
                  </table>
                  <div class="col-sm-12">
                    <h4 class="text-center">KETERANGAN KELENGKAPAN DOKUMEN</h4>
                    <textarea name="kelengkapandokumen" id="kelengkapandokumen" class="form-control" rows="5" autocomplete="off"><?= $kelengkapandokumen; ?></textarea>
                  </div>                                    
                </div>
                <div class="form-group m-b-0">
                  <div class="col-sm-12 text-center">
                    <a href="prapialang.php?opt=<?= $list ?>" class="btn btn-danger width-xs">Cancel</a>
                    <button type="submit" id="load" class="btn btn-success width-xs" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">Submit</button>
                  </div> 
                </div>                
              </form>
            </div>
            <div class="tab-pane fade" id="data-surat">
              <form action="" id="inputmember" class="form-horizontal" method="post" enctype="multipart/form-data">                
                <input type="hidden" name="act" value="save">
                <div class="panel-body">
                  <div class="form-group">
                    <label for="nosuratclient" class="col-sm-2 col-form-label">No Surat Client : </label>
                    <div class="col-sm-4">
                      <input type="text" class="form-control" name="nosuratclient" value="<?= $nosuratclient; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="nopengajuan" class="col-sm-2 col-form-label">No Pengajuan</label>
                    <div class="col-sm-4">
                      <input type="text" class="form-control" name="nopengajuan" value="<?= $nopengajuan; ?>">                      
                    </div>
                  </div>
                  <?php $idpeserta = AES::encrypt128CBC($id, ENCRYPTION_KEY); ?>
                  <a href="../modules/modmPdfdl.php?pdf=pengajuanklaimpra&s=<?= $idpeserta ?>" target="_blank" class="btn btn-success btn-sm">Cetak Surat Pengajuan</a>
                  <a href="../modules/modmPdfdl.php?pdf=suratpengantar&s=<?= $idpeserta ?>" target="_blank" class="btn btn-primary btn-sm">Cetak Surat Pengantar</a>                  
                </div>

                <div class="form-group m-b-0">
                  <div class="col-sm-12 text-center">
                    <a href="prapialang.php?opt=<?= $list ?>" class="btn btn-danger width-xs">Cancel</a>
                    <button type="submit" id="load" class="btn btn-success width-xs" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">Submit</button>
                  </div> 
                </div>

              </form>
            </div>
            <?php } ?>
          </div>

          
          <?php }?>
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

			// $(".active").removeClass("active");
			// document.getElementById("has_input").classList.add("active");

      document.getElementById("has_klaim").classList.add("active");
      document.getElementById("idsub_klaimprapialang").classList.add("active");

      $('.mydatepicker').datepicker({
        autoclose: true,

        todayHighlight: true,
        format: 'dd/mm/yyyy',
      });
      $('#jenis_klaim').change(function(){
        var jenis = $(this).val();
        if(jenis==="Meninggal Dunia"){
          document.getElementById('tempatmeninggal').style.display = "block";
        }else{
          document.getElementById('tempatmeninggal').style.display = "none";
        }

      });
    
      $('#cabang').select2();
      $('#table-prapialang').DataTable();
			$('.duit').mask('000,000,000,000,000' , {reverse: true});
		});
	</script>
</body>

</html>
