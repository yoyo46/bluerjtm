<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Customer', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'settings',
                        'action' => 'search',
                        'customers'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('code',array(
                                'label'=> __('Kode Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Kode Customer')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Customer.customer_type_id',array(
                                'label'=> __('Tipe Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Tipe Customer'),
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('name',array(
                                'label'=> __('Nama Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nama Customer')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Customer.customer_group_id',array(
                                'label'=> __('Grup Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Grup Customer'),
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
                                'controller' => 'settings', 
                                'action' => 'customers', 
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