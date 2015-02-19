<?php
        $idx = !empty($idx)?$idx:0;
?>
<div class="row list-asdp" rel="<?php echo $idx; ?>">
    <div class="col-sm-4">
        <div class="form-group">
            <?php
                    echo $this->Form->input('AsdpGroupMotor.group_motor_id.'.$idx, array(
                        'label' => __('Group Motor'),
                        'empty' => __('Pilih Group Motor'),
                        'class' => 'form-control',
                        'required' => false,
                        'options' => $groupMotors,
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->label('AsdpGroupMotor.asdp.'.$idx, __('Uang Penyebrangan'));
            ?>
            <div class="input-group">
                <?php 
                        echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                            'class' => 'input-group-addon'
                        ));
                        echo $this->Form->input('AsdpGroupMotor.asdp.'.$idx,array(
                            'label'=> false, 
                            'class'=>'form-control input_price',
                            'required' => false,
                            'type' => 'text',
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
                    'action_type' => 'asdp'
                ));
        ?>
    </div>
</div>