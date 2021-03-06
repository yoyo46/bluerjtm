<div class="box">
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
                        'invoices'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('no_invoice',array(
                            'label'=> __('Kode Invoice'),
                            'class'=>'form-control',
                            'required' => false,
                            'placeholder' => __('Kode Invoice')
                        ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('status',array(
                            'label'=> __('Status'),
                            'class'=>'form-control',
                            'required' => false,
                            'empty' => __('Pilih Status'),
                            'options' => $invStatus,
                        ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('Invoice.customer_id',array(
                            'label'=> __('Customer'),
                            'class'=>'form-control',
                            'required' => false,
                            'empty' => __('Pilih customer'),
                            'options' => $customers
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
                                'controller' => 'revenues', 
                                'action' => 'invoices', 
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