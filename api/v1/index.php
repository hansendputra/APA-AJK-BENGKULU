<?php
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  error_reporting(E_ALL);

  include_once ("../../includes/fu6106.php");
  
  // header('Content-type: application/json');
  // ini_set('max_input_vars', 10000);
  // ini_set('post_max_size', '16M');
  // ini_set('memory_limit','2048M');

  $inputJSON = file_get_contents('php://input');
  $input = json_decode($inputJSON, true);

  $action = $input['action'];
  
  if ($action != 'timelineklaim') {
    $action = 'doNothing';
  }
  $action($input);

  function timelineklaim($input){
    $idpeserta = $input['idpeserta'];
      
    $klaim = mysql_fetch_array(mysql_query("
    SELECT ifnull(date_format(approve_time,'%d-%m-%Y'),'')as tglpengajuan, 
        ifnull(date_format(tgllengkapdokumen,'%d-%m-%Y'),'') as tgldoklengkap,    		
        ifnull(date_format(tglinfoasuransi,'%d-%m-%Y'),'')as tgllaporklaim,
        ifnull(date_format(tglbayarasuransi,'%d-%m-%Y'),'')as tglbayarasuransi,
        ifnull(date_format(tglbayar,'%d-%m-%Y'),'')as tglbayarclient  
    FROM ajkcreditnote
    WHERE del is null"));
    $query = "SELECT * FROM ajktimeline";
    
    $result = mysql_query($query);
    $rs = array();
    // while($row = mysql_fetch_assoc($result))
    // {
    //   $rs[] = $row;
    // }
    while($row = mysql_fetch_array($result)){
      if($row['nmtimeline'] == "Pengajuan Klaim"){
        $date = $klaim['tglpengajuan'];
      }elseif($row['nmtimeline'] == "Dokumen Lengkap"){
        $date = $klaim['tgldoklengkap'];
      }elseif($row['nmtimeline'] == "Info Ke Asuransi"){
        $date = $klaim['tgllaporklaim'];
      }elseif($row['nmtimeline'] == "Dibayar dari Asuransi"){
        $date = $klaim['tglbayarasuransi'];
      }elseif($row['nmtimeline'] == "Dibayar Ke Bank"){
        $date = $klaim['tglbayarclient'];
      }elseif($row['nmtimeline'] == "Finish"){
        $date = $klaim['tglbayarclient'];
      }
      if($date == "00-00-0000"){
        $date = '';
      }
      $rs[] = array(
              'id'=>$row['id'],
              'status'=>$row['nmtimeline'],
              'date'=>$date
              );
    }

    if ($rs) {
      $result = array(
        'RESPONSE_CODE' => '200',
        'RESPONSE_DESC' => 'Berhasil',
        'PAGE' => 1,
        'TOTAL_DATA' => count($rs),
        'RESULT' => $rs
      );
     
    } else {
      $result = array(
        'RESPONSE_CODE' => '404',
        'RESPONSE_DESC' => 'User tidak ada'
      );
    }
    echo json_encode($result); 
  }
?>
