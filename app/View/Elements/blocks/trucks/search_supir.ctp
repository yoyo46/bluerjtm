<div class="box">
    <div class="box-header">
        <h3 class="box-title">Search</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Driver', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'trucks',
                    'action' => 'search',
                    'drivers'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
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
        <div class="box-footer">
            <?php
                echo $this->Form->button(__('Submit'), array(
                    'div' => false, 
                    'class'=> 'btn btn-primary',
                    'type' => 'submit',
                ));
            ?>
        </div>
        <?php 
            echo $this->Form->end();
        ?>
    </div>
</div>