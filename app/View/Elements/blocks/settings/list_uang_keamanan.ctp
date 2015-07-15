<?php
        $idx = !empty($idx)?$idx:0;
?>
<div class="row list-uang-keamanan" rel="<?php echo $idx; ?>">
    <div class="col-sm-4">
        <div class="form-group">
            <?php
                    echo $this->Form->input('UangKeamananGroupMotor.group_motor_id.', array(
                        'label' => __('Group Motor'),
                        'empty' => __('Pilih Group Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $groupMotors,
                        'value' => !empty($this->request->data['UangKeamananGroupMotor']['group_motor_id'][$idx])?$this->request->data['UangKeamananGroupMotor']['group_motor_id'][$idx]:false,
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->label('UangKeamananGroupMotor.uang_keamanan.', __('Uang Keamanan'));
            ?>
            <div class="input-group">
                <?php 
                        echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                            'class' => 'input-group-addon'
                        ));
                        echo $this->Form->input('UangKeamananGroupMotor.uang_keamanan.',array(
                            'label'=> false, 
                            'class'=>'form-control input_price',
                            'required' => false,
                            'type' => 'text',
                            'value' => !empty($this->request->data['UangKeamananGroupMotor']['uang_keamanan'][$idx])?$this->request->data['UangKeamananGroupMotor']['uang_keamanan'][$idx]:false,
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
                    'action_type' => 'uang_keamanan'
                ));
        ?>
    </div>
</div>