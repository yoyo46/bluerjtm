<style>
    .string{ mso-number-format:\@; }
</style>
<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Referensi'),
                'field_model' => 'Laka.id',
            ),
            'nodoc' => array(
                'name' => __('No. Dokumen'),
                'field_model' => 'Laka.nodoc',
            ),
            'tgl_laka' => array(
                'name' => __('Tgl Laka'),
                'field_model' => 'Laka.tgl_laka',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('NO. POL'),
                'field_model' => 'Laka.nopol',
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
                'field_model' => 'Laka.driver_name',
                'display' => true,
            ),
            'location' => array(
                'name' => __('Lokasi'),
                'field_model' => false,
                'display' => true,
            ),
            'description_laka' => array(
                'name' => __('Deskripsi'),
                'field_model' => false,
                'display' => true,
            ),
            'insurance' => array(
                'name' => __('Asuransi'),
                'field_model' => false,
                'display' => true,
            ),
            'completed' => array(
                'name' => __('Status'),
                'field_model' => 'Laka.completed',
                'display' => true,
            ),
            'biaya' => array(
                'name' => __('Total Biaya'),
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
                $filename = $this->Common->toSlug($sub_module_title);
                
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$filename.'.xls');
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
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'lakas',
                        'action' => 'search',
                        'reports',
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
                            echo $this->Form->input('noref',array(
                                'label'=> __('No. Referensi'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('status',array(
                                'label'=> __('Status'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status'),
                                'options' => array(
                                    'active' => __('Aktif'),
                                    'completed' => __('Selesai'),
                                ),
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
                            echo $this->Form->input('nodoc',array(
                                'label'=> __('No. Dokumen'),
                                'class'=>'form-control',
                                'required' => false,
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
                <!-- <div class="form-group">
                    <?php 
                            // echo $this->Form->input('insurance',array(
                            //     'label'=> __('Memiliki Asuransi?'),
                            //     'class'=>'form-control',
                            //     'required' => false,
                            //     'empty' => __('Pilih'),
                            //     'options' => array(
                            //         'no' => __('Tidak'),
                            //         'yes' => __('Ya'),
                            //     ),
                            // ));
                    ?>
                </div> -->
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
                        echo $this->element('blocks/lakas/tables/reports');
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($lakas)){
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
                echo $this->Html->tag('div', $this->element('pagination'), array(
                    'class' => 'pagination-report'
                ));
    ?>
</div>
<?php 
            }
        } else if( $data_action == 'pdf' ) {
            echo $this->element('blocks/common/tables/export_pdf', array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element('blocks/lakas/tables/reports'),
            ));
    }
?>