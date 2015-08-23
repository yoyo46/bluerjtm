<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No. Doc'),
                'field_model' => 'TruckMutation.no_doc',
                'display' => true,
            ),
            'date' => array(
                'name' => __('Tgl Mutasi'),
                'field_model' => 'TruckMutation.mutation_date',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('No. Pol'),
                'field_model' => 'TruckMutation.nopol',
                'display' => true,
            ),
            'description' => array(
                'name' => __('Keterangan'),
                'field_model' => 'TruckMutation.description',
                'display' => true,
            ),
            // 'truck' => array(
            //     'name' => __('Data Truk'),
            //     'field_model' => false,
            //     'display' => true,
            // ),
            // 'mutation' => array(
            //     'name' => __('Data Mutasi'),
            //     'field_model' => false,
            //     'display' => true,
            // ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'TruckMutation.created',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'TruckMutation.status',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/searchs/truck_mutation');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'trucks',
                        'action' => 'mutation_add',
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <thead>
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
                        if(!empty($truckMutations)){
                            foreach ($truckMutations as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'TruckMutation', 'id');
                                $no_doc = $this->Common->filterEmptyField($value, 'TruckMutation', 'no_doc');
                                $nopol = $this->Common->filterEmptyField($value, 'TruckMutation', 'nopol');
                                $mutation_date = $this->Common->filterEmptyField($value, 'TruckMutation', 'mutation_date');
                                $created = $this->Common->filterEmptyField($value, 'TruckMutation', 'created');
                                $status = $this->Common->filterEmptyField($value, 'TruckMutation', 'status');
                                $void_date = $this->Common->filterEmptyField($value, 'TruckMutation', 'void_date');
                                $description = $this->Common->filterEmptyField($value, 'TruckMutation', 'description');
                                
                                $iconStatus = $this->Common->getCheckStatus( $status );
                ?>
                <tr>
                    <td><?php echo $no_doc;?></td>
                    <td><?php echo $this->Common->customDate($mutation_date, 'd M Y');?></td>
                    <td><?php echo $nopol;?></td>
                    <td><?php echo $this->Common->getFormatDesc($description);?></td>
                    <!-- <td>
                        <?php
                                // echo $this->element('blocks/trucks/tables/data_mutation', array(
                                //     'truck' => $value,
                                //     'type' => 'truck',
                                // ));
                        ?>
                    </td> -->
                    <!-- <td>
                        <?php
                                // echo $this->element('blocks/trucks/tables/data_mutation', array(
                                //     'truck' => $value,
                                //     'type' => 'mutation',
                                // ));
                        ?>
                    </td> -->
                    <td><?php echo $this->Time->niceShort($created);?></td>
                    <td>
                        <?php
                                echo $iconStatus;

                                if( !empty($void_date) ) {
                                    echo '<br>';
                                    echo $this->Common->customDate($void_date, 'd/m/Y');
                                }
                        ?>
                    </td>
                    <td class="action">
                        <?php
                                echo $this->Html->link('Detail', array(
                                    'controller' => 'trucks',
                                    'action' => 'mutation_detail',
                                    $id,
                                ), array(
                                    'class' => 'btn btn-info btn-xs',
                                    'allow' => true,
                                ));

                                if( !empty($status) ) {
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'trucks',
                                        'action' => 'mutation_toggle',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs ajaxModal',
                                        'data-action' => 'submit_form',
                                    ), __('Apakah Anda yakin akan menghapus data ini?'));
                                }
                        ?>
                    </td>
                </tr>
                <?php
                            }
                        }else{
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                                'class' => 'alert alert-warning text-center',
                                'colspan' => '12'
                            )));
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>