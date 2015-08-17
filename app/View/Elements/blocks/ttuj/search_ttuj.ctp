<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Ttuj', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'revenues',
                        'action' => 'search',
                        $this->action,
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', $label_tgl);
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('Ttuj.date',array(
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('nottuj',array(
                                'label'=> __('No. Doc'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. Doc')
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
                                'action' => $this->action, 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('type', __('Truk'));
                    ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <?php 
                                    echo $this->Form->input('type',array(
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
                                    echo $this->Form->input('nopol',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'required' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('customer',array(
                                'label'=> __('Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Customer')
                            ));
                    ?>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_draft', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Draft')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_commit', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Commit')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_arrive', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Tiba ditujuan')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_bongkaran', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Bongkaran')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_balik', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Balik dari tujuan')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_pool', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Sampai pool')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_sj_not_completed', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('SJ belum kembali')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_sj_completed', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('SJ sudah kembali')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_revenue', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('SJ dibuatkan revenue')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_completed', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Closing')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.is_not_revenue', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('SJ belum dibuatkan revenue')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>