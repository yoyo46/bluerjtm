<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Stnk', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'trucks',
                    'action' => 'search',
                    'stnk'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="form-group">
            <?php 
                    echo $this->Form->label('Truck.type', __('Truk'));
            ?>
            <div class="row">
                <div class="col-sm-4">
                    <?php 
                            echo $this->Form->input('Truck.type',array(
                                'label'=> false,
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                'options' => array(
                                    '1' => __('Nopol'),
                                    '2' => __('ID Truk'),
                                ),
                            ));
                    ?>
                </div>
                <div class="col-sm-8">
                    <?php 
                            echo $this->Form->input('Truck.nopol',array(
                                'label'=> false,
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm',
                        'type' => 'submit',
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'action' => 'stnk', 
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