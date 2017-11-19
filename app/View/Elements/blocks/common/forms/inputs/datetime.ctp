<div class="row">
	<div class="col-sm-8">
		<?php 
				echo $this->Form->input(__('%s_date', $fieldName),array(
					'label'=> false, 
					'class'=>'form-control custom-date',
					'required' => false,
					'type' => 'text',
				));
		?>
	</div>
	<div class="col-sm-4">
		<div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
            </div>
			<?php 
					echo $this->Form->input(__('%s_time', $fieldName),array(
						'label'=> false, 
						'class'=>'form-control pull-right timepicker',
						'required' => false,
						'type' => 'text'
					));
			?>
        </div>
	</div>
</div>
<?php 
		if( !empty($errorMsg) ) {
			echo $errorMsg;
		}
?>