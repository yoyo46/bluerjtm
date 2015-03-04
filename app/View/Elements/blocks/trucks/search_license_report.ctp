<div class="box box-primary hidden-print">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Truck', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'trucks',
                        'action' => 'search',
                        'licenses_report'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('nopol',array(
                                'label'=> __('Nopol'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nopol')
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('TruckCustomer.customer_id',array(
                                'label'=> __('Alokasi'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Alokasi'),
                                'options' => $customers
                            ));
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('status_expired',array(
                                'label'=> __('Status'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status'),
                                'options' => array(
                                    'expired' => 'Expired',
                                    'expired_soon' => __('Akan Segera Expire')
                                )
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'licenses_report', 
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