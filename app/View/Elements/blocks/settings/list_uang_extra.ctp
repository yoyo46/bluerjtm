<?php
		$idx = !empty($idx)?$idx:0;
?>
<div class="row list-uang-jalan-extra" rel="<?php echo $idx; ?>">
    <div class="col-sm-3">
		<div class="form-group">
            <?php
                    echo $this->Form->input('UangExtraGroupMotor.group_motor_id.', array(
                        'label' => __('Group Motor'),
                        'empty' => __('Pilih Group Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $groupMotors,
                        'value' => !empty($this->request->data['UangExtraGroupMotor']['group_motor_id'][$idx])?$this->request->data['UangExtraGroupMotor']['group_motor_id'][$idx]:false,
                    ));
            ?>
    	</div>
    </div>
    <div class="col-sm-7">
		<div class="form-group">
            <?php 
                    echo $this->Form->label('UangExtraGroupMotor.uang_jalan_extra.', __('Uang Jalan Extra'));
            ?>
            <div class="row">
                <div class="col-sm-4 no-pright">
                    <div class="input-group">
                        <span class="input-group-addon">></span>
                        <?php 
                                echo $this->Form->input('UangExtraGroupMotor.min_capacity.',array(
                                    'label'=> false, 
                                    'class'=>'form-control input_number',
                                    'required' => false,
                                    'placeholder' => __('Kapasitas'),
                                    'type' => 'text',
                                    'value' => !empty($this->request->data['UangExtraGroupMotor']['min_capacity'][$idx])?$this->request->data['UangExtraGroupMotor']['min_capacity'][$idx]:false,
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
                                echo $this->Form->input('UangExtraGroupMotor.uang_jalan_extra.',array(
                                    'label'=> false, 
                                    'class'=>'form-control input_price',
                                    'required' => false,
                                    'placeholder' => __('Uang Jalan Extra'),
                                    'type' => 'text',
                                    'value' => !empty($this->request->data['UangExtraGroupMotor']['uang_jalan_extra'][$idx])?$this->request->data['UangExtraGroupMotor']['uang_jalan_extra'][$idx]:false,
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <?php
                echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                    'class' => 'delete-custom-field btn btn-danger btn-xs',
                    'escape' => false,
                    'action_type' => 'uang_jalan_extra'
                ));
        ?>
    </div>
</div>