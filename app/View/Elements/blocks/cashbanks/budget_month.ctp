<?php 
		$month = !empty($month)?$month:'';
		$budget = !empty($budget)?$budget:'';
		$rel = !empty($rel)?$rel:0;
?>
<div class="col-sm-6">
	<div class="list-month form-group" rel="<?php echo $rel; ?>">
		<div class="form-group has-feedback">
	        <?php 
					echo $this->Form->input('BudgetDetail.budget.'.$rel,array(
						'label'=> false, 
						'required' => false,
						'class' => 'form-control input_price_coma',
						'placeholder'=> __('Budget'), 
					));
			?>
			<span class="form-control-feedback"><?php echo date('F', mktime(0, 0, 0, $month, 1, date("Y"))); ?></span>
	    </div>
	</div>
</div>