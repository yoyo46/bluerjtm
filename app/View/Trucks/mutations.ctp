<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No. Dokumen'),
                'field_model' => 'TruckMutation.no_doc',
                'display' => true,
            ),
            'date' => array(
                'name' => __('Tgl Mutasi'),
                'field_model' => 'TruckMutation.mutation_date',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'field_model' => 'TruckMutation.nopol',
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
        echo $this->element('blocks/trucks/search_truck');
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
                                $mutation_date = $this->Common->filterEmptyField($value, 'TruckMutation', 'mutation_date');
                                $nopol = $this->Common->filterEmptyField($value, 'TruckMutation', 'nopol');
                                $created = $this->Common->filterEmptyField($value, 'TruckMutation', 'created');
                ?>
                <tr>
                    <td><?php echo $no_doc;?></td>
                    <td><?php echo $this->Common->customDate($mutation_date);?></td>
                    <td><?php echo $nopol;?></td>
                    <td><?php echo $this->Time->niceShort($created);?></td>
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

                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'trucks',
                                    'action' => 'mutation_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                ), __('Apakah Anda yakin akan menghapus data ini?'));
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