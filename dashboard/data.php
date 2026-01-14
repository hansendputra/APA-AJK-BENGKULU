<?php
/********************************************************************
 DESC  : Create by satrya;
 EMAIL : satryaharahap@gmail.com;
 Create Date : 2015-04-08
 ********************************************************************/
require_once('../includes/fu6106.php');
session_start();
$user = $_SESSION['User'];
$queryuser = mysql_query("SELECT * FROM  useraccess WHERE  username = '".$user."'");
$rowuser = mysql_fetch_array($queryuser);
$cabang = $rowuser['branch'];
$idc = $rowuser['idclient'];

function duit($value)
{
    $orro = number_format($value, 0, ',', '.');
    return $orro;
}

function _convertDate2($date)
{
    if (empty($date)) {
        return null;
    }
    $date = explode("/", $date);
    return
    $date[2] . '-' . $date[1] . '-' . $date[0];
}

function yearfrac($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    $diff = $start->diff($end);

    $totalDays = $diff->y;

    return $totalDays;
}

function usia($date_1,$date_2){
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    $interval = date_diff($datetime1, $datetime2);
    $result = $interval->format('%y,%m,%d');
    $rresult = explode(',', $result);

    if($rresult[1] >= 6 && $rresult[2] >= 1){    
      return $rresult[0] + 1;
    }elseif($rresult[1] == 6 && $rresult[2] == 0){
      return $rresult[0];
    }elseif($rresult[1] >= 6 && $rresult[2] == 0){
      return $rresult[0] + 1;
    }else{
      return $rresult[0];
    }
}

if(isset($cabang))
{
    $cekCabang = mysql_fetch_array(mysql_query('SELECT * FROM ajkcabang WHERE idclient="'.$idc.'" AND er="'.$cabang.'"'));
    if ($cekCabang['level'] == 1) {
      $cabangverifikasi = '';
    }elseif($cekCabang['level'] == 2){
      $cabangverifikasi = " AND regional = '".$cekCabang['idreg']."' ";
    }else{
      $cabangverifikasi = " AND cabang = '".$cabang."'";
    }
}else{
    $cabangverifikasi = '';
}


switch($_POST["functionname"]){
  case 'piedata':
    $qry = 'SELECT count(*) AS jml_peserta, statusaktif 
        FROM ajkpeserta
        WHERE del is null 
        AND MONTH(input_time) <= MONTH(NOW())
        AND YEAR(input_time) = YEAR(NOW())
        AND idbroker = "'.$_POST['idbro'].'" 
        AND idclient = "'.$_POST['idclient'].'"
        '.$cabangverifikasi.' 
        GROUP BY statusaktif
        ORDER BY jml_peserta DESC';

    $cnt = mysql_num_rows(mysql_query($qry));
    if($cnt > 0){
      $sql = mysql_query($qry);

      while($row = mysql_fetch_assoc($sql)){
        $jmlpeserta = $row['jml_peserta'];       
        $datapost[] = $jmlpeserta;
        $post = json_encode($datapost);
        $post = str_replace('"','',$post);
      }
    }else{
      $datapost[] = 0;
      $post = json_encode($datapost);
      $post = str_replace('"','',$post);
    }
    echo $post;
  break;

  case 'pielabel':
    $qry = 'SELECT count(*) AS jml_peserta, statusaktif FROM ajkpeserta
    WHERE ajkpeserta.del is null AND MONTH(input_time) <= MONTH(NOW())
    AND YEAR(input_time) = YEAR(NOW())
    AND idbroker = "'.$_POST['idbro'].'" AND idclient = "'.$_POST['idclient'].'"
    '.$cabangverifikasi.'
    GROUP BY statusaktif
    ORDER BY jml_peserta DESC';

    $cnt = mysql_num_rows(mysql_query($qry));
    if($cnt > 0){

      $sql = mysql_query($qry);
      while($row = mysql_fetch_assoc($sql)){
        $statusaktif = $row['statusaktif'];

        $datapost[] = $statusaktif;

        $post = json_encode($datapost);
      }
    }else{
      $datapost[] = 'Null';        
      $post = json_encode($datapost);
    }
    echo $post;
  break;

  case 'piebg':
    $qry = 'SELECT count(*) AS jml_peserta, statusaktif 
            FROM ajkpeserta
            WHERE  del is null 
                  AND MONTH(input_time) <= MONTH(NOW())
                  AND YEAR(input_time) = YEAR(NOW())
                  AND idbroker = "'.$_POST['idbro'].'" AND idclient = "'.$_POST['idclient'].'"
                  '.$cabangverifikasi.'
                  GROUP BY statusaktif
                  ORDER BY jml_peserta DESC';
    $cnt = mysql_num_rows(mysql_query($qry));
    if($cnt > 0){

      $sql = mysql_query($qry);
      $li_row=1;

      while($row = mysql_fetch_assoc($sql)){
        if($li_row==1){
          $bgcolor = '#17B6A4';
        }elseif($li_row==2){
          $bgcolor = '#F04B46';
        }elseif($li_row==3){
          $bgcolor = '#2184DA';
        }elseif($li_row==4){
          $bgcolor = '#ca8c34';
        }elseif($li_row==5){
          $bgcolor = '#F04B46';
        }elseif($li_row==6){
          $bgcolor = '#9b59b6';
        }elseif($li_row==7){
          $bgcolor = '#ca8c34';
        }elseif($li_row==8){
          $bgcolor = '#F04B46';
        }elseif($li_row==9){
          $bgcolor = '#38AFD3';
        }elseif($li_row==10){
          $bgcolor = '#aab3ba';
        }else{
          $bgcolor = '#6FBDD5';
        }
        $datapost[] = $bgcolor;

        $post = json_encode($datapost);
        $li_row++;
      }
    }else{
      $datapost[] = '#6FBDD5';        
      $post = json_encode($datapost);
    }
    echo $post;
  break;

  case 'grapbulan':
    $qry = 'SELECT bulanname, IFNULL(sum(premiclient),0) as totalpremi 
            FROM mstbulan
            LEFT JOIN ajkpeserta ON idbroker = "'.$_POST['idbro'].'" AND idclient = "'.$_POST['idclient'].'" AND ajkpeserta.del is null AND
            mstbulan.bulan = MONTH(tgltransaksi) AND statusaktif in ("Inforce","Maturity")
            GROUP BY bulanname
            ORDER BY bulan ASC';
    $sql = mysql_query($qry);

    while($row = mysql_fetch_assoc($sql)){
      $bulanname = $row['bulanname'];
      $totalpremi = $row['totalpremi'];
      if($totalpremi==null){
        $totalpremi = 0;
      }
      $datapost[] = $bulanname;
      $premipost[] = $totalpremi;

      $post_bln = json_encode($datapost);
    }
    echo $post_bln;
  break;

  case 'grappremium':
    $qry = 'SELECT bulanname, (SELECT SUM(totalpremi) AS totalpremi 
            FROM ajkpeserta
            WHERE del is null 
                  AND idbroker = "'.$_POST['idbro'].'" AND idclient =  "'.$_POST['idclient'].'"
                  AND id !=""
                  '.$cabangverifikasi.'
                  AND MONTH(tgltransaksi) = mstbulan.bulan
                  AND YEAR(tgltransaksi) = YEAR(NOW()) AND statusaktif in ("Inforce","Maturity")) as totalpremi
                  FROM mstbulan
                  GROUP BY bulanname
                  ORDER BY bulan ASC';
    $sql = mysql_query($qry);
    while($row = mysql_fetch_assoc($sql)){
      $bulanname = $row['bulanname'];
      $totalpremi = $row['totalpremi'];

      $premipost[] = $totalpremi;

      $post_premi = json_encode($premipost);
    }
    echo $post_premi;
  break;

  case 'grappremiumpaid':
    $qry = 'SELECT bulanname, 
                  (SELECT sum(ifnull((select sum(nilaibayar) from ajkbayar where ajkbayar.idpeserta = ajkpeserta.idpeserta and tipebayar = "premibank"),0)) 
                    FROM ajkpeserta
                    WHERE idbroker = "'.$_POST['idbro'].'" 
                    AND idclient =  "'.$_POST['idclient'].'" 
                    '.$cabangverifikasi.'
                    AND MONTH(tgltransaksi) = mstbulan.bulan
                    AND YEAR(tgltransaksi) = YEAR(NOW())
                    and del is null
                    AND statusaktif in ("Inforce","Maturity")) as totalpremi
            FROM mstbulan
            GROUP BY bulanname
            ORDER BY bulan ASC';

    $sql = mysql_query($qry);

    while($row = mysql_fetch_assoc($sql)){
      $bulanname = $row['bulanname'];
      $totalpremi = $row['totalpremi'];

      $premipost[] = $totalpremi;

      $post_premi = json_encode($premipost);
    }
    echo $post_premi;
  break;

  case 'grappremiumunpaid':
    $qry = 'SELECT bulanname, 
                  (SELECT sum(premi - ifnull((select sum(nilaibayar) from ajkbayar where ajkbayar.idpeserta = ajkpeserta.idpeserta and tipebayar = "premibank"),0))
                    FROM ajkpeserta
                    WHERE idbroker = "'.$_POST['idbro'].'" 
                    AND idclient =  "'.$_POST['idclient'].'" 
                    AND MONTH(tgltransaksi) = mstbulan.bulan
                    AND YEAR(tgltransaksi) = YEAR(NOW())
                    '.$cabangverifikasi.'
                    AND statusaktif in ("Inforce","Maturity") and del is null) as totalpremi
            FROM mstbulan
            GROUP BY bulanname
            ORDER BY bulan ASC';    
    ;
    $sql = mysql_query($qry);

    while($row = mysql_fetch_assoc($sql)){
      $bulanname = $row['bulanname'];
      $totalpremi = $row['totalpremi'];

      $premipost[] = $totalpremi;

      $post_premi = json_encode($premipost);
    }
    echo $post_premi;
  break;

  case 'grapplafon':
    $qry = 'SELECT bulanname, IFNULL(sum(plafond),0) as totalplafond 
            FROM mstbulan
            LEFT JOIN ajkpeserta ON idbroker = "'.$_POST['idbro'].'" AND idclient = "'.$_POST['idclient'].'" AND mstbulan.bulan = MONTH(tgltransaksi)
            WHERE bulan <= MONTH(NOW())
            '.$cabangverifikasi.'
            AND statusaktif in ("Inforce","Maturity")
            GROUP BY bulanname
            ORDER BY bulan ASC';

    $sql = mysql_query($qry);
    while($row = mysql_fetch_assoc($sql)){
      $bulanname = $row['bulanname'];
      $totalplafond = $row['totalplafond'];

      $plafondpost[] = $totalplafond;

      $post_plafon = json_encode($plafondpost);
    }
    echo $post_plafon;
  break;

  case 'grappeserta':
    $qry = 'SELECT bulanname, IFNULL(count(nama),0) as totalpeserta  
            FROM mstbulan
            LEFT JOIN ajkpeserta ON idbroker = "'.$_POST['idbro'].'" AND 
            idclient = "'.$_POST['idclient'].'" AND 
            mstbulan.bulan = MONTH(tgltransaksi)
            WHERE bulan <= MONTH(NOW())
            '.$cabangverifikasi.'
            GROUP BY bulanname
            ORDER BY bulan ASC';

    $sql = mysql_query($qry);
    while($row = mysql_fetch_assoc($sql)){
      $bulanname = $row['bulanname'];
      $totalpeserta = $row['totalpeserta'];

      $pertapost[] = $totalpeserta;

      $post_peserta = json_encode($pertapost);
    }

    echo $post_peserta;
  break;

  case 'tipepinjaman':
    
    $produk = mysql_query("SELECT * FROM ajkpolis WHERE del is null");

    $hasil = '<select class="form-control" id="tipepinjaman">';
    while($qproduk = mysql_fetch_array($produk)){
      $hasil = $hasil.'<option value="'.$qproduk['id'].'">'.$qproduk['produk'].'</option>';
    }              
    $hasil = $hasil.'</select>';

    echo $hasil;
  break;

  case 'kalkulatorhitung':
    $karpot = $_POST['valkarpot'];
    $tenor = $_POST['valtenor'];
    $plafond = $_POST['valplafond'];
    $usia = $_POST['valusia'];

    $tenor = $tenor * 12;
    $query = 'SELECT * FROM ajkratepremi WHERE '.$tenor.' between tenorfrom and tenorto and '.$usia.' between agefrom and ageto and idpolis = "'.$karpot.'" and del is null';      
    $rate = mysql_fetch_array(mysql_query($query));      
    $premi = ($plafond/1000)*$rate['rate'];
    echo $premi;
  break;

  case 'kalkulatorhitungarray':
    $karpot = $_POST['valkarpot'];
    $tenor = $_POST['valtenor'];
    $plafond = $_POST['valplafond'];
    // $usia = $_POST['valusia'];
    $jiwa = $_POST['valjiwa'];
    $macet = $_POST['valmacet'];
    $tgllahir = _convertDate2($_POST['valtgllahir']);
    $tglakad = _convertDate2($_POST['valtglakad']);

    $usia = usia($tglakad,$tgllahir);

    if($tenor > 60){
      $tenormacet = 60;
    }else{
      $tenormacet = $tenor;
    }
    $query = 'SELECT * FROM ajkratepremi WHERE '.$tenormacet.' between tenorfrom and tenorto and idas = 3 and idpolis = "'.$karpot.'" and del is null';
    $query2 = 'SELECT * FROM ajkratepremi WHERE '.$tenor.' between tenorfrom and tenorto and idas = 2 and idpolis = "'.$karpot.'" and '.$usia.' between agefrom and ageto  and del is null';
    $medical = 'SELECT * FROM ajkmedical WHERE '.$usia.' between agefrom and ageto and idproduk = "'.$karpot.'" and del is null and '.$plafond.' between upfrom and upto';
    $ratemacet = mysql_fetch_array(mysql_query($query));
    $ratejiwa = mysql_fetch_array(mysql_query($query2));
    $medical_ = mysql_fetch_array(mysql_query($medical));

    $rate = 0;
    $premi = 0;
    $medicals = "";

    if($karpot != 11 and $karpot != 12){
      if($macet == "true"){
        $ratemacet_ = ($ratemacet['rate']/12)*$tenormacet;
        $rate += $ratemacet_; 
        $medicals .= 'FCL';
        $premi += ($plafond/1000)*$ratemacet_;
      }

      if($jiwa == "true"){
        $ratejiwa_ += $ratejiwa['rate'];
        $rate += $ratejiwa_;
        $medicals .= $medical_['type'];
        $premi += ($plafond/1000)*$ratejiwa_;
      }
    }else{
      $rate = $ratejiwa['rate'];
      $medicals .= $medical_['type'];
      $premi = ($plafond/1000)*$rate;
      $test = 'masuk else';
    }
    $return = array(
      'rate'    => $rate,
      'premi'   => $premi,
      'medical' => $medicals,
      'usia'    => $usia,
    );
    echo json_encode($return); 
  break;
}
?>