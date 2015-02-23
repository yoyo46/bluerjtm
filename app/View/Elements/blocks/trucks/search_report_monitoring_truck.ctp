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
                        'controller' => 'revenues',
                        'action' => 'search',
                        'monitoring_truck'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
                $i = 0;
        ?>
        <ul class="list-report-monitoring">
            <li>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Ttuj.monitoring_customer_id.'.$i,array(
                                'type' => 'checkbox',
                                'label'=> __('Pilih Semua'),
                                'required' => false,
                                'div' => array(
                                    'class' => 'checkbox'
                                ),
                                'class' => 'checkAll',
                            ));
                    ?>
                </div>
            </li>
            <?php 
                    if( !empty($customers) ) {
                        foreach ($customers as $key => $customer) {
                            $checked = false;
                            if( in_array($key, $customerId) ) {
                                $checked = true;
                            }
            ?>
            <li>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Ttuj.monitoring_customer_id.'.$i,array(
                                'type' => 'checkbox',
                                'label'=> $customer,
                                'required' => false,
                                'value' => $key,
                                'div' => array(
                                    'class' => 'checkbox'
                                ),
                                'checked' => $checked,
                                'class' => 'check-option',
                            ));
                    ?>
                </div>
            </li>
            <?php 
                            $i++;
                        }
                    }
            ?>
            <div class="clear"></div>
        </ul>
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
                                'action' => 'monitoring_truck', 
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