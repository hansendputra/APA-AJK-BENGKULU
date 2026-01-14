<?php
  // ----------------------------------------------------------------------------------
  // Original Author Of File : Hansen
  // E-mail :hansendputra@gmail.com
  // @ Copyright 2019
  // ----------------------------------------------------------------------------------
  error_reporting(0);
  session_start();
  require_once('../includes/mpdf/mpdf.php');
  include_once('../koneksi.php');
  include_once('../includes/functions.php');
  
  
  $id = AES::decrypt128CBC($_REQUEST['s'], ENCRYPTION_KEY);

  switch ($_REQUEST['pdf']) {
    case "pengajuanklaimpra":

      $query = "
      SELECT * 
      FROM ajkdocumentclaimmemberpra 
      WHERE idmemberpra = '".$id."'";
      
      $res = mysql_query($query);

      $dokumen = '<table>';
      while($row = mysql_fetch_array($res)){        
        $dok = ucwords(strtolower($row['nmdoc']));
        $dokumen .= '<tr><td>'.++$i.'. '.$dok.'</td></tr>';
      }
      $dokumen .= '</table>';

      $qprapialang = "
      SELECT ajkklaimprapialang.*,ajkclient.companyname as nmclient,ajksignature.ttd,ajksignature.nama as nmpic,ajksignature.jabatan,ajkinsurance.companyname as nmcompanyins,ajkinsurance.address1,ajkinsurance.city
      FROM ajkklaimprapialang 
      INNER JOIN ajkclient ON ajkclient.id = ajkklaimprapialang.idclient
      INNER JOIN ajksignature ON ajksignature.idbroker = ajkklaimprapialang.idbroker
      LEFT JOIN ajkinsurance ON ajkinsurance.id = ajkklaimprapialang.asuransi
      WHERE ajkklaimprapialang.id = '".$id."'";
      
      $rprapialang = mysql_fetch_array(mysql_query($qprapialang));

      $html ='		
        <table width="100%">
          <tr>
            <td width="20%">Nomor</td>
            <td>:</td>
            <td width="48%">'.$rprapialang['nopengajuan'].'</td>
            <td width="30%" style="text-align:right;">Surabaya, '.viewBulanIndo($futoday).'</td>
          </tr>
          <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
            <td></td>
          </tr>        
          <tr>
            <td>Perihal</td>
            <td>:</td>
            <td>Pengajuan Klaim</td>
            <td></td>
          </tr>
        </table>
        
        <br />
        <div>      
          Kepada Yth,<br />
          '.$rprapialang['nmcompanyins'].'<br />
          '.$rprapialang['address1'].'<br />
          '.$rprapialang['city'].'<br />
        </div>

        <div style="float:left;width:100%;">
          <br />
          Dengan hormat,
          <br />
          <p style="justify">Menindaklanjuti laporan klaim awal tanggal '.viewBulanIndo($rprapialang['tglpengajuan']).' serta merunjuk surat '.$rprapialang['nmclient'].' Nomor : '.$rprapialang['nosuratclient'].' tertanggal '.viewBulanIndo($rprapialang['tgllaporas']).'. Bersama ini kami sampaikan pengajuan klaim '.$rprapialang['jenis_klaim'].' dengan data debitur sebagai berikut :</p>

          <table width="100%">
            <tr>
              <td width="29%">Nama Debitur</td>
              <td width="1%">:</td>
              <td width="70%">'.$rprapialang['nama'].'</td>
            </tr>
            <tr>
              <td>Tanggal Lahir</td>
              <td>:</td>
              <td>'.viewBulanIndo($rprapialang['tgllahir']).'</td>
            </tr>
            <tr>
              <td>Tanggal Klaim</td>
              <td>:</td>
              <td>'.viewBulanIndo($rprapialang['tglklaim']).'</td>
            </tr>
            <tr>
              <td>Plafond Kredit</td>
              <td>:</td>
              <td>'.duit($rprapialang['plafond']).'</td>
            </tr>
            <tr>
              <td>Nilai Tuntutan Klaim</td>
              <td>:</td>
              <td>'.duit($rprapialang['nilaiklaimdiajukan']).'</td>
            </tr>
            <tr>
              <td>No Polis</td>
              <td>:</td>
              <td>'.$rprapialang['nopolis'].'</td>
            </tr>
          </table>
          
          <p style="justify">Sebagai syarat pengajuan klaim tersebut, kami sampaikan dokumen pendukung klaim (terlampir) terdiri dari :</p>
          '.$dokumen.'
          <p style="justify">Demikian kami sampaikan untuk menjadi perhatian dan dapat ditindaklanjuti. Atas perhatian dan kerjasamanya, kami haturkan terimakasih.</p>
          
          <p>Hormat kami,</p>
          <img src="'.'../'.$PathSignature.$rprapialang['ttd'].'" height="70" width="140">
          
          <br />
          <p><u>'.$rprapialang['nmpic'].'</u>
          <br />
          '.$rprapialang['jabatan'].'</p>
        </div>';	
      
      $mpdf=new mPDF(); 
      $mpdf->AddPage();
      
      $mpdf->WriteHTML($html);
      // $mpdf->Output();
      $mpdf->Output();
    break;    

    case "suratpengantar":
      $jenis= $_REQUEST['j'];
      if($jenis=='sertifikat'){
        $desc = 'SERTIFIKAT PENJAMINAN JAMKRIDA JAIM';
        $jml = '7';
        $ket = '1. SAFIT';
      }elseif($jenis=='restitusi'){
        $desc = 'SURAT PEMBAYARAN RESTITUSI';
        $jml = '';
        $ket = '';
      }elseif($jenis=='pengajuanklaim'){
        $desc = 'BERKAS KLAIM';
        $jml = '';
        $ket = '';
      }elseif($jenis=='tolakklaim'){
        $desc = 'PENOLAKAN KLAIM';
        $jml = '';
        $ket = '';
      }elseif($jenis=='pembayaranklaim'){
        $desc = 'PERSETUJUAN DAN PEMBAYARAN KLAIM';
        $jml = '';
        $ket = '';
      }elseif($jenis=='kelengkapanklaim'){
        $desc = 'SURAT KELENGKAPAN BERKAS KLAIM';
        $jml = '';
        $ket = '';
      }elseif($jenis=='persetujuanklaim'){
        $desc = 'SURAT PERSETUJUAN KLAIM';
        $jml = '';
        $ket = '';
      }elseif($jenis=='kurangdokumenklaim'){
        $desc = 'SURAT KEKURANGAN BERKAS KLAIM';
        $jml = '';
        $ket = '';
      }

      $html = '
      <style>
      table {
        border-collapse: collapse;
      }
      
      table, th, td {
        border: 1px solid black;
      }
      </style>
      <div style="width:100%">
        <div style="float:left;width:20%;height:100px">
          <img src="../'.$PathPhoto.'small_logo-adonai.png"><br>
        </div>
        <div style="float:left;width:50%">
          PT. Adonai Pialang Asuransi<br>
          Kantor Cabang Surabaya : <br>
          Bumi Mandiri II, lantai 7 ruang 701<br>
          Jl. Panglima Sudirman 66 - 68 Surabaya<br>
          Telp : 031 - 5358031 Fax : 031 - 5358032
        </div>
        <div style="width:25%">
          Surabaya, '.viewBulanIndo($futoday).'
        </div>
      </div>
      
      <div style="text-align:center;font-size:20;"><b><u>SURAT PENGANTAR</u></b></div>
      
      <div style="text-align:center;font-size:12;">No. : {{nosuratpengantar}}</div>
      
      
      
      <p style="margin:0 0 5px">Kepada Yth :<br />
      BANK JATIM {{cabang}}<br />
      {{alamat cabang}}<br />
      {{kota cabang}}<br />
      </p>
      
      <p>Dari : PT. Adonai Pialang Asuransi Cabang Surabaya</p>
      <table width="100%">
        <tr>
          <th width="10%">NO.</th>
          <th width="40%">YANG DIKIRIM</th>
          <th width="10%">JUMLAH</th>
          <th width="40%">KETERANGAN</th>
        </tr>
        <tr style="border-top:-5">
          <td height="300" style="vertical-align:top;text-align:right;">1</td>
          <td style="vertical-align:top;text-align:left;">'.$desc.'</td>
          <td style="vertical-align:top;text-align:center;">'.$jml.'</td>
          <td style="vertical-align:top;text-align:left;">'.$ket.'</td>
        </tr>
      </table>
      <div style="width:100%;margin:20px">
      <div style="float:left; width:50%; padding-left:70px">Tanda Penerimaan</div>
      <div style="float:left; width:30%;">Hormat Kami,<br \><b>PT.Adonai Pialang Asuransi</b></div>
      </div>';
      // echo $html;
      $mpdf=new mPDF('',    // mode - default ''
      'A3',    // format - A4, for example, default ''
      10,     // font size - default 0
      '',    // default font family
      15,    // margin_left
      15,    // margin right
      16,     // margin top
      16,    // margin bottom
      9,     // margin header
      9,     // margin footer
      'L');  // L ); 

      $mpdf->AddPage();
      
      $mpdf->WriteHTML($html);
      $mpdf->Output();
    break;

    case "covernote":
      $query = "
      SELECT ajkpeserta.*,ajkcobroker.name as nmbroker,ajkpolis.produk,ajkinsurance.companyname as nmasuransi,ajkcabang.name as nmcabang,bankdebitnote,bankdebitnotenama,bankdebitnotecabang,bankdebitnoteaccount
      FROM ajkpeserta 
      LEFT JOIN ajkpolis on ajkpolis.id = ajkpeserta.idpolicy
      LEFT JOIN ajkinsurance on ajkinsurance.id = ajkpeserta.asuransi
      LEFT JOIN ajkcabang on ajkcabang.er = ajkpeserta.cabang
      LEFT JOIN ajkcobroker on ajkcobroker.id = ajkpeserta.idbroker
      where idpeserta = '".$id."'";

      $query2 = "
      SELECT ajkpesertaas.*,ajkinsurance.companyname as nmasuransi
      FROM ajkpesertaas
      LEFT JOIN ajkinsurance on ajkinsurance.id = ajkpesertaas.idas
      where idpeserta = '".$id."'";
      
      
      $res = mysql_query($query);
      $res2 = mysql_query($query2);
      $peserta = mysql_fetch_array($res);
      
      $tanggal = viewBulanIndo(date('Y-m-d'));
      $tanggalakhir = date_diff(date_create($peserta['tgllahir']), date_create($peserta['tglakhir']))->format('%y Tahun, %m Bulan, %d Hari');

      $birthDate = new DateTime($peserta['tgllahir']);
      $endDate = new DateTime($peserta['tglakhir']);
      $interval = $birthDate->diff($endDate);
      $years = $interval->y;
      $months = $interval->m;

      if ($months > 6) {
          $years++;
      }

      $usiaakhir = $years . ' Tahun';

      
      $html1 = '
      <style>
        .border {  
          border: 20px solid orange; /* Border around the page */  
          height: 100%; /* Full height */  
          width: 100%; /* Full width */  
          box-sizing: border-box; /* Include border in the elements total width and height */  
        } 
      </style>

      <div class="border">
        <table width="100%" border="0" style="padding-top:30px;padding-bottom:100px">
          <tr>
            <td colspan="4" style="text-align:center;"><img src="images/adonai.png" width="300" height="75"></td>
          </tr>
          <tr>
            <td colspan="4" style="text-align:center;padding-bottom:50px;font-size:40px;font-weight:bold">BUKTI KEPESERTAAN ASURANSI</td>
          </tr>
          <tr>
            <td width="15%"></td>
            <td width="20%" style="font-size:25px;font-weight:bold">Produk</td>
            <td width="1%">:</td>
            <td width="40%" style="font-size:25px;font-weight:bold">{{$produk->produk}}</td>
          </tr>
          <tr>
            <td></td>
            <td style="font-size:25;font-weight:bold">Asuransi</td>
            <td>:</td>
            <td style="font-size:25;font-weight:bold">{{$asuransi->companyname$asuransi->name}}</td>
          </tr>  
          <tr>
            <td></td>
            <td style="font-size:25;font-weight:bold">Nama Debitur</td>
            <td>:</td>
            <td style="font-size:25;font-weight:bold">{{$peserta->nama}}</td>
          </tr>
          <tr>
            <td></td>
            <td style="font-size:25;font-weight:bold">Nomor KTP</td>
            <td>:</td>
            <td style="font-size:25;font-weight:bold">{{$peserta->noktp}}</td>
          </tr>
          <tr>
            <td></td>
            <td style="font-size:25;font-weight:bold">Umur / Tanggal Lahir</td>
            <td>:</td>
            <td style="font-size:25;font-weight:bold">{{$peserta->usia}} tahun / {{$peserta->tgllahir}}</td>
          </tr>
          <tr>
            <td></td>
            <td style="font-size:25;font-weight:bold">Resiko Yang Dijamin</td>
            <td>:</td>
            <td style="font-size:25;font-weight:bold">{{$produk->cover}}</td>
          </tr>    
          <tr style="padding-bottom:50%">
            <td></td>
            <td style="font-size:25px;font-weight:bold">Periode Pertanggungan</td>
            <td>:</td>
            <td style="font-size:25px;font-weight:bold">{{viewBulanIndo($pesertaas->tglawal)viewBulanIndo($pesertaas->tglakhir)}}</td>
          </tr>
          <tr>
            <td></td>
            <td style="font-size:25px;font-weight:bold">Uang Pertanggungan</td>
            <td>:</td>
            <td style="font-size:25px;font-weight:bold">Rp. {{duit($peserta->plafond)}},-</td>
          </tr>
        </table>
      </div>';

      $mpdf=new mPDF(); 

      while($pesertaas = mysql_fetch_array($res2)){   
        $html = '
        
          <div style="float:left;width:20%;height:10px">
            <img src="../'.$PathPhoto.'small_logo-adonai.png">
          </div>
        <br>
        <br>
        <p style="text-align:center;font-weight:bold">NOTA PENUTUPAN ASURANSI</p>
        <p style="text-align:center;font-size:12px">No : N/'.date('ym').'/'.$peserta['idpeserta'].'</p>
        <p style="font-size:12px">Atas permintaan Tertanggung, bersama ini kami konfirmasikan penerimaan penutupan asuransi sbb :</p>
        <table width="100%" style="border-collapse: collapse;font-size:12px;" border="0">
          <tr>
            <td width="40%">NOMOR LOAN</td>
            <td width="1%">:</td>
            <td>'.$peserta['nopinjaman'].'</td>
          </tr>
          <tr>
            <td>NAMA DEBITUR</td>
            <td>:</td>
            <td>'.$peserta['nama'].'</td>
          </tr>
          <tr>
            <td>TANGGAL LAHIR / USIA</td>
            <td>:</td>
            <td>'.viewBulanIndo($peserta['tgllahir']).' / '.$peserta['usia'].' Tahun</td>
          </tr>
          <tr>
            <td>NO REFERENSI</td>
            <td>:</td>
            <td>016/PKS/APA-VAI/II/2025</td>
          </tr>
          <tr>
            <td>JENIS PERTANGGUNGAN</td>
            <td>:</td>
            <td>'.$peserta['produk'].'</td>
          </tr>
          <tr>
            <td>PLAFOND KREDIT</td>
            <td>:</td>
            <td>'.duit($pesertaas['tsi']).'</td>
          </tr>
          <tr>
            <td>PREMI ASURANSI</td>
            <td>:</td>
            <td>'.duit($pesertaas['totalpremi']).'</td>
          </tr>        
          <tr>
            <td>JANGKA WAKTU</td>
            <td>:</td>
            <td>'.$peserta['tenor'].' Bulan</td>
          </tr>        
          <tr>
            <td>PERIODE PERTANGGUNGAN</td>
            <td>:</td>
            <td>'.viewBulanIndo($pesertaas['tglawal']).' s.d '.viewBulanIndo($peserta['tglakhir']).'</td>
          </tr>      
          <tr>
            <td>RESIKO YANG DIJAMIN/JENIS ASURANSI</td>
            <td>:</td>
            <td>'.$pesertaas['keterangan'].'</td>
          </tr>
          <tr>
            <td>PENANGGUNG</td>
            <td>:</td>
            <td>'.$pesertaas['nmasuransi'].'</td>
          </tr>
        </table>
        <p style="font-size:12px">Masa Berlaku Cover Note ini akan berakhir bila polis sudah diterbitkan</p>
        <p style="font-size:12px;font-weight:bold">Pembayaran Premi Ke Rekening No. '.$peserta['bankdebitnoteaccount'].'<br>'.$peserta['bankdebitnote'].' '.$peserta['bankdebitnotecabang'].'<br>Atas Nama '.$peserta['bankdebitnotenama'].'</p>
        <p style="font-size:12px">Bengkulu, '.$tanggal.'<br>Untuk dan Atas Nama<br><b>'.$peserta['nmbroker'].'</b></p>
        <p style="font-size:12px;font-weight:bold">Kartu ini merupakan bagian yang tidak terpisahkan dari Polis Induk Asuransi/ Sertifikat Asuransi / Perjanjian Kerja sama</p>
        <p style="font-size:12px;font-weight:bold">*Surat elektronik ini ditarik secara otomatis dari system sehingga tidak memerlukan tanda tangan</p>';
                
        $mpdf->AddPage();      
        $mpdf->WriteHTML($html);
      } 

      $html2 = '
      <p style="text-align:center;font-weight:bold">NOTA ASURANSI/PENJAMINAN PEMBIAYAAN '.strtoupper($peserta['produk']).'</p>
      <table width="100%" style="border-collapse: collapse;font-size:12px;" border="0">
        <tr>
          <td width="40%">NOMOR LOAN</td>
          <td width="1%">:</td>
          <td>'.$peserta['nopinjaman'].'</td>
        </tr>
        <tr>
          <td>NOMOR KARTU PESERTA ASURANSI</td>
          <td>:</td>
          <td>'.$peserta['idpeserta'].'</td>
        </tr>
        <tr>
          <td>CABANG</td>
          <td>:</td>
          <td>'.$peserta['nmcabang'].'</td>
        </tr>        
        <tr>
          <td>NAMA NASABAH (SESUAI KTP)</td>
          <td>:</td>
          <td>'.$peserta['nama'].'</td>
        </tr>
        <tr>
          <td>NO IDENTITAS (KTP)</td>
          <td>:</td>
          <td>'.$peserta['nomorktp'].'</td>
        </tr>        
        <tr>
          <td>TANGGAL LAHIR / USIA</td>
          <td>:</td>
          <td>'.viewBulanIndo($peserta['tgllahir']).' / '.$peserta['usia'].' Tahun</td>
        </tr>
        <tr>
          <td>USIA SAAT AKAD</td>
          <td>:</td>
          <td>'.$peserta['usia'].' Tahun ('.date_diff(date_create($peserta['tgllahir']), date_create($peserta['tglakad']))->format('%y Tahun, %m Bulan, %d Hari').')</td>
        </tr>
        <tr>
          <td>USIA SAAT AKHIR ASURANSI</td>
          <td>:</td>
          <td>'.$usiaakhir.' ('.$tanggalakhir.')</td>
        </tr>        
        <tr>
          <td>PERIODE PERTANGGUNGAN</td>
          <td>:</td>
          <td>'.viewBulanIndo($peserta['tglakad']).' s.d '.viewBulanIndo($peserta['tglakhir']).'</td>
        </tr>       
        <tr>
          <td>JANGKA WAKTU (BULAN)</td>
          <td>:</td>
          <td>'.$peserta['tenor'].' Bulan</td>
        </tr>  
        <tr>
          <td>PLAFOND KREDIT</td>
          <td>:</td>
          <td>'.duit($peserta['plafond']).'</td>
        </tr>               
        <tr>
          <td>RATE</td>
          <td>:</td>
          <td>'.round($peserta['premirate'],2).'‰</td>
        </tr>
        <tr>
          <td>PREMI ASURANSI</td>
          <td>:</td>
          <td>'.duit($peserta['totalpremi']).'</td>
        </tr>     
        <tr>
          <td>PRODUK</td>
          <td>:</td>
          <td>'.$peserta['produk'].'</td>
        </tr>                   
        <tr>
          <td>MANFAAT ASURANSI</td>
          <td>:</td>
          <td>ASURANSI JIWA</td>
        </tr>    
        <tr>
          <td>STATUS DATA</td>
          <td>:</td>
          <td>Free Cover (CAC)</td>
        </tr>
      </table>
      <p style="font-size:12px;">Keterangan :</p>
      <p style="font-size:12px;font-weight:bold">Nilai Premi tersebut diatas harap dibayarkan ke rekening atas nama '.$peserta['bankdebitnotenama'].' No. Rek : '.$peserta['bankdebitnoteaccount'].'</p>
      <p style="font-size:12px;font-weight:bold">*Surat elektronik ini ditarik secara otomatis dari system sehingga tidak memerlukan tanda tangan</p>
      ';
     
      $mpdf->AddPage();
      $mpdf->WriteHTML($html2);
      $mpdf->Output();
    break;

    case "feebase":
      $qdn = "SELECT nomordebitnote,tgldebitnote,ajkclient.companyname,ajkpolis.produk,ajkcabang.name as nmcabang,COUNT(ajkpeserta.nama) AS jData,address1
              FROM ajkdebitnote 
              INNER JOIN ajkclient on ajkclient.id = ajkdebitnote.idclient
              INNER JOIN ajkpolis on ajkpolis.id = ajkdebitnote.idproduk 
              INNER JOIN ajkcabang on ajkcabang.er = ajkdebitnote.idcabang
              INNER JOIN ajkpeserta ON ajkpeserta.iddn = ajkdebitnote.id
              WHERE ajkdebitnote.id=".$id."
              GROUP BY nomordebitnote,tgldebitnote,ajkclient.companyname,ajkpolis.produk,ajkcabang.name";
      
      $qpeserta ="SELECT ajkpeserta.nopinjaman,idpeserta,nama,tgllahir,usia,plafond,tglakad,tenor,tglakhir,totalpremi
                  FROM ajkpeserta
                  WHERE iddn = ".$id;

      $peserta = mysql_query($qpeserta);
      $dn = mysql_fetch_array(mysql_query($qdn));
      $company = $dn['companyname'];
      $produk = $dn['produk'];
      $nocn = str_replace('DN','CN',$dn['nomordebitnote']);
      $tglcn = $dn['tgldebitnote'];
      $cabang = $dn['nmcabang'];
      $jmldata = $dn['jData'];
      $address = $dn['address1'];
      $i = 0;
      $tFeebase = 0;

      while($row = mysql_fetch_array($peserta)){                
        $feebase = $row['totalpremi'];
        $pesertah .= '
        <tr>
          <td style="text-align:center;font-size:12px">'.++$i.'</td>
          <td style="font-size:12px">'.$row['nopinjaman'].'</td>
          <td style="font-size:12px">'.$row['idpeserta'].'</td>
          <td style="font-size:12px">'.$row['nama'].'</td>
          <td style="text-align:center;font-size:12px">'.$row['tgllahir'].'</td>
          <td style="text-align:center;font-size:12px">'.$row['usia'].'</td>
          <td style="text-align:right;font-size:12px">'.duit($row['plafond']).'</td>
          <td style="text-align:center;font-size:12px">'._convertDate($row['tglakad']).'</td>
          <td style="text-align:center;font-size:12px">'.$row['tenor'].'</td>
          <td style="text-align:center;font-size:12px">'._convertDate($row['tglakhir']).'</td>
          <td style="text-align:right;font-size:12px">'.duit($feebase).'</td>
        </tr>';
        $tFeebase += $feebase;
      }


      $pph = $tFeebase/100 * 2;
      $ppn = $tFeebase/100 * 11;
      $html = '
        <div style="text-align:center;padding-bottom:-20px">
          <img src="../'.$PathPhoto.'small_logo-adonai.png" width="320" height="60">
        </div>
        <br>
        <br>
        <br>
        <table style="border-collapse: collapse; border: 1px solid black;" width="100%">
          <tr>
            <td style="text-align:center;font-weight:bold;padding-top:10px;padding-bottom:10px;font-size:20px"><u>CREDITNOTE</u><br>'.$nocn.'</td>
          </tr>
        </table>
        <table width="100%" style="border-collapse: collapse; border: 1px solid black;font-weight:bold;font-size:12px;" >          
          <tr>
            <td width="10%" style="padding-top:10px;padding-bottom:10px;"><u>Kepada</u><br><i>To</i></td>
            <td width="1%">:</td>
            <td width="40%">'.$company.'<br>'.$address.'</td>
            <td width="10%"></td>
            <td width="10%"><u>Tanggal</u><br><i>Date</i></td>
            <td width="1%">:</td>
            <td width="10%">'.viewBulanIndo(date('Y-m-d')).'</td>
          </tr>
          <tr>
            <td></td>
          </tr>
        </table>
        <table width="100%" border="0" style="font-size:12px;border: 1px solid black;font-weight:bold;" >
          <tr>
            <td rowspan="2" width="45%" style="vertical-align: text-top;">
              <table style="border-right: 1px solid black;">
                <tr >
                  <td style="padding-bottom:10px">Nama Produk</td>
                  <td style="padding-bottom:10px">:</td>
                  <td style="padding-bottom:10px">'.$produk.'</td>
                </tr>
                <tr>
                  <td style="padding-bottom:10px">Tanggal Nota Kredit</td>
                  <td style="padding-bottom:10px">:</td>
                  <td style="padding-bottom:10px">'.viewBulanIndo($tglcn).'</td>
                </tr>
                <tr>
                  <td>Jumlah Debitur</td>
                  <td>:</td>
                  <td>'.$jmldata.'</td>
                </tr>
                <tr>
                  <td colspan="3" style="padding-top:160px;padding-bottom:40px">
                    <table style="border-collapse: collapse;font-weight:bold" width="100%">
                      <tr>
                        <td colspan="2">Pembayaran dapat dilakukan pada account berikut:</td>
                      </tr>
                      <tr>
                        <td style="padding-top:20px">Nama Bank</td>
                        <td style="padding-top:20px">BPT Bengkulu</td>
                      </tr>
                      <tr>
                        <td>Nama Account</td>
                        <td>PT ADONAI PIALANG ASURANSI</td>
                      </tr>
                      <tr>
                        <td>Nomor Rekening</td>
                        <td>008-0109-000-963</td>
                      </tr>
                      <tr>
                        <td>Cabang</td>
                        <td>JAKARTA</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
            <td style="vertical-align: text-top;padding-left:-10px">
              <table width="100%" style="border-bottom: 1px solid black;">
                  <tr>
                    <td rowspan="4" style="vertical-align: text-top;" width="10%"><u>Keterangan</u><br><i>Particulars</i></td>
                    <td width="50%" style="padding-bottom:10px">Fee Based / Komisi</td>
                    <td width="1%" style="padding-bottom:10px">Rp</td>
                    <td width="10%" style="text-align:right;padding-bottom:10px">'.duitkoma($tFeebase).'</td>
                  </tr>
                  <tr>
                    <td style="padding-bottom:10px">PPN</td>
                    <td style="padding-bottom:10px">Rp</td>
                    <td style="text-align:right;padding-bottom:10px">'.duitkoma($ppn).'</td>
                  </tr>
                  <tr>
                    <td>PPH23</td>
                    <td>Rp</td>
                    <td style="text-align:right;"><u>('.duitkoma($pph).')</u></td>
                  </tr>
                  <tr>
                    <td style="padding-bottom:10px">Jumlah Tagihan</td>
                    <td style="padding-bottom:10px">Rp</td>
                    <td style="text-align:right;padding-bottom:10px">'.duitkoma($tFeebase+$ppn-$pph).'</td>
                  </tr>
                  <tr>
                    <td><u>Terbilang</u><br><i>Say</i></td>
                    <td colspan="3" style="vertical-align: text-top;">Sembilan Puluh Enam Ribu Empat Ratus Tujuh Koma Enam Puluh Rupiah</td>
                  </tr>
                  <tr>
                    <td colspan="4" style="padding-top:40px;padding-bottom:40px">Telah diterima dari PT. ADONAI PIALANG ASURANSI untuk pembayaran komisi atas asuransi tersebut di atas.</td>
                  </tr>                  
              </table>
            </td>
          </tr>
          <tr>
            <td style="text-align:center">
              <table width="100%">
                <tr>
                  <td style="text-align:center;padding-bottom:60px">PT. ADONAI PIALANG ASURANSI<td>
                </tr>
                <tr>
                  <td style="text-align:center"><u>Ing Sriwati</u><br><i>General Manager</i><td>
                </tr>
              </table>
            </td>
          </tr>
        </table>';

      $html2 = '
      <style>
      .txt14{
        font-size:14px
      }
      </style>
      <div>
          <img src="../'.$PathPhoto.'small_logo-adonai.png" width="200" height="50">
      </div>
      <table width="100%" style="border-collapse: collapse;" border="0">
        <tr>
          <td colspan="5" style="text-align:center;font-weight:bold;padding-bottom:20px;font-size:20px">Daftar Peserta Feebase</td>
        </tr>
        <tr>
          <td width="10%" class="txt14">Perusahaan</td>
          <td width="30%" class="txt14">: '.$company.'</td>
          <td width="25%"></td>
          <td width="15%" class="txt14">Tanggal Creditnote</td>
          <td width="20%" class="txt14">: '.$tglcn.'</td>
        </tr>
        <tr>
          <td class="txt14">Produk</td>
          <td class="txt14">: '.$produk.'</td>
          <td></td>
          <td class="txt14">Nota Kredit</td>
          <td class="txt14">: '.$nocn.'</td>
        </tr>
        <tr>
          <td class="txt14">Cabang</td>
          <td class="txt14">: '.$cabang.'</td>
          <td colspan="3"></>
        </tr>
      </table>
      <br>
      <table width="100%" border="1" style="border-collapse: collapse;">
        <tr style="background-color:rgb(233, 233, 233);">
          <td style="text-align:center" class="txt14">No</td>
          <td style="text-align:center" class="txt14">No Pinjaman</td>
          <td style="text-align:center" class="txt14">Id Peserta</td>
          <td style="text-align:center" class="txt14">Nama</td>
          <td style="text-align:center" class="txt14">Tgl. Lahir</td>
          <td style="text-align:center" class="txt14">Usia</td>
          <td style="text-align:center" class="txt14">Plafond</td>
          <td style="text-align:center" class="txt14">Tgl. Mulai</td>
          <td style="text-align:center" class="txt14">Tenor</td>
          <td style="text-align:center" class="txt14">Tgl. Akhir</td>
          <td style="text-align:center" class="txt14">Feebase</td>
        </tr>
        '.$pesertah.'
        <tr style="background-color:rgb(233, 233, 233)">
          <td colspan="10" style="font-size:12px"><b>Total Feebase</b></td>
          <td style="text-align:right;font-size:12px"><b>'.duit($tFeebase).'</b></td>
        </tr>        
      </table>
      ';
      

      $mpdf=new mPDF(); 
      $mpdf->AddPageByArray([
        'orientation' => 'P',
        'margin-left' => 5,
        'margin-right' => 5,
      ]);
      $mpdf->WriteHTML($html);
      $mpdf->AddPage('L');
      $mpdf->WriteHTML($html2);
      $mpdf->Output();
    break; 
    
    case "spajkvictoria":
      $q = "SELECT ajkpeserta.*,ajkcabang.name as nmcabang
      FROM ajkpeserta 
      INNER JOIN ajkcabang ON ajkcabang.er = ajkpeserta.cabang
      WHERE idpeserta = '".$id."'";
      $peserta = mysql_fetch_array(mysql_query($q));
      $gender = $peserta['gender'] == 'L' ? 'Pria': 'Wanita';
      $nmcabang = $peserta['nmcabang'];
      $tenor = $peserta['tenor'];

      $qanswer = "SELECT * FROM ajkformpesertaanswer WHERE idpeserta = '".$id."'";
      $rbb = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 7'));
      $rtb = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 8'));
      $ranswer1 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 1'));
      $ranswer2 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 2'));
      $ranswer3 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 3'));
      $ranswer41 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 41'));
      $ranswer42 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 42'));
      $ranswer43 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 43'));
      $ranswer44 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 44'));
      $ranswer45 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 45'));
      $ranswer46 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 46'));
      $ranswer5 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 5'));
      $ranswer6 = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 6'));
      $rketerangan = mysql_fetch_array(mysql_query($qanswer.' and idquestion = 9'));
      $bb = $rbb['answer'];
      $tb = $rtb['answer'];
      $keterangan = $rketerangan['answer'];
      $answer1 = $ranswer1['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer2 = $ranswer2['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer3 = $ranswer3['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer41 = $ranswer41['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer42 = $ranswer42['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer43 = $ranswer43['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer44 = $ranswer44['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer45 = $ranswer45['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer46 = $ranswer46['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer5 = $ranswer5['answer'] == 'T' ? 'Ya' : 'Tidak';
      $answer6 = $ranswer6['answer'] == 'T' ? 'Ya' : 'Tidak';

      $chars = preg_split('//u', $peserta['nama'], null, PREG_SPLIT_NO_EMPTY);
      $jumlah_kotak = 17;
            
      $html = '<!DOCTYPE html>
      <html lang="id">
      <head>
      <meta charset="UTF-8" />
      <style>

      </style>
      </head>
      <body>        
        <table width="100%" style="padding-bottom:-10px">
        <tr>
        <td><img src="../'.$PathPhoto.'victorialife.png" width="250" height="35" style="margin-left:-10px;"></td>
        <td style="font-weight:bold;font-size:17px;text-align:right">SURAT PERMOHONAN ASURANSI JIWA KREDIT</td>
        </tr>
        </table>
        <p style="font-size:11px;padding-bottom:-20px">Graha BIP Lantai 3A<br>Jl Gatot Subroto Kavling 23<br>Jakarta Selatan 12930<br>Tel. 021-50992930 | Fax. 021-50992931</p>
        <hr style="border: 3px solid black;">
        <table width="100%" style="padding-top:-10px;font-size:12px" border="0">
          <tr>
            <td colspan="6" style="color:white;background-color:#bf0000;padding-left:20px;font-size:12px"><b>l. DATA CALON TERTANGGUNG/PESERTA</b> <i>(Lampirkan fotokopi identitas diri yang masih berlaku)</i></td>
          </tr>
          <tr>
            <td width="25%">Nama Lengkap</td>
            <td width="1%">:</td>
            <td colspan="4">'.$peserta['nama'].'</td>
          </tr>
          <tr style="">
            <td>Tempat/Tanggal Lahir</td>
            <td>:</td>
            <td colspan="2">'.$peserta['tptlahir'].','._convertDate($peserta['tgllahir']).'</td>
            <td colspan="2">Usia : '.$peserta['usia'].' Tahun</td>
          </tr> 
          <tr>
            <td width="20%">No. KTP/Paspor</td>
            <td width="1%">:</td>
            <td colspan="2">'.$peserta['nomorktp'].'</td>
            <td colspan="2">Jenis Kelamin : '.$gender.'</td>
          </tr>
          <tr>
            <td width="20%">Alamat Rumah</td>
            <td width="1%">:</td>
            <td colspan="4">'.$peserta['alamatobjek'].'</td>
          </tr>
          <tr>
            <td width="20%">Status</td>
            <td width="1%">:</td>
            <td colspan="4">'.$peserta['stsmarital'].'</td>
          </tr>
          <tr>
            <td width="20%">No. Telepon</td>
            <td width="1%">:</td>
            <td colspan="2">'.$peserta['notelp'].'</td>
            <td colspan="2">Email : '.$peserta['email'].'</td>
          </tr>
          <tr>
            <td width="20%">Pekerjaan</td>
            <td width="1%">:</td>
            <td colspan="2">'.$peserta['pekerjaan'].'</td>
            <td colspan="2">Jabatan : '.$peserta['jabatan'].'</td>
          </tr>
          <tr>
            <td colspan="6" style="color:white;background-color:#bf0000;padding-left:20px;font-size:12px"><b>ll. DATA PERTANGGUNGAN</i></td>
          </tr>
          <tr>
            <td width="20%">No. Polis</td>
            <td width="1%">:</td>
            <td colspan="4"></td>
          </tr>
          <tr>
            <td width="20%">Nama Pemegang Polis</td>
            <td width="1%">:</td>
            <td colspan="4">'.$peserta['nama'].'</td>
          </tr>
          <tr>
            <td width="20%">Masa Asuransi</td>
            <td width="1%">:</td>
            <td colspan="2">'.$tenor.' Bulan</td>
            <td colspan="2">Mulai : '._convertDate($peserta['tglakad']).' s.d '._convertDate($peserta['tglakhir']).'</td>
          </tr>
          <tr>
            <td width="20%">Uang Pertanggungan Awal</td>
            <td width="1%">:</td>
            <td colspan="2">'.duit($peserta['plafond']).',- <i style="color:red;">(sebesar pinjaman awal)</i></td>
            <td colspan="2">Suku Bunga Pinjaman : '.$peserta['sukubunga'].'%</td>
          </tr>
          <tr>
            <td width="20%">Jenis Pertanggungan</td>
            <td width="1%">:</td>
            <td colspan="4">'.$peserta['jenispertanggungan'].'</td>
          </tr>
          <tr>
            <td colspan="6" style="color:white;background-color:#bf0000;padding-left:20px;font-size:12px"><b>lll. PERNYATAAN KESEHATAN CALON TERTANGGUNG/PESERTA</i></td>
          </tr>
          <tr>
            <td>Berat Badan '.$bb.' kg</td>
            <td colspan="5">Tinggi Badan '.$tb.' cm</td>
          </tr>          
          <tr>
            <td colspan="6">
              <table style="border-collapse: collapse;">                
                <tr>
                  <td style="vertical-align:top">1.</td>
                  <td>Apakah anda dalam keadaan sehat ?</td>
                  <td style="text-align:center;vertical-align:top">'.$answer1.'</td>
                </tr>
                <tr>
                  <td style="vertical-align:top">2.</td>
                  <td>Dalam 5 tahun terakhir, termasuk hari ini, apakah anda pernah atau sedang dalam perawatan dokter atau menerima pengobatan atau mengalami pembedahan atau dirawat di rumah sakit?</td>
                  <td style="text-align:center;vertical-align:top">'.$answer2.'</td>
                </tr>
                <tr>
                  <td style="vertical-align:top">3.</td>
                  <td>Apakah ada anggota keluarga (ayah/ibu/adik/kakak) yang menderita penyakit jantung, kanker, stroke, diabetes mellitus sebelum mencapai usia 60 tahun?</td>
                  <td style="text-align:center;vertical-align:top">'.$answer3.'</td>
                </tr>
                <tr>
                  <td style="vertical-align:top">4.</td>
                  <td colspan="2">Apakah Anda pernah menderita sakit atau sedang dalam perawatan dokter atau menerima pengobatan atau mempunyai keluhan/gejala-gejala atas penyakit-penyakit dibawah ini:</td>
                </tr>
                <tr>
                  <td></td>
                  <td>a. Jantung, Stroke atau Gangguan Pembuluh Darah Otak, Nyeri Dada atau Penyakit Jantung, Tekanan Darah Tinggi</td>
                  <td style="text-align:center">'.$answer41.'</td>
                </tr>
                <tr>
                  <td></td>
                  <td>b. Gagal Ginjal, Hati (Liver), Lambung atau Usus (Saluran Cerna), Paru-paru atau Saluran Pernafasan</td>
                  <td style="text-align:center">'.$answer42.'</td>
                </tr>
                <tr>
                  <td></td>
                  <td>c. Diabetes Mellitus (Kencing Manis), Kelumpuhan atau Paralisis, Kanker atau Tumor, AIDS</td>
                  <td style="text-align:center">'.$answer43.'</td>
                </tr>
                <tr>
                  <td></td>
                  <td>d. Vertigo, Epilepsi, Kejang Demam, Nyeri Sendi (Rheumatism)</td>
                  <td style="text-align:center">'.$answer44.'</td>
                </tr>
                <tr>
                  <td></td>
                  <td>e. Apakah Anda merokok? Bila YA, berapa batang sehari?</td>
                  <td style="text-align:center">'.$answer45.'</td>
                </tr>
                <tr>
                  <td></td>
                  <td>f. Khusus wanita, apakah Anda dalam keadaan hamil? Jika Ya, berapa usia kandungan?</td>
                  <td style="text-align:center">'.$answer46.'</td>
                </tr>
                <tr>
                  <td style="vertical-align:top">5.</td>
                  <td>Apakah Anda melakukan/pernah melakukan olahraga yang berisiko tinggi (mendaki gunung, layang gantung, olahraga bermotor, menyelam, dll) atau melakukan penerbangan selain sebagai penumpang pesawat komersial yang berjadwal?</td>
                  <td style="text-align:center;vertical-align:top">'.$answer5.'</td>
                </tr>
                <tr>
                  <td style="vertical-align:top">6.</td>
                  <td>Pernahkah perusahaan asuransi lain menolak atau menerima permohonan Anda dengan kondisi khusus atau selama 3 tahun terakhir pernah mengajukan klaim pada asuransi manapun?</td>
                  <td style="text-align:center;vertical-align:top">'.$answer6.'</td>
                </tr>             
              </table>
            </td>
          </tr>
          <tr>
            <td colspan="6" style="color:white;background-color:#bf0000;padding-left:20px;font-size:12px"><b>Beri penjelasan Nama Penyakit, Tanggal Pertama kali di Diagnosa, Lama menderita penyakit, Nama & alamat dokter yang merawat atau informasi lain yang relevan dan Kondisi saat ini apabila terdapat jawaban "Ya" dari pertanyaan diatas.</i></td>
          </tr>
          <tr>
            <td colspan="6" style="border: 1px solid black;vertical-align:top;height:100px">'.$keterangan.'</td>
          </tr>
        </table>
        </body>
        </html>';
      
      $html2 = '
      <table width="100%" style="font-size:12px" border="0">
        <tr>
          <td colspan="3" style="color:white;background-color:#bf0000;font-size:12px;text-align:center"><b>PERNYATAAN dan KUASA CALON TERTANGGUNG/PESERTA</b><br><i>(Harap dibaca dengan teliti sebelum menandatangani Formulir ini ini)</i></td>
        </tr>
        <tr>
          <td colspan="3">SAYA yang bertanda tangan dibawah ini, sebagai Pemegang Polis/Tertanggung/Peserta menyatakan bahwa :</td>
        </tr>
        <tr>
          <td width="3%" style="vertical-align: top;">1.</td>
          <td colspan="2" style="text-align: justify;">Kondisi Kesehatan Yang Sudah Ada Sebelumnya berarti segala penyakit, cidera atau kondisi yang secara langsung atau tidak langsung menyebabkan Peserta menjalani Rawat Inap dalam 12 (dua belas) bulan sebelum tanggal berlakunya asuransi atas namanya, atau menyebabkan Peserta didiagnosa, sedang dalam pengawasan Dokter atau mendapatkan pengobatan medis, diberikan resep pengobatan, atau berusaha mendapatkan nasihat medis atau berkonsultasi, sebelum tanggal berlakunya asuransi atas namanya</td>
        </tr>
        <tr>
          <td style="vertical-align: top;">2.</td>
          <td colspan="2" style="text-align: justify;">SAYA menyatakan dan menjamin bahwa semua informasi, jawaban, pernyataan dan/atau keterangan yang SAYA berikan dalam SPAJ ini, serta setiap formulir dan dokumen lainnya yang disyaratkan oleh PT Victoria Alife Indonesia sebagai bagian dari pengajuan permohonan asuransi adalah lengkap, benar, akurat, terkini dan sesuai dengan kenyataan, dan tidak ada informasi, jawaban, pernyataan dan/atau keterangan yang SAYA sembunyikan, baik dengan sengaja maupun tidak sengaja. Apabila informasi, jawaban dan/atau keterangan tersebut ternyata tidak lengkap, tidak benar, tidak akurat, tidak terkini dan/atau tidak sesuai dengan kenyataan, atau terdapat informasi, jawaban dan/atau keterangan yang SAYA sembunyikan, SAYA SEPAKAT DAN MENYETUJUI bahwa PT Victoria Alife Indonesia dapat melakukan hal-hal berikut ini:</td>
        </tr>
        <tr>
          <td width="3%"></td>
          <td width="3%" style="vertical-align: top;">a.</td>
          <td style="text-align: justify;">Menolak setiap klaim yang diajukan dan tidak membayarkan seluruh atau sebagian Manfaat Asuransi;</td>
        </tr>
        <tr>
          <td></td>
          <td style="vertical-align: top;">b.</td>
          <td style="text-align: justify;">Membatalkan Polis (baik secara keseluruhan atau hanya terbatas pada Pertanggungan Tambahan);</td>
        </tr>
        <tr>
          <td></td>
          <td style="vertical-align: top;">c.</td>
          <td style="text-align: justify;">Mengakhiri Polis (baik secara keseluruhan atau hanya terbatas pada Pertanggungan Tambahan) tanpa kewajiban untuk mengembalikan Premi dan/atau Biaya Asuransi;</td>
        </tr>
        <tr>
          <td></td>
          <td style="vertical-align: top;">d.</td>
          <td style="text-align: justify;">Melakukan penilaian ulang risiko (re-underwriting ), dan menambahkan syarat dan ketentuan tambahan ke dalam Polis (baik Polis Dasar dan/atau setiap Pertanggungan Tambahan), termasuk menambahkan risiko yang dikecualikan dari Polis, menyesuaikan uang pertanggungan, dan/atau menyesuaikan jumlah Premi atau Biaya Asuransi yang harus dibayar;</td>
        </tr>
        <tr>
          <td></td>
          <td style="vertical-align: top;">e.</td>
          <td style="text-align: justify;">Menagih kekurangan Premi dan/atau Biaya Asuransi jika, sebagai hasil dari penilaian ulang risiko (re-underwriting ), jumlah Premi dan/atau Biaya Asuransi yang harus dibayar lebih besar daripada yang tercantum dalam Polis. PT Victoria Alife Indonesia juga berhak untuk melakukan perjumpaan (set-off ) atas kekurangan pembayaran tersebut dengan jumlah premi yang ada dalam Polis SAYA dan/atau Manfaat Asuransi yang akan dibayarkan; dan SAYA wajib membayarkan kekurangan Premi dan/atau Biaya Asuransi (jika ada); dan/atau</td>
        </tr>
        <tr>
          <td></td>
          <td style="vertical-align: top;">f.</td>
          <td style="text-align: justify;">Menagih kembali kepada SAYA atas semua Manfaat Asuransi yang telah dibayarkan dan SAYA wajib membayarkan kembali semua Manfaat Asuransi yang telah dibayarkan tersebut (jika ada).</td>
        </tr>        
        <tr>
          <td style="vertical-align: top;">3.</td>
          <td colspan="2" style="text-align: justify;">SAYA memberikan kuasa kepada Dokter, Klinik, Rumah Sakit, Perusahaan Asuransi, Institusi yang berwenang dan Organisasi lain ataupun perorangan untuk memberikan keterangan mengenai riwayat kesehatan yang diperlukan oleh Penanggung.</td>
        </tr>
        <tr>
          <td style="vertical-align: top;">4.</td>
          <td colspan="2" style="text-align: justify;">Fotokopi dari surat kuasa ini sah dan berlaku seperti dokumen asli.</td>
        </tr>
        <tr>
          <td style="vertical-align: top;">5.</td>
          <td colspan="2" style="text-align: justify;">Dengan mengesampingkan pasal 1813 KUH Perdata, pemberian kuasa ini tidak dapat dicabut/dibatalkan dan tetap berlaku meskipun SAYA meninggal dunia. Faks atau Fotokopi dari pernyataan dan pemberian kuasa ini mempunyai kekuatan hukum yang sama kuat dan sah seperti aslinya.</td>
        </tr>
        <tr>
          <td style="vertical-align: top;">6.</td>
          <td colspan="2" style="text-align: justify;">SAYA memberikan kuasa kepada pihak Bank untuk memberikan fotokopi Perjanjian Pinjaman kepada Penanggung dan SAYA telah membaca dan mengerti produk ini. Oleh karena itu, SAYA memahami seluruh manfaat yang ada pada produk ini.</td>
        </tr>
        <tr>
          <td style="vertical-align: top;">7.</td>
          <td colspan="2" style="text-align: justify;">SAYA memahami dan telah mendapat penjelasan mengenai perhitungan besarnya pengembalian premi apabila dilakukan pelunasan kredit atau pinjaman sebelum Perjanjian Kredit berakhir. SAYA telah mendapatkan informasi bahwa produk Asuransi Jiwa ini telah memperoleh surat penegasan dan/atau persetujuan dari Bank Indonesia dan otoritas terkait lainnya.</td>
        </tr>
        <tr>
          <td style="vertical-align: top;">8.</td>
          <td colspan="2" style="text-align: justify;">SAYA dengan ini memberi ijin kepada PT Victoria Alife Indonesia untuk menggunakan atau memberikan informasi atau keterangan mengenai SAYA yang tersedia, diperoleh atau disimpan oleh PT Victoria Alife Indonesia termasuk tetapi tidak terbatas kepada data informasi terkait nama, alamat, tanggal kelahiran, no.telpon, alamat e-mail dan data-data terkait lainnya untuk kepentingan penawaran produk-produk asuransi lainnya milik PT Victoria Alife Indonesia, produk-produk rekanan/mitra usaha PT Victoria Alife Indonesia maupun Group Victoria dan dengan ditandatanganinya SPAJ ini oleh SAYA adalah merupakan bukti tertulis pemberian ijin oleh SAYA.</td>
        </tr>
      </table>
      <p style="font-size:12px">Tempat.............................................., Tanggal '.date('d-m-Y').'</p>
      <table width="100%" style="font-size:12px" border="0">
        <tr>
          <td width="50%" style="text-align:center">
            <table style="border-collapse: collapse;width:500px" border="1">
              <tr>
                <td style="text-align:center">Calon Tertanggung/Peserta</td>
              </tr>
              <tr>
                <td style="padding:50px"></td>
              </tr>
              <tr>
                <td style="text-align:center">( tandatangan dan nama jelas)</td>
              </tr>
            </table>
          </td>
          <td>
            <table>
              <tr>
                <td style="padding:10px">Nama Petugas Bank</td>
                <td style="padding:10px">:</td>
                <td style="padding:10px">.............................................</td>
              </tr>
              <tr>
                <td style="padding:10px">Nama Cabang</td>
                <td style="padding:10px">:</td>
                <td style="padding:10px">'.$nmcabang.'</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>      
      <p style="text-align:center;color:red;font-size:12px">Formulir ini berlaku 60 (enam puluh) hari sejak ditandatangani oleh Calon Tertanggung/Peserta</p>';

      $mpdf = new Mpdf();
      $mpdf->AddPageByArray([
        'margin-left' => 8,
        'margin-right' => 8,
        'margin-top' => 8,
        'margin-bottom' => 8,
      ]);

      $mpdf->WriteHTML($html);

      $mpdf->AddPageByArray([
        'margin-left' => 8,
        'margin-right' => 8,
        'margin-top' => 8,
        'margin-bottom' => 8,
      ]);
      $mpdf->WriteHTML($html2);
      $mpdf->Output('form_spajk_'.$peserta['nama'].'.pdf', 'D');
    break;

    case "sertifikatvictoria":
      $q = "SELECT ajkpeserta.*,ajkcabang.name as nmcabang,DATE_ADD(tglakhir, INTERVAL -1 DAY)as tglakhiras
      FROM ajkpeserta 
      INNER JOIN ajkcabang ON ajkcabang.er = ajkpeserta.cabang
      WHERE idpeserta = '".$_REQUEST['id']."'";
      
      $peserta = mysql_fetch_array(mysql_query($q));

      $qproduk = "SELECT * FROM ajkpolisasuransi WHERE idproduk = '".$peserta['idpolicy']."' and del is null and idas = 2 ";

      $produk = mysql_fetch_array(mysql_query($qproduk));

      if($peserta['idpolicy'] != 11 and $peserta['idpolicy'] != 12){
        $manfaat = 'APABILA TERTANGGUNG MENINGGAL DUNIA KARENA SEBAB APAPUN DALAM MASA ASURANSI, MAKA AKAN DIBAYARKAN SEBESAR SISA POKOK PINJAMAN/KREDIT POKOK DITAMBAH BUNGA PINJAMAN MAKSIMUM 1 BULAN (JIKA ADA) TIDAK TERMASUK TUNGGAKAN ANGSURAN POKOK DAN DENDA (BILA ADA) SELANJUTNYA PERTANGGUNGAN OTOMATIS BERAKHIR.';
        $lampiran = '../modules/Ringkasan-Polis-VAI-Multiguna.png';
      }elseif($peserta['idpolicy'] == 11){
        $manfaat = 'APABILA TERTANGGUNG MENINGGAL DUNIA KARENA SAKIT/ALAMI ATAU KECELAKAAN DALAM MASA ASURANSI, MAKA KEPADA PENERIMA MANFAAT AKAN DIBAYARKAN SEBESAR SISA PINJAMAN/KREDIT POKOK TIDAK TERMASUK TUNGGAKAN ANGSURAN POKOK, BUNGA DAN DENDA (BILA ADA) DAN SELANJUTNYA PERTANGGUNGAN OTOMATIS BERAKHIR.';
        $lampiran = '../modules/Ringkasan-Polis-VAI-KPR.png';
      }elseif($peserta['idpolicy'] == 12){
        $manfaat = 'APABILA TERTANGGUNG/PESERTA MENGALAMI MUSIBAH MENINGGAL DUNIA KARENA SAKIT/ALAMI ATAU KECELAKAAN DALAM MASA ASURANSI, MAKA KEPADA PENERIMA MANFAAT AKAN DIBAYARKAN SEBESAR PLAFOND KREDIT POKOK DAN SELANJUTNYA PERTANGGUNGAN OTOMATIS BERAKHIR.';
        $lampiran = '../modules/Ringkasan-Polis-VAI-THT.png';
      }
      if(isset($peserta['noasuransi'])){
        $sertifikat = $peserta['noasuransi'];
      }else{
        $sertifikat = $produk['policymanual'].'-'.$peserta['idpeserta'];
      }
      

      
      $html = '       
      <style>
        .border {  
          border: 4px solid black; /* Border around the page */  
          height: 100%; /* Full height */  
          width: 100%; /* Full width */  
          box-sizing: border-box; /* Include border in the elements total width and height */  
          padding: 10px;
        } 
      </style>
      <div class="border" style="font-family:Arial">
      <table width="100%" style="padding-bottom:-10px">
        <tr>
          <td><img src="../'.$PathPhoto.'victorialife.png" width="260" height="45" style="margin-left:-10px;"></td>
          <td></td>
          <td><img src="../'.$PathPhoto.'berasuransi.png" width="150" height="80" style="margin-left:-10px;"></td>
        </tr>
      </table>
      <p style="text-align:center;font-weight:bold;font-size:16px">SERTIFIKAT ASURANSI JIWA KREDIT<br>VAI CREDIT LIFE ASSURANCE</p>
      <p style="text-align:center;margin:0"><b>PT VICTORIA ALIFE INDONESIA</b></p>
      <p style="text-align:center;font-size:13px;margin:0">[Selanjutnya disebut PENANGGUNG] Dengan ini</p>

      <p style="text-align:center;font-size:13px;">Memberikan pertanggungan kepada:</p>

      <p style="text-align:center;margin:0"><b>'.$peserta['nama'].'</b></p>
      <p style="text-align:center;margin:0;font-size:13px">[Selanjutnya disebut TERTANGGUNG]</p>

      <p style="text-align:justify">Berdasarkan Data Polis dibawah ini, Penanggung menyatakan bahwa Tertanggung yang namanya tercantum pada Sertifikat Asuransi ini diasuransikan jiwanya dengan pengaturan sepenuhnya pada Polis Asuransi Jiwa Kredit Kumpulan.</p>
      <p style="text-align:center"><b>DATA PERTANGGUNGAN</b></p>
      <table width="100%" style="font-family:Arial;font-size:13px;">
        <tr>
          <td>Nomor Polis</td>
          <td>:</td>
          <td>'.$produk['policymanual'].'</td>
        </tr>
        <tr>
          <td>Nama Pemegang Polis</td>
          <td>:</td>
          <td>'.$peserta['nama'].'</td>
        </tr>
        <tr>
          <td>Kode Cabang</td>
          <td>:</td>
          <td>'.$peserta['cabang'].'</td>
        </tr>
        <tr>
          <td>Nomor Sertifikat</td>
          <td>:</td>
          <td>'.$sertifikat.'</td>
        </tr>
        <tr>
          <td>Tanggal Lahir Peserta/Tertanggung</td>
          <td>:</td>
          <td>'.viewBulanIndo($peserta['tgllahir']).'</td>
        </tr>
        <tr>
          <td>Usia Masuk Peserta/Tertanggung</td>
          <td>:</td>
          <td>'.$peserta['usia'].' TAHUN</td>
        </tr>
        <tr>
          <td>Pertanggungan Dasar</td>
          <td>:</td>
          <td>VAI CREDIT LIFE ASSURANCE</td>
        </tr>
        <tr>
          <td>Uang Pertanggungan Awal</td>
          <td>:</td>
          <td>Rp '.duit($peserta['plafond']).'</td>
        </tr>
        <tr>
          <td>PREMI</td>
          <td>:</td>
          <td>Rp '.duit($peserta['totalpremi']).'</td>
        </tr>
        <tr>
          <td>Frekuensi Pembayaran Premi</td>
          <td>:</td>
          <td>SEKALIGUS</td>
        </tr>
        <tr>
          <td>Kelas Resiko</td>
          <td>:</td>
          <td>1</td>
        </tr>
        <tr>
          <td>Masa Asuransi</td>
          <td>:</td>
          <td>'.viewBulanIndo($peserta['tglakad']).' sampai dengan '.viewBulanIndo($peserta['tglakhiras']).'</td>
        </tr>
        <tr>
          <td style="vertical-align:top;">Manfaat Asuransi</td>
          <td style="vertical-align:top;">:</td>
          <td style="text-align:justify">'.$manfaat.'</td>
        </tr>
      </table>
      <p style="text-align:justify;font-size:13px;">Sertifikat ini berlaku apabila pembayaran Premi telah diterima secara penuh oleh Penanggung dan diberlakukan menurut data yang disimpan dan dikeluarkan oleh Penanggung, serta ditandatangani pada tanggal diterbitkan</p>
      <table width="100%">
        <tr>
          <td width="35%"></td>
          <td width="65%">
          <table>
            <tr>
              <td style="text-align:center;font-size:12px"><p>Jakarta,'.viewBulanIndo(date('Y-m-d')).'<br>PT Victoria Alife Indonesia</p></td>
            </tr>
            <tr>
              <td style="text-align:center;font-size:12px;padding-bottom:80px;padding-top:30px">Surat ini dicetak secara komputerisasi sehingga tidak memerlukan tanda tangan</td>
            </tr>            
          </table>
          </td>
        </tr>
      </table>
      <table width="100%" style="color:red;font-size:9px;font-style: italic;">
        <tr>
          <td colspan="3">PERHATIAN:</td>
        </tr>
        <tr>
          <td width="2%"></td>
          <td width="1%">(1)</td>
          <td width="90%">Pemegang Polis wajib memberitahukan secara tertulis apabila ditemukan kekeliruan data dalam waktu 7 (tujuh) hari setelah e-Sertifikat Asuransi diterima.</td>          
        </tr>
        <tr>
          <td></td>
          <td>(2)</td>
          <td>Pengajuan klaim wajib melampirkan copy e-Sertifikat Asuransi</td>          
        </tr>
        <tr>
          <td></td>
          <td style="vertical-align:top">(3)</td>
          <td>Sertifikat ini tunduk pada Ketentuan Umum Polis Asuransi dan ketentuan-ketentuan lain yang tercantum di dalam atau melekat pada Polis dan merupakan bagian yang tidak terpisahkan dari Perjanjuan Asuransi.</td>          
        </tr>
      </table>
      </div>
      ';
      $html2 = '';
      $mpdf = new Mpdf();
      
      $mpdf->AddPageByArray([
        'margin-left' => 8,
        'margin-right' => 8,
        'margin-top' => 8,
        'margin-bottom' => 8,
      ]);
      $mpdf->SetWatermarkImage('../modules/background-vai.png',0.15,[170,150],'P');
      $mpdf->showWatermarkImage = true;

      $mpdf->WriteHTML($html);

      $mpdf->AddPage();
      $mpdf->SetWatermarkImage($lampiran,1,'D','P');
      $mpdf->showWatermarkImage = true;


      $mpdf->Output('SERTIFIKAT_'.$peserta['nama'].'.pdf', 'I');
    break;
  }
?>