<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Revenue', array(
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
                            echo $this->Form->input('no_doc',array(
                                'label'=> __('No. Doc'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. Doc')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('date',array(
                                'label'=> __('Tanggal'),
                                'class'=>'form-control date-range',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('transaction_status',array(
                                'label'=> __('Status Revenue'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status Revenue'),
                                'options' => array(
                                    'unposting' => 'Unposting',
                                    'posting' => 'Posting',
                                    'invoiced' => 'Invoiced',
                                    'paid' => 'Paid',
                                )
                            ));
                    ?>
                </div>
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
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Ttuj.no_ttuj',array(
                                'label'=> __('No. TTUJ'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. TTUJ')
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
                            echo $this->Form->input('RevenueDetail.no_reference',array(
                                'label'=> __('No. Reference'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. Reference')
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