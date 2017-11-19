<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<?php
			echo $this->Html->css(array(
				'bootstrap.min',
				'bootstrap-responsive.min',
				'login',
			));

			echo $this->fetch('meta');
			echo $this->fetch('css');
	?>
	<style>
		body {
		  background-color: #eee;
		}
	</style>
	<title>ERP RJTM | Login Site</title>
	<link rel="icon" href="/img/favicon.png" type="image/jpg" />
</head>
<body>
	<div class="container">
		<?php 
			echo $this->fetch('content'); 
		?>
    </div> <!-- /container -->
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>