<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Lku', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'lkus',
                    'action' => 'search',
                    'index'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('no_doc',array(
                                'label'=> __('No Dokumen'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No Dokumen')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php
                        echo $this->Form->label('from_date', 'Tanggal'); 
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?php 
                                    echo $this->Form->input('from_date',array(
                                        'label'=> false,
                                        'class'=>'form-control custom-date',
                                        'required' => false,
                                        'placeholder' => __('Dari')
                                    ));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?php 
                                    echo $this->Form->input('to_date',array(
                                        'label'=> false,
                                        'class'=>'form-control custom-date',
                                        'required' => false,
                                        'placeholder' => __('Sampai')
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
                                'controller' => 'lkus', 
                                'action' => 'index', 
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
                            echo $this->Form->input('no_ttuj',array(
                                'label'=> __('No TTUJ'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No TTUJ')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php
                           echo $this->Form->input('customer_id',array(
                                'label'=> __('Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Customer'),
                                'options' => $customers
                            )); 
                    ?>
                </div>
                <div class="form-group">
                    <?php
                           echo $this->Html->tag('label', __('Status LKU')); 
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?php 
                                    echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ksu.closing', array(
                                        'type' => 'checkbox',
                                        'label'=> false,
                                        'div' => false,
                                        'required' => false,
                                        'value' => 1,
                                    )).__('Telah Closing?')), array(
                                        'class' => 'checkbox',
                                    ));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?php 
                                    echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ksu.paid', array(
                                        'type' => 'checkbox',
                                        'label'=> false,
                                        'div' => false,
                                        'required' => false,
                                        'value' => 1,
                                    )).__('Lunas?')), array(
                                        'class' => 'checkbox',
                                    ));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?php 
                                    echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ksu.half_paid', array(
                                        'type' => 'checkbox',
                                        'label'=> false,
                                        'div' => false,
                                        'required' => false,
                                        'value' => 1,
                                    )).__('Dibayar Sebagian?')), array(
                                        'class' => 'checkbox',
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>