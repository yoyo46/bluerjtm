<?php
        $model = !empty($model)?$model:false;
		$idx = !empty($idx)?$idx:0;
?>
<div class="row list-uang-kuli" rel="<?php echo $idx; ?>">
    <div class="col-sm-3">
		<div class="form-group">
            <?php
                    echo $this->Form->input($model.'.group_motor_id.'.$idx, array(
                        'label' => __('Group Motor'),
                        'empty' => __('Pilih Group Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $groupMotors,
                        'error' => false,
                    ));
            ?>
    	</div>
    </div>
    <div class="col-sm-5">
		<div class="form-group">
    		<?php 
    				echo $this->Form->label($model.'.uang_kuli.'.$idx, __('Uang Kuli'));
    		?>
            <div class="input-group">
		    	<?php 
		    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
		    				'class' => 'input-group-addon'
	    				));
						echo $this->Form->input($model.'.uang_kuli.'.$idx,array(
							'label'=> false, 
							'class'=>'form-control input_price',
							'required' => false,
							'type' => 'text',
                            'error' => false,
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
                    'action_type' => 'uang_kuli'
                ));
        ?>
    </div>
</div>