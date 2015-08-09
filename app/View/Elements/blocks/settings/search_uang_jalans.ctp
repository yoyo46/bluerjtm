<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('UangJalan', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'settings',
                        'action' => 'search',
                        'uang_jalan'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('name',array(
                            'label'=> __('Nama Uang Jalan'),
                            'class'=>'form-control',
                            'required' => false,
                        ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('capacity',array(
                            'label'=> __('Kapasitas'),
                            'class'=>'form-control',
                            'required' => false,
                        ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-6">
                    <?php 
                        echo $this->Form->input('from_city',array(
                            'label'=> __('Kota Asal'),
                            'class'=>'form-control',
                            'required' => false,
                        ));
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php 
                        echo $this->Form->input('to_city',array(
                            'label'=> __('Kota Tujuan'),
                            'class'=>'form-control',
                            'required' => false,
                        ));
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group action">
            <?php
                    echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                        'div' => false, 
                        'class'=> 'btn btn-success btn-sm',
                        'type' => 'submit',
                    ));
                    echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'controller' => 'settings', 
                        'action' => 'uang_jalan', 
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