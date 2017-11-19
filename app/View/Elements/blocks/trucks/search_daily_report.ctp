<div class="box hidden-print">
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
                        'daily_report'
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
                            echo $this->Form->label('date', __('Tanggal'));
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
                            echo $this->Form->input('Ttuj.no_ttuj',array(
                                'label'=> __('No TTUJ'),
                                'class'=>'form-control',
                                'required' => false,
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
                                'action' => 'daily_report', 
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
                <div class="row">
                    <?php 
                            echo $this->Html->tag('div', $this->Common->buildInputForm('Search.fromcity', __('Dari'), array(
                                'empty' => __('Pilih Kota'),
                                'options' => $cities,
                                'class' => 'chosen-select form-control',
                            )), array(
                                'class' => 'col-sm-6',
                            ));
                            echo $this->Html->tag('div', $this->Common->buildInputForm('Search.tocity', __('Tujuan'), array(
                                'empty' => __('Pilih Kota'),
                                'options' => $cities,
                                'class' => 'chosen-select form-control',
                            )), array(
                                'class' => 'col-sm-6',
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('customer_group_id',array(
                                'label'=> __('Group Customer'),
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                // 'empty' => __('Pilih COA'),
                                'options' => !empty($customerGroups)?$customerGroups:false,
                                'multiple' => true,
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