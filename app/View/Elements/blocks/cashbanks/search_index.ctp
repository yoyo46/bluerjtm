<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('CashBank', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'cashbanks',
                        'action' => 'search',
                        'index'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
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
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                echo $this->Form->input('receiving_cash_type',array(
                                    'label'=> __('Tipe Kas Bank'),
                                    'class'=>'form-control',
                                    'required' => false,
                                    'empty' => __('Pilih Tipe Kas Bank'),
                                    'options' => array(
                                        'in' => 'Cash In',
                                        'out' => 'Cash Out'
                                    )
                                ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                        echo $this->Form->label('date_from', 'Tanggal Kas Bank');
                    ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <?php 
                                echo $this->Form->input('date_from',array(
                                    'label'=> false,
                                    'class'=>'form-control custom-date',
                                    'required' => false,
                                    'placeholder' => __('Dari')
                                ));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?php 
                                echo $this->Form->input('date_to',array(
                                    'label'=> false,
                                    'class'=>'form-control custom-date',
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
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
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