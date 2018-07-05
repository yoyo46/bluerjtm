<?php 
        $dataColumns = array(
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => false,
                'display' => true,
            ),
            'nodoc' => array(
                'name' => __('No. Polis'),
                'field_model' => 'Insurance.nodoc',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama Asuransi'),
                'field_model' => 'Insurance.name',
                'display' => true,
            ),
            'to_name' => array(
                'name' => __('Nama Tertanggung'),
                'field_model' => 'Insurance.to_name',
                'display' => true,
            ),
            'start_date' => array(
                'name' => __('Tgl Insurance'),
                'field_model' => 'Insurance.start_date',
                'class' => 'text-center',
                'display' => true,
            ),
            'grandtotal' => array(
                'name' => __('Total'),
                'field_model' => 'Insurance.grandtotal',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Insurance.transaction_status',
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
        echo $this->element('blocks/insurances/searchs/index');
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
                        'controller' => 'insurances',
                        'action' => 'add'
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
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = Common::hashEmptyField($value, 'Insurance.id');
                            $nodoc = Common::hashEmptyField($value, 'Insurance.nodoc');
                            $name = Common::hashEmptyField($value, 'Insurance.name');
                            $to_name = Common::hashEmptyField($value, 'Insurance.to_name');
                            $start_date = Common::hashEmptyField($value, 'Insurance.start_date');
                            $end_date = Common::hashEmptyField($value, 'Insurance.end_date');
                            $status = Common::hashEmptyField($value, 'Insurance.status');
                            $transaction_status = Common::hashEmptyField($value, 'Insurance.transaction_status');
                            $grandtotal = Common::hashEmptyField($value, 'Insurance.grandtotal', 0, array(
                                'type' => 'currency',
                            ));
                            $branch_id = Common::hashEmptyField($value, 'Insurance.branch_id');
                            $branch = Common::hashEmptyField($value, 'Branch.code');

                            if( empty($status) ) {
                                $transaction_status = 'void';
                                $value = Hash::insert($value, 'Insurance.transaction_status', $transaction_status);
                            }

                            $date = Common::getCombineDate($start_date, $end_date);
                            $status = $this->Common->_callTransactionStatus($value, 'Insurance');
            ?>
            <tr>
                <td><?php echo $branch;?></td>
                <td><?php echo $nodoc;?></td>
                <td><?php echo $name;?></td>
                <td><?php echo $to_name;?></td>
                <td><?php echo $date;?></td>
                <td class="text-right"><?php echo $grandtotal;?></td>
                <td class="text-center"><?php echo $status;?></td>
                <td class="action">
                    <?php
                            echo $this->Html->link(__('Info'), array(
                                'controller' => 'insurances',
                                'action' => 'detail',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs',
                                'branch_id' => $branch_id,
                            ));

                            if( $transaction_status == 'unpaid' ) {
                                echo $this->Html->link(__('Edit'), array(
                                    'controller' => 'insurances',
                                    'action' => 'edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    'branch_id' => $branch_id,
                                ));
                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'insurances',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'branch_id' => $branch_id,
                                    'data-alert' => __('Apakah Anda yakin akan void kontrak ini?'),
                                ));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '9'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>