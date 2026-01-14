<?php
include "../param.php";
include_once('../includes/functions.php');

if($iduser==''){
  ob_end_clean();
  echo json_encode(['rs'=>false,'url'=>"../dologout.php"]);
}else{
  $id_peserta = isset($_POST['idper']) ? $_POST['idper'] : '';
  $no_asuransi = isset($_POST['nocert']) ? $_POST['nocert'] : '';
  $action = isset($_POST['action']) ? $_POST['action'] : '';
  $time = date("YmdHis");

  if($action=='upload'){
    
    $target_dir = "../myFiles/_sertifikat/";
    $target_file = $target_dir . basename($_FILES["filecert"]["name"]);

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $filename = $target_dir.'SERTIFIKAT_'.$id_peserta.'_'.$no_asuransi.'.'.$imageFileType;
    $msg = '';
    
    if(basename($_FILES["filecert"]["name"]) != ""){
      // Check if file already exists
      if (file_exists($target_file)) {
        $msg .= "File sudah pernah diupload.";
        $uploadOk = 0;
      }
      // Check file size
      if ($_FILES["filecert"]["size"] > 2000000) {
          $msg .= "File sertifikat max 20mb.";
          $uploadOk = 0;
      }
      // Allow certain file formats
      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf") {
          $msg .= "Format file upload harus JPG, PNG atau PDF";
          $uploadOk = 0;
      }
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
          $msg .= "File anda tidak bisa diupload.";
          echo json_encode(['rs'=>false,'msg'=>'Set sertifikat gagal. '.$msg,'url'=>'']);
      // if everything is ok, try to upload file
      } else {
          if (move_uploaded_file($_FILES["filecert"]["tmp_name"], $filename))
          {
              $update = mysql_query('UPDATE ajkpeserta SET noasuransi="'.$no_asuransi.'", noasuransi_img="'.$filename.'", update_by="'.$iduser.'", update_time="'.date('Y-m-d H:i:s').'" WHERE idpeserta="'.$id_peserta.'"');
              $msg .= "File ". basename( $_FILES["filecert"]["name"]). " berhasil diupload.";
              echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
          }
          else
          {
              $msg .= "Sorry, there was an error uploading your file.";
              echo json_encode(['rs'=>false,'msg'=>'Set sertifikat gagal. '.$msg,'url'=>'']);
          }
      }
    }else{
      $update = mysql_query('UPDATE ajkpeserta SET noasuransi="'.$no_asuransi.'", update_by="'.$iduser.'", update_time="'.date('Y-m-d H:i:s').'" WHERE idpeserta="'.$id_peserta.'"');
      $msg .= "No Polis berhasil ditambahkan.";
      echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
    }


    // if($update){
    //   echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
    // }else{
    //   echo json_encode(['rs'=>false,'msg'=>'Set sertifikat gagal. '.$msg,'url'=>'']);
    // }
  }else if($action=='remove'){
    $select = mysql_query('SELECT noasuransi_img FROM ajkpeserta WHERE idpeserta="'.$id_peserta.'"');
    $row = mysql_fetch_array($select);

    if (file_exists($row['noasuransi_img'])) {
        unlink($row['noasuransi_img']);
    }

    $update = mysql_query('UPDATE ajkpeserta SET noasuransi=null, noasuransi_img=null , update_by="'.$iduser.'", update_time="'.date('Y-m-d H:i:s').'" WHERE idpeserta="'.$id_peserta.'"');

    if($update){
      echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
    }else{
      echo json_encode(['rs'=>false,'msg'=>'Set sertifikat gagal. '.$msg,'url'=>'']);
    }
  }else if($action == 'uploadktp'){
    $target_dir = "../myFiles/_peserta/".$id_peserta."/";
    $target_file = $target_dir . basename($_FILES["filektp"]["name"]);

    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777);
      chmod($target_dir, 0777);
    }

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $filename = 'KTP_'.$time.'_'.basename($_FILES["filektp"]["name"]);
    $msg = '';
    if(basename($_FILES["filektp"]["name"]) != ""){
      
      // Check if file already exists
      if (file_exists($target_file)) {
        $msg .= "File sudah pernah diupload.";
        $uploadOk = 0;
      }
      // Check file size
      if ($_FILES["filektp"]["size"] > 2000000) {
          $msg .= "File sertifikat max 20mb.";
          $uploadOk = 0;
      }
      // Allow certain file formats
      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf") {
          $msg .= "Format file upload harus JPG, PNG atau PDF";
          $uploadOk = 0;
      }
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
          $msg .= "File anda tidak bisa diupload.";
          echo json_encode(['rs'=>false,'msg'=>'Set ktp gagal. '.$msg,'url'=>'']);
      // if everything is ok, try to upload file
      } else {
          if (move_uploaded_file($_FILES["filektp"]["tmp_name"], $target_dir.$filename))
          {
              $update = mysql_query('UPDATE ajkpeserta SET ktp_file="'.$filename.'", update_by="'.$iduser.'", update_time="'.date('Y-m-d H:i:s').'" WHERE idpeserta="'.$id_peserta.'"');
              $msg .= "File ". basename( $_FILES["filektp"]["name"]). " berhasil diupload.";
              echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
          }
          else
          {
              $msg .= "Sorry, there was an error uploading your file.";
              echo json_encode(['rs'=>false,'msg'=>'Set ktp gagal. '.$msg,'url'=>'']);
          }
      }
    }
  }else if($action == 'uploadsppa'){
    $target_dir = "../myFiles/_peserta/".$id_peserta."/";
    $target_file = $target_dir . basename($_FILES["filesppa"]["name"]);

    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777);
      chmod($target_dir, 0777);
    }

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $filename = 'SPPA_'.$time.'_'.basename($_FILES["filesppa"]["name"]);
    $msg = '';
    if(basename($_FILES["filesppa"]["name"]) != ""){
      
      // Check if file already exists
      if (file_exists($target_file)) {
        $msg .= "File sudah pernah diupload.";
        $uploadOk = 0;
      }
      // Check file size
      if ($_FILES["filesppa"]["size"] > 2000000) {
          $msg .= "File sertifikat max 20mb.";
          $uploadOk = 0;
      }
      // Allow certain file formats
      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "pdf") {
          $msg .= "Format file upload harus JPG, PNG atau PDF";
          $uploadOk = 0;
      }
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
          $msg .= "File anda tidak bisa diupload.";
          echo json_encode(['rs'=>false,'msg'=>'Set SPPA gagal. '.$msg,'url'=>'']);
      // if everything is ok, try to upload file
      } else {
          if (move_uploaded_file($_FILES["filesppa"]["tmp_name"], $target_dir.$filename))
          {
              $update = mysql_query('UPDATE ajkpeserta SET sppa_file="'.$filename.'", update_by="'.$iduser.'", update_time="'.date('Y-m-d H:i:s').'" WHERE idpeserta="'.$id_peserta.'"');
              $msg .= "File ". basename( $_FILES["filesppa"]["name"]). " berhasil diupload.";
              echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
          }
          else
          {
              $msg .= "Sorry, there was an error uploading your file.";
              echo json_encode(['rs'=>false,'msg'=>'Set SPPA gagal. '.$msg,'url'=>'']);
          }
      }
    }
  }else if($action == 'batal'){
    $update = mysql_query('UPDATE ajkpeserta SET del = 1, statusaktif="Batal",keterangan = "'.$_POST['keterangan'].'", update_by="'.$iduser.'", update_time="'.date('Y-m-d H:i:s').'" WHERE idpeserta="'.$id_peserta.'"');
    echo json_encode(['rs'=>true,'msg'=>$msg,'url'=>'']);
  }
}
 ?>
