<?php 
		$month = !empty($month)?$month:'';
		$unit = !empty($unit)?$unit:'';
		$rel = !empty($rel)?$rel:0;
?>
<div class="col-sm-6">
	<div class="list-month form-group" rel="<?php echo $rel; ?>">
		<div class="form-group has-feedback">
	        <?php 
					echo $this->Form->input('CustomerTargetUnitDetail.unit.'.$rel,array(
						'label'=> false, 
						'required' => false,
						'class' => 'form-control',
						'placeholder'=> __('Target Unit'), 
					));
			?>
			<span class="form-control-feedback"><?php echo date('F', mktime(0, 0, 0, $month, 1, date("Y"))); ?></span>
	    </div>
	</div>
</div>