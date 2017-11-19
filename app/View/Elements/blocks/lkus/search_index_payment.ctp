<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                $action = (isset($action) && !empty($action)) ? $action : 'payments';
                
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'lkus',
                        'action' => 'search',
                        $action
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
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
                <div class="form-group">
                    <?php
                           echo $this->Form->input('customer',array(
                                'label'=> __('Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Customer'),
                                'options' => $customers
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
                                'controller' => 'lkus', 
                                'action' => $action, 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
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
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>