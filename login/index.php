<?php
include "../koneksi.php";

if (isset($_REQUEST['pesan'])) {
    $pesan = AES::decrypt128CBC($_REQUEST['pesan'], ENCRYPTION_KEY);
} else {
    $pesan = '';
}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<!-- Mirrored from seantheme.com/color-admin-v1.9/admin/html/login_v3.html by HTTrack Website Copier/3.x [XR&CO'2013], Fri, 04 Mar 2016 10:07:00 GMT -->
<head>
	<meta charset="utf-8" />
	<title>A.J.K | Login Page</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />

	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="assets/css/animate.min.css" rel="stylesheet" />
	<link href="assets/css/style.min.css" rel="stylesheet" />
	<link href="assets/css/style-responsive.min.css" rel="stylesheet" />
	<link href="assets/css/theme/default.css" rel="stylesheet" id="theme" />
  <link href="assets/css/custom.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->

	<!-- ================== BEGIN BASE JS ================== -->
	<script src="assets/plugins/pace/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->
</head>
<body class="pace-top bg-white">
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade in"><span class="spinner"></span></div>
	<!-- end #page-loader -->

	<!-- begin #page-container -->
	<div id="page-container" class="fade">
	    <!-- begin login -->
        <div class="login login-with-news-feed">
            <!-- begin news-feed -->
            <div class="news-feed">
              <div id="carousel-example" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#carousel-example" data-slide-to="0" class="active"></li>
                </ol>

                <div class="carousel-inner">
                  <div class="item active">
                    <a href="#"><img src="../assets/img/photo1.jpg" class="img-responsive" /></a>
                  </div>
                </div>

                <!-- <a class="left carousel-control" href="#carousel-example" data-slide="prev">
                  <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-example" data-slide="next">
                  <span class="glyphicon glyphicon-chevron-right"></span>
                </a> -->
              </div>
            </div>


            <!-- end news-feed -->
            <!-- begin right-content -->
            <div class="right-content">
                <!-- begin login-header -->
                <div class="login-header">
                    <div class="brand">
                        <img src="../assets/img/logo_bengkulu.png" height="100" width="400"/>
                    </div>
                </div>
                <!-- end login-header -->
                <!-- begin login-content -->
                <div class="login-content">
                    <form action="dologin.php" method="POST" name="login_form" id="login_form" class="form-input-flat">
                        <div class="form-group m-b-15">
                            <input type="text" name="username" id="username" class="form-control input-lg" placeholder="Username" />
                        </div>
                        <div class="form-group m-b-15">
                            <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" />
                        </div>
                        <div class="login-buttons">
                            <button type="submit" class="btn btn-success btn-block btn-lg">Sign me in</button>
                        </div>
                        <div class="row m-b-20">
                          <div class="col-md-12"><span class='text-danger'><center><?= $pesan ?></center></span> </div>
                        </div>

                        <div class="form-group m-b-15">
                          <div class="row">
                            <div class="col-lg-12">
                              <p>Powered by</p>
                              <img src="assets/img/login-bg/adonai.png"  class="img-rounded img-responsive" />
                            </div>

                          </div>
                          <br /><br />
                          <div class="row">
                            <div class="col-lg-6">
                              <p>Registered and Supervised by</p>
                              <img src="assets/img/login-bg/ojk.png" style="width:100px" class="img-rounded img-responsive" />
                            </div>
                            <div class="col-lg-6">
                              <p>Member of</p>
                              <img src="assets/img/login-bg/apparindo.png" style="width:100px" class="img-rounded img-responsive" />
                            </div>
                          </div>              
                        </div>

                        <div class="row m-b-20">
                            <hr /><br />
                            <p class="text-center text-inverse">
                                &copy; Adonai All Right Reserved 2025
                            </p>
                        </div>

                  </form>
                <!-- end login-content -->
            </div>
            <!-- end right-container -->
        </div>
        <!-- end login -->
	</div>
	<!-- end page container -->

	<!-- ================== BEGIN BASE JS ================== -->
	<script src="assets/plugins/jquery/jquery-1.9.1.min.js"></script>
	<script src="assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script src="assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
	<script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="assets/plugins/jquery-cookie/jquery.cookie.js"></script>
	<!-- ================== END BASE JS ================== -->

	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="assets/js/apps.min.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->

	<script>
    document.addEventListener("contextmenu", event => event.preventDefault());
		$(document).ready(function() {
			App.init();

      $('#login_form').bootstrapValidator({
        err: {
            container: 'tooltip'
        },
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },

        fields: {
            username: {
                validators: {
                    notEmpty: {
                        message: 'Username harus diisi'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'Password harus diisi'
                    }
                }
            }
        }
      });
		});
	</script>
</body>
</html>
