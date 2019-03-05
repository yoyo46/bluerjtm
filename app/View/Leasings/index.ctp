<?php 
        $dataColumns = array(
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => false,
                'display' => true,
            ),
            'no_contract' => array(
                'name' => __('No Kontrak'),
                'field_model' => 'Leasing.no_contract',
                'display' => true,
            ),
            'company' => array(
                'name' => __('Supplier'),
                'field_model' => 'Vendor.name',
                'display' => true,
            ),
            'paid_date' => array(
                'name' => __('Tgl Leasing'),
                'field_model' => 'Leasing.paid_date',
                'class' => 'text-center',
                'display' => true,
            ),
            'down_payment' => array(
                'name' => __('DP'),
                'field_model' => 'Leasing.down_payment',
                'class' => 'text-right',
                'display' => true,
            ),
            'installment' => array(
                'name' => __('Cicilan PerBln'),
                'field_model' => 'Leasing.installment',
                'class' => 'text-right',
                'display' => true,
            ),
            'date_first_installment' => array(
                'name' => __('Tgl Angsuran Pertama'),
                'field_model' => 'Leasing.date_first_installment',
                'class' => 'text-center',
                'display' => true,
            ),
            'payment_status' => array(
                'name' => __('Status Angsuran'),
                'field_model' => 'Leasing.payment_status',
                'display' => true,
            ),
            'dp_payment_status' => array(
                'name' => __('Status DP'),
                'field_model' => 'Leasing.dp_payment_status',
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
        echo $this->element('blocks/leasings/search_index');
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
                        'controller' => 'leasings',
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
                    if(!empty($leasings)){
                        foreach ($leasings as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Leasing', 'id');
                            $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');
                            $installment = $this->Common->filterEmptyField($value, 'Leasing', 'installment');
                            $down_payment = $this->Common->filterEmptyField($value, 'Leasing', 'down_payment');
                            $paid_date = $this->Common->filterEmptyField($value, 'Leasing', 'paid_date', false, false, array(
                                'date' => 'd M Y',
                            ));
                            $date_first_installment = $this->Common->filterEmptyField($value, 'Leasing', 'date_first_installment', false, false, array(
                                'date' => 'd M Y',
                            ));
                            $status = $this->Common->filterEmptyField($value, 'Leasing', 'status');
                            $payment_status = $this->Common->filterEmptyField($value, 'Leasing', 'payment_status');
                            $dp_payment_status = $this->Common->filterEmptyField($value, 'Leasing', 'dp_payment_status');
                            $branch_id = $this->Common->filterEmptyField($value, 'Leasing', 'branch_id');
                            // $company = $this->Common->filterEmptyField($value, 'LeasingCompany', 'name');
                            $company = $this->Common->filterEmptyField($value, 'Vendor', 'name');
                            $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');
            ?>
            <tr>
                <td><?php echo $branch;?></td>
                <td><?php echo $no_contract;?></td>
                <td><?php echo $company;?></td>
                <td class="text-center"><?php echo $paid_date;?></td>
                <td class="text-right"><?php echo Common::getFormatPrice($down_payment);?></td>
                <td class="text-right"><?php echo Common::getFormatPrice($installment);?></td>
                <td class="text-center"><?php echo $date_first_installment;?></td>
                <td>
                    <?php 
                            if(!empty($status)){
                                if( $payment_status == 'paid' ) {
                                    echo $this->Html->tag('span', __('Lunas'), array(
                                        'class' => 'label label-success',
                                    ));
                                } else if( $payment_status == 'half_paid' ) {
                                    echo $this->Html->tag('span', __('Dibayar Sebagian'), array(
                                        'class' => 'label label-primary',
                                    ));
                                } else {
                                    echo $this->Html->tag('span', __('Belum Dibayar'), array(
                                        'class' => 'label label-info',
                                    ));
                                }
                            }else{
                                echo $this->Html->tag('span', __('Void'), array(
                                    'class' => 'label label-danger',
                                ));
                            }
                    ?>
                </td>
                <td>
                    <?php 
                            if(!empty($status)){
                                if( $dp_payment_status == 'paid' ) {
                                    echo $this->Html->tag('span', __('Lunas'), array(
                                        'class' => 'label label-success',
                                    ));
                                } else if( $dp_payment_status == 'half_paid' ) {
                                    echo $this->Html->tag('span', __('Dibayar Sebagian'), array(
                                        'class' => 'label label-primary',
                                    ));
                                } else {
                                    echo $this->Html->tag('span', __('Belum Dibayar'), array(
                                        'class' => 'label label-info',
                                    ));
                                }
                            }else{
                                echo $this->Html->tag('span', __('Void'), array(
                                    'class' => 'label label-danger',
                                ));
                            }
                    ?>
                </td>
                <td class="action">
                    <?php
                            echo $this->Html->link(__('Info'), array(
                                'controller' => 'leasings',
                                'action' => 'detail',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs',
                                'branch_id' => $branch_id,
                            ));

                            if( $payment_status == 'unpaid' && $dp_payment_status == 'unpaid' ) {
                                if(!empty($status)){
                                    echo $this->Html->link(__('Edit'), array(
                                        'controller' => 'leasings',
                                        'action' => 'edit',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-primary btn-xs',
                                        'branch_id' => $branch_id,
                                    ));
                                }

                                if( !empty($status) ){
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'leasings',
                                        'action' => 'toggle',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs trigger-disabled',
                                        'branch_id' => $branch_id,
                                        'data-alert' => __('Apakah Anda yakin akan void kontrak ini?'),
                                    ));
                                }else{
                                    // echo $this->Html->link('Enable', array(
                                    //     'controller' => 'leasings',
                                    //     'action' => 'toggle',
                                    //     $id
                                    // ), array(
                                    //     'class' => 'btn btn-success btn-xs',
                                    //     'title' => 'enable status brand'
                                    // ), __('Apakah Anda yakin akan mengaktifkan kontrak ini?'));
                                }
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>