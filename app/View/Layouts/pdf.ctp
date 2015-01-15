<?php
header("Content-type: application/pdf");
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>Laporan</title>
	<?php
			echo $this->Html->meta('icon');
			if(isset($layout_js) && !empty($layout_js)) {
				echo $this->Html->script($layout_js);
			}

			if(isset($layout_css) && !empty($layout_css)) {
				echo $this->Html->css($layout_css);
			}

	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1></h1>
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		</div>
	</div>
</body>
</html>