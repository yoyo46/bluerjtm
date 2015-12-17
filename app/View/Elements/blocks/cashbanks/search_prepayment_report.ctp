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
                        'controller' => 'cashbanks',
                        'action' => 'search',
                        'prepayment_report'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
               <div class="form-group">
                    <?php 
                            echo $this->Form->input('date',array(
                                'label'=> __('Tgl Prepayment'),
                                'class'=>'form-control date-range',
                                'required' => false,
                            ));
                    ?>
                </div>
                <!-- <div class="form-group">
                    <?php 
                            // echo $this->Form->input('noref', array(
                            //     'label'=> __('No. Referensi'), 
                            //     'class'=>'form-control',
                            // ));
                    ?>
                </div> -->
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('name',array(
                                'label'=> __('Diterima/Dibayar kepada'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('total',array(
                                'label'=> __('Nominal'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <!-- <div class="form-group">
                    <?php 
                            // echo $this->Form->input('nodoc', array(
                            //     'label'=> __('No. Dokumen'), 
                            //     'class'=>'form-control',
                            // ));
                    ?>
                </div> -->
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('documenttype', array(
                                'label'=> __('Tampilkan'), 
                                'class'=>'form-control',
                                'options' => array(
                                    'all' => __('Semua'),
                                    'outstanding' => __('Outstanding'),
                                ),
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('description',array(
                                'label'=> __('Keterangan'),
                                'class'=>'form-control',
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
                                'controller' => 'cashbanks', 
                                'action' => 'prepayment_report', 
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