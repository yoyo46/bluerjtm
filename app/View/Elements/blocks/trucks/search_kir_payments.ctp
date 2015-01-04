<div class="box">
    <div class="box-header">
        <h3 class="box-title">Search</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('KirPayment', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'trucks',
                    'action' => 'search',
                    'kir_payments'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="form-group">
            <?php 
                echo $this->Form->input('Truck.nopol',array(
                    'label'=> __('No. Pol'),
                    'class'=>'form-control',
                    'required' => false,
                    'placeholder' => __('No. Pol')
                ));
            ?>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm',
                        'type' => 'submit',
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'action' => 'kir_payments', 
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