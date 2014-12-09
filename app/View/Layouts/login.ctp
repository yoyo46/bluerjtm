<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<?php
			echo $this->Html->css(array(
				'bootstrap.min',
				'bootstrap-responsive.min',
			));

			echo $this->Html->script(array(
				'jquery',
			));

			echo $this->fetch('meta');
			echo $this->fetch('css');
			echo $this->fetch('script');
	?>
	<style>
		body {
		  background-color: #eee;
		}
	</style>
</head>
<body class="metro">
	<div class="container" style="width:320px !important;">
		<?php 
			echo $this->fetch('content'); 
		?>
    </div> <!-- /container -->
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>