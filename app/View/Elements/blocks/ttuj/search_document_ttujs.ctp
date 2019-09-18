<?php 
        $data_action = !empty($data_action)?$data_action:false;
        $title = !empty($title)?$title:false;
        $cities = !empty($cities)?$cities:false;
?>
<div class="box collapsed-box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => $this->params['controller'],
                        'action' => 'search',
                        $this->action,
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('datettuj', __('Tgl Berangkat'));
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('datettuj',array(
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', __('Tgl TTUJ'));
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
                            echo $this->Form->input('nodoc',array(
                                'label'=> __('No. TTUJ'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('No. TTUJ')
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
            <div class="col-sm-4">
                <div class="form-group select-block">
                    <?php 
                            echo $this->Form->label('fromcity', __('Asal'), array(
                                'class' => 'control-label',
                            ));
                            echo $this->Form->input('fromcity',array(
                                'label'=> false,
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                'options' => $cities,
                                'empty' => __('Pilih Kota Asal'),
                            ));
                    ?>
                </div>
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
                            echo $this->Form->input('note',array(
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
                                'class'=> 'btn btn-success btn-sm ajaxModal',
                                'type' => 'submit',
                                'data-action' => $data_action,
                                'data-parent' => true,
                                'title' => $title,
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => $this->action, 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm ajaxModal',
                                'data-action' => $data_action,
                                'title' => $title,
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group select-block">
                    <?php 
                            echo $this->Form->label('tocity', __('Tujuan'), array(
                                'class' => 'control-label',
                            ));
                            echo $this->Form->input('tocity',array(
                                'label'=> false,
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                'options' => $cities,
                                'empty' => __('Pilih Kota Tujuan'),
                            ));
                    ?>
                </div>
                <div class="form-group select-block">
                    <?php 
                            echo $this->Form->label('customerid', __('Customer'), array(
                                'class' => 'control-label',
                            ));
                            echo $this->Form->input('customerid',array(
                                'label'=> false,
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                'empty' => __('Pilih Customer'),
                                'options' => $customers
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('driver',array(
                                'label'=> __('Supir'),
                                'class'=>'form-control',
                                'required' => false,
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