<div class="box">
    <div class="box-header">
        <h3 class="box-title">Search</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('TipeMotor', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'settings',
                        'action' => 'search',
                        'type_motors'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="form-group">
            <?php 
                echo $this->Form->input('name',array(
                    'label'=> __('Nama Tipe Motor'),
                    'class'=>'form-control',
                    'required' => false,
                    'placeholder' => __('Nama Tipe Motor')
                ));
            ?>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm',
                        'type' => 'submit',
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'action' => 'type_motors', 
                    ), array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm',
                    ));
            ?>
        </div>
        <?php 
            echo $this->Form->end();
        ?>
    </div>
</div>