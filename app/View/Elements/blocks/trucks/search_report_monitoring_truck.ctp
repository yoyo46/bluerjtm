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
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('type', __('Truk'));
                    ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <?php 
                                    echo $this->Form->input('Ttuj.type',array(
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
                                    echo $this->Form->input('Ttuj.nopol',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'required' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->getCheckboxBranch();
                ?>
            </div>
        </div>
        <hr>
        <div class="list-report-monitoring overflow-y">
            <?php 
                    echo $this->Form->label('monitoring_customer_id', __('Customer'));
            ?>
            <div class="form-group">
                <div class="row">
                    <?php
                            echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.monitoring_customer_id.'.$i,array(
                                'type' => 'checkbox',
                                'label'=> false,
                                'required' => false,
                                'div' => false,
                                'class' => 'checkAll',
                            )).__('Pilih Semua')), array(
                                'class' => 'checkbox',
                            )), array(
                                'class' => 'col-sm-3',
                            ));

                            if( !empty($customers) ) {
                                foreach ($customers as $key => $customer) {
                                    $checked = false;
                                    if( in_array($key, $customerId) ) {
                                        $checked = true;
                                    }

                                    echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Ttuj.monitoring_customer_id.'.$i,array(
                                        'type' => 'checkbox',
                                        'label'=> false,
                                        'required' => false,
                                        'value' => $key,
                                        'div' => false,
                                        'checked' => $checked,
                                        'class' => 'check-option',
                                    )).$customer), array(
                                        'class' => 'checkbox',
                                    )), array(
                                        'class' => 'col-sm-3',
                                    ));
                                    $i++;
                                }
                            }
                    ?>
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
                                'controller' => 'revenues', 
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