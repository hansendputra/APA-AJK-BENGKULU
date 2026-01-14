<?php
include "../param.php";
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
	<!-- begin #page-container -->
	<div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-two-sidebar page-with-footer page-with-top-menu page-without-sidebar">
		<?php
		_header($user,$namauser,$photo,$logo,$logoklient);
		_sidebar($user,$namauser,'','');
  
    $type = AES::decrypt128CBC($_REQUEST['type'], ENCRYPTION_KEY);
    if( $type == 'lpk'){
      $file = "Victoria-Asuransi Jiwa-FRM-NB02-008_Formulir Laporan Pemeriksaan Kesehatan.pdf";
      $message = ', silahkan isi form lalu di upload di halaman berikut';
    }elseif($type == 'spd'){
      $file = "Surat Pernyataan Debitur.pdf";      
      $message = ', silahkan isi form lalu di upload di halaman berikut';
    }else{
      $file = 'blank';
    }
		?>
		<!-- begin #content -->
		<div id="content" class="content">
			 <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="m-t-0">Input Peserta</h4>
          </div>
          <div class="panel-body">
            <div class="alert alert-warning fade in m-b-10"><h4><strong> Data telah berhasil disimpan <?= $message ?>  </strong><br /></h4></div>
          </div>
        </div>
        <?php 
       
        ?>
        <meta http-equiv="refresh" content="5; url=../masterdata?type=<?= AES::encrypt128CBC('peserta', ENCRYPTION_KEY) ?>">
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
    
      <?php 
      if( $type != 'blank'){
        echo 'setTimeout(function() {
          window.open("../myFiles/'.$file.'", "_blank");
        }, 3000); ';
      }
      ?>
    
			$(".active").removeClass("active");
			document.getElementById("has_input").classList.add("active");			
		});
	</script>
</body>

</html>
