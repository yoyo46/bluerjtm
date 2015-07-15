<?php
		$idx = !empty($idx)?$idx:0;
?>
<div class="row list-commission-extra" rel="<?php echo $idx; ?>">
    <div class="col-sm-3">
		<div class="form-group">
            <?php
                    echo $this->Form->input('CommissionExtraGroupMotor.group_motor_id.'.$idx, array(
                        'label' => __('Group Motor'),
                        'empty' => __('Pilih Group Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $groupMotors,
                    ));
            ?>
    	</div>
    </div>
    <div class="col-sm-7">
		<div class="form-group">
            <?php 
                    echo $this->Form->label('CommissionExtraGroupMotor.commission.'.$idx, __('Komisi Extra'));
            ?>
            <div class="row">
                <div class="col-sm-4 no-pright">
                    <div class="input-group">
                        <span class="input-group-addon">></span>
                        <?php 
                                echo $this->Form->input('CommissionExtraGroupMotor.min_capacity.'.$idx,array(
                                    'label'=> false, 
                                    'class'=>'form-control input_number',
                                    'required' => false,
                                    'placeholder' => __('Kapasitas'),
                                    'type' => 'text',
                                ));
                        ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="input-group">
                        <?php 
                                echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                                    'class' => 'input-group-addon'
                                ));
                                echo $this->Form->input('CommissionExtraGroupMotor.commission.'.$idx,array(
                                    'label'=> false, 
                                    'class'=>'form-control input_price',
                                    'required' => false,
                                    'placeholder' => __('Komisi'),
                                    'type' => 'text',
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <?php
                echo $this->Common->rule_link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                    'escape' => false,
                    'action_type' => 'commission_extra'
                ));
        ?>
    </div>
</div>