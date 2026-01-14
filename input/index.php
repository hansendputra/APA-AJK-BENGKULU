<?php
include "../param.php";
$setproduk = '';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<style type="text/css">
	#canvas{
  border: solid 1px blue;  
  width: 100%;
}
</style>

<?php
	_head($user,$namauser,$photo,$logo);

	$newdata ="Asuransi Jiwa Kredit(AJK)";
	$cekprod =" AND general ='T'";

  $peserta = null;
  $jiwa = null;
  $macet = null;
  if(isset($_REQUEST['i'])){
    $idpeserta = $_REQUEST['i'];
    $peserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."'"),MYSQLI_ASSOC);
    $pesertaas = mysql_query("SELECT * FROM ajkpesertaas WHERE idpeserta = '".$idpeserta."'");
    while($rowas = mysql_fetch_array($pesertaas)){
      if($rowas['idas'] == 2){
        $jiwa = "checked";
      }
      if($rowas['idas'] != 2){
        $macet = "checked";
      }
    }
  }

	if($_REQUEST['preview'] == "true"){
    $idpeserta = $_REQUEST['idpeserta'];
    $peserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."'"));
    $idproduk = $peserta['idpolicy'];
    $produk = mysql_fetch_array(mysql_query("select produk from ajkpolis where id = '".$idproduk."'"));
    $setproduk = '
    <input type="hidden" name="namaproduk" value="'.$idproduk.'">
    <div class="form-group">
        <label class="control-label col-sm-2">Pekerjaan</label>
        <div class="col-sm-10">
          <input class="form-control"type="text" value="'.$produk['produk'].'" readonly>
        </div>
    </div>';
  }else{
    $setproduk .=
    '<div class="form-group">
      <label class="control-label col-sm-2">Pekerjaan <span class="text-danger">*</span></label>
      <div class="col-sm-10">
        <select class="form-control" name="namaproduk" id="namaproduk" '.(isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '') .'>
          <option value="">-- Pilih Pekerjaan --</option>';
            $queryprod = mysql_query("SELECT * FROM ajkpolis WHERE del is null and idcost = '".$idclient."' ".$cekprod."");
            while($rowprod = mysql_fetch_array($queryprod)){
              if ($rowprod['status']=="Aktif") {
                $prodDis = '';
              }else{
                $prodDis = 'disabled';
              }
                $idprod = $rowprod['id'];
                $namaprod = $rowprod['produk'];
                $selectced = isset($peserta) && $peserta['idpolicy'] == $idprod ? 'selected' : '';
                $setproduk .= '<option value="'.$idprod.'" '.$prodDis.' '.$selectced.' >'.$namaprod.'</option>';
            }

          $setproduk .='
        </select>
        '.(isset($peserta) && $peserta['keterangan']!='' ? '<input type="hidden" name="namaproduk" value="'.$peserta['idpolicy'].'">' : '').'
      </div>
    </div>';
  }
?>

<body>
	<!-- begin #page-loader -->
	<div id="page-loader" class="page-loader fade in"><span class="spinner">Loading...</span></div>
	<!-- end #page-loader -->

	<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><p id="modal-header">Modal Header</p></h4>
        </div>
        <div class="modal-body">
          <video id="video" width="100%" height="auto" autoplay></video>
        </div>
        <div class="modal-footer text-center" id="modal-footer">
          <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Capture</button> -->
          <!-- <a href="javascript:;" onclick="getpict('cvsdebitur');$('#myModal').modal('hide');" class="btn btn-default">Simpan</a> -->
        </div>
      </div>
      
    </div>
  </div>
  <!-- Modal -->

  <!-- Modal -->
  <div class="modal fade" id="myModalSignPad" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><p id="modal-header-sign">Modal Header</p></h4>
        </div>
        <div class="modal-body">
          <canvas id="signpad">Sign Pad</canvas>
        </div>
        <div class="modal-footer text-center" id="modal-footer-sign">
        </div>
      </div>
      
    </div>
  </div>
  <!-- Modal -->

	<!-- begin #page-container -->
	<div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-two-sidebar page-with-footer page-with-top-menu page-without-sidebar">
		<?php
		_header($user,$namauser,$photo,$logo,$logoklient);
		_sidebar($user,$namauser,'','');
		?>
		<!-- begin #content -->
		<div id="content" class="content">
			<div class="panel p-30"><h4 class="m-t-0"><?php echo $newdata; ?></h4>
				<!-- begin section-container -->            
				<div class="section-container section-with-top-border">
			    <h4 class="m-t-0">Input Data Debitur</h4>
          <div class="alert alert-danger" id="err-message" style="display:none">
               <ul id="ul-err"></ul>
            </div>
          <?php 
          if(isset($peserta) && $peserta['keterangan']!=''){
            echo '
            <div class="alert alert-success" id="err-message">
              '.$peserta['keterangan'].'
            </div>';
          }
          if($_REQUEST['preview']=="true") { ?>
          
			    <form action="doinput.php" id="inputmember" class="form-horizontal" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label class="control-label col-sm-2">Cabang </label>
                <div class="col-sm-10">
                	<label class="control-label "><?php echo $namacabang ?> </label>
                </div>
            </div>
            <?php
            echo $setproduk;
            ?>
            <input type="hidden" name="medical" value="<?= $_POST['medical']?>">
            <div class="form-group">
                <label class="control-label col-sm-2">Nama</label>
                <div class="col-sm-10">
                	<input name="namatertanggung" id="namatertanggung" class="form-control"type="text" value="<?= $peserta['nama'] ?>" readonly>
                </div>
            </div>
						<div class="form-group">
                <label class="control-label col-sm-2">Jenis Kelamin</label>
                <div class="col-sm-10">
                	<select class="form-control" name="jnsklmn" readonly>
										<option value="">-- Pilih --</option>
										<option value="L" <?= $peserta['gender'] == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
										<option value="P" <?= $peserta['gender'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
									</select>
                </div>
            </div>	
            <div class="form-group coverage" style="display:none">
              <label class="control-label col-md-2">Cover Asuransi</label>
             <div class="col-md-10">
                <label class="checkbox-inline">
                  <input type="checkbox" value="T" id="jiwa" name="jiwa">Jiwa
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" value="T" id="macet" name="macet">Macet
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Tempat Lahir</label>
              <div class="col-sm-10">
                  <input name="tptlahir" class="form-control" type="text" value="<?= $peserta['tptlahir'] ?>" readonly>
              </div>
            </div>                                         
            <div class="form-group">
                <label class="control-label col-sm-2">Tanggal Lahir</label>
                <div class="col-sm-10">
                  <div class="input-group date" >
                      <input type="text" name="tgllahir" class="form-control" value="<?= _convertDate($peserta['tgllahir'])?>" readonly/>
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Usia</label>
                <div class="col-sm-10">
                  <div class="input-group date" >
                      <input type="text" name="tgllahir" class="form-control" value="<?= $peserta['usia']?>" readonly/>
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Nomor Identitas</label>
                <div class="col-sm-10">
                    <input type="text" name="nomorktp" class="form-control" value="<?= $peserta['nomorktp']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Alamat</label>
                <div class="col-sm-10">
                    <input type="text" name="alamat" class="form-control" value="<?= $peserta['alamatobjek']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Kota</label>
                <div class="col-sm-10">
                    <input type="text" name="kota" class="form-control" value="<?= $peserta['kota']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Kode Pos</label>
                <div class="col-sm-10">
                    <input type="text" name="kodepos" class="form-control" value="<?= $peserta['kodepos']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Pekerjaan</label>
                <div class="col-sm-10">
                    <input type="text" name="pekerjaan" class="form-control" value="<?= $peserta['pekerjaan']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Email</label>
                <div class="col-sm-10">
                    <input type="text" name="email" class="form-control" value="<?= $peserta['email']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">No. Telepon</label>
                <div class="col-sm-10">
                    <input type="text" name="notelp" class="form-control" value="<?= $peserta['notelp']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">No. Perjanjian Kredit</label>
                <div class="col-sm-10">
                    <input type="text" name="nopinjaman" class="form-control" value="<?= $peserta['nopinjaman']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">No. Rekening</label>
                <div class="col-sm-10">
                    <input type="text" minlength="13" maxlength="13" name="nomorpk" class="form-control" value="<?= $peserta['nomorpk']?>" readonly/>
                </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Tanggal Akad</label>
              <div class="col-sm-10">
                <div class="input-group date" >
                    <input type="text" name="tglakad" class="form-control" value="<?= _convertDate($peserta['tglakad'])?>" readonly/>
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
              </div>
            </div>            
            <div class="form-group">
                <label class="control-label col-sm-2">Plafond Kredit</label>
                <div class="col-sm-10">
                  <input type="text" name="plafon" class="form-control" value="<?= duit($peserta['plafond'])?>" readonly/>
                </div>
            </div>	                    
            <div class="form-group">
                <label class="control-label col-sm-2">Tenor (Bulan)</label>
                <div class="col-sm-10">
                  <input type="text" name="tenor" class="form-control" value="<?= $peserta['tenor']?>" readonly/>
                </div>                 
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Asumsi Premi</label>
                <div class="col-sm-10">
                  <input type="text" name="tenor" class="form-control" value="<?= duit($peserta['totalpremi']) ?>" readonly/>
                </div>                 
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">KTP</label>
                <div class="col-sm-10">
                  <a href="<?= '../myFiles/_peserta/'.$peserta['idpeserta'].'/'.$peserta['ktp_file'] ?>" target="_blank" class="btn-sm btn-primary">Download</a>
                </div>                 
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">SPAJK/LPK</label>
                <div class="col-sm-10">
                  <a href="<?= '../myFiles/_peserta/'.$peserta['idpeserta'].'/'.$peserta['sppa_file'] ?>" target="_blank" class="btn-sm btn-primary">Download</a>
                </div>                 
            </div>            

            <div class="form-group m-b-0">
              <div class="col-sm-12 text-center">
                <a href="javascript:;" onClick="simpan()" class="btn btn-success width-xs">Submit</a>
                <a href="javascript:;" onClick="kembali()" class="btn btn-danger width-xs">Edit</a>
              </div>
            </div>
          </form>
          <?php }else{ ?>            
          <form action="doinput.php" id="inputmember" class="form-horizontal" method="post" enctype="multipart/form-data">
            <?php if(isset($peserta)){ ?>
              <input type="hidden" name="idpeserta" value="<?= $peserta['idpeserta'] ?>">
            <?php } ?>
            <div class="form-group">
                <label class="control-label col-sm-2">Cabang </label>
                <div class="col-sm-10">
                	<label class="control-label "><?php echo $namacabang ?> </label>
                </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Produk <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <select class="form-control" name="typedata" id="typedata" <?= isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '' ?>>
                  <option value="">-- Pilih Produk --</option>
                  <option value="Multiguna" <?= isset($peserta) && $peserta['typedata'] == 'Multiguna' ? 'selected' : '' ?>>Multiguna</option>
                  <option value="KGU" <?= isset($peserta) && $peserta['typedata'] == 'KGU' ? 'selected' : '' ?>>KGU</option>
                  <option value="KUR" <?= isset($peserta) && $peserta['typedata'] == 'KUR' ? 'selected' : '' ?>>KUR</option>
                  <option value="KPR" <?= isset($peserta) && $peserta['typedata'] == 'KPR' ? 'selected' : '' ?>>KPR</option>
                  <option value="THT" <?= isset($peserta) && $peserta['typedata'] == 'THT' ? 'selected' : '' ?>>THT</option>
                </select>
                 <?php if(isset($peserta) && $peserta['keterangan']!=''){ ?>
                <input type="hidden" name="typedata" value="<?= $peserta['typedata'] ?>">
                <?php } ?>
              </div>
            </div>            
            <?php
            echo $setproduk;
            ?>
            <input type="hidden" name="medical" value="<?= $_REQUEST['med']?>">
            <div class="form-group">
                <label class="control-label col-sm-2">Nama <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                	<input name="namatertanggung" id="namatertanggung" class="form-control text-uppercase" placeholder="Nama" type="text" value="<?= isset($peserta) ? $peserta['nama'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?>>
                </div>
            </div>
						<div class="form-group">
                <label class="control-label col-sm-2">Jenis Kelamin <span class="text-danger">*</span></label>
                <div class="col-sm-10">                  
                	<select class="form-control" name="jnsklmn" <?= isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '' ?>>
										<option value="">-- Pilih --</option>
										<option value="L" <?= isset($peserta) && $peserta['gender'] == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
										<option value="P" <?= isset($peserta) && $peserta['gender'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
									</select>
                  <?php if(isset($peserta) && $peserta['keterangan']!=''){ ?>
                  <input type="hidden" name="jnsklmn" value="<?= $peserta['gender'] ?>">
                  <?php } ?>
                </div>
            </div>	
            <div class="form-group coverage" <?= isset($peserta) ? '' : 'style="display:none"' ?>>
              <label class="control-label col-md-2">Cover Asuransi</label>
             <div class="col-md-10">
                <label class="checkbox-inline">
                  <input type="checkbox" value="T" id="jiwa" name="jiwa" <?= $jiwa ?> <?= isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '' ?> >Jiwa
                </label>
                <label class="checkbox-inline">
                  <input type="checkbox" value="T" id="macet" name="macet" <?= $macet ?> <?= isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '' ?> >Macet
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Tempat Lahir <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                  <input name="tptlahir" id="tptlahir" class="form-control" placeholder="Tempat Lahir" type="text" value="<?= isset($peserta) ? $peserta['tptlahir'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?>>
              </div>
            </div>                                         
            <div class="form-group">
                <label class="control-label col-sm-2">Tanggal Lahir <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <div class="input-group date" >
                      <input type="text" id="tgllahir" name="tgllahir" class="form-control" placeholder="Tanggal Lahir" value="<?= isset($peserta) ? date("d/m/Y", strtotime($peserta['tgllahir'])) : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '' ?> />
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <?php if(isset($peserta) && $peserta['keterangan']!=''){ ?>
                      <input type="hidden" name="tgllahir" value="<?= isset($peserta) ? date("d/m/Y", strtotime($peserta['tgllahir'])) : '' ?>">
                      <?php } ?>
                  </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Nomor Identitas <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input name="nomorktp" id="nomorktp" class="form-control" placeholder="Nomor Identitas" type="text" maxlength="16" value="<?= isset($peserta) ? $peserta['nomorktp'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?>/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Alamat <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input name="alamat" id="alamat" class="form-control" placeholder="Alamat" type="text" value="<?= isset($peserta) ? $peserta['alamatobjek'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Kota <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input name="kota" id="kota" class="form-control" placeholder="kota" type="text" value="<?= isset($peserta) ? $peserta['kota'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?>>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Kode Pos </label>
                <div class="col-sm-10">
                    <input name="kodepos" id="kodepos" class="form-control" placeholder="Kode Pos" type="text" value="<?= isset($peserta) ? $peserta['kodepos'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Pekerjaan <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input name="pekerjaan" id="pekerjaan" class="form-control text-uppercase" placeholder="Pekerjaan" type="text" value="<?= isset($peserta) ? $peserta['pekerjaan'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Email </label>
                <div class="col-sm-10">
                    <input name="email" id="email" class="form-control" placeholder="Email" type="email" value="<?= isset($peserta) ? $peserta['email'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">No. Telepon </label>
                <div class="col-sm-10">
                    <input name="notelp" id="notelp" class="form-control text-uppercase" placeholder="No. Telepon" type="text" value="<?= isset($peserta) ? $peserta['notelp'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">No. Perjanjian Kredit <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input name="nopinjaman" id="nopinjaman" class="form-control text-uppercase" placeholder="No. Perjanjian Kredit" type="text" value="<?= isset($peserta) ? $peserta['nopinjaman'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">No. Rekening <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input name="nomorpk" id="nomorpk" class="form-control text-uppercase" placeholder="No. Rekening" type="text" value="<?= isset($peserta) ? $peserta['nomorpk'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Tanggal Akad <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <div class="input-group date" >
                    <input type="text" id="tglakad" name="tglakad" class="form-control" placeholder="Tanggal Akad" value="<?= isset($peserta) ? date("d/m/Y", strtotime($peserta['tglakad'])) : date('d/m/Y') ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'disabled ' : '' ?> />  
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <?php if(isset($peserta) && $peserta['keterangan']!=''){ ?>
                    <input type="hidden" name="tglakad" value="<?= isset($peserta) ? date("d/m/Y", strtotime($peserta['tglakad'])) : '' ?>">
                    <?php } ?>
                </div>
              </div>
            </div>            
            <div class="form-group">
                <label class="control-label col-sm-2">Plafond Kredit <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                	<input name="plafon" id="plafon" class="form-control" placeholder="Silahkan Input Nilai Plafon Kredit" type="text" value="<?= isset($peserta) ? duit($peserta['plafond']) : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>	                    
            <div class="form-group">
                <label class="control-label col-sm-2">Tenor (Bulan) <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                	<input name="tenor" id="tenor" class="form-control" placeholder="Silahkan Input Tenor" type="text" value="<?= isset($peserta) ? $peserta['tenor'] : '' ?>" <?= isset($peserta) && $peserta['keterangan']!='' ? 'readonly ' : '' ?> />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Upload KTP<span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <?php if(isset($peserta) && $peserta['ktp_file']!=''){ ?>
                    <a href="<?= '../myFiles/_peserta/'.$peserta['idpeserta'].'/'.$peserta['ktp_file'] ?>" target="_blank" class="btn-sm btn-primary">Download</a>
                    <a href="javascript:;" onclick="hapusfile('ktp','<?= $peserta['idpeserta'] ?>')" class="btn-sm btn-danger">Hapus</a>
                  <?php }else{ ?>
                	<input name="filektp" id="filektp" class="form-control" placeholder="Silahkan Upload KTP" type="file" accept="image/*,application/pdf">
                  <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Upload SPAJK/LPK<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                  <?php if(isset($peserta) && $peserta['sppa_file']!=''){ ?>
                    <a href="<?= '../myFiles/_peserta/'.$peserta['idpeserta'].'/'.$peserta['sppa_file'] ?>" target="_blank" class="btn-sm btn-primary">Download</a>
                    <a href="javascript:;" onclick="hapusfile('sppa','<?= $peserta['idpeserta'] ?>')" class="btn-sm btn-danger">Hapus</a>
                  <?php }else{ ?>
                	<input name="filesppa" id="filesppa" class="form-control" placeholder="Silahkan Upload SPPA" type="file" accept="image/*,application/pdf">
                  <?php } ?>
                </div>
                <div class="col-sm-2">
                	<a href="../myFiles/Surat Pernyataan Debitur.pdf" id="document" target="_blank" class="btn btn-success" download>Download SPD</a>
                </div>                
                <div class="col-sm-2">
                	<a href="../myFiles/FRM-NB01-004_Surat Permohonan Asuransi Jiwa_rev162025.pdf" id="document" target="_blank" class="btn btn-success" download>Download SPAJK</a>
                </div>
                <div class="col-sm-2">
                	<a href="../myFiles/Victoria-Asuransi Jiwa-FRM-NB02-008_Formulir Laporan Pemeriksaan Kesehatan.pdf" id="document" target="_blank" class="btn btn-success" download>Download LPK</a>
                </div>
                
            </div>
            <div class="form-group m-b-0">
              <div class="col-sm-12 text-center">
                <button type="submit" id="load" class="btn btn-success width-xs" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">Submit</button>
              </div>
            </div>
          </form>
          <?php } ?>
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

      $('#namaproduk').change(function() {
        if(this.value !== "11" && this.value !== "12"){
          $('.coverage').show();
        }else{
          $('.coverage').hide();
        }          
      });
       $('#typedata').change(function() {
         $.ajax({
          type: "GET",
          dataType: 'JSON',
          url : "data.php?produk="+this.value,
          success: function(data){
            var $el = $("#namaproduk");
            $el.empty(); // remove old options
            $el.append($("<option></option>").attr("value", "").text("- Pilih -"));
            $.each(data.produk, function(key,value) {     
              $el.append($("<option></option>").attr("value", value.id).text(value.produk));
            });
          },          
        });
      });

      <?php
      	if(isset($_REQUEST['pesan'])){
          echo 
          'swal({
            title: "Information",
            text: "Success",
            type: "success",
            confirmButtonColor: "#DD6B55",
            showConfirmButton:true,
          });
          setTimeout(function() {
            window.location = "../masterdata?type='.AES::encrypt128CBC('peserta', ENCRYPTION_KEY) .'";
          }, 2000);	';
          }
      ?>

			$(".active").removeClass("active");
			document.getElementById("has_input").classList.add("active");
			$('#inputmember').bootstrapValidator({
				err: {
					container: 'tooltip'
				},
				framework: 'bootstrap',
				icon: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
				},

				fields: {
					namaproduk: {
						validators: {	notEmpty: {	message: 'Silahkan pilih nama produk'	}	}
					},
					alamat: {
						validators: {	notEmpty: {	message: 'Silahkan input alamat member'	}	}
					},
					metkotamember: {
						validators: {	notEmpty: {	message: 'Silahkan input alamat kota member'	}	}
					},
					metkodeposmember: {
						validators: {	notEmpty: {	message: 'Silahkan input alamat kodepos member'	}	}
					},
					metalamatobjek: {
						validators: {	notEmpty: {	message: 'Silahkan input alamat objek'	}	}
					},
					metkotaobjek: {
						validators: {	notEmpty: {	message: 'Silahkan input alamat kota objek'	}	}
					},
					metkodeposobjek: {
						validators: {	notEmpty: {	message: 'Silahkan input alamat kodepos objek'	}	}
					},
					namatertanggung: {
						validators: {	notEmpty: {	message: 'Silahkan input nama tertanggung'	}	}
					},
					nomorktp: {
						validators: {	notEmpty: {	message: 'Silahkan input nomor KTP '	}, 
            callback: {
                        message: 'please enter only numbers',
                        callback: function(value, validator, $field) {
                            if (isValid(value)) {
                              return {
                                valid: false,
                              };
                            }
                            else
                            {
                              return {
                                valid: true,
                              };    
                            }

                        }
                    }	}
					},
					nomorpk: {
						validators: {	
              notEmpty: {	message: 'Silahkan input nomor Rekening'	},
              stringLength: {
                  min: 13,
                  max: 13,
                  message: 'Nomor Rekening harus 13 digit'
              }, 
              callback: {
                message: 'please enter only numbers',
                callback: function(value, validator, $field) {
                  if (isValid(value)) {
                    return {
                      valid: false,
                    };
                  }
                  else
                  {
                    return {
                      valid: true,
                    };    
                  }

              }
          }	}
					},
					tgllahir: {
						validators: {
							notEmpty: {	message: 'Silahkan input tanggal lahir'	},
							date: {	format: 'DD/MM/YYYY',
									message: 'Format tanggal lahir dd/mm/yyyy'
							}
						}
					},
					tglakad: {
						validators: {
							notEmpty: {	message: 'Silahkan input tanggal akad'	},
							date: {	format: 'DD/MM/YYYY',
									message: 'Format tanggal akad dd/mm/yyyy'
							}
						}
					},
					tenor: {
						validators: {	notEmpty: {	message: 'Silahkan input tenor (bulan)'	}	}
					},
					jnsklmn: {
						validators: {	notEmpty: {	message: 'Silahkan input jenis kelamin'	}	}
					},
					nilaidiajukan: {
						validators: {	notEmpty: {	message: 'Silahkan input nilai objek yang diajukan'	}	}
					},
					plafon: {
						validators: {	notEmpty: {	message: 'Silahkan input plafon'	}	}
					},
          pekerjaan: {
						validators: {	notEmpty: {	message: 'Silahkan input Pekerjaan'	}	}
					},
          nopinjaman: {
						validators: {	notEmpty: {	message: 'Silahkan input No. Perjanjian Kredit'	}	}
					},
          tptlahir: {
						validators: {	notEmpty: {	message: 'Silahkan input Tempat Lahir'	}	}
					},
          kota: {
						validators: {	notEmpty: {	message: 'Silahkan input Kota'	}	}
					},
				}
			}).on('success.form.bv',function(e){
          e.preventDefault();



          var $form = $(e.target);
          var bv = $form.data('bootstrapValidator');
		      var dataform = new FormData($("#inputmember")[0]);

            // $("#load").button('loading');
            // $('#load').prop('disabled', false);
            // $("#load").button('reset');
            $("#load").removeAttr("disabled");
            $("#load").removeClass("disabled");
            
            $('#ul-err li').remove();
            $.ajax({
              type: "POST",
              url : "doinput.php",
              data:dataform,
              cache: false,
              processData: false,
              contentType: false,
              success: function(msg){
                var result = JSON.parse(msg);
                if(result.status==="success"){
                  window.location.href = window.location.href+"&preview=true&idpeserta="+result.idpeserta;
                }else{
                  swal({
                        title: "Information",
                        text: "Data Gagal disimpan",
                        type: "error",
                        confirmButtonColor: "#DD6B55",
                        showConfirmButton:true,
                        timer: 2000,
                  });
                  window.scrollTo(0,0);
                  $('#err-message').show();
                  $.each(JSON.parse(msg),function(i,error)
                  {
                    $('#ul-err').append('<li>'+error+'</li>');
                  });
                  
                }	            	                     
              },          
            });
            
         });

      
			$("#tgllahir").datepicker({
				todayHighlight: !0,
				format:'dd/mm/yyyy',
				autoclose: true
			}).on('changeDate', function(e) {
				$('#inputmember').bootstrapValidator('revalidateField', 'tgllahir');
			});      
      $("#tglakad").datepicker({
				todayHighlight: !0,
				format:'dd/mm/yyyy',
				autoclose: true,
			}).on('changeDate', function(e) {
				$('#inputmember').bootstrapValidator('revalidateField', 'tglakad');
			});    

			$('#plafon').mask('000,000,000,000,000' , {reverse: true});
			$('#nilaidiajukan').mask('000,000,000,000,000' , {reverse: true});
			$('#tgllahir').mask('99/99/9999');
			$('#tglakad').mask('99/99/9999');
			$('#tenor').mask('000' , {reverse: true});    
		});

    function simpan(){
      $.ajax({
        type: "POST",
        url : "doinput.php?action=submit&idpeserta=<?= $idpeserta ?>",
        cache: false,
        processData: false,
        contentType: false,
        success: function(msg){
          var result = JSON.parse(msg);
          if(result.status==="success"){
            window.location.replace("redirect.php?type=<?= AES::encrypt128CBC('blank', ENCRYPTION_KEY) ?>");
          }else{
            swal({
                  title: "Information",
                  text: "Data Gagal disimpan",
                  type: "error",
                  confirmButtonColor: "#DD6B55",
                  showConfirmButton:true,
                  timer: 2000,
            });
            window.scrollTo(0,0);
            $('#err-message').show();
            $.each(JSON.parse(msg),function(i,error)
            {
              $('#ul-err').append('<li>'+error+'</li>');
            });
            
          }	            	                     
        },          
      });
    }

    function kembali(){
      $.ajax({
        type: "POST",
        url : "doinput.php?action=edit&idpeserta=<?= $idpeserta ?>",
        cache: false,
        processData: false,
        contentType: false,
        success: function(msg){
          var result = JSON.parse(msg);
          if(result.status==="success"){
            history.back();
          }else{
            swal({
                  title: "Information",
                  text: "Data Gagal disimpan",
                  type: "error",
                  confirmButtonColor: "#DD6B55",
                  showConfirmButton:true,
                  timer: 2000,
            });
            window.scrollTo(0,0);
            $('#err-message').show();
            $.each(JSON.parse(msg),function(i,error)
            {
              $('#ul-err').append('<li>'+error+'</li>');
            });
            
          }	            	                     
        },          
      });
    }
   
    function isValid(value)
    {
      var fieldNum = /^[a-z]+$/i;

      if ((value.match(fieldNum))) {
          return true
      }
      else
      {
          return false
      }

    }

    function hapusfile(jenis,idpeserta){
      $.ajax({
        type: "POST",
        url : "doinput.php?action=hapusfile&jenis="+jenis+"&idpeserta="+idpeserta,
        cache: false,
        processData: false,
        contentType: false,
        success: function(msg){
          var result = JSON.parse(msg);
          if(result.status==="success"){          
            swal({
                  title: "Information",
                  text: "File berhasil dihapus",
                  type: "success",
                  confirmButtonColor: "#DD6B55",
                  showConfirmButton:true,
                  timer: 2000,
            });
            window.location.reload();
          }else{
            swal({
                  title: "Information",
                  text: "File gagal dihapus",
                  type: "error",
                  confirmButtonColor: "#DD6B55",
                  showConfirmButton:true,
                  timer: 2000,
            });
            window.scrollTo(0,0);
            $('#err-message').show();
            $.each(JSON.parse(msg),function(i,error)
            {
              $('#ul-err').append('<li>'+error+'</li>');
            });          
          }	            	                     
        },          
      });
    }
	</script>
</body>

</html>
