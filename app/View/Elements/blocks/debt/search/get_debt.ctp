<?php 
        $data_action = !empty($data_action)?$data_action:false;
        $payment_id = !empty($payment_id)?$payment_id:false;
        $title = !empty($title)?$title:__('Hutang');
        
        $urlForm = !empty($urlForm)?$urlForm:false;
        $urlReset = !empty($urlReset)?$urlReset:false;

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url($urlForm), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tgl Hutang'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tanggal'),
                        'autocomplete'=> 'off', 
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No. Doc'),
                        'class'=>'form-control',
                        'required' => false,
                        'autocomplete'=> 'off', 
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('staff_id',array(
                        'type' => 'text',
                        'label'=> __('ID Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('ID Supir'),
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama Karyawan'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Karyawan'),
                    ));
            ?>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'data-parent' => true,
                        'title' => $title,
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), $urlReset, array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxModal',
                        'data-action' => $data_action,
                        'title' => $title,
                    ));
            ?>
        </div>
    </div>
</div>
<?php 
    echo $this->Form->end();
?>