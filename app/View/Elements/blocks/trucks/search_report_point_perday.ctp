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
                        'point_perday_report'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php
                        echo $this->Html->tag('label', __('Periode'));
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('month', array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        'name' => 'data[Truck][month]',
                                        'empty' => __('Pilih Bulan'),
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->year('year', 1949, date('Y') + 5, array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        'name' => 'data[Truck][year]',
                                        'empty' => __('Pilih Tahun'),
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <?php 
                        // Custom Otorisasi
                        echo $this->Common->getCheckboxBranch();
                ?>
            </div>
            <div class="col-sm-12">
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'point_perday_report', 
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