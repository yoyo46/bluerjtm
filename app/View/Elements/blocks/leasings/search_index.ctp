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
                        'controller' => 'leasings',
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
                            echo $this->Form->input('nodoc',array(
                                'label'=> __('No. Kontrak'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. Kontrak')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('vendor_id',array(
                                'label'=> __('Supplier'),
                                'class'=>'form-control',
                                'empty' => __('Pilih Supplier'),
                                'required' => false,
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('payment_status',array(
                                'label'=> __('Status Angsuran'),
                                'class'=>'form-control',
                                'empty' => __('Semua'),
                                'options' => array(
                                    'unpaid' => __('Belum dibayar'),
                                    'half_paid' => __('Dibayar sebagian'),
                                    'paid' => __('Lunas'),
                                ),
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('dp_payment_status',array(
                                'label'=> __('Status DP'),
                                'class'=>'form-control',
                                'empty' => __('Semua'),
                                'options' => array(
                                    'unpaid' => __('Belum dibayar'),
                                    'half_paid' => __('Dibayar sebagian'),
                                    'paid' => __('Lunas'),
                                ),
                                'required' => false,
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
                                'controller' => 'leasings', 
                                'action' => 'index', 
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