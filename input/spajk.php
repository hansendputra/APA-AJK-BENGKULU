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
    $newdata ="Asuransi Jiwa Kredit(AJK)";
		?>
		<!-- begin #content -->
		<div id="content" class="content">
      <?php 
      // Fungsi untuk upload dokumen
      function uploadDocuments($idpeserta) {
        $uploadDir = '../myFiles/_peserta/'.$idpeserta.'/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $document1_name = '';
        $document2_name = '';
        
        // Upload Document 1
        if(isset($_FILES['document1']) && $_FILES['document1']['error'] == 0) {
            $allowed = array('pdf', 'jpg', 'jpeg', 'png');
            $filename = $_FILES['document1']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed) && $_FILES['document1']['size'] <= 5242880) { // 5MB
                $document1_name = 'doc1_'.time().'_'.uniqid().'.'.$ext;
                move_uploaded_file($_FILES['document1']['tmp_name'], $uploadDir.$document1_name);
            }
        }
        
        // Upload Document 2
        if(isset($_FILES['document2']) && $_FILES['document2']['error'] == 0) {
            $allowed = array('pdf', 'jpg', 'jpeg', 'png');
            $filename = $_FILES['document2']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed) && $_FILES['document2']['size'] <= 5242880) { // 5MB
                $document2_name = 'doc2_'.time().'_'.uniqid().'.'.$ext;
                move_uploaded_file($_FILES['document2']['tmp_name'], $uploadDir.$document2_name);
            }
        }
        
        // Return array dengan nama file dan JSON
        $documents = array();
        if($document1_name != '') $documents['document1'] = $document1_name;
        if($document2_name != '') $documents['document2'] = $document2_name;
        
        return array(
          'documents' => $documents,
          'json' => json_encode($documents)
        );
      }
      
      switch (AES::decrypt128CBC($_REQUEST['xq'],ENCRYPTION_KEY)) { 
      case "form":
        $peserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$_REQUEST['is']."'"));
        $queryanswer = "SELECT * FROM ajkformpesertaanswer WHERE idpeserta= '".$_REQUEST['is']."'";
        $queryanswer1 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 1'));
        $queryanswer2 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 2'));
        $queryanswer3 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 3'));
        $queryanswer4 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 4'));
        $queryanswer5 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 5'));
        $queryanswer6 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 6'));
        $tb = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 7'));
        $bb = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 8'));
        $keterangan = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 9'));
        $queryanswer41 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 10'));
        $queryanswer42 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 11'));
        $queryanswer43 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 12'));
        $queryanswer44 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 13'));
        $queryanswer45 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 14'));
        $queryanswer46 = mysql_fetch_array(mysql_query($queryanswer. ' AND idquestion = 15'));

        if(isset($idas))
        {
          $submission = AES::encrypt128CBC('approveas', ENCRYPTION_KEY);
        }else{
          $submission = AES::encrypt128CBC('simpan', ENCRYPTION_KEY);
        }
      ?>
			<div class="panel p-30"><h4 class="m-t-0"><?php echo $newdata; ?></h4>
				<!-- begin section-container -->            
				<div class="section-container section-with-top-border">
			    <h4 class="m-t-0">Input SPAJK</h4>
          <div class="alert alert-danger" id="err-message" style="display:none">
               <ul id="ul-err"></ul>
            </div>
			    <form action="spajk.php?xq=<?= $submission ?>" id="inputmember" class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" name="idpeserta" value="<?= $_REQUEST['is']?>">
            <div class="form-group">
              <label class="control-label col-sm-2">Nama Lengkap </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" value="<?= $peserta['nama'] ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Tempat/Tanggal Lahir </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="tptlahir" <?= isset($idas) ? 'required': '';?> value="<?= $peserta['tptlahir'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
              <div class="col-sm-3">
                <input type="text" class="form-control" value="<?= _convertDate($peserta['tgllahir']) ?>" readonly>
              </div>
              <div class="col-sm-3">
                <input type="text" class="form-control" value="Usia <?= $peserta['usia'] ?> Tahun" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">No. KTP/Paspor </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" value="<?= $peserta['nomorktp'] ?>" readonly>
              </div>
              <label class="control-label col-sm-2">Jenis Kelamin </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" value="<?= $peserta['gender'] == 'L' ? 'Laki - Laki' : 'Perempuan' ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Alamat Rumah </label>
              <div class="col-sm-10">
                <textarea class="form-control" readonly><?= $peserta['alamatobjek'] ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Kota </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="kota" value="<?= $peserta['nomorktp'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
              <label class="control-label col-sm-2">Kode POS </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="kodepos" value="<?= $peserta['kodepos'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
            </div>
            <div class="form-group" style="display:none">
              <label class="control-label col-sm-2">Status </label>
              <div class="col-sm-10">
                <select name="stsmarital" id="stsmarital" class="form-control" <?= isset($idas) ? 'readonly': '';?>>
                  <option value="">-Pilih-</option>
                  <option value="Belum Menikah" <?= $peserta['stsmarital'] == 'Belum Menikah' ? 'checked' : '' ?>>Belum Menikah</option>
                  <option value="Menikah" <?= $peserta['stsmarital'] == 'Menikah' ? 'checked' : '' ?>>Menikah</option>
                  <option value="Janda/Duda" <?= $peserta['stsmarital'] == 'Janda/Duda' ? 'checked' : '' ?>>Janda/Duda</option>
                  <option value="Cerai" <?= $peserta['stsmarital'] == 'Cerai' ? 'checked' : '' ?>>Cerai</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">No. Telepon </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="notelp" value="<?= $peserta['notelp'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
              <label class="control-label col-sm-2">Email </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="email" value="<?= $peserta['email'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Pekerjaan </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="pekerjaan" value="<?= $peserta['pekerjaan'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
            </div>
            <hr>
            <div class="form-group" style="display:none">
              <label class="control-label col-sm-2">No. Polis </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Nama Pemegang Polis </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" value="<?= $peserta['nama'] ?>" readonly>
              </div>
            </div>
             <div class="form-group">
              <label class="control-label col-sm-2">Masa Asuransi </label>
              <div class="col-sm-2">
                <input type="text" class="form-control" value="<?= $peserta['tenor'] ?> Tahun" readonly>
              </div>
              <label class="control-label col-sm-2">Mulai </label>
              <div class="col-sm-6">
                <input type="text" class="form-control" value="<?= _convertDate($peserta['tglakad']) ?> s.d <?= _convertDate($peserta['tglakhir']) ?>" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Uang Pertanggungan Awal </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" value="<?= duit($peserta['plafond']) ?>" readonly>
              </div>
            </div>
            <div class="form-group" style="display:none">
              <label class="control-label col-sm-2">Jenis Pertanggungan </label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="jenispertanggungan" value="<?= $peserta['jenispertanggungan'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
            </div>
            <hr>
            <div class="form-group"  style="display:none">
              <label class="control-label col-sm-2">Berat Badan </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="bb" value="<?= $bb['answer'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>
              <label class="control-label col-sm-2">Tinggi Badan </label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="tb" value="<?= $tb['answer'] ?>" <?= isset($idas) ? 'readonly': '';?>>
              </div>              
            </div>
            <div class="form-group" style="display:none">
              <table class="table" width="100%">
                <tr>
                  <td style="vertical-align: top;" width="1%">1</td>
                  <td style="vertical-align: top;" width="90%">Apakah anda dalam keadaan sehat ?</td>
                  <td style="vertical-align: top;" width="10%"><input class="form-check-input" type="radio" name="q1" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer1) && $queryanswer1['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q1" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer1) && $queryanswer1['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td style="vertical-align: top;">2</td>
                  <td style="vertical-align: top;">Dalam 5 tahun terakhir, termasuk hari ini, apakah anda pernah atau sedang dalam perawatan dokter atau menerima pengobatan atau mengalami pembedahan atau dirawat di rumah sakit?</td>
                  <td style="vertical-align: top;"><input class="form-check-input" type="radio" name="q2" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer1) && $queryanswer1['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q2" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer2) && $queryanswer2['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td style="vertical-align: top;">3</td>
                  <td>Apakah ada anggota keluarga (ayah/ibu/adik/kakak) yang menderita penyakit jantung, kanker, stroke, diabetes mellitus sebelum mencapai usia 60 tahun?</td>
                  <td><input class="form-check-input" type="radio" name="q3" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer3) && $queryanswer3['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q3" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer3) && $queryanswer3['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td style="vertical-align: top;">4</td>
                  <td style="vertical-align: top;">Apakah Anda pernah menderita sakit atau sedang dalam perawatan dokter atau menerima pengobatan atau mempunyai keluhan/gejala-gejala atas penyakit-penyakit dibawah ini:
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td>a. Jantung, Stroke atau Gangguan Pembuluh Darah Otak, Nyeri Dada atau Penyakit Jantung, Tekanan Darah Tinggi</td>
                  <td><input class="form-check-input" type="radio" name="q41" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer41) && $queryanswer41['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q41" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer41) && $queryanswer41['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td></td>
                  <td>b. Gagal Ginjal, Hati (Liver), Lambung atau Usus (Saluran Cerna), Paru-paru atau Saluran Pernafasan</td>
                  <td><input class="form-check-input" type="radio" name="q42" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer42) && $queryanswer42['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q42" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer42) && $queryanswer42['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td></td>
                  <td>c. Diabetes Mellitus (Kencing Manis), Kelumpuhan atau Paralisis, Kanker atau Tumor, AIDS Pernahkah perusahaan asuransi lain menolak atau menerima permohonan Anda dengan kondisi khusus atau selama 3 tahun terakhir pernah mengajukan klaim pada asuransi manapun?</td>
                  <td><input class="form-check-input" type="radio" name="q43" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer43) && $queryanswer43['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q43" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer43) && $queryanswer43['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td></td>
                  <td>d. Vertigo, Epilepsi, Kejang Demam, Nyeri Sendi (Rheumatism)</td>
                  <td><input class="form-check-input" type="radio" name="q44" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer44) && $queryanswer44['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q44" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer44) && $queryanswer44['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td></td>
                  <td>e. Apakah Anda merokok? Bila YA, berapa batang sehari?</td>
                  <td><input class="form-check-input" type="radio" name="q45" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer45) && $queryanswer45['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q45" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer45) && $queryanswer45['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td></td>
                  <td>f. Khusus wanita, apakah Anda dalam keadaan hamil? Jika Ya, berapa usia kandungan?</td>
                  <td><input class="form-check-input" type="radio" name="q46" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer46) && $queryanswer1['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q46" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer46) && $queryanswer46['answer'] == 'F' ? 'checked' : '' ?>> Tidak</td>
                </tr>                                                                                
                <tr>
                  <td style="vertical-align: top;">5</td>
                  <td style="vertical-align: top;">Apakah Anda melakukan/pernah melakukan olahraga yang berisiko tinggi (mendaki gunung, layang gantung, olahraga bermotor, menyelam, dll) atau melakukan penerbangan selain sebagai penumpang pesawat komersial yang berjadwal?</td>
                  <td><input class="form-check-input" type="radio" name="q5" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer5) && $queryanswer5['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q5" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer5) && $queryanswer5['answer'] == 'F' ? 'checked' : '' ?> > Tidak</td>
                </tr>
                <tr>
                  <td style="vertical-align: top;">6</td>
                  <td style="vertical-align: top;">Pernahkah perusahaan asuransi lain menolak atau menerima permohonan Anda dengan kondisi khusus atau selama 3 tahun terakhir pernah mengajukan klaim pada asuransi manapun?</td>
                  <td><input class="form-check-input" type="radio" name="q6" value="T" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer6) && $queryanswer6['answer'] == 'T' ? 'checked' : '' ?> > Ya <input class="form-check-input" type="radio" name="q6" value="F" <?= isset($idas) ? 'disabled': '';?> <?= isset($queryanswer6) && $queryanswer6['answer'] == 'F' ? 'checked' : '' ?>> Tidak</td>
                </tr>
                <tr>
                  <td colspan="3">Beri penjelasan Nama Penyakit, Tanggal Pertama kali di Diagnosa, Lama menderita penyakit, Nama & alamat dokter yang merawat atau informasi lain yang relevan dan Kondisi saat ini apabila terdapat jawaban "Ya" dari pertanyaan diatas.<br><textarea class="form-control" name="keterangan" <?= isset($idas) ? 'readonly': '';?>></textarea></td>
                </tr>
              </table>            
            </div>            
            <hr>
            <div class="form-group">
                <label class="control-label col-sm-2">KTP</label>
                <div class="col-sm-10">
                  <?php if(isset($peserta) && $peserta['ktp_file']!=''){ ?>
                    <a href="<?= '../myFiles/_peserta/'.$peserta['idpeserta'].'/'.$peserta['ktp_file'] ?>" target="_blank" class="btn-sm btn-primary">Download</a>
                  <?php } ?>
                </div>                 
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">SPAJK/LPK</label>
                <div class="col-sm-10">
                  <?php if(isset($peserta) && $peserta['sppa_file']!=''){ ?>
                    <a href="<?= '../myFiles/_peserta/'.$peserta['idpeserta'].'/'.$peserta['sppa_file'] ?>" target="_blank" class="btn-sm btn-primary">Download</a>
                  <?php } ?>
                </div>                 
            </div>
            <?php 
            if(isset($idas)) {
            ?>
            <div class="form-group">
              <label class="control-label col-sm-2">Pilihan Aksi <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <label class="radio-inline">
                  <input type="radio" name="aksi" value="Approve" id="aksi_approve"> Approve
                </label>
                <label class="radio-inline">
                  <input type="radio" name="aksi" value="Revisi" id="aksi_revisi"> Revisi
                </label>
                <label class="radio-inline">
                  <input type="radio" name="aksi" value="Tolak" id="aksi_tolak"> Tolak
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Keterangan <span class="text-danger">*</span></label>
              <div class="col-sm-10">
                <textarea name="keterangan_aksi" id="keterangan_aksi" class="form-control" placeholder="Masukkan keterangan untuk aksi yang dipilih"></textarea>
              </div>
            </div>
            <div class="form-group" id="extrapremi-group" style="display:none">
              <label class="control-label col-sm-2">Extrapremi</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="extrapremi" id="extrapremi">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Dokumen 1</label>
              <div class="col-sm-10">
                <input type="file" class="form-control" name="document1" id="document1" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
              </div>                 
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Dokumen 2</label>
                <div class="col-sm-10">
                  <input type="file" class="form-control" name="document2" id="document2" accept=".pdf,.jpg,.jpeg,.png">
                  <small class="text-muted">Format: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
                </div>                 
            </div>            
            <div class="form-group m-b-0">
              <div class="col-sm-12 text-center">
                <button type="button" id="submit-btn" class="btn btn-success width-xs" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">Submit</button>
              </div>
            </div>
            <?php 
            }else{
            ?>
            <div class="form-group m-b-0">
              <div class="col-sm-12 text-center">
                <button type="submit" id="load" class="btn btn-success width-xs" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">Submit</button>
              </div>
            </div>
            <?php
            }
            ?>
            
          </form>
	      </div>
	        <!-- end section-container -->
	    </div>
      <?php 
      break; 

      case "simpan":              
        $idpeserta = $_POST['idpeserta'];
        $tptlahir = $_POST['tptlahir'];
        $kota = $_POST['kota'];
        $kodepos = $_POST['kodepos'];
        $stsmarital = $_POST['stsmarital'];
        $notelp = $_POST['notelp'];
        $email = $_POST['email'];
        $jabatan = $_POST['jabatan'];
        $sukubunga = $_POST['sukubunga'];
        $jenispertanggungan = $_POST['jenispertanggungan'];

        $redirect = '../masterdata?type='.AES::encrypt128CBC('peserta', ENCRYPTION_KEY);
        
        // Upload dokumen
        $uploadResult = uploadDocuments($idpeserta);
        $documents = $uploadResult['documents'];
        $documents_json = $uploadResult['json'];

        $bb = $_POST['bb'];
        $tb = $_POST['tb'];
        $q1 = $_POST['q1'];
        $q2 = $_POST['q2'];
        $q3 = $_POST['q3'];
        $q41 = $_POST['q41'];
        $q42 = $_POST['q42'];
        $q43 = $_POST['q43'];
        $q44 = $_POST['q44'];
        $q45 = $_POST['q45'];
        $q46 = $_POST['q46'];
        $q5 = $_POST['q5'];
        $q6 = $_POST['q6'];
        $keterangan = $_POST['keterangan'];

        $querypeserta = "UPDATE ajkpeserta 
        SET tptlahir = '".$tptlahir."',
        kota = '".$kota."',
        kodepos = '".$kodepos."',
        stsmarital = '".$stsmarital."',
        notelp = '".$notelp."',
        email = '".$email."',
        jabatan = '".$jabatan."',
        sukubunga = '".$sukubunga."',
        jenispertanggungan = '".$jenispertanggungan."'
        WHERE idpeserta = '".$idpeserta."'";

        $queryanswer = "SELECT * FROM ajkformpesertaanswer WHERE idpeserta= '".$idpeserta."'";
        $queryanswer1 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 1')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q1."' WHERE idpeserta = '".$idpeserta."' and idquestion = 1" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 1, answer = '".$q1."'";
        $queryanswer2 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 2')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q2."' WHERE idpeserta = '".$idpeserta."' and idquestion = 2" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 2, answer = '".$q2."'";
        $queryanswer3 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 3')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q3."' WHERE idpeserta = '".$idpeserta."' and idquestion = 3" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 3, answer = '".$q3."'";
        $queryanswer41 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 10')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q41."' WHERE idpeserta = '".$idpeserta."' and idquestion = 10" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 10, answer = '".$q41."'";
        $queryanswer42 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 11')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q42."' WHERE idpeserta = '".$idpeserta."' and idquestion = 11" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 11, answer = '".$q42."'";
        $queryanswer43 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 12')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q43."' WHERE idpeserta = '".$idpeserta."' and idquestion = 12" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 12, answer = '".$q43."'";
        $queryanswer44 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 13')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q44."' WHERE idpeserta = '".$idpeserta."' and idquestion = 13" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 13, answer = '".$q44."'";
        $queryanswer45 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 14')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q45."' WHERE idpeserta = '".$idpeserta."' and idquestion = 14" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 14, answer = '".$q45."'";
        $queryanswer46 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 15')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q46."' WHERE idpeserta = '".$idpeserta."' and idquestion = 15" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 15, answer = '".$q46."'";
        $queryanswer5 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 5')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q5."' WHERE idpeserta = '".$idpeserta."' and idquestion = 5" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 5, answer = '".$q5."'";
        $queryanswer6 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 6')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$q6."' WHERE idpeserta = '".$idpeserta."' and idquestion = 6" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 6, answer = '".$q6."'";
        $queryanswer7 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 7')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$tb."' WHERE idpeserta = '".$idpeserta."' and idquestion = 7" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 7, answer = '".$tb."'";
        $queryanswer8 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 8')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$bb."' WHERE idpeserta = '".$idpeserta."' and idquestion = 8" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 8, answer = '".$bb."'";
        $queryanswer9 = mysql_num_rows(mysql_query($queryanswer. ' AND idquestion = 9')) > 0 ? "UPDATE ajkformpesertaanswer SET answer = '".$keterangan."' WHERE idpeserta = '".$idpeserta."' and idquestion = 9" : "INSERT INTO ajkformpesertaanswer SET idpeserta= '".$idpeserta."',idquestion = 9, answer = '".$keterangan."'";

        // Update documents di ajkpesertaas
        $querydocuments = "UPDATE ajkpesertaas SET documents = '".$documents_json."' WHERE idpeserta = '".$idpeserta."'";
        
        try{
          mysql_query("START TRANSACTION");
          mysql_query($querypeserta);
          mysql_query($queryanswer1);
          mysql_query($queryanswer2);
          mysql_query($queryanswer3);
          mysql_query($queryanswer41);
          mysql_query($queryanswer42);
          mysql_query($queryanswer43);
          mysql_query($queryanswer44);
          mysql_query($queryanswer45);
          mysql_query($queryanswer46);
          mysql_query($queryanswer5);
          mysql_query($queryanswer6);
          mysql_query($queryanswer7);
          mysql_query($queryanswer8);
          mysql_query($queryanswer9);
          
          if(!empty($documents)) {
            mysql_query($querydocuments);
          }

          mysql_query("COMMIT");
          echo '
          <div class="panel panel-default">
						<div class="panel-heading">
			           	<h4 class="m-t-0">SPAJK</h4>
			       	</div>
			       	<div class="panel-body">
			       		<div class="alert alert-warning fade in m-b-10"><h4><strong> SPAJK telah dibuat oleh '.$namauser.'.</strong> <a href=""></a></h4></div>
			        </div>
				    </div>
            <script>
              setTimeout(function() {
                window.location.href = "'.$redirect.'"; 
              }, 5000); 
            </script>
            <meta http-equiv="refresh" content="1; url=../modules/modmPdfdl.php?pdf=spajkvictoria&s='.AES::encrypt128CBC($idpeserta, ENCRYPTION_KEY).'">
				    ';
        }catch(Exception $e){
          mysql_query("ROLLBACK");
        }        
      break;

      case "approveas":              
        $idpeserta = $_POST['idpeserta'];
        $aksi = $_POST['aksi'];
        $keterangan = $_POST['keterangan_aksi'];
        $extrapremi = isset($_POST['extrapremi']) ? $_POST['extrapremi'] : 0;
        
        // Upload dokumen
        $uploadResult = uploadDocuments($idpeserta);
        $documents = $uploadResult['documents'];
        $documents_json = $uploadResult['json'];
        
        $qpeserta = mysql_fetch_array(mysql_query("SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."'"));

        $statusaktif = '';
        $pesan = '';
        
        if($aksi === 'Approve') {
          $statusaktif = 'Approve Asuransi';
          $premi = $qpeserta['premi'];
          $em = $extrapremi;
          $totalpremi = $premi + $em;
          
          $querypeserta = "UPDATE ajkpeserta 
          SET statusaktif = '".$statusaktif."',
          extrapremi = '".$em."',
          totalpremi = '".$totalpremi."',
          keterangan = '".$keterangan."'
          WHERE idpeserta = '".$idpeserta."'";
          
          $querypesertaas = "UPDATE ajkpesertaas
          SET em = '".$em."',
          totalpremi = '".$totalpremi."'
          WHERE idpeserta = '".$idpeserta."' and idas = 2";
          
          // Update documents di ajkpesertaas jika ada file yang diupload
          $querydocuments = '';
          if(!empty($documents)) {
            $querydocuments = "UPDATE ajkpesertaas SET documents = '".$documents_json."' WHERE idpeserta = '".$idpeserta."'";
          }
          
          $pesan = 'SPAJK telah diapprove oleh '.$namauser.'.';
          
          try{
            mysql_query("START TRANSACTION");
            mysql_query($querypeserta);
            mysql_query($querypesertaas);
            
            if(!empty($documents)) {
              mysql_query($querydocuments);
            }
            
            mysql_query("COMMIT");
            
            echo '
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="m-t-0">SPAJK</h4>
              </div>
              <div class="panel-body">
                <div class="alert alert-warning fade in m-b-10"><h4><strong> '.$pesan.'<br /></h4></div>
              </div>
            </div>
            <meta http-equiv="refresh" content="3; url=../dashboard">';
          }catch(Exception $e){
            mysql_query("ROLLBACK");
          }
        } 
        else if($aksi === 'Revisi') {
          $statusaktif = 'Pending';
          $pesan = 'SPAJK diminta untuk revisi oleh '.$namauser.'.';
          
          $querypeserta = "UPDATE ajkpeserta 
          SET statusaktif = '".$statusaktif."',
          keterangan = '".$keterangan."'
          WHERE idpeserta = '".$idpeserta."'";
          
          // Update documents di ajkpesertaas jika ada file yang diupload
          $querydocuments = '';
          if(!empty($documents)) {
            $querydocuments = "UPDATE ajkpesertaas SET documents = '".$documents_json."' WHERE idpeserta = '".$idpeserta."'";
          }
          
          try{
            mysql_query("START TRANSACTION");
            mysql_query($querypeserta);
            
            if(!empty($documents)) {
              mysql_query($querydocuments);
            }
            
            mysql_query("COMMIT");
            
            echo '
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="m-t-0">SPAJK</h4>
              </div>
              <div class="panel-body">
                <div class="alert alert-warning fade in m-b-10"><h4><strong> '.$pesan.'<br /></h4></div>
              </div>
            </div>
            <meta http-equiv="refresh" content="3; url=../masterdata?type='.AES::encrypt128CBC('pesertapendingspajk', ENCRYPTION_KEY).'">';
          }catch(Exception $e){
            mysql_query("ROLLBACK");
          }
        }
        else if($aksi === 'Tolak') {
          $statusaktif = 'Tolak Asuransi';
          $pesan = 'SPAJK telah ditolak oleh '.$namauser.'.';
          
          $querypeserta = "UPDATE ajkpeserta 
          SET statusaktif = '".$statusaktif."',
          keterangan = '".$keterangan."'
          WHERE idpeserta = '".$idpeserta."'";
          
          // Update documents di ajkpesertaas jika ada file yang diupload
          $querydocuments = '';
          if(!empty($documents)) {
            $querydocuments = "UPDATE ajkpesertaas SET documents = '".$documents_json."' WHERE idpeserta = '".$idpeserta."'";
          }
          
          try{
            mysql_query("START TRANSACTION");
            mysql_query($querypeserta);
            
            if(!empty($documents)) {
              mysql_query($querydocuments);
            }
            
            mysql_query("COMMIT");
            
            echo '
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="m-t-0">SPAJK</h4>
              </div>
              <div class="panel-body">
                <div class="alert alert-danger fade in m-b-10"><h4><strong> '.$pesan.'<br /></h4></div>
              </div>
            </div>
            <meta http-equiv="refresh" content="3; url=../masterdata?type='.AES::encrypt128CBC('pesertapendingspajk', ENCRYPTION_KEY).'">';
          }catch(Exception $e){
            mysql_query("ROLLBACK");
          }
        }


      }
      ?>

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
    
			$(".active").removeClass("active");
			var hasInputEl = document.getElementById("has_input");
			if(hasInputEl) {
				hasInputEl.classList.add("active");
			}
			
			// Handle radio button change untuk menampilkan/menyembunyikan extrapremi
			$('input[name="aksi"]').change(function() {
				if($(this).val() === 'Approve') {
					$('#extrapremi-group').show();
				} else {
					$('#extrapremi-group').hide();
					$('#extrapremi').val('');
				}
			});
			
			// Handle submit button dengan konfirmasi
			$('#submit-btn').click(function(e) {
				e.preventDefault();
				
				// Validasi aksi dipilih
				var aksi = $('input[name="aksi"]:checked').val();
				if(!aksi) {
					alert('Silahkan pilih aksi terlebih dahulu');
					return false;
				}
				
				// Validasi keterangan mandatori
				var keterangan = $('#keterangan_aksi').val().trim();
				if(!keterangan) {
					alert('Silahkan masukkan keterangan');
					return false;
				}
				
				// Konfirmasi dengan user
				var pesan = 'Apakah Anda yakin untuk melakukan aksi ' + aksi + '?';
				if(confirm(pesan)) {
					$('#inputmember').submit();
				}
			});
		});
	</script>
</body>

</html>
