<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Log in | Admin Technofab</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/fontawesome-free/css/all.min.css'); ?>">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url('admin_assets/dist/css/adminlte.min.css'); ?>">
	<!-- Google Font: Source Sans Pro -->
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<b>Technofab </b>Warehouse
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg"></p>

				<?= form_open('/check-login'); ?>
					<div class="input-group mb-3">
						<input type="text" class="form-control" placeholder="Username" name="username">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-user"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" class="form-control" placeholder="Password" name="password">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<!-- /.col -->
						<div class="col-6 offset-3">
							<button type="submit" class="btn btn-primary btn-block">Sign In</button>
						</div>
						<!-- /.col -->
					</div>
				<?= form_close(); ?>
			</div>
			<!-- /.login-card-body -->
		</div>
	</div>
	<!-- /.login-box -->
	<script src="<?php echo base_url('admin_assets/plugins/jquery/jquery.min.js'); ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url('admin_assets/plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>
	<!-- jQuery -->
	<script src="<?php echo base_url('admin_assets/plugins/toastr/toastr.min.js'); ?>"></script>
	<?php
	 $session = \Config\Services::session();
	if($session->getFlashdata('error_message')) { ?>
	<script>
	toastr.error('<?= $session->getFlashdata('error_message'); ?>');
	</script>
	<?php } ?>
</body>

</html>
