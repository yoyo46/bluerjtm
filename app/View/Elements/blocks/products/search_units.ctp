<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Search', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'products',
                    'action' => 'search',
                    'units'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="form-group">
            <?php 
                echo $this->Form->input('name',array(
                    'label'=> __('Satuan Barang'),
                    'class'=>'form-control',
                    'required' => false,
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
                        'controller' => 'products', 
                        'action' => 'units', 
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