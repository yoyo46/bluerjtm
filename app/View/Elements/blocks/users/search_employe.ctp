<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Employe', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'users',
                    'action' => 'search',
                    $this->action,
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('name',array(
                                'label'=> __('Nama'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nama')
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('employe_position_id',array(
                                'label'=> __('Posisi'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Posisi'),
                                'options' => $employe_positions
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('phone',array(
                                'label'=> __('No. Telp'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. Telp')
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Common->rule_link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'employes', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>