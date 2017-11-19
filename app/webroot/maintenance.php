<?php 
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 3600');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Site is temporary unavailable due to server maintenance</title>
</head>
<body style="margin: 0 auto;text-align: center;">
	<div><img src="http://erp.sweetestilo.com/img/under-maintenance.jpg"></div>
</body>
</html>