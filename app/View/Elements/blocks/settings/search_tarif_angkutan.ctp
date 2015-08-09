<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('TarifAngkutan', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'settings',
                    'action' => 'search',
                    'tarif_angkutan'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('name',array(
                                'label'=> __('Nama Tarif Angkutan'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nama Tarif Angkutan')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('UangJalan.from_city',array(
                                'label'=> __('Kota Asal'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('TarifAngkutan.jenis_unit',array(
                                'label'=> __('Jenis Unit'),
                                'class'=>'form-control',
                                'required' => false,
                                'options' => array(
                                    'per_unit' => __('Per Unit'),
                                    'per_truck' => __('Per Truk'),
                                ),
                                'empty' => __('Pilih Jenis Unit'),
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
                                'action' => 'tarif_angkutan', 
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
                            echo $this->Form->input('customer_name',array(
                                'label'=> __('Nama Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nama Customer')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('UangJalan.to_city',array(
                                'label'=> __('Kota Tujuan'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('UangJalan.capacity',array(
                                'label'=> __('Kapasitas'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Kapasitas')
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