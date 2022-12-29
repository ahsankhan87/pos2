<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8">
	<title>Password Reset | GuvenFI Accounting Software</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="GuvenFI Accounting Software to run your business, Point of Sale, Accounts Management Systems" name="description">
	<meta content="Ahsan khan" name="author">
	<meta name="MobileOptimized" content="320">
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="<?php echo base_url(); ?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.css">
	<!-- END PAGE LEVEL SCRIPTS -->
	<!-- BEGIN THEME STYLES -->
	<link href="<?php echo base_url(); ?>assets/css/style-conquer.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/style-responsive.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/plugins.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color">
	<link href="<?php echo base_url(); ?>assets/css/pages/login.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/custom.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>assets/css/animate.min.css" rel="stylesheet" type="text/css">
	<!-- END THEME STYLES -->
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/flags/favicon.ico">

</head>
<!-- BEGIN BODY -->

<body class="login">
	<!-- BEGIN LOGO -->
	<div class="logo animated bounceIn">
		<h1 class="form-title  logo-color">GuvenFi</h1>
		<!-- <a href="<?php echo base_url(); ?>">
	<img src="<?php echo base_url(); ?>assets/img/logo.jpg" width="200" height="70" alt="Logo">
	</a> -->
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content animated bounceIn">
			<?php
			if ($this->session->flashdata('message')) {
				echo "<div class='alert alert-success fade in'>";
				echo '<span>' . $this->session->flashdata('message') . '</span>';
				echo '</div>';
			}
			if ($this->session->flashdata('error')) {
				echo "<div class='alert alert-danger fade in'>";
				echo '<span>' . $this->session->flashdata('error') . '</span>';
				echo "</div>";
			}
			?>
			
		<!-- BEGIN REGISTRATION FORM -->
		<form class="register-form" action="" method="post" novalidate="novalidate">
            
            <div id="results" class="alert alert-success display-hide">
				<button class="close" data-close="alert"></button>
				<span>Forgot Password </span>
			</div>
			<h3>Create New Password</h3>
			
			<input type="hidden" id="url" value="<?php echo site_url('en/C_login/forget_password_reset') ?>" />
            <input type="hidden" name="forgot_pass_identity" value="<?php echo $forgot_pass_identity; ?>" />
                    
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Password</label>
				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input class="form-control placeholder-no-fix" type="password" autofocus autocomplete="off" id="register_password" placeholder="Password" name="password">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
				<div class="controls">
					<div class="input-icon">
						<i class="fa fa-check"></i>
						<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Re-type Your Password" name="rpassword">
					</div>
				</div>
			</div>

			<div class="form-actions">
				<a href="<?php echo base_url('Login') ?>" id="login-btn" class="btn btn-primary pull-left">Login</a>
				
				<button type="submit" id="register-submit-btn" class="btn btn-info pull-right">
					Submit <i class="m-icon-swapright m-icon-white"></i>
				</button>
				<img src="<?php echo base_url('images/wait.gif') ?>" width="20" height="20" class="loading  pull-right" />

			</div>
		</form>
		<!-- END REGISTRATION FORM -->
	</div>
	<!-- END LOGIN -->
	<!-- BEGIN COPYRIGHT -->
	<div class="copyright">
		<?php echo date("Y"); ?> &copy; guvenfi.com
	</div>
	<!-- END COPYRIGHT -->
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
	<script async="" src="//www.google-analytics.com/analytics.js"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<!-- END JAVASCRIPTS -->
	<script src="<?php echo base_url(); ?>assets/scripts/app.js" type="text/javascript"></script>
	<!-- <script src="<?php echo base_url(); ?>assets/scripts/login.js" type="text/javascript"></script> -->


	<!-- END PAGE LEVEL SCRIPTS -->
	<script>
		jQuery(document).ready(function() {

			var site_url = '<?php echo site_url(); ?>';
			var base_url = '<?php echo base_url(); ?>';
            $('#login-btn').hide();

			App.init();
			//   Login();
			handleRegister();
			var action = location.hash.substr(1);
			if (action == 'createaccount') {
				$('.register-form').show();
				
			} 
			function handleRegister() {
				//console.log(site_url);
                $('.register-form').show();
                $(".loading").hide();

				$('.register-form').validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					ignore: "",
					rules: {
						password: {
							required: true,
							minlength: 3,
							maxlength: 12,
						},
						rpassword: {
							minlength: 3,
							maxlength: 12,
							equalTo: "#register_password"
						},

						tnc: {
							required: true
						}
					},

					invalidHandler: function(event, validator) { //display error alert on form submit   
						$("#register-submit-btn").show();
						$(".loading").hide();
					},

					highlight: function(element) { // hightlight error inputs
						$(element)
							.closest('.form-group').addClass('has-error'); // set error class to the control group
					},

					success: function(label) {
						label.closest('.form-group').removeClass('has-error');
						label.remove();
					},

					errorPlacement: function(error, element) {
						if (element.attr("name") == "tnc") { // insert checkbox errors after the container                  
							error.insertAfter($('#register_tnc_error'));
						} else if (element.closest('.input-icon').size() === 1) {
							error.insertAfter(element.closest('.input-icon'));
						} else {
							error.insertAfter(element);
						}
					},

					submitHandler: function(form) {

						$("#register-submit-btn").hide();
						$(".loading").show();

						$.ajax({
							type: 'POST',
							url: $('#url').val(),
							data: $('.register-form').serialize(),
							success: function(data) {
								//console.log($('#url').val());
								
								if(data == '1')
                                {
                                    $("#results").show();
                                    $("#results").html('Password changed successfully');
                                    $('#login-btn').show();
                                    $(".loading").hide();
                                    //$(".results-success").append(' <a href');
                                }else if(data == 'false')
                                {
                                    // toastr.error("verification failed",'Error');
                                    $("#results").show();
									$("#results").removeClass('alert-success');
									$("#results").addClass('alert-danger');

                                    $("#results").html("- Verification failed");
                                }else
                                {
                                    // toastr.error("password not reset",'Error');
                                    $("#results").show();
                                    $("#results").removeClass('alert-sucess');
									$("#results").addClass('alert-danger');
									$("#results").html("- Password not reset");
                                }
								//window.location.href = $('#url').val();
							},
							error: function(request, status, error) {
								console.log(request);
								console.log(error);
							}
						});
						return false; // required to block normal submit since you used ajax

					}
				});

				$('.register-form input').keypress(function(e) {
					if (e.which == 13) {
						if ($('.register-form').validate().form()) {
							$('.register-form').submit();
						}
						return false;
					}
				});

				jQuery('#register-btn').click(function() {
					jQuery('.login-form').hide();
					jQuery('.register-form').show();
					//alert('You are not a premuim user. please contact the vendor');
				});

				jQuery('#register-back-btn').click(function() {
					jQuery('.login-form').show();
					jQuery('.register-form').hide();
				});
			}




		});
	</script>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css">

	<!-- END BODY -->
	<span role="status" aria-live="polite" class="select2-hidden-accessible"></span>
</body>

</html>