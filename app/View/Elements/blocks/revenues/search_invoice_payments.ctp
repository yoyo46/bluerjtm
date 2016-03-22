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
                        'controller' => 'revenues',
                        'action' => 'search',
                        'invoice_payments'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', __('Tgl Bayar'));
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('date',array(
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('noref',array(
                            'label'=> __('No. Referensi'),
                            'class'=>'form-control',
                            'required' => false,
                            'placeholder' => __('No. Referensi')
                        ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->input('nodoc',array(
                            'label'=> __('No. Dokumen'),
                            'class'=>'form-control',
                            'required' => false,
                            'placeholder' => __('No. Dokumen')
                        ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('transaction_status',array(
                                'label'=> __('Status'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status'),
                                'options' => array(
                                    'draft' => 'Draft',
                                    'commit' => 'Commit',
                                    'void' => 'Void',
                                )
                            ));
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'controller' => 'revenues', 
                                'action' => 'invoice_payments', 
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