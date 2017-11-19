<?php
		$idx = !empty($idx)?$idx:0;
?>
<div class="row list-uang-jalan" rel="<?php echo $idx; ?>">
    <div class="col-sm-4">
		<div class="form-group">
            <?php
                    echo $this->Form->input('UangJalanTipeMotor.group_motor_id.', array(
                        'label' => __('Group Motor'),
                        'empty' => __('Pilih Group Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $groupMotors,
                        'value' => !empty($this->request->data['UangJalanTipeMotor']['group_motor_id'][$idx])?$this->request->data['UangJalanTipeMotor']['group_motor_id'][$idx]:false,
                    ));
            ?>
    	</div>
    </div>
    <div class="col-sm-6">
		<div class="form-group">
    		<?php 
    				echo $this->Form->label('UangJalanTipeMotor.uang_jalan_1.', __('Uang Jalan Pertama'));
    		?>
            <div class="input-group">
		    	<?php 
		    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
		    				'class' => 'input-group-addon'
	    				));
						echo $this->Form->input('UangJalanTipeMotor.uang_jalan_1.',array(
							'label'=> false, 
							'class'=>'form-control input_price',
							'required' => false,
							'type' => 'text',
                            'value' => !empty($this->request->data['UangJalanTipeMotor']['uang_jalan_1'][$idx])?$this->request->data['UangJalanTipeMotor']['uang_jalan_1'][$idx]:false,
						));
				?>
			</div>
		</div>
    </div>
    <div class="col-sm-2">
        <?php
                echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                    'escape' => false,
                    'action_type' => 'uang_jalan'
                ));
        ?>
    </div>
</div>