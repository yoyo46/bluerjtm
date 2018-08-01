<div class="box hidden-print">
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
                        'controller' => 'trucks',
                        'action' => 'search',
                        'profit_loss'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                    'class' => 'form-search',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Form->label('fromMonth', __('Dari Bulan'));
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('from', array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        // 'value' => $monthFrom,
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->year('from', 1949, date('Y'), array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        // 'value' => $yearFrom,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Form->label('fromMonth', __('Sampai Bulan'));
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('to', array(
                                        'label'=> false, 
                                        'class'=>'form-control target-month',
                                        'required' => false,
                                        'empty' => false,
                                        // 'disabled' => true,
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->year('to', 1949, date('Y'), array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        // 'value' => $yearFrom,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?php
                        // echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                        //     'textGroup' => $this->Common->icon('calendar'),
                        //     'positionGroup' => 'positionGroup',
                        //     'class' => 'form-control pull-right date-range',
                        // ));
                ?>
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
                            echo $this->Form->input('capacity',array(
                                'label'=> __('Kapasitas'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Kapasitas')
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('category',array(
                                'label'=> __('Jenis Truk'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Jenis Truk')
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
                                'action' => 'profit_loss', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->hidden('title',array(
                    'value'=> str_replace('<br>', ' - ', $sub_module_title),
                ));
                echo $this->Form->end();
        ?>
    </div>
</div>