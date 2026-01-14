<?php
    
    error_reporting(0);
    session_start();
    include_once('../includes/jjt1502.php');
    include_once('../includes/db.php');
    include_once("../includes/Encrypter.class.php");
    $thisEncrypter = new textEncrypter();
    $today = date('Y-m-d h:i:s');
    include_once('../includes/functions.php');
    $database = new db();

    $idpeserta = $thisEncrypter->decode($_REQUEST['i']);
    $query = "SELECT * FROM ajkpeserta WHERE idpeserta = '".$idpeserta."'";
    $queryas = "SELECT * FROM ajkpesertaas WHERE idpeserta = '".$idpeserta."' and idas= 2";
      
    $peserta = mysql_fetch_array($database->doQuery($query));
    $pesertaas = mysql_fetch_array($database->doQuery($queryas));
    
    $nama = $peserta['nama'];
    $tgllahir = $peserta['tgllahir'];
    $usia = $peserta['usia'];
    $plafond = $peserta['plafond'];
    $tglakad = $peserta['tglakad'];
    $tenor = round($peserta['tenor']/12,0);
    $tglakhir = $peserta['tglakhir'];
    $premi = $peserta['totalpremi'];
    $extrapremi = $peserta['extrapremi'];
    $nopinjaman = $peserta['nopinjaman'];
    $address_value = (isset($peserta['alamatobjek']) ? $peserta['alamatobjek'] : "-");
    
    $identity_value = (isset($peserta['nomorktp']) ? $peserta['nomorktp'] : "-");
    $contact_value = (isset($peserta['notelp']) ? $peserta['notelp'] : "-");
    $place_birth = (isset($peserta['tptlahir']) ? $peserta['tptlahir'] : "-");
    $gender = (isset($peserta['gender']) ? $peserta['gender'] == "L" ? "m" : "f" : "-");
    $sts_marital = (isset($peserta['stsmarital']) ? $peserta['stsmarital'] : "single");
    $pekerjaan = (isset($peserta['pekerjaan']) ? $peserta['pekerjaan'] : "-");
    $medical = (isset($pesertaas['medical']) ? $pesertaas['medical'] : "-");

    // normalize dates to Unix milliseconds (null when not available)
    $birthUnix = ($tgllahir ? (int)(strtotime($tgllahir) * 1000) : null);
    $effectiveUnix = ($tglakad ? (int)(strtotime($tglakad) * 1000) : null);
    $transactionUnix = ($today ? (int)(strtotime($today) * 1000) : null);

    $policyholder_id = "68070439404dc1df6ca1c898";
    $product_sub_id = "68085ce8333af5cea83c6fdc";
    $policyno = "VCLA000125046312";

    $pekerjaan = $peserta['idpolicy'] == 1 ? 'PEGAWAI BANK BENGKULU' : 'STANDARD';
    
    $data = array(
      "policy_no" => $policyno,
      "certificate_no" => $policyno."-".$idpeserta,
      "policyholder_id" => $policyholder_id,
      "policyholder_type" => "company",
      "product_sub_id" => $product_sub_id,
      "amount" => (int)$premi,
      "detail_amount" => array(
        "input_amount" => (int)$plafond
      ),
      "transaction_ref" => $nopinjaman,
      "detail_policy" => array(
        "extra_premi_amount" => (int)$extrapremi,
        "effective_age" => (int)$usia,
        "effective_date" => $effectiveUnix,
        "issued_date" => $transactionUnix,
        "end_date" => ($tglakhir ? (int)(strtotime($tglakhir) * 1000) : null),
        "period" => (int)$tenor
      ),
      "status" => "issued",
      "underwriting_result" => $medical,
      "insured" => array(
        "fullname" => $nama,
        "birth" => $birthUnix,
        "gender" => $gender,
        "place_birth" => $place_birth,
        "marital_status" => $sts_marital,
        "identities" => array(
          array("type" => "id", "value" => $identity_value)
        ),
        "address" => array(
          array("type" => "domicile", "value" => $address_value)
        ),
        "contact" => array(
          array("type" => "phone", "value" => $contact_value)
        ),
        "job" => $pekerjaan,
        "is_client_api" => true
      )
    );

    // API Configuration
    // $api_url = "https://api.client.sandbox.vlife.id/policy";
    // $api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRfaWQiOiI2ODNkMGIzMTczMDUxZDFiYTc3ZGIwMGMiLCJob3N0IjoiKiIsImlhdCI6MTc2NDc1MTQxNH0.TcSX6HJcj7Y85MEZQOAeQTvYNehsbGyeyyUi4YoAUak";
    $api_url = "https://api.client.vlife.id/policy";
    $api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRfaWQiOiI2ODk0MzA1NjBiMzYyMjRlZTY0MTBkNGMiLCJob3N0IjoiKiIsImlhdCI6MTc2NjM5NzM4MH0.HVLjfzosqJPGQl1skacxhUExF6fnA198n8ZwirgJIbU";

    // Convert data to JSON
    $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Initialize cURL
    $ch = curl_init($api_url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'api-key: ' . $api_key,
        'Content-Length: ' . strlen($json_data)
    ));
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    curl_close($ch);

    // Prepare response
    $result = array(
        "success" => ($http_code >= 200 && $http_code < 300),
        "http_code" => $http_code,
        "response" => json_decode($response),
        "sent_data" => $data
    );

    if ($curl_error) {
        $result["error"] = $curl_error;
    }

    // If created successfully, save returned _id into ajkpeserta_callback
    if ($http_code == 201) {
      $resp_arr = json_decode($response, true);
      $policy_api_id = null;
      if (is_array($resp_arr)) {
        if (isset($resp_arr['_id'])) {
          $policy_api_id = $resp_arr['_id'];
        } elseif (isset($resp_arr['data']['_id'])) {
          $policy_api_id = $resp_arr['data']['_id'];
        }
      }

      if ($policy_api_id) {
        // prepare values
        $idas = 2;
        $status_to_save = isset($resp_arr['status']) ? $resp_arr['status'] : 'issued';
        $other_json = json_encode(array('_id' => $policy_api_id), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // basic escaping to avoid query errors
        $idpeserta_esc = addslashes($idpeserta);
        $status_esc = addslashes($status_to_save);
        $other_esc = addslashes($other_json);

        $sql = "INSERT INTO ajkpeserta_callback (idpeserta, idas, status, other, input_time) VALUES ('".$idpeserta_esc."', " . intval($idas) . ", '".$status_esc."', '".$other_esc."', NOW())";
        $sql2 = "UPDATE ajkpeserta SET noasuransi = '".$policyno."-".$idpeserta."' where idpeserta = '".$idpeserta_esc."'";
        $res = $database->doQuery($sql);
        $res2 = $database->doQuery($sql2);
        $insert_id = null;
        if ($res) {
          $insert_id = mysql_insert_id();
          $result['callback_saved'] = array('insert_id' => $insert_id, 'policy_api_id' => $policy_api_id);
        } else {
          $result['callback_saved_error'] = mysql_error();
        }
      } else {
        $result['callback_saved_error'] = 'no_policy_id_in_response';
      }
    }
    return $result;
    