<?php
// ----------------------------------------------------------------------------------
// Original Author Of File : Rahmad
// E-mail :penting_kaga@yahoo.com
// Copyright (C) 2016
// ----------------------------------------------------------------------------------
include_once('ui.php');
ini_set("memory_limit","-1");

// VLife API Class
class VlifeApi {
    private $apiUrl;
    private $clientName;
    private $clientSecret;
    private $accessToken;
    private $refreshToken;
    
    public function __construct() {
        // Configure your VLife API credentials here
        $this->apiUrl = 'https://api-client.vlife.id'; // VLife API Base URL
        $this->clientName = 'Adonai'; // Client name for authentication
        $this->clientSecret = 'Adonai2025'; // Client secret for authentication
        $this->accessToken = null;
        $this->refreshToken = null;
    }
    
    /**
     * Get access token from VLife API
     */
    private function getAccessToken() {
        // Check if we already have a valid token (you might want to implement token caching)
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }
        
        $endpoint = $this->apiUrl . '/auth/get-token';
        
        $requestData = array(
            'name' => $this->clientName,
            'secret' => $this->clientSecret
        );
        
        $headers = array(
            'Content-Type: application/json',
        );
        
        $requestBody = json_encode($requestData);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new Exception('cURL Error during authentication: ' . $curlError);
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode == 200 && isset($responseData['accessToken'])) {
            $this->accessToken = $responseData['accessToken'];
            $this->refreshToken = isset($responseData['refreshToken']) ? $responseData['refreshToken'] : null;
            return $this->accessToken;
        } else {
            $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Authentication failed';
            throw new Exception('VLife Authentication Error (HTTP ' . $httpCode . '): ' . $errorMessage);
        }
    }
    
    /**
     * Map peserta data to VLife API format
     */
    public function mapPesertaToVlifeFormat($pesertaData) {
        // Convert date to Unix timestamp
        $birthTimestamp = strtotime($pesertaData['tgllahir']);
        $effectiveDate = strtotime($pesertaData['tglakad']);
        $transactionDate = time(); // Current timestamp
        
        return array(
            'master_policy_id' => '680b39cd58a9785755374570', // Master Policy ID dari VLife
            'product_sub_id' => '68085ce8333af5cea83c6fdc', // Product Sub ID dari VLife
            'insured_data' => array(
                'fullname' => $pesertaData['nama'],
                'gender' => $this->getGender($pesertaData),
                'birth' => $birthTimestamp,
                'place_birth' => isset($pesertaData['tempat_lahir']) ? $pesertaData['tempat_lahir'] : '-',
                'marital_status' => isset($pesertaData['status_kawin']) ? $pesertaData['status_kawin'] : '-',
                'address' => array(
                    array(
                        'value' => isset($pesertaData['alamat']) ? $pesertaData['alamat'] : '-',
                        'type' => 'domicile'
                    )
                ),
                'contact' => array(
                    array(
                        'value' => isset($pesertaData['telp']) ? $pesertaData['telp'] : '-',
                        'type' => 'phone_no'
                    )
                ),
                'identities' => array(
                    array(
                        'value' => $pesertaData['nomorktp'],
                        'type' => 'id'
                    )
                ),
                'job' => isset($pesertaData['pekerjaan']) ? $pesertaData['pekerjaan'] : '-'
            ),
            'policyholder_data' => array(
                'fullname' => $pesertaData['nama'],
                'gender' => $this->getGender($pesertaData),
                'birth' => $birthTimestamp,
                'place_birth' => isset($pesertaData['tempat_lahir']) ? $pesertaData['tempat_lahir'] : '-',
                'marital_status' => isset($pesertaData['status_kawin']) ? $pesertaData['status_kawin'] : '-',
                'address' => array(
                    array(
                        'value' => isset($pesertaData['alamat']) ? $pesertaData['alamat'] : '-',
                        'type' => 'domicile'
                    )
                ),
                'contact' => array(
                    array(
                        'value' => isset($pesertaData['telp']) ? $pesertaData['telp'] : '-',
                        'type' => 'phone_no'
                    )
                ),
                'identities' => array(
                    array(
                        'value' => $pesertaData['nomorktp'],
                        'type' => 'id'
                    )
                ),
                'job' => isset($pesertaData['pekerjaan']) ? $pesertaData['pekerjaan'] : '-'
            ),
            'request_letter' => array(
                'agree' => true
            ),
            'input_amount' => floatval($pesertaData['plafond']),
            'amount' => floatval($pesertaData['totalpremi']),
            'additional_amount' => isset($pesertaData['extrapremi']) ? floatval($pesertaData['extrapremi']) : 0,
            'underwriting_result' => isset($pesertaData['medical']) ? $pesertaData['medical'] : 'NM',
            'underwriting_result_detail' => array(
                'status' => 'approved',
                'notes' => isset($pesertaData['keterangan']) ? $pesertaData['keterangan'] : 'ok'
            ),
            'installment' => intval($pesertaData['tenor']),
            'period' => 1,
            'effective_date' => $effectiveDate,
            'transaction_date' => $transactionDate,
            'transaction_ref' => isset($pesertaData['nopinjaman']) ? $pesertaData['nopinjaman'] : $pesertaData['idpeserta'],
            'category' => $this->determineCategory($pesertaData)
        );
    }
    
    /**
     * Get gender format for VLife API
     */
    private function getGender($pesertaData) {
        if (isset($pesertaData['jenis_kelamin'])) {
            $gender = strtolower($pesertaData['jenis_kelamin']);
            if ($gender == 'perempuan' || $gender == 'p' || $gender == 'wanita') {
                return 'f';
            } else {
                return 'm';
            }
        }
        return 'm'; // Default male
    }
    
    /**
     * Determine category based on occupation or other criteria
     */
    private function determineCategory($pesertaData) {
        // You can customize this logic based on your business rules
        // Example: based on occupation code
        if (isset($pesertaData['pekerjaan'])) {
            $pekerjaan = $pesertaData['pekerjaan'];
            // Add your category mapping logic here
            // For now, return default
            return 'cat_2';
        }
        return 'cat_2'; // Default category
    }
    
    /**
     * Submit policy group to VLife API
     */
    public function submitPolicyGroup($policyData) {
        // Get access token first
        $accessToken = $this->getAccessToken();
        
        $endpoint = $this->apiUrl . '/policy/submit';
        
        // Prepare request headers with Bearer token
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        );
        
        // Prepare request body
        $requestBody = json_encode($policyData);
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Handle errors
        if ($curlError) {
            throw new Exception('cURL Error: ' . $curlError);
        }
        
        // Parse response
        $responseData = json_decode($response, true);
        
        if ($httpCode == 200 || $httpCode == 201) {
            // Success
            return $responseData;
        } else {
            // Error
            $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Unknown error';
            throw new Exception('VLife API Error (HTTP ' . $httpCode . '): ' . $errorMessage);
        }
    }
    
    /**
     * Get policy status from VLife API
     */
    public function getPolicyStatus($policyNumber) {
        // Get access token first
        $accessToken = $this->getAccessToken();
        
        $endpoint = $this->apiUrl . '/policy/status/' . urlencode($policyNumber);
        
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new Exception('cURL Error: ' . $curlError);
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode == 200) {
            return $responseData;
        } else {
            $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Unknown error';
            throw new Exception('VLife API Error (HTTP ' . $httpCode . '): ' . $errorMessage);
        }
    }
    
    /**
     * Log API call to history table
     */
    private function logApiHistory($url, $method, $request, $response, $statusCode, $idpeserta = null, $idas = null, $type = 'out') {
        $requestJson = is_array($request) ? json_encode($request) : $request;
        $responseJson = is_array($response) ? json_encode($response) : $response;
        
        $query = "INSERT INTO api_history SET 
                  url = '".mysql_real_escape_string($url)."',
                  method = '".mysql_real_escape_string($method)."',
                  request = '".mysql_real_escape_string($requestJson)."',
                  response = '".mysql_real_escape_string($responseJson)."',
                  status_code = '".(int)$statusCode."',
                  idpeserta = ".(is_null($idpeserta) ? 'NULL' : "'".mysql_real_escape_string($idpeserta)."'").",
                  idas = ".(is_null($idas) ? 'NULL' : (int)$idas).",
                  type = '".mysql_real_escape_string($type)."',
                  created_at = NOW(),
                  updated_at = NOW()";
        
        mysql_query($query);
        return mysql_insert_id();
    }
    
    /**
     * Save callback data to ajkpeserta_callback
     */
    private function saveCallback($idpeserta, $idas, $responseData) {
        $other = is_array($responseData) ? json_encode($responseData) : $responseData;
        
        $query = "INSERT INTO ajkpeserta_callback SET 
                  idpeserta = '".mysql_real_escape_string($idpeserta)."',
                  idas = '".(int)$idas."',
                  other = '".mysql_real_escape_string($other)."'";
        
        mysql_query($query);
        return mysql_insert_id();
    }
    
    /**
     * Update policy certificate number
     */
    public function updateCertificate($policyNumber, $certificateNumber, $certificateFile = null) {
        // Get access token first
        $accessToken = $this->getAccessToken();
        
        $endpoint = $this->apiUrl . '/policy/certificate';
        
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        );
        
        $requestData = array(
            'policy_number' => $policyNumber,
            'certificate_number' => $certificateNumber,
        );
        
        if ($certificateFile) {
            $requestData['certificate_file'] = base64_encode(file_get_contents($certificateFile));
        }
        
        $requestBody = json_encode($requestData);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            throw new Exception('cURL Error: ' . $curlError);
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode == 200 || $httpCode == 201) {
            return $responseData;
        } else {
            $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Unknown error';
            throw new Exception('VLife API Error (HTTP ' . $httpCode . '): ' . $errorMessage);
        }
    }
}
echo '
<section id="main" role="main">
<div class="container-fluid" style="min-height:1024px;"><!-- add min-height to simulate scrolling -->
  <div class="page-header page-header-block">';
switch ($_REQUEST['dt']) {

  case "edit":
    $idpeserta = $_REQUEST['id'];
    $query = "SELECT * FROM vpeserta where id = '".$idpeserta."'";
    $qpes = mysql_fetch_array(mysql_query($query));
    $qpekerjaan = mysql_query("SELECT * FROM ajkprofesi");
    $qins = mysql_query("SELECT * FROM ajkinsurance");

    if($_REQUEST['btnedit']=="procedit"){
      $newas = $_REQUEST['asuransi']; 
      $newprofesi = $_REQUEST['pekerjaan'];
      $newrate = $_REQUEST['rate'];
      $newbunga = $_REQUEST['bunga_bank'];
      $tgltransaksi = $_REQUEST['tgltransaksi'];
      $newpremi = str_replace(",","",$_POST['premi']); 
      $newplafond = str_replace(",","",$_POST['plafond']); 
      $newketerangan = $_REQUEST['keterangan'];
      $newmppbln = $_REQUEST['mppbln'];
	  
	  
		$target_dir    = "../image/bukti_gambar/";
		$namafile      = date('dmYHis').str_replace(" ", "", basename($_FILES["bukti_gambar"]["name"]));
		$target_file   = $target_dir . $namafile;
		$uploadOk      = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
 
		$check = getimagesize($_FILES["bukti_gambar"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1; $bukti_gambar = $namafile;
		} else {
			$uploadOk = 0; $bukti_gambar = $qpes['bukti_gambar'];
		}
 
		if ($uploadOk == 1) { 
			move_uploaded_file($_FILES["bukti_gambar"]["tmp_name"], $target_file); 
		}
		if(isset($newbunga) or $newbunga != ""){
      
      $hostb = "localhost:3362";
      $userb = "jatimsql";
      $passb = "ved+-18bios";
      $dbb   = "biosjatim";
    
      $link = mysqli_connect($hostb, $userb, $passb, $dbb);
      
      if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
      } 

      $query = "call sp_reset_cadangan('".$qpes['idpeserta']."','".$newbunga."')";
      // echo $query;
      $result = mysqli_query($link,$query);
    
      while ($row = mysqli_fetch_array($result)){   
          echo $row[0] . " - " . + $row[1]; 
      }
    
      mysqli_close($link);
      
    }	  

    $queryold = "INSERT INTO ajkhispeserta 
                SET idpeserta='".$qpes['idpeserta']."',his='OLD',bungabank= '".$qpes['bunga_bank']."',asuransi='".$qpes['asuransi']."',plafond = '".$qpes['plafond']."',tgltransaksi='".$qpes['tgltransaksi']."',pekerjaan='".$qpes['pekerjaan']."',rate='".$qpes['premirate']."',premi='".$qpes['premi']."',keterangan='".$qpes['keterangan']."',input_by = '".$q['username']."',input_date=now();";
    $querynew = "INSERT INTO ajkhispeserta 
                SET idpeserta='".$qpes['idpeserta']."',his='NEW',bungabank='$newbunga',asuransi='$newas',tgltransaksi='$tgltransaksi',pekerjaan='$newprofesi',rate='$newrate',plafond = '$newplafond',premi='$newpremi',keterangan='$newketerangan',input_by='".$q['username']."',input_date=now();";      
            
	  $queryupdate = "UPDATE ajkpeserta
                      SET asuransi = '".$newas."',
                          pekerjaan = '".$newprofesi."',
                          premirate = '".$newrate."',
                          plafond = '".$newplafond."',
                          premi = '".$newpremi."',
                          tgltransaksi ='".$tgltransaksi."',
                          totalpremi = '".$newpremi."',
                          keterangan = '".$newketerangan."',
						              bukti_gambar = '".$bukti_gambar."',
                          mppbln = '".$newmppbln."',
                          update_by='".$q['id']."',
                          update_time='".$today."'
                      WHERE idpeserta = '".$qpes['idpeserta']."' and del is null";
					  
      mysql_query($queryold);
      mysql_query($querynew);
      mysql_query($queryupdate);

      //EMAIL START
      $subject = 'Update Data Peserta '.$qpes['nama'].' - ['.$qpes['idpeserta'].']';
      $message = 'Data Peserta '.$qpes['nama'].' - ['.$qpes['idpeserta'].'] telah di update.';
      $ajkmailto = 'hansen@adonai.co.id';
      $ajkmailnameto = 'Hansen Dwi Putra';
      $ajkmailtocount = 1;
      $ajkmailfromname = $q['firstname'];
      $ajkmailfrom = $q['email'];
      // $ajkmailccname = $emailuserapprove['firstname'].'|'.$emailuserchecker['firstname'];
      $ajkmailccname = '';
      // $ajkmailccmail = $emailuserapprove['email'].'|'.$emailuserchecker['email'];
      $ajkmailccmail = '';
      // $ajkmailcccount = 2;
      kirimemail($ajkmailfromname,$ajkmailfrom,$ajkmailnameto, $ajkmailto,$ajkmailtocount, $ajkmailccname, $ajkmailccmail, $ajkmailcccount, $subject,$message);
      //EMAIL END

      echo '
      <meta http-equiv="refresh" content="2; url=ajk.php?re=data&dt=edtdata">
        <div class="alert alert-dismissable alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong>Update Success!
        </div>';      
    }
    while ($qpekerjaan_ = mysql_fetch_array($qpekerjaan)) {
      if($qpekerjaan_['ref_mapping'] == $qpes['pekerjaan']){
        $selected = 'selected';
      }else{
        $selected = '';
      }
      $listpekerjaan = $listpekerjaan.'<option value="'.$qpekerjaan_['ref_mapping'].'" '.$selected.'>'.$qpekerjaan_['ref_mapping'].' - '.$qpekerjaan_['nm_profesi'].'</option>';
    }
    
    while ($qins_ = mysql_fetch_array($qins)) {
      if($qins_['id'] == $qpes['asuransi']){
        $selected = 'selected';
      }else{
        $selected = '';
      }
      $listins = $listins.'<option value="'.$qins_['id'].'" '.$selected.'>'.$qins_['name'].'</option>';
    }    

    if($qpes['pekerjaan'] == "BJ"){
      $bunga_bank = '
      <div class="form-group">
        <label class="col-sm-2 control-label">Bunga Bank</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="bunga_bank" value="'.$qpes['bunga_bank'].'" required>
        </div>
      </div>';
      $plafondro = 'required';
    }else{
      $plafondro = 'readonly';
    }

    echo '
      <div class="page-header-section"><h2 class="title semibold">'.$qpes['nama'].' - ['.$qpes['idpeserta'].']</h2></div>
        <div class="page-header-section"></div>
      </div>  
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <form action="#" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label class="col-sm-2 control-label">Plafond</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="plafond" name="plafond" value="'.$qpes['plafond'].'" '.$plafondro.'>
                </div>
              </div>
               <div class="form-group">
                <label class="col-sm-2 control-label">Tenor</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="tenor" value="'.$qpes['tenor'].'" readonly>
                </div>
              </div>
              <br><hr>
              <br>
              <div class="form-group">
                <label class="col-sm-2 control-label">Asuransi</label>
                <div class="col-sm-10">
                  <!--<input type="text" class="form-control" name="asuransi" id="asuransi" value="'.$qpes['nm_asuransi'].'" required>-->
                  <select name="asuransi" class="form-control" required>
                    '.$listins.'
                  </select>                  
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Pekerjaan</label>
                <div class="col-sm-10">
                  <select name="pekerjaan" class="form-control" required>
                    '.$listpekerjaan.'
                  </select>
                </div>
              </div>
              '.$bunga_bank.'
              <div class="form-group">
                <label class="col-sm-2 control-label">Rate</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="rate" value="'.$qpes['premirate'].'" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Premi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="premi" name="premi" value="'.$qpes['premi'].'" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Tgl Transaksi</label>
                <div class="col-sm-10">
                  <input type="date" class="form-control" id="tgltransaksi" name="tgltransaksi" value="'.$qpes['tgltransaksi'].'" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">MPP (Bulan)</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="mppbln" name="mppbln" value="'.$qpes['mppbln'].'">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Keterangan <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <textarea class="form-control" name="keterangan" required>'.$qpes['keterangan'].'</textarea>
                </div>
              </div>  
				  
			  <div class="form-group">
                <label class="col-sm-2 control-label">Bukti Gambar</label>
                <div class="col-sm-10">
				   <input type="file" class="form-control" id="bukti_gambar" name="bukti_gambar">
				';
				
				if($qpes['bukti_gambar']){ echo '<br><br><img src="../image/bukti_gambar/'.$qpes['bukti_gambar'].'" style="max-width:250px;"><br><br>'; }
				
			echo '</div>
              </div>';  
			  
            echo  '<input type="hidden" id="btnedit" name="btnedit" value="procedit">

              <div class="panel-footer text-center">
                <button type="submit" class="btn btn-success text-center"><i class="ico-save"></i> Save</button>                    
                <a href="ajk.php?re=data&dt=edtdata" class="btn btn-danger" ><i class="ico-close"></i> Close</a>
              </div>
            </form>
          </div>
        </div>
      </div>    
    </div>

    <script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>
    <script type="text/javascript">  

      $("#premi").inputmask("numeric", {
        radixPoint: ".",
        groupSeparator: ",",
        digits: 2,
        autoGroup: true,
        prefix: "", //No Space, this will truncate the first character
        rightAlign: false,
        oncleared: function () { self.Value(""); }
      });  
      $("#plafond").inputmask("numeric", {
        radixPoint: ".",
        groupSeparator: ",",
        digits: 2,
        autoGroup: true,
        prefix: "", //No Space, this will truncate the first character
        rightAlign: false,
        oncleared: function () { self.Value(""); }
      });        
    </script>';    
  break;

	case "edtdata":
    echo '
      <div class="page-header-section"><h2 class="title semibold">Members</h2></div>
        <div class="page-header-section"></div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <table class="table table-hover table-bordered table-striped table-responsive" id="column-filtering">
              <thead>
              <tr>
                <th width="1%">No</th>
                <th width="1%">Action</th>
                <th>Product</th>
                <th width="1%">ID Peserta</th>
                <th width="1%">No Pinjaman</th>
                <th>Name</th>
                <th width="1%">DOB</th>
                <th width="1%">Age</th>
                <th width="10%">Plafond</th>
                <th width="10%">Tenor</th>              
                <th width="10%">Tgl Akad</th>
                <th width="10%">Tgl Akhir</th>
                <th width="1%">Premi</th>
                <th>Status</th>
                <th width="1%">Cabang</th>
                <th width="1%">Asuransi</th>
              </tr>
              </thead>
              <tbody>';
                if($_REQUEST['filter']){
                  $query = $thisEncrypter->decode($_SESSION[$_REQUEST['filter']]);
                }else{
                  $query = "SELECT * FROM vpeserta WHERE statusaktif in ('Pending','Approve') and idbroker = 1";
                }
                
                $metData = $database->doQuery($query);
                while ($metData_ = mysql_fetch_array($metData)) {
                  $idpes = $metData_['id'];
                  echo '
                  <tr>
                    <td align="center">'.++$no.'</td>
                    <td align="center"><a href="ajk.php?re=data&dt=edit&id='.$idpes.'">'.BTN_EDIT.'</a></td>
                    <td align="center">'.$metData_['produk'].'</td>
                    <td align="center">'.$metData_['idpeserta'].'</td>
                    <td align="center">'.$metData_['nopinjaman'].'</td>
                    <td>'.$metData_['nama'].'</td>
                    <td align="center">'._convertDate($metData_['tgllahir']).'</td>
                    <td align="center">'.$metData_['usia'].'</td>
                    <td align="right">'.duit($metData_['plafond']).'</td>
                    <td align="center">'.$metData_['tenor'].'</td>
                    <td align="center">'._convertDate($metData_['tglakad']).'</td>
                    <td align="center">'._convertDate($metData_['tglakhir']).'</td>
                    <td align="right">'.duit($metData_['totalpremi']).'</td>                  
                    <td align="center">'.$metData_['statusaktif'].'</td>
                    <td>'.$metData_['nmcabang'].'</td>
                    <td>'.$metData_['nm_asuransi'].'</td>
                  </tr>';
                }
                echo '
              </tbody>         
            </table>
          </div>
        </div>
      </div>
    </div>';
	break;

	case "pending":
    $periode1 = $_REQUEST['periode1'];
		$periode2 = $_REQUEST['periode2'];
    echo '<div class="page-header-section"><h2 class="title semibold">Pending/Medical Members</h2></div>
          	<div class="page-header-section"></div>
          </div>';

    		//echo '<div class="table-responsive panel-collapse pull out">
    echo '
    <div class="row">
      <div class="col-md-12">
				<form method="post" action="#">
					<label>Tgl Akad</label>
					<input type="text" class="datepickers" name="periode1" value="'.$periode1.'" required autocomplete="off">
					<input type="text" class="datepickers" name="periode2" value="'.$periode2.'" required autocomplete="off">
					<input type="hidden" name="btncari" value="cari">
					'.BTN_SUBMIT.'
				</form>      
        <div class="panel panel-default">
          <table class="table table-hover table-bordered table-striped table-responsive" id="column-filtering">
            <thead>
              <tr>
                <th width="1%">No</th>
                <th width="1%">Broker</th>
                <th>Partner</th>
                <th>Insurance</th>
                <th>Product</th>
                <th>Pekerjaan</th>
                <th>Name</th>
                <th width="1%">DOB</th>
                <th width="1%">Age</th>
                <th width="10%">Plafond</th>
                <th width="10%">Tgl Akad</th>
                <th width="1%">Tenor</th>
                <th width="10%">Tgl Akhir</th>
                <th width="1%">Premium</th>
                <th>Medical</th>
                <th>Status</th>
                <th width="1%">Branch</th>
                <th width="1%">Option</th>
              </tr>
            </thead>
            <tbody>';
            	if ($_REQUEST['btncari']=="cari"){
                $filter = " AND tglakad BETWEEN '"._convertDate($periode1)."' and '"._convertDate($periode2)."'";
              }else{
                $filter = "";
              }

              if($q['level'] == 90){
                $status = " AND ajkpeserta.statusaktif in ('Approve','Validasi')";
              }elseif($q['level'] > 90 && $q['level'] < 99){
                $status = " AND ajkpeserta.statusaktif in ('Analisa','SPV Validasi','Validasi','Analisa Asuransi')";
              }else{
                $status = " AND ajkpeserta.statusaktif in ('Approve','Analisa','SPV Validasi','Validasi','Analisa Asuransi')";
              }

              $query = 'SELECT ajkpeserta.id,
              ajkcobroker.`name` AS namebroker,
              ajkclient.`name` AS nameclient,
              ajkpolis.produk,
              ajkpeserta.idpeserta,
              ajkpeserta.nomorktp,
              ajkpeserta.nama,
              ajkpeserta.typedata,
              ajkpeserta.tgllahir,
              ajkpeserta.usia,
              ajkpeserta.plafond,
              ajkpeserta.tglakad,
              ajkpeserta.tenor,
              ajkpeserta.tglakhir,
              ajkpeserta.totalpremi,
              ajkpeserta.astotalpremi,
              ajkpeserta.statusaktif,
              ajkpeserta.medical,
              ajkpeserta.keterangan,
              ajkcabang.`name` AS cabang,              
              (SELECT GROUP_CONCAT(ajkinsurance.name SEPARATOR " , ")
              FROM ajkpesertaas 
              inner join ajkinsurance on ajkinsurance.id = ajkpesertaas.idas
              where ajkpesertaas.idpeserta = ajkpeserta.idpeserta)as nminsurance
              FROM
              ajkpeserta
              INNER JOIN ajkcobroker ON ajkpeserta.idbroker = ajkcobroker.id
              INNER JOIN ajkclient ON ajkpeserta.idclient = ajkclient.id
              LEFT JOIN ajkpolis ON ajkpeserta.idpolicy = ajkpolis.id and ajkpolis.del is null
              INNER JOIN ajkcabang ON ajkpeserta.cabang = ajkcabang.er
              WHERE ajkpeserta.iddn IS NULL AND ajkpeserta.del IS NULL '.$status.' '.$filter.'
              ORDER BY ajkpeserta.input_time DESC';
              // echo $query;
              $metData = $database->doQuery($query);
              while ($metData_ = mysql_fetch_array($metData)) {
                if($metData_['medical'] == "NM" or $metData_['medical'] == "A" or $metData_['medical'] == "B"){
                  $label_medical = 'label-danger';
                }else{
                  $label_medical = 'label-success';
                }
                $nama = isset($metData_['keterangan']) && $metData_['keterangan'] != "" ? '<strong style="color: red;">'.$metData_['nama'].'</strong>' : $metData_['nama'];
              echo '<tr>
                  <td align="center">'.++$no.'</td>
                  <td>'.$metData_['namebroker'].'</td>
                  <td>'.$metData_['nameclient'].'</td>
                  <td>'.$metData_['nminsurance'].'</td>        
                  <td align="center">'.$metData_['typedata'].'</td>
                  <td align="center">'.$metData_['produk'].'</td>
                  <td>'.$nama.'</td>
                  <td align="center">'._convertDate($metData_['tgllahir']).'</td>
                  <td align="center">'.$metData_['usia'].'</td>
                  <td align="right">'.duit($metData_['plafond']).'</td>
                  <td align="center">'._convertDate($metData_['tglakad']).'</td>
                  <td align="center">'.$metData_['tenor'].'</td>
                  <td align="center">'._convertDate($metData_['tglakhir']).'</td>
                  <td align="right">'.duit($metData_['totalpremi']).'</td>
                  <td align="center"><span class="label '.$label_medical.'">'.$metData_['medical'].'</span></td>
                  <td align="center"><span class="label label-danger">'.$metData_['statusaktif'].'</span></td>
                  <td>'.$metData_['cabang'].'</td>
                  <td><a href="ajk.php?re=data&dt=viewpending&id='.$thisEncrypter->encode($metData_['idpeserta']).'">'.BTN_VIEW.'</a></td>
                  </tr>';
              }
              echo '
            </tbody>
            <tfoot>
              <tr>
                <th><input type="hidden" class="form-control" name="search_engine"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Broker"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Partner"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Insurance"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Product"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Pekerjaan"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Name"></th>
                <th><input type="hidden" class="form-control" name="search_engine"></th>
                <th><input type="hidden" class="form-control" name="search_engine" placeholder="Age"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Plafond"></th>
                <th><input type="hidden" class="form-control" name="search_engine"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Tenor"></th>
                <th><input type="hidden" class="form-control" name="search_engine"></th>
                <th><input type="hidden" class="form-control" name="search_engine"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Medical"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Status"></th>
                <th><input type="search" class="form-control" name="search_engine" placeholder="Branch"></th>
                <th><input type="hidden" class="form-control" name="search_engine"></th>
              </tr>
            </tfoot>
          </table>
        </div>
    	</div>
    </div>
    </div>';
  break;

  case "viewpending":
    $idpeserta = $thisEncrypter->decode($_REQUEST['id']);
    $query = "select * from ajkpeserta where idpeserta = '".$idpeserta."'";
    $peserta = mysql_fetch_array($database->doQuery($query));

    if (
      (
        strpos($peserta['medical'], 'NM') !== false ||
        strpos($peserta['medical'], 'A') !== false ||
        strpos($peserta['medical'], 'B') !== false
      )
    ) {
      $medical = '<button class="btn btn-danger btn-xs">'.$peserta['medical'].'</button>';
      $NMStatus = true;
    } else {
      $medical = '<strong>'.$peserta['medical'].'</strong>';
      $NMStatus = false;
    }

    if ($_REQUEST['src']=="approvepending") {
      if ($NMStatus === true && $_REQUEST['confirmNMStatus'] != 'ya') {
        $currentUrl = $_SERVER['REQUEST_URI'];
        // Preserve extrapremi value in URL for confirmation
        $extrapremi = isset($_REQUEST['extrapremi']) ? $_REQUEST['extrapremi'] : '0';
        if (strpos($currentUrl, 'extrapremi=') === false) {
          $currentUrl .= '&extrapremi=' . urlencode($extrapremi);
        }
        // Make sure src parameter is preserved
        if (strpos($currentUrl, 'src=') === false) {
          $currentUrl .= '&src=approvepending';
        }
        echo '<script>
          if (confirm("Medical mengandung NM/A/B. Apakah Anda yakin ingin melanjutkan approve?") ) {
            window.location.href = "' . $currentUrl . '&confirmNMStatus=ya";
          } else {
            window.location.href = "ajk.php?re=data&dt=pending";
          }
        </script>';
        exit; // Stop execution to prevent form from showing
      } else {
        // Direct approval execution
        $em = isset($_REQUEST['extrapremi']) ? $_REQUEST['extrapremi'] : '0';
        $totalpremi = $peserta['premi'] + $em;
        
        // Handle file uploads
        $documentsArray = array();
        $uploadErrors = array();
        $target_dir = $PathPeserta."/".$peserta['idpeserta']."/";
        
        // Create directory if not exists
        if (!is_dir($target_dir)) {
          if (!mkdir($target_dir, 0755, true)) {
            echo '
            <div class="alert alert-dismissable alert-danger">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <strong>Error!</strong> Gagal membuat direktori untuk upload file.
            </div>';
            exit;
          }
        }
        
        // Allowed file types
        $allowed_types = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif');
        
        // Upload Document 1
        if (isset($_FILES["documents1"]) && $_FILES["documents1"]["name"] != "") {
          if ($_FILES["documents1"]["error"] == UPLOAD_ERR_OK) {
            $namafile1 = date('dmYHis').'_1_'.str_replace(" ", "", basename($_FILES["documents1"]["name"]));
            $target_file1 = $target_dir . $namafile1;
            $imageFileType1 = strtolower(pathinfo($target_file1, PATHINFO_EXTENSION));
            
            if (!in_array($imageFileType1, $allowed_types)) {
              $uploadErrors[] = "Dokumen/File 1: Tipe file tidak diizinkan. Hanya PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF yang diperbolehkan.";
            } elseif ($_FILES["documents1"]["size"] > 5242880) {
              $uploadErrors[] = "Dokumen/File 1: Ukuran file terlalu besar. Maksimal 5MB.";
            } else {
              if (!move_uploaded_file($_FILES["documents1"]["tmp_name"], $target_file1)) {
                $uploadErrors[] = "Dokumen/File 1: Gagal mengupload file ke server.";
              } else {
                $documentsArray['DOCUMENT_APPROVE_1'] = $namafile1;
              }
            }
          } else {
            $uploadErrors[] = "Dokumen/File 1: Error upload - " . $_FILES["documents1"]["error"];
          }
        }
        
        // Upload Document 2
        if (isset($_FILES["documents2"]) && $_FILES["documents2"]["name"] != "") {
          if ($_FILES["documents2"]["error"] == UPLOAD_ERR_OK) {
            $namafile2 = date('dmYHis').'_2_'.str_replace(" ", "", basename($_FILES["documents2"]["name"]));
            $target_file2 = $target_dir . $namafile2;
            $imageFileType2 = strtolower(pathinfo($target_file2, PATHINFO_EXTENSION));
            
            if (!in_array($imageFileType2, $allowed_types)) {
              $uploadErrors[] = "Dokumen/File 2: Tipe file tidak diizinkan. Hanya PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF yang diperbolehkan.";
            } elseif ($_FILES["documents2"]["size"] > 5242880) {
              $uploadErrors[] = "Dokumen/File 2: Ukuran file terlalu besar. Maksimal 5MB.";
            } else {
              if (!move_uploaded_file($_FILES["documents2"]["tmp_name"], $target_file2)) {
                $uploadErrors[] = "Dokumen/File 2: Gagal mengupload file ke server.";
              } else {
                $documentsArray['DOCUMENT_APPROVE_2'] = $namafile2;
              }
            }
          } else {
            $uploadErrors[] = "Dokumen/File 2: Error upload - " . $_FILES["documents2"]["error"];
          }
        }
        
        // Check if there are upload errors
        if (!empty($uploadErrors)) {
          echo '
          <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Error Upload!</strong>
            <ul>';
          foreach ($uploadErrors as $error) {
            echo '<li>' . $error . '</li>';
          }
          echo '</ul>
          </div>
          <script>
            setTimeout(function() {
              window.location.href = "ajk.php?re=data&dt=viewpending&id='.$_REQUEST['id'].'";
            }, 5000);
          </script>';
          exit;
        }
        
        // Convert to JSON
        $documentsJson = !empty($documentsArray) ? json_encode($documentsArray) : '';
        
        if($q['level'] == 90){
          $query = "UPDATE ajkpeserta set statusaktif = 'Validasi',extrapremi= '".$em."',totalpremi= '".$totalpremi."',update_by='".$q['id']."',update_time='".$today."' where idpeserta = '".$idpeserta."'";
        }elseif($q['level'] > 90){
          $query = "UPDATE ajkpeserta set statusaktif = 'Analisa Asuransi',extrapremi= '".$em."',totalpremi= '".$totalpremi."',update_by='".$q['id']."',update_time='".$today."' where idpeserta = '".$idpeserta."'";
        }
        
        $result = mysql_query($query);
        
        // Update ajkpesertaas with documents if file uploaded
        if ($result && !empty($documentsJson)) {
          $query_update_as = "UPDATE ajkpesertaas 
                             SET documents = '".mysql_real_escape_string($documentsJson)."',
                                 update_by = '".$q['id']."',
                                 update_time = '".$today."'
                             WHERE idpeserta = '".$idpeserta."'";
          mysql_query($query_update_as);
        }
        
        if($result){
          echo '
          <meta http-equiv="refresh" content="2; url=ajk.php?re=data&dt=pending">
            <div class="alert alert-dismissable alert-success">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <strong>Success!</strong>
            </div>';
          exit; // Stop execution to prevent form from showing
        }
      }

    }elseif($_REQUEST['src']=="rejectpending"){
      $query = "UPDATE ajkpeserta set statusaktif = 'Pending',keterangan='".$_REQUEST['keterangan']."',pending_by='".$q['id']."',pending_time='".date("Y-m-d H:i:s")."' where idpeserta = '".$idpeserta."'";      
      $result = mysql_query($query);
      if($result){
        echo '
        <meta http-equiv="refresh" content="2; url=ajk.php?re=data&dt=pending">
          <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
            <strong>Success!</strong>
          </div>';
      }
    }

    if($_REQUEST['src']=="tolak"){
      $query = "UPDATE ajkpeserta set statusaktif = 'Tolak Asuransi',keterangan='".$_REQUEST['keterangan']."',update_by='".$q['id']."',update_time='".date("Y-m-d H:i:s")."' where idpeserta = '".$idpeserta."'";      
      $result = mysql_query($query);
      if($result){
        echo '
        <meta http-equiv="refresh" content="2; url=ajk.php?re=data&dt=pending">
          <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
            <strong>Success!</strong>
          </div>';
        exit; // Stop execution to prevent form from showing
      }
    }

    // Handle VLife API submission
    if($_REQUEST['src']=="sendtovlife"){
      try {
        // Get full peserta data
        $queryFull = "SELECT * FROM vpeserta WHERE idpeserta = '".$idpeserta."'";
        $pesertaFull = mysql_fetch_array($database->doQuery($queryFull));
        
        // Get JSON data from json.php
        $jsonUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/json.php?i=' . $thisEncrypter->encode($idpeserta);
        $jsonData = file_get_contents($jsonUrl);
        $requestData = json_decode($jsonData, true);
        
        if (!$requestData) {
          throw new Exception('Failed to get JSON data from json.php');
        }
        
        // Initialize VLife API
        $vlifeApi = new VlifeApi();
        
        // Get API endpoint from VLife API settings
        $apiUrl = $vlifeApi->apiUrl . '/policy/group';
        
        // Submit to VLife API
        $response = $vlifeApi->submitPolicyGroup($requestData);
        
        // Decode response if it's a string
        $responseData = is_string($response) ? json_decode($response, true) : $response;
        $httpCode = 200; // Assume success if no exception
        
        // Log API call to history
        $vlifeApi->logApiHistory(
          $apiUrl,
          'POST',
          $requestData,
          $responseData,
          $httpCode,
          $idpeserta,
          $pesertaFull['asuransi'],
          'out'
        );
        
        if ($responseData && isset($responseData['_id'])) {
          // Save callback with full response data
          $vlifeApi->saveCallback($idpeserta, $pesertaFull['asuransi'], $responseData);
          
          // Extract certificate and policy numbers
          $certificateNo = isset($responseData['certificate_no']) ? $responseData['certificate_no'] : '';
          $policyNo = isset($responseData['policy_no']) ? $responseData['policy_no'] : '';
          
          // Update ajkpeserta with certificate number
          $updateQuery = "UPDATE ajkpeserta SET 
                          noasuransi = '".mysql_real_escape_string($certificateNo)."',
                          update_by='".$q['id']."',
                          update_time='".date("Y-m-d H:i:s")."'
                          WHERE idpeserta = '".$idpeserta."'";
          mysql_query($updateQuery);
          
          echo '
          <meta http-equiv="refresh" content="3; url=ajk.php?re=data&dt=pending">
            <div class="alert alert-dismissable alert-success">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <strong>Success!</strong> Data berhasil dikirim ke Asuransi API.
              <br>Certificate No: '.$certificateNo.'
              <br>Policy No: '.$policyNo.'
              <br>API ID: '.$responseData['_id'].'
            </div>';
          exit;
        } else {
          // Error
          echo '
            <div class="alert alert-dismissable alert-danger">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <strong>Error!</strong> Gagal mengirim data ke Asuransi API. Response tidak valid.
            </div>';
        }
      } catch (Exception $e) {
        // Log error to API history
        if (isset($vlifeApi) && isset($apiUrl) && isset($requestData)) {
          $vlifeApi->logApiHistory(
            $apiUrl,
            'POST',
            $requestData,
            array('error' => $e->getMessage()),
            500,
            $idpeserta,
            isset($pesertaFull['asuransi']) ? $pesertaFull['asuransi'] : null,
            'out'
          );
        }
        
        echo '
          <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Error!</strong> '.$e->getMessage().'
          </div>';
      }
    }

    echo '<div class="page-header-section"><h2 class="title semibold">Pending/Medical Members</h2></div>
    <div class="page-header-section">
      </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="col-md-12">
                <table class="table table-bordered table-condensed">
                  <tbody>
                    <tr>
                      <th>KTP</th><td><strong>'.$peserta['nomorktp'].'</strong></td>
                    </tr>
                    <tr>
                      <th>ID Peserta</th><td><strong>'.$peserta['idpeserta'].'</strong></td>
                    </tr>
                    <tr>
                      <th>No Pinjaman</th><td><strong>'.$peserta['nopinjaman'].'</strong></td>
                    </tr>
                    <tr>
                      <th>Nama</th><td><strong>'.$peserta['nama'].'</strong></td>
                    </tr>
                    <tr>
                      <th>Tanggal Lahir</th><td><strong>'.$peserta['tgllahir'].'</strong></td>
                    </tr>
                    <tr>
                      <th>Tanggal Usia</th><td><strong>'.$peserta['usia'].'</strong></td>
                    </tr>
                    <tr>
                      <th>Plafond</th><td><strong>'.duit($peserta['plafond']).'</strong></td>
                    </tr>
                    <tr>
                      <th>Jangka Waktu</th><td><strong>'.$peserta['tenor'].'</strong></td>
                    </tr>
                    <tr>
                      <th>Tanggal Asuransi</th><td><strong>'._convertDate($peserta['tglakad']).' to '._convertDate($peserta['tglakhir']).'</strong></td>
                    </tr>
                    <tr>
                      <th>Total Premium</th><td><strong>'.duit($peserta['totalpremi']).'</strong></td>
                    </tr>
                    <tr>
                      <th>Medical</th><td><strong>'.$medical.'</strong></td>
                    </tr>
                    <tr>
                      <th>File KTP</th><td><a href="../'.$PathPeserta.''.$peserta['idpeserta'].'/'.$peserta['ktp_file'].'" target="_blank">'.BTN_VIEW.'</a></td>
                    </tr>
                    <tr>
                      <th>File SPPA</th><td><a href="../'.$PathPeserta.''.$peserta['idpeserta'].'/'.$peserta['sppa_file'].'" target="_blank">'.BTN_VIEW.'</a></td>
                    </tr>
                    <!--<tr>
                      <th>File Sertifikat</th><td>'.(isset($peserta['noasuransi_img']) && $peserta['noasuransi_img'] ? '<a href="'.$peserta['noasuransi_img'].'" target="_blank">'.BTN_VIEW.'</a>' : '-').'</td>
                    </tr>
                    <tr>
                      <th>VLife Status</th><td>'.(isset($peserta['vlife_sent_date']) && $peserta['vlife_sent_date'] ? '<span class="label label-success">Sent on '._convertDate($peserta['vlife_sent_date']).'</span>' : '<span class="label label-default">Not Sent</span>').'</td>
                    </tr>-->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>';

      // Only show approval form if not processing approval
      if ($_REQUEST['src'] != "approvepending" && $_REQUEST['src'] != "rejectpending" && $_REQUEST['src'] != "tolak" && $_REQUEST['src'] != "sendtovlife") {
        $keterangan = isset($peserta['keterangan']) && $peserta['keterangan'] != "" ? $peserta['keterangan'] : '';
        echo '<div class="row">
          <div class="col-md-12">            
            <form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
              <div class="panel-heading"><h3 class="panel-title">Pending Accept</h3></div>
              <div class="panel-body">
                <div class="form-group">
                  <label class="control-label col-sm-2">Extrapremi</label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-10">
                        <input type="text" name="extrapremi" class="form-control"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-sm-2">Keterangan</label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-10">
                        <textarea name="keterangan" class="form-control">'.$keterangan.'</textarea>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-sm-2">Dokumen/File 1</label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-10">
                        <input type="file" name="documents1" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"/>
                        <small class="form-text text-muted">Tipe file: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF (Max 5MB)</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-sm-2">Dokumen/File 2</label>
                  <div class="col-sm-10">
                    <div class="row">
                      <div class="col-md-10">
                        <input type="file" name="documents2" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"/>
                        <small class="form-text text-muted">Tipe file: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF (Max 5MB)</small>
                      </div>
                    </div>
                  </div>
                </div>                
              </div>
              <div class="panel-footer">
                <input type="hidden" name="src" value="approvepending">
                <input type="hidden" name="extrapremi" value="0">'.BTN_APPROVEAS.' 
                <button type="button" class="btn btn-danger mb5 btn-xs" onclick="rejectWithKeterangan(this.form)"><i class="ico-cancel"></i> Pending</button>
                <button type="button" class="btn btn-danger mb5 btn-xs" onclick="tolak(this.form)"><i class="ico-cancel"></i> Tolak Asuransi</button>
                <!--<button type="button" class="btn btn-info mb5 btn-xs" onclick="sendToVlife(this.form)"><i class="ico-upload"></i> Send to VAI</button>-->
              </div>
            </form>
          </div>
        </div>
        
        <script>
          function rejectWithKeterangan(form){
            var k = (form.keterangan && form.keterangan.value) ? form.keterangan.value.trim() : "";
            if(k === ""){
              if(!confirm("Keterangan kosong. Lanjut reject?")) return;
            }
            form.src.value = "rejectpending";
            form.submit();
          }
          function tolak(form){
            var k = (form.keterangan && form.keterangan.value) ? form.keterangan.value.trim() : "";
            if(k === ""){
              if(!confirm("Keterangan kosong. Lanjut Tolak?")) return;
            }
            form.src.value = "tolak";
            form.submit();
          }
          function sendToVlife(form){
            if(!confirm("Kirim data peserta ke VLife API?")) return;
            form.src.value = "sendtovlife";
            form.submit();
          }
        </script>';
      }
  break;

  case "ApproveIns":
    $qmember = "SELECT idpeserta,
                       nama,
                       nomorpk,
                       tglakad,
                       plafond,
                       tenor,
                       premi,
                       premirate,
                       aspremi,
                       aspremirate,
                       astotalpremi,
                       tgllunas,
                       statuslunas,
                       usia,
                       tglakhir,
                       tgllahir,
                       ajkpolis.produk,
                       ajkcabang.name as nmcabang,
                       nopinjaman,
                       nm_kategori_profesi,
                       ajkinsurance.name as nmasuransi,
                       round(premi*ajkinsurance.bf/100,2)as bf,
                       round(premi*ajkinsurance.cad_klaim/100,2)as cad_klaim,
                       round(premi*ajkinsurance.cad_premi/100,2)as cad_premi
                FROM ajkpeserta
                INNER JOIN ajkpolis
                ON ajkpolis.id = ajkpeserta.idpolicy
                INNER JOIN ajkcabang
                ON ajkcabang.er = ajkpeserta.cabang
                LEFT JOIN ajkinsurance
                ON ajkinsurance.id = ajkpeserta.asuransi
                LEFT JOIN ajkprofesi
                ON ajkprofesi.ref_mapping = ajkpeserta.pekerjaan
                LEFT JOIN ajkkategoriprofesi
                ON ajkkategoriprofesi.id = ajkprofesi.idkategoriprofesi
                WHERE statusaktif = 'Inforce' and
                      checker_by is null";
      $_SESSION['lprmemberasviewapp'] = $thisEncrypter->encode($qmember);

    if ($_REQUEST['btnsubmit']=="submit") {
      $query = '';
      foreach($_REQUEST['idtemp'] as $k => $val){
        $query = "UPDATE ajkpeserta SET checker_by = '".$q['id']."',checker_time='".$today."',update_by='".$q['id']."',update_time='".$today."' WHERE idpeserta = '".$val."' and del is null; ";
        mysql_query($query);              
        // echo $query;  
      }
    }
    
    echo '<script type="text/javascript" src="templates/{template_name}/javascript/jquery.inputmask.bundle.js"></script>';
    echo '<script>
        $(function(){
          $(".datepicker").datepicker({dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true});
        });
        </script>';
         
    echo '
      <div class="page-header-section"><h2 class="title semibold">List View Insurance</h2></div>
      <div class="page-header-section"></div>
    </div>
    <div class="row">
    <div class="col-md-12">
    <div class="panel panel-default">
    <a href="ajk.php?re=dlExcel&Rxls=lprviewappins" target="_blank"><img src="../image/excel.png" width="20"><br>Excel</a>

    <form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
    <table class="table table-hover table-bordered table-striped table-responsive" id="">
      <thead>
        <tr>
          <th width="5%"><input type="checkbox" id="selectall"/></th>         
          <th width="5%">ID Peserta</th>
          <th width="5%">Nomor Pinjaman</th>
          <th width="5%">Nama</th>          
          <th width="5%">Tgl Akad</th>
          <th width="2%">Plafond</th>
          <th width="1%">Tenor</th>
          <th width="1%">Pekerjaan</th>
          <th width="1%">Rate As</th>
          <th width="1%">Premi As</th>
          <th width="5%">Cabang</th> 
          <th width="5%">Asuransi</th>          
        </tr>
      </thead>
      <tbody>';

    $metMember = $database->doQuery($qmember);
    while ($metMember_ = mysql_fetch_array($metMember)) {     
      $dataceklist = '<input type="checkbox" class="case" name="idtemp[]" value="'.$metMember_['idpeserta'].'">';
      echo '
      <tr>
        <td align="center">'.$dataceklist.' '.++$no.'</td>
        <td align="center">'.$metMember_['idpeserta'].'</td>
        <td align="center">'.$metMember_['nopinjaman'].'</td>
        <td>'.$metMember_['nama'].'</td>
        <td align="center">'._convertDate($metMember_['tglakad']).'</td>
        <td align="right">'.duit($metMember_['plafond']).'</td>
        <td align="center">'.$metMember_['tenor'].'</td>
        <td align="center">'.$metMember_['nm_kategori_profesi'].'</td>
        <td align="center">'.ROUND($metMember_['aspremirate'],2).'</td>
        <td align="center">'.duit($metMember_['astotalpremi']).'</td>
        <td>'.$metMember_['nmcabang'].'</td>
        <td align="center">'.$metMember_['nmasuransi'].'</td>
      </tr>';  
            
    }
        echo '
              </tbody>
            </table>
            <div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>
            </form>
          </div>
                
          </div>

        </div>
      </div>
    </div> 

    <script language="javascript">
      $(function(){
          $("#selectall").click(function () { $(\'.case\').attr(\'checked\', this.checked); });         // add multiple select / deselect functionality
          $(".case").click(function(){                                  // if all checkbox are selected, check the selectall checkbox // and viceversa
              if($(".case").length == $(".case:checked").length) {
                  $("#selectall").attr("checked", "checked");
              } else {
                  $("#selectall").removeAttr("checked");
              }

          });
      });
    </script>'; 
  break;

  case "SertifikatIns":
    $query = "SELECT noasuransi,
                      idasuransi,
                     ajkinsurance.name as nmasuransi,
                     (SELECT count(*) FROM ajkpeserta WHERE ajkpeserta.noasuransi = ajksertifikatins.noasuransi)as jmlpes
              FROM ajksertifikatins
              INNER JOIN ajkinsurance on ajkinsurance.id = ajksertifikatins.idasuransi";

    if($_REQUEST['d']=="form"){
      if($_REQUEST['i']){
        $noasuransi = $thisEncrypter->decode($_REQUEST['i']);
      }
      

      if($_REQUEST['btnsubmit']=="submit"){
        if($noasuransi){
          $query="UPDATE ajksertifikatins SET noasuransi = '".$_REQUEST['noasuransi']."',idasuransi = '".$_REQUEST['asuransi']."' WHERE noasuransi = '".$noasuransi."'";
        }else{
          $query="INSERT INTO ajksertifikatins SET noasuransi = '".$_REQUEST['noasuransi']."',idasuransi = '".$_REQUEST['asuransi']."'";
        }

        $result = mysql_query($query);
        if($result){
          echo '
          <meta http-equiv="refresh" content="2; url=ajk.php?re=data&dt=SertifikatIns">
            <div class="alert alert-dismissable alert-success">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
              <strong>Success!</strong>
            </div>';
          }          
        
      }else{
        
        if($noasuransi){
          $qas = $query." WHERE noasuransi = '".$noasuransi."'";
          $rowas = mysql_fetch_array(mysql_query($qas));
        }
  
        $query = "SELECT * FROM ajkinsurance WHERE del is null";
        $result = mysql_query($query);
  
        while($row = mysql_fetch_array($result)){
          if($row['id'] == $rowas['idasuransi']){
            $selected = " selected";
          }else{
            $selected = "";
          }
          $asuransi .='<option value="'.$row['id'].'"'.$selected.'>'.$row['name'].'</option>';
        }
  
        echo '
        <div class="page-header-section"><h2 class="title semibold">Input Sertifikat</h2></div> 
        </div>
        <div class="row">
          <div class="col-md-12">
            <form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
              <div class="panel-body">
                <div class="form-group">
                  <label class="col-lg-2 control-label">No Sertifikat</label>
                  <div class="col-lg-10"><input type="text" class="form-control" name="noasuransi" value="'.$rowas['noasuransi'].'" placeholder="No Sertifikat"/></div>
                </div>
  
                <div class="form-group">
                  <label class="col-lg-2 control-label">Asuransi</label>
                  <div class="col-lg-10">
                    <select class="form-control" name="asuransi">
                      <option value="">- Pilih -</option>
                      '.$asuransi.'
                    </select>
                  </div>
                </div>              
  
              </div>
              <div class="panel-footer text-center"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>
            </form>
          </div>
        </div>';
      } 
    }elseif($_REQUEST['d']=="peserta"){
      $query = "SELECT *
                FROM vpeserta 
                WHERE noasuransi = '".$thisEncrypter->decode($_REQUEST['i'])."'";
      $queryas = mysql_fetch_array(mysql_query("SELECT idasuransi FROM ajksertifikatins WHERE noasuransi = '".$thisEncrypter->decode($_REQUEST['i'])."'"));
      
      echo '
        <div class="page-header-section"><h2 class="title semibold">List View Peserta Sertifikat '.$thisEncrypter->decode($_REQUEST['i']).'</h2></div>        
        <div class="page-header-section"><div class="toolbar"><a href="ajk.php?re=data&dt=SertifikatIns">'.BTN_BACK.'</a></div></div>
      </div>
      <div class="row">
      <div class="col-md-12">
      <a href="ajk.php?re=data&dt=SertifikatIns&d=pesertaadd&i='.$thisEncrypter->encode($queryas['idasuransi']).'&a='.$_REQUEST['i'].'" class="btn btn-success">Tambah Peserta</a>
      <div class="panel panel-default">
      <form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
        <table class="table table-hover table-bordered table-striped table-responsive" id="">
          <thead>
            <tr>
              <th width="5%" class="text-center">No</th>
              <th width="5%" class="text-center">ID Peserta</th>
              <th width="5%" class="text-center">Nomor Pinjaman</th>
              <th width="5%" class="text-center">Nama</th>          
              <th width="5%" class="text-center">Tgl Akad</th>
              <th width="2%" class="text-center">Plafond</th>
              <th width="1%" class="text-center">Tenor</th>
              <th width="1%" class="text-center">Pekerjaan</th>
              <th width="1%" class="text-center">Rate As</th>
              <th width="1%" class="text-center">Premi As</th>
              <th width="5%" class="text-center">Cabang</th> 
              <th width="5%" class="text-center">Asuransi</th>          
            </tr>
          </thead>
          <tbody>';

            $metMember = $database->doQuery($query);
            while ($metMember_ = mysql_fetch_array($metMember)) {     
              
              echo '
              <tr>
                <td align="center">'.++$no.'</td>
                <td align="center">'.$metMember_['idpeserta'].'</td>
                <td align="center">'.$metMember_['nopinjaman'].'</td>
                <td>'.$metMember_['nama'].'</td>
                <td align="center">'._convertDate($metMember_['tglakad']).'</td>
                <td align="right">'.duit($metMember_['plafond']).'</td>
                <td align="center">'.$metMember_['tenor'].'</td>
                <td align="center">'.$metMember_['nm_kategori_profesi'].'</td>
                <td align="center">'.ROUND($metMember_['aspremirate'],2).'</td>
                <td align="center">'.duit($metMember_['astotalpremi']).'</td>
                <td>'.$metMember_['nmcabang'].'</td>
                <td align="center">'.$metMember_['nm_asuransi'].'</td>
              </tr>';  
                    
            }
            echo '
          </tbody>
        </table>
        
        </div>            
          </div>
        </div>
      </form>
            
      <script language="javascript">
        $(function(){
            $("#selectall").click(function () { $(\'.case\').attr(\'checked\', this.checked); });         // add multiple select / deselect functionality
            $(".case").click(function(){                                  // if all checkbox are selected, check the selectall checkbox // and viceversa
                if($(".case").length == $(".case:checked").length) {
                    $("#selectall").attr("checked", "checked");
                } else {
                    $("#selectall").removeAttr("checked");
                }

            });
        });
      </script>'; 
    }elseif($_REQUEST['d']=="pesertaadd"){
      
      $query = "SELECT * FROM vpeserta WHERE asuransi = '".$thisEncrypter->decode($_REQUEST['i'])."' and ifnull(noasuransi,'')='' and iddn !='' ";
      
      if ($_REQUEST['btnsubmit']=="submit") {
        $query = '';
        // echo $_REQUEST['idtemp'];
        foreach($_REQUEST['idtemp'] as $k => $val){
          $query = "UPDATE ajkpeserta SET noasuransi = '".$thisEncrypter->decode($_REQUEST['a'])."' WHERE idpeserta = '".$val."'; ";
          
          $result = mysql_query($query);
          if($result){
            echo '
            <meta http-equiv="refresh" content="2; url=ajk.php?re=data&dt=SertifikatIns&d=peserta&i='.$_REQUEST['a'].'">
              <div class="alert alert-dismissable alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                <strong>Success!</strong>
              </div>';
            }          
        }
      }
           
      echo '
        <div class="page-header-section"><h2 class="title semibold">Peserta Sertifikat</h2></div>
        <div class="page-header-section"><div class="toolbar"><a href="ajk.php?re=data&dt=SertifikatIns&d=peserta&i='.$_REQUEST['a'].'">'.BTN_BACK.'</a></div></div>
      </div>
      <div class="row">
      <div class="col-md-12">
      <div class="panel panel-default">
      <form method="post" class="panel panel-color-top panel-default form-horizontal" action="#" data-parsley-validate enctype="multipart/form-data">
      <table class="table table-hover table-bordered table-striped table-responsive" id="">
        <thead>
          <tr>
            <th width="5%"><input type="checkbox" id="selectall"/></th>         
            <th width="5%">ID Peserta</th>
            <th width="5%">Nomor Pinjaman</th>
            <th width="5%">Nama</th>          
            <th width="5%">Tgl Akad</th>
            <th width="2%">Plafond</th>
            <th width="1%">Tenor</th>
            <th width="1%">Pekerjaan</th>
            <th width="1%">Rate As</th>
            <th width="1%">Premi As</th>
            <th width="5%">Cabang</th> 
            <th width="5%">Asuransi</th>          
          </tr>
        </thead>
        <tbody>';
  
      $metMember = $database->doQuery($query);
      while ($metMember_ = mysql_fetch_array($metMember)) {     
        $dataceklist = '<input type="checkbox" class="case" name="idtemp[]" value="'.$metMember_['idpeserta'].'">';
        echo '
        <tr>
          <td align="center">'.$dataceklist.' '.++$no.'</td>
          <td align="center">'.$metMember_['idpeserta'].'</td>
          <td align="center">'.$metMember_['nopinjaman'].'</td>
          <td>'.$metMember_['nama'].'</td>
          <td align="center">'._convertDate($metMember_['tglakad']).'</td>
          <td align="right">'.duit($metMember_['plafond']).'</td>
          <td align="center">'.$metMember_['tenor'].'</td>
          <td align="center">'.$metMember_['nm_kategori_profesi'].'</td>
          <td align="center">'.ROUND($metMember_['aspremirate'],2).'</td>
          <td align="center">'.duit($metMember_['astotalpremi']).'</td>
          <td>'.$metMember_['nmcabang'].'</td>
          <td align="center">'.$metMember_['nm_asuransi'].'</td>
        </tr>';  
              
      }
          echo '
                </tbody>
              </table>
              <div class="panel-footer"><input type="hidden" name="btnsubmit" value="submit">'.BTN_SUBMIT.'</div>
              </form>
            </div>
                  
            </div>
  
          </div>
        </div>
      </div> 
  
      <script language="javascript">
        $(function(){
            $("#selectall").click(function () { $(\'.case\').attr(\'checked\', this.checked); });         // add multiple select / deselect functionality
            $(".case").click(function(){                                  // if all checkbox are selected, check the selectall checkbox // and viceversa
                if($(".case").length == $(".case:checked").length) {
                    $("#selectall").attr("checked", "checked");
                } else {
                    $("#selectall").removeAttr("checked");
                }
  
            });
        });
      </script>';
    }else{
        echo '
        <div class="page-header-section"><h2 class="title semibold">List View Sertifikat</h2></div> 
        </div>
        <div class="row">
          <div class="col-md-12">
            <a href="ajk.php?re=data&dt=SertifikatIns&d=form" class="btn btn-success">New</a>
            <div class="panel panel-default">
              <table class="table table-hover table-bordered table-striped table-responsive" id="">
                <thead>
                  <tr>
                    <th width="1%" class="text-center">No</th>         
                    <th width="20%" class="text-center">Asuransi</th>
                    <th width="20%" class="text-center">Sertifikat</th>
                    <th width="20%" class="text-center">Jumlah Peserta</th>
                    <th width="40%" class="text-center">Action</th>          
                  </tr>
                </thead>
                <tbody>';

                  $row = $database->doQuery($query);
                  while ($row_ = mysql_fetch_array($row)) {     
                    $edit = '<a href="ajk.php?re=data&dt=SertifikatIns&d=form&i='.$thisEncrypter->encode($row_['noasuransi']).'" class="btn btn-warning">Edit</a>';
                    $addpeserta = '<a href="ajk.php?re=data&dt=SertifikatIns&d=peserta&i='.$thisEncrypter->encode($row_['noasuransi']).'" class="btn btn-primary">Peserta</a>';

                    echo '
                    <tr>
                      <td align="center">'.++$no.'</td>
                      <td align="center">'.$row_['nmasuransi'].'</td>
                      <td align="center">'.$row_['noasuransi'].'</td>
                      <td align="center">'.$row_['jmlpes'].'</td>
                      <td align="center">'.$edit.' '.$addpeserta.'</td>
                    </tr>';                            
                  }
                  echo '
                </tbody>
              </table>
            </div>            
          </div>
        </div>';
    }
      echo'
      </div>
    </div>'; 
  break;

  default:
  
    echo '
      <div class="page-header-section"><h2 class="title semibold">Members</h2></div>
      <div class="page-header-section"></div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-default">
          <table class="table table-hover table-bordered table-striped table-responsive" id="column-filtering">
            <thead>
              <tr>
                <th width="1%">No</th>
                <th>Product</th>
                <th>Pekerjaan</th>
                <th width="1%">Debit Note</th>
                <th width="1%">ID Member</th>
                <th width="1%">Certificate</th>
                <th>Name</th>
                <th width="1%">DOB</th>
                <th width="1%">Age</th>
                <th width="10%">Plafond</th>
                <th width="10%">Start Insurance</th>
                <th width="10%">Tenor</th>
                <th width="10%">Start Insurance</th>
                <th width="1%">Premium (Bank)</th>
                <th width="1%">Tgl. Bayar</th>
                <th>Status</th>
                <th width="1%">Branch</th>
              </tr>
            </thead>
            <tbody>';
              $metData = $database->doQuery('SELECT
              ajkpeserta.id,
              ajkcobroker.`name` AS namebroker,
              ajkclient.`name` AS nameclient,
              ajkpolis.produk,
              ajkdebitnote.nomordebitnote,
              ajkdebitnote.tgldebitnote,
              ajkpeserta.idpeserta,
              ajkpeserta.nomorktp,
              ajkpeserta.typedata,
              ajkpeserta.nama,
              ajkpeserta.tgllahir,
              ajkpeserta.usia,
              ajkpeserta.plafond,
              ajkpeserta.tglakad,
              ajkpeserta.tenor,
              ajkpeserta.tglakhir,
              ajkpeserta.totalpremi,
              ajkpeserta.noasuransi_img,
              ajkpeserta.astotalpremi,
              ajkpeserta.statusaktif,
              ajkpeserta.noasuransi,
              ajkcabang.`name` AS cabang,
              (select tglbayar from ajkbayar where ajkbayar.idpeserta = ajkpeserta.idpeserta and tipebayar = "premibank" order by ajkbayar.id desc limit 1) as tgllunas,
              (select buktibayar_path from ajkbayar where ajkbayar.idpeserta = ajkpeserta.idpeserta and tipebayar = "premibank" order by ajkbayar.id desc limit 1) as bukti
              FROM ajkpeserta
              INNER JOIN ajkcobroker ON ajkpeserta.idbroker = ajkcobroker.id
              INNER JOIN ajkclient ON ajkpeserta.idclient = ajkclient.id
              LEFT JOIN ajkpolis ON ajkpeserta.idpolicy = ajkpolis.id and ajkpolis.del is null
              LEFT JOIN ajkdebitnote ON ajkpeserta.iddn = ajkdebitnote.id
              INNER JOIN ajkcabang ON ajkpeserta.cabang = ajkcabang.er
              WHERE (ajkpeserta.iddn IS NOT NULL or ajkpeserta.statusaktif = "Tolak Asuransi") AND 
              ajkpeserta.del IS NULL AND 
              (ajkpeserta.statusaktif!="Pending" OR ajkpeserta.statusaktif!="Upload") '.$q___1.'
              ORDER BY ajkdebitnote.tgldebitnote DESC');              

              while ($metData_ = mysql_fetch_array($metData)) {
                if ($metData_['statusaktif'] == "Lapse" OR $metData_['statusaktif'] == "Batal" OR $metData_['statusaktif'] == "Claim" OR $metData_['statusaktif'] == "Reject") {
                  $metStatus='<span class="label label-danger">'.$metData_['statusaktif'].'</span>';
                }elseif ($metData_['statusaktif']=="Maturity") {
                  $metStatus='<span class="label label-warning">'.$metData_['statusaktif'].'</span>';
                }elseif ($metData_['statusaktif']=="Request") {
                  $metStatus='<span class="label label-teal">'.$metData_['statusaktif'].'</span>';
                }else{
                  $metStatus='<span class="label label-primary">'.$metData_['statusaktif'].'</span>';
                }

                if(isset($metData_['noasuransi_img'])){
                  $certificate = '<a href="'.$metData_['noasuransi_img'].'" target="_blank">'.$metData_['noasuransi'].'</a>';
                }elseif(isset($metData_['nomordebitnote']) && $metData_['nomordebitnote'] != ""){
                  $certificate = '<a href="ajk.php?re=dlmPdf&pdf=sertifikatvictoria&id='.$metData_['idpeserta'].'" class="btn btn-primary btn-xs" target="_blank">Download</a>';
                }else{
                  $certificate = '';
                }

                
                if($q['level'] == 99){
                  $idpeserta = '<a href="json.php?i='.$thisEncrypter->encode($metData_['idpeserta']).'" title="Create Json" target="_blank">'.$metData_['idpeserta'].'</a>';
                }else{
                  $idpeserta = $metData_['idpeserta'];
                }
                if($metData_['tgllunas']){
                  if($metData_['bukti']){
                    $tglbayar = '<a href="../'.$PathPembayaran.$metData_['bukti'].'" target="_blank">'._convertDate($metData_['tgllunas']).'</a>';
                  }else{
                    $tglbayar = _convertDate($metData_['tgllunas']);
                  }
                }else{
                  $tglbayar = '';
                }
                
                echo '
                <tr>
                  <td align="center">'.++$no.'</td>
                  <td align="center">'.$metData_['typedata'].'</td>
                  <td align="center">'.$metData_['produk'].'</td>
                  <td>'.$metData_['nomordebitnote'].'</td>
                  <td align="center">'.$idpeserta.'</td>
                  <td align="center">'.$certificate.'</td>
                  <td>'.$metData_['nama'].'</td>
                  <td align="center">'.$metData_['tgllahir'].'</td>
                  <td align="center">'.$metData_['usia'].'</td>
                  <td align="right">'.duit($metData_['plafond']).'</td>
                  <td align="center">'.$metData_['tglakad'].'</td>
                  <td align="center">'.$metData_['tenor'].'</td>
                  <td align="center">'.$metData_['tglakhir'].'</td>
                  <td align="right">'.duit($metData_['totalpremi']).'</td>
                  <td align="right">'.$tglbayar.'</td>
                  <td align="center">'.$metStatus.'</td>
                  <td>'.$metData_['cabang'].'</td>
                </tr>';
              }
              echo '
            </tbody>
            <tfoot>
                <tr>
                  <th><input type="hidden" class="form-control" name="search_engine"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Product"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Pekerjaan"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Debit Note"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="ID Member"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Certificate"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Name"></th>
                  <th><input type="hidden" class="form-control" name="search_engine"></th>
                  <th><input type="hidden" class="form-control" name="search_engine" placeholder="Age"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Plafond"></th>
                  <th><input type="hidden" class="form-control" name="search_engine"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Tenor"></th>
                  <th><input type="hidden" class="form-control" name="search_engine"></th>
                  <th><input type="hidden" class="form-control" name="search_engine"></th>
                  <th><input type="hidden" class="form-control" name="search_engine"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Status"></th>
                  <th><input type="search" class="form-control" name="search_engine" placeholder="Branch"></th>
                </tr>
            </tfoot>
          </table>
        </div>
    	</div>
    </div>
  </div>';
    		;
} // switch

echo '</div>
		<a href="#" class="totop animation" data-toggle="waypoints totop" data-showanim="bounceIn" data-hideanim="bounceOut" data-offset="50%"><i class="ico-angle-up"></i></a>
    </section>';
?>