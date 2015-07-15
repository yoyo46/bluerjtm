<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('BranchModule', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'users',
                    'action' => 'search',
                    'action_modules'
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
                            'label'=> __('Nama Module'),
                            'class'=>'form-control',
                            'required' => false,
                            'placeholder' => __('Nama Module')
                        ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('parent_id',array(
                            'label'=> __('Parent Module'),
                            'class'=>'form-control',
                            'required' => false,
                            'empty' => __('Pilih Parent Module'),
                            'options' => $parent_group
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
                    echo $this->Common->rule_link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                        'action' => 'action_modules', 
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