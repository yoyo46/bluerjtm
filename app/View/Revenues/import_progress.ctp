<div class="wrapper-download">
	<?php         
			if( !empty($error_message) ) {
	?>
	<div class="alert alert-danger alert-dismissable">
	    <b>Alert!</b> <?php echo $error_message; ?>
	</div>
	<?php
	        }

			echo $this->Form->hidden('interval', array(
				'class' => 'trigger-interval',
				'value' => 'false',
			));
	?>
</div>