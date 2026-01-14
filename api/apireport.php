<?php
  $host = "192.168.10.252";
  $port = "33252";
  $db = "JATIM";
  $user = "reports";
  $pass = "rpt!2019";
  
  $con = mysqli_connect($host,$user,$pass,$db,$port);  
  
  // Check connection
  if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  // print_r($_POST);exit;
  // echo 'test';exit;
  switch($_POST['opt']){
    case "report":
      $query = "SELECT count(*)as jml,
                        round(sum(plafond),0)as plafond,
                        round(sum(premi),0)as premi,
                        round(sum(aspremi),0)as aspremi,
                        round(sum(tuntutan_klaim),0)as nilai_klaim
                FROM vpeserta_konsolidasi";
      $result = mysqli_query($con,$query);
      $row = mysqli_fetch_assoc($result);      
      $qsince = "SELECT DATE_FORMAT(tgldn,'%M %Y')as since
      FROM vpeserta_konsolidasi
      WHERE TGLDN IS NOT NULL
      ORDER BY TGLDN ASC
      LIMIT 1";

      $rsince = mysqli_query($con,$qsince);
      $rowsince = mysqli_fetch_assoc($rsince);  
      $data['since']= $rowsince['since'];       
      $data['nm_aplikasi']= 'AJK BANK JATIM';
      $data['data']= array(
        '0'=>array('name'=>'Debitur','value'=>$row['jml']),
        '1'=>array('name'=>'Plafond','value'=>$row['plafond']),
        '2'=>array('name'=>'Premi','value'=>$row['premi']),
        '3'=>array('name'=>'Klaim','value'=>$row['nilai_klaim'])
      );
      echo json_encode($data);
    break;

    case "prodbulan":
      

      isset($_POST['tahun']) ? $sqltahun = " AND YEAR(TGLDN) = '".$_POST['tahun']."' " : $sqltahun = '';
      
      $query = "SELECT bulan,
                        bulanname as nama,
                      IFNULL((SELECT round(SUM(premi),0)
                        FROM vpeserta_konsolidasi
                        WHERE MONTH(TGLDN) = mstbulan.bulan 
                              ".$sqltahun."),0)as premi
                FROM mstbulan";

      $result = mysqli_query($con,$query);
      $i = 1;

      
      while($row = mysqli_fetch_assoc($result)){
        // $data[] = array(
        //               $row['nama'],
        //               $row['premi'],
        //                       );
        $data[] = (int) $row['premi'];
        $i++;
      }

      $json[] = array(
        'name' => $_POST['tahun'],
        'data' => $data
      );
      
      // $return = array('data'=>$data);

      echo json_encode($json);
    break;

    case "prodasuransi":
      
      isset($_POST['tahun']) ? $sqltahun = " AND YEAR(TGLDN) = '".$_POST['tahun']."' " : $sqltahun = '';
      
      $query = "SELECT *
                FROM(
                SELECT count(*)as JML,ASURANSI,round(SUM(PLAFOND),0)as PLAFOND,round(SUM(PREMI),0)as PREMI,round(sum(IFNULL(TUNTUTAN_KLAIM,0)),0)as KLAIM,round((sum(IFNULL(TUNTUTAN_KLAIM,0))/sum(PREMI))*100,2) as RATIO
                FROM vpeserta_konsolidasi 
                WHERE 1=1 ".$sqltahun." AND STATUS = 'Inforce'
                GROUP BY ASURANSI
                )AS TEMP 
                ORDER BY RATIO DESC";

      $result = mysqli_query($con,$query);
      $i = 1;

      $data= array();
      while($row = mysqli_fetch_assoc($result)){
        $data[] = array(                    
                      'asuransi'=>$row['ASURANSI'],
                      'debitur'=>$row['JML'],
                      'plafond'=>$row['PLAFOND'],
                      'premi'=>$row['PREMI'],
                      'klaim'=>$row['KLAIM'],
                      'ratio'=>$row['RATIO']
                              );
        $i++;
      }
      
      // $return = array('data'=>$data);

      echo json_encode($data);
    break;

    case "prodcabang":
      
      isset($_POST['tahun']) ? $sqltahun = " AND YEAR(TGLDN) = '".$_POST['tahun']."' " : $sqltahun = '';
      
      $query = "SELECT *
                FROM(
                SELECT count(*)as JML,KOTA,round(SUM(PLAFOND),0)as PLAFOND,round(SUM(PREMI),0)as PREMI,round(SUM(IFNULL(TUNTUTAN_KLAIM,0)),0)as KLAIM,round((sum(IFNULL(TUNTUTAN_KLAIM,0))/sum(PREMI))*100,2) as RATIO
                FROM vpeserta_konsolidasi 
                WHERE 1=1 ".$sqltahun." AND STATUS = 'Inforce'
                GROUP BY KOTA
                )AS TEMP 
                ORDER BY RATIO DESC,PREMI DESC";

      $result = mysqli_query($con,$query);
      $i = 1;

      $data= array();
      while($row = mysqli_fetch_assoc($result)){
        $data[] = array(                    
                      'cabang'=>$row['KOTA'],
                      'debitur'=>$row['JML'],
                      'plafond'=>$row['PLAFOND'],
                      'premi'=>$row['PREMI'],                      
                      'klaim'=>$row['KLAIM'],
                      'ratio'=>$row['RATIO']
                              );
        $i++;
      }
      
      // $return = array('data'=>$data);

      echo json_encode($data);
    break;

    case "prodproduk":      
      isset($_POST['tahun']) ? $sqltahun = " AND YEAR(TGLDN) = '".$_POST['tahun']."' " : $sqltahun = '';
      
      $query = "SELECT *
                FROM(
                SELECT count(*)as JML,PRODUK,round(SUM(PLAFOND),0)as PLAFOND,round(SUM(PREMI),0)as PREMI,round(SUM(IFNULL(TUNTUTAN_KLAIM,0)),0)as KLAIM,round((sum(IFNULL(TUNTUTAN_KLAIM,0))/sum(PREMI))*100,2) as RATIO
                FROM vpeserta_konsolidasi 
                WHERE 1=1 ".$sqltahun." AND STATUS = 'Inforce'
                GROUP BY PRODUK
                )AS TEMP
                ORDER BY RATIO DESC";

      $result = mysqli_query($con,$query);
      $i = 1;

      $data= array();
      while($row = mysqli_fetch_assoc($result)){
        $data[] = array(
                      'produk'=>$row['PRODUK'],
                      'debitur'=>$row['JML'],
                      'plafond'=>$row['PLAFOND'],
                      'premi'=>$row['PREMI'],                      
                      'klaim'=>$row['KLAIM'],
                      'ratio'=>$row['RATIO']
                              );
        $i++;
      }
      
      // $return = array('data'=>$data);

      echo json_encode($data);
    break;

    case "gettahun":
      $query = "SELECT year(tgldn)as tahun 
                FROM vpeserta_konsolidasi
                WHERE STATUS = 'Inforce'
                GROUP BY year(tgldn)";

      $result = mysqli_query($con,$query);
      $i = 1;

      // $data= array();
      while($row = mysqli_fetch_assoc($result)){
      $data[] = $row['tahun'];
      $i++;
      }

      // $return = array('data'=>$data);

      echo json_encode($data);      
    break;

    case "topbadcabang":
      isset($_POST['tahun']) ? $sqltahun = " AND YEAR(TGLDN) = '".$_POST['tahun']."' " : $sqltahun = '';
        
      $query = "SELECT *
                FROM(
                SELECT KOTA,round(SUM(PLAFOND),0)as PLAFOND,round(SUM(PREMI),0)as PREMI,round(SUM(IFNULL(TUNTUTAN_KLAIM,0)),0)as KLAIM,round((sum(IFNULL(TUNTUTAN_KLAIM,0))/sum(PREMI))*100,2) as RATIO
                FROM vpeserta_konsolidasi 
                WHERE 1=1 ".$sqltahun." AND STATUS = 'Inforce'
                GROUP BY KOTA
                )AS TEMP
                ORDER BY RATIO DESC,PREMI ASC limit 10";

      $result = mysqli_query($con,$query);
      $i = 1;

      $data= array();
      while($row = mysqli_fetch_assoc($result)){
        $data[] = array(
                      'kota'=>$row['KOTA'],
                      'plafond'=>$row['PLAFOND'],
                      'premi'=>$row['PREMI'],                      
                      'klaim'=>$row['KLAIM'],
                      'ratio'=>$row['RATIO']
                    );
        $i++;
      }
      
      // $return = array('data'=>$data);

      echo json_encode($data);
    break;

    case "topgoodcabang":
      isset($_POST['tahun']) ? $sqltahun = " AND YEAR(TGLDN) = '".$_POST['tahun']."' " : $sqltahun = '';
          
      $query = "SELECT *
                FROM(
                SELECT KOTA,round(SUM(PLAFOND),0)as PLAFOND,round(SUM(PREMI),0)as PREMI,round(SUM(IFNULL(TUNTUTAN_KLAIM,0)),0)as KLAIM,round((sum(IFNULL(TUNTUTAN_KLAIM,0))/sum(PREMI))*100,2) as RATIO
                FROM vpeserta_konsolidasi 
                WHERE 1=1 ".$sqltahun." AND STATUS = 'Inforce'
                GROUP BY KOTA
                )AS TEMP
                ORDER BY RATIO ASC,PREMI DESC LIMIT 10";

      $result = mysqli_query($con,$query);
      $i = 1;

      $data= array();
      while($row = mysqli_fetch_assoc($result)){
        $data[] = array(
                      'kota'=>$row['KOTA'],
                      'plafond'=>$row['PLAFOND'],
                      'premi'=>$row['PREMI'],                      
                      'klaim'=>$row['KLAIM'],
                      'ratio'=>$row['RATIO']
                    );
        $i++;
      }
      
      // $return = array('data'=>$data);

      echo json_encode($data);    
    break;

    case "prodbulantahun":
      $query = "SELECT mstbulan.bulanname as nm_bulan,month(tgldn)as bulan,year(tgldn)as tahun,round(sum(premi),0)as premi
                FROM vpeserta_konsolidasi
                LEFT JOIN mstbulan on mstbulan.bulan = month(tgldn)
                GROUP BY month(tgldn),year(tgldn)";
      $result = mysqli_query($con,$query);
      $i = 1;

      
      while($row = mysqli_fetch_assoc($result)){
        $data[] = array(
                      $row['nm_bulan'],
                      $row['tahun'],
                      $row['premi'],
                              );
        $i++;
      }

      echo json_encode($data);      
    break;

    default:
    break;
  }  
?>


