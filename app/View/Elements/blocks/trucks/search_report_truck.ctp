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
                        'controller' => 'trucks',
                        'action' => 'search',
                        'reports'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
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
                                    echo $this->Form->input('type',array(
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
                                    echo $this->Form->input('nopol',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'required' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Truck.capacity',array(
                                'label'=> __('Kapasitas'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Kapasitas')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Truck.year',array(
                                'label'=> __('Tahun Truk'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Tahun Truk')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Truck.company_id',array(
                                'label'=> __('Pemilik Truk'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Pemilik Truk')
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
                                'action' => 'reports', 
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
                            echo $this->Form->input('Truck.alokasi',array(
                                'label'=> __('Alokasi Truk'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Alokasi Truk')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Driver.name',array(
                                'label'=> __('Nama Supir'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nama Supir')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Truck.category',array(
                                'label'=> __('Jenis Truk'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Jenis Truk')
                            ));
                    ?>
                </div>
                <?php 
                        // Custom Otorisasi
                        echo $this->Common->getCheckboxBranch();
                ?>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>