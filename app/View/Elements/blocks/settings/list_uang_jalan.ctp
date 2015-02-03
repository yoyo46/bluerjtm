<?php
		$idx = !empty($idx)?$idx:0;
?>
<div class="row list-uang-jalan" rel="<?php echo $idx; ?>">
    <div class="col-sm-2">
		<div class="form-group">
            <?php
                    echo $this->Form->input('UangJalanTipeMotor.tipe_motor_id.'.$idx, array(
                        'label' => __('Tipe Motor'),
                        'empty' => __('Pilih Tipe Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $tipeMotors,
                    ));
            ?>
    	</div>
    </div>
    <div class="col-sm-3">
		<div class="form-group">
    		<?php 
    				echo $this->Form->label('UangJalanTipeMotor.uang_jalan_1.'.$idx, __('Uang Jalan Pertama'));
    		?>
            <div class="input-group">
		    	<?php 
		    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
		    				'class' => 'input-group-addon'
	    				));
						echo $this->Form->input('UangJalanTipeMotor.uang_jalan_1.'.$idx,array(
							'label'=> false, 
							'class'=>'form-control input_price',
							'required' => false,
							'type' => 'text',
						));
				?>
			</div>
		</div>
    </div>
    <div class="col-sm-3">
		<div class="form-group">
    		<?php 
    				echo $this->Form->label('UangJalanTipeMotor.uang_kuli_muat.'.$idx, __('Uang Kuli Muat'));
    		?>
            <div class="input-group">
		    	<?php 
		    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
		    				'class' => 'input-group-addon'
	    				));
						echo $this->Form->input('UangJalanTipeMotor.uang_kuli_muat.'.$idx,array(
							'label'=> false, 
							'class'=>'form-control input_price',
							'required' => false,
							'type' => 'text',
						));
				?>
			</div>
		</div>
    </div>
    <div class="col-sm-3">
		<div class="form-group">
    		<?php 
    				echo $this->Form->label('UangJalanTipeMotor.uang_kuli_bongkar.'.$idx, __('Uang Kuli Bongkar'));
    		?>
            <div class="input-group">
		    	<?php 
		    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
		    				'class' => 'input-group-addon'
	    				));
						echo $this->Form->input('UangJalanTipeMotor.uang_kuli_bongkar.'.$idx,array(
							'label'=> false, 
							'class'=>'form-control input_price',
							'required' => false,
							'type' => 'text',
						));
				?>
			</div>
    	</div>
    </div>
    <div class="col-sm-1">
        <?php
                echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                    'escape' => false,
                    'action_type' => 'uang_jalan'
                ));
        ?>
    </div>
</div>