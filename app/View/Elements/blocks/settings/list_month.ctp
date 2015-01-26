<?php 
		$month = !empty($month)?$month:'';
		$unit = !empty($unit)?$unit:'';
		$rel = !empty($rel)?$rel:0;
?>
<div class="list-month form-group" rel="<?php echo $rel; ?>">
    <div class="row">
    	<div class="col-sm-6">
            <?php
                    echo $this->Form->input('CustomerTargetUnitDetail.month.'.$rel, array(
                        'label'=> false, 
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Bulan'),
                        'options' => $monthArr,
                        'value' => $month,
                    ));
            ?>
    	</div>
    	<div class="col-sm-5">
            <?php 
					echo $this->Form->input('CustomerTargetUnitDetail.unit.'.$rel,array(
						'label'=> false, 
						'class'=>'form-control',
						'required' => false,
						'placeholder'=> __('Target Unit'), 
                        'value' => $unit,
					));
			?>
        </div>
        <?php 
        		if( !empty($rel) ) {
        ?>
		<div class="col-sm-1 no-pleft">
	        <?php
					echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
						'class' => 'delete-custom-field has-danger',
						'action_type' => 'target-unit',
						'escape' => false
					));
	        ?>
		</div>
		<?php 
				}
		?>
    </div>
</div>