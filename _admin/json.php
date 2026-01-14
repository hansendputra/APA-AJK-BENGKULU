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
    // $tenor = $peserta['tenor'];
    $tenor = round($peserta['tenor']/12,0);
    $tglakhir = $peserta['tglakhir'];
    $premi = $peserta['totalpremi'];
    $extrapremi = $peserta['extrapremi'];
    $nopinjaman = $peserta['nopinjaman'];
    $address_value = (isset($peserta['alamatobjek']) ? $peserta['alamatobjek'] : "-");
    $master_policy_id = "680b39cd58a9785755374570";
    $product_sub_id = "68085ce8333af5cea83c6fdc";
    
    // multiguna 68085ce8333af5cea83c6fdc
    // KPR 68085962333af5cea83c6efa

    $identity_value = (isset($peserta['nomorktp']) ? $peserta['nomorktp'] : "-");
    $contact_value = (isset($peserta['notelp']) ? $peserta['notelp'] : "-");
    $place_birth = (isset($peserta['tptlahir']) ? $peserta['tptlahir'] : "-");
    $gender = (isset($peserta['gender']) ? $peserta['gender'] == "L" ? "m" : "f" : "-");
    $sts_marital = (isset($peserta['stsmarital']) ? $peserta['stsmarital'] : "single");
    $pekerjaan   = (isset($peserta['pekerjaan']) ? $peserta['pekerjaan'] : "-");
    $medical = (isset($pesertaas['medical']) ? $pesertaas['medical'] : "-");

		// normalize dates to Unix milliseconds (null when not available)
		$birthUnix = ($tgllahir ? (int)(strtotime($tgllahir) * 1000) : null);
		$effectiveUnix = ($tglakad ? (int)(strtotime($tglakad) * 1000) : null);
    $transactionUnix = ($today ? (int)(strtotime($today) * 1000) : null);
    $category = $peserta['idpolicy'] == 1 ? "cat_1": "cat_2";

    $data = array(
      "master_policy_id" => $master_policy_id,
      "product_sub_id" => $product_sub_id,
      "insured_data" => array(
        "fullname" => $nama,
        "gender" => $gender,
        "birth" => $birthUnix,
        "place_birth" => $place_birth,
        "marital_status" => $sts_marital,
        "address" => array(
          array("value" => $address_value, "type" => "domicile")
        ),
        "contact" => array(
          array("value" => $contact_value, "type" => "phone_no")
        ),
        "identities" => array(
          array("value" => $identity_value, "type" => "id")
        ),
        "job" => $pekerjaan
      ),
      "policyholder_data" => array(
        "fullname" => $nama,
        "gender" => $gender,
        "birth" => $birthUnix,
        "place_birth" => $place_birth,
        "marital_status" => $sts_marital,
        "address" => array(
          array("value" => $address_value, "type" => "domicile")
        ),
        "contact" => array(
          array("value" => $contact_value, "type" => "phone_no")
        ),
        "identities" => array(
          array("value" => $identity_value, "type" => "id")
        ),
        "job" => $pekerjaan
      ),
      "request_letter" => array("agree" => true),
      "input_amount" => (int)$plafond,
      "amount" => (int)$premi,
      "additional_amount" => (int)$extrapremi,
      "underwriting_result" => $medical,
      "underwriting_result_detail" => array(
        "status" => "approved",
        "notes" => "ok"
      ),
      "installment" => (int)$tenor,
      "period" => 1,
      "effective_date" => $effectiveUnix,
      "transaction_date" => $transactionUnix,
      "transaction_ref" => $nopinjaman,
      "category" => $category
    );

    $policyholder_id = "68070439404dc1df6ca1c898";
    $product_sub_id = "68085ce8333af5cea83c6fdc";
    $policyno = "VCLA000125046312";

    $pekerjaan = $peserta['idpolicy'] == 1 ? 'PEGAWAI BANK BENGKULU' : 'STANDARD';
    
    $data2 = array(
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

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);