<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'no_doc' => array(
                'name' => __('NO. Doc'),
                'field_model' => $modelName.'.no_doc',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => false,
                'display' => true,
            ),
            'kekurangan_md' => array(
                'name' => __('Kekurangan MD?'),
                'field_model' => false,
                'display' => ($type=='ksu')?true:false,
            ),
            'tgl_'.$type => array(
                'name' => __('Tanggal'),
                'field_model' => $modelName.'.tgl_'.$type,
                'display' => true,
            ),
            'no_ttuj' => array(
                'name' => __('No TTUJ'),
                'field_model' => false,
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('NO. POL'),
                'field_model' => false,
                'display' => true,
            ),
            'capacity' => array(
                'name' => __('Kapasitas'),
                'field_model' => false,
                'display' => true,
            ),
            'category' => array(
                'name' => __('Jenis Truk'),
                'field_model' => false,
                'display' => true,
            ),
            'driver_name' => array(
                'name' => __('Supir'),
                'field_model' => false,
                'display' => true,
            ),
            'from_city' => array(
                'name' => __('Dari'),
                'field_model' => false,
                'display' => true,
            ),
            'to_city' => array(
                'name' => __('Tujuan'),
                'field_model' => false,
                'display' => true,
            ),
            'part_motor' => array(
                'name' => __('Part Motor'),
                'field_model' => false,
                'display' => true,
            ),
            'unit' => array(
                'name' => __('Jumlah Unit'),
                'field_model' => false,
                'display' => true,
            ),
            'biaya' => array(
                'name' => __('Biaya'),
                'field_model' => false,
                'display' => true,
            ),
            'total' => array(
                'name' => __('Total'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn($dataColumns, 'field-table', false, array(
            'style' => 'text-align: center;'
        ));
        
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
            }

            if( $data_action != 'excel' ) {
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Lku', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'lkus',
                        'action' => 'search',
                        'reports',
                        $type,
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
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
                            echo $this->Form->input('no_doc',array(
                                'label'=> __('No. Dokumen'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->input('status',array(
                                        'label'=> __('Status'),
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => __('Pilih Semua'),
                                        'options' => array(
                                            'closing' => __('Selesai'),
                                            'pending' => __('Belum'),
                                        ),
                                    ));
                            ?>
                        </div>
                    </div>
                    <?php 
                            if( $type == 'ksu' ) {
                    ?>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->input('Ksu.atpm',array(
                                        'label'=> __('Kekurangan MD'),
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => __('Pilih Semua'),
                                        'options' => array(
                                            'yes' => __('Ya'),
                                            'no' => __('Tidak'),
                                        ),
                                    ));
                            ?>
                        </div>
                    </div>
                    <?php 
                            }
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
                                $type,
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
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('driver_name',array(
                                'label'=> __('Supir'),
                                'class'=>'form-control',
                                'required' => false,
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
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'escape' => false,
                ),
            ));
    ?>
    <div class="table-responsive">
        <?php 
                }
        ?>
        <table id="tt" class="table table-bordered sorting" singleSelect="true" border="<?php echo $border; ?>">
            <thead frozen="true">
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                        echo $this->element('blocks/lkus/tables/reports');
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($datas)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div><!-- /.box-body -->
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            
            if( $data_action != 'excel' ) {
    ?>
</div>
<?php 
            }
        } else if( $data_action == 'pdf' ) {
            echo $this->element('blocks/common/tables/export_pdf', array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element('blocks/lkus/tables/reports'),
            ));
    }
?>