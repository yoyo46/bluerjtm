<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('City', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'settings',
                    'action' => 'search',
                    'cities'
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
                            'label'=> __('Nama Kota'),
                            'class'=>'form-control',
                            'required' => false,
                            'placeholder' => __('Nama Kota')
                        ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Form->label('branch', '&nbsp;');
                ?>
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('is_head_office', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Head Office')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('is_branch', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Cabang')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('is_plant', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Plant')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
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
                        'action' => 'cities', 
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