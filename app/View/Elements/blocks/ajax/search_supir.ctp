<?php
        $action_type = !empty($action_type)?$action_type:false;
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'search',
                'getDrivers',
                'driver_id' => $id,
                'action_type' => $action_type,
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('no_id',array(
                        'type' => 'text',
                        'label'=> __('ID Supir'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                        'placeholder' => __('ID Supir')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('name',array(
                        'label'=> __('Nama Supir'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Supir')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('alias',array(
                        'label'=> __('Nama Panggilan'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Panggilan')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('number',array(
                        'label'=> __('No. Identitas'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('No. Identitas')
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('phone',array(
                        'label'=> __('Telepon'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Telepon')
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
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'controller' => 'ajax',
                        'action' => 'getDrivers',
                        'action_type' => $action_type,
                        $id,
                    ), array(
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