<div class="box hidden-print">
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
                    'id' => 'form-report',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Search.date',array(
                                'label'=> __('Tgl Piutang'),
                                'class'=>'form-control date-range',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Invoice.due_15', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Over Due 1 - 15')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Invoice.due_30', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Over due 16 - 30')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                    <div class="col-sm-4">
                        <?php 
                                echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('Invoice.due_above_30', array(
                                    'type' => 'checkbox',
                                    'label'=> false,
                                    'required' => false,
                                    'value' => 1,
                                    'div' => false,
                                )).__('Over due above 30')), array(
                                    'class' => 'checkbox',
                                ));
                        ?>
                    </div>
                </div>
            </div> 
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
                <?php 
                        // Custom Otorisasi
                        echo $this->Common->getCheckboxBranch();
                ?>
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
                        'controller' => 'revenues', 
                        'action' => 'invoice_reports', 
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