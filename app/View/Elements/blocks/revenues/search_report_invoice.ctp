<div class="box box-primary hidden-print">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Invoice', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'revenues',
                        'action' => 'search',
                        'invoice_reports'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('customer_id',array(
                                'label'=> __('Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Customer'),
                                'options' => $list_customer,
                                'empty' => __('Pilih customer')
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php
                        echo $this->Form->label('from_date', __('Tanggal Laporan'), array(
                            'class' => 'control-label'
                        ));
                    ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?php 
                                    echo $this->Form->input('from_date',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'id' => 'fromdatepicker',
                                        'required' => false,
                                        'placeholder' => __('Dari')
                                    ));
                            ?>
                        </div>
                        <div class="col-xs-6">
                            <?php 
                                    echo $this->Form->input('to_date',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'id' => 'todatepicker',
                                        'required' => false,
                                        'placeholder' => __('Sampai')
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'reports', 
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