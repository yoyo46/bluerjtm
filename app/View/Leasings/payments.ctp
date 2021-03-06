<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/leasings/search_index_payment');

        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Referensi'),
                'field_model' => 'LeasingPayment.id',
                'display' => true,
            ),
            'no_doc' => array(
                'name' => __('No. Dokumen'),
                'field_model' => 'LeasingPayment.no_doc',
                'display' => true,
            ),
            'installment' => array(
                'name' => __('Tgl Pembayaran'),
                'field_model' => 'LeasingPayment.payment_date',
                'class' => 'text-center',
                'display' => true,
            ),
            'type' => array(
                'name' => __('Jenis'),
                'field_model' => 'LeasingPayment.type',
                'display' => true,
            ),
            'vendor' => array(
                'name' => __('Supplier'),
                'field_model' => false,
                'display' => true,
            ),
            'grandtotal' => array(
                'name' => __('Total Pembayaran'),
                'field_model' => 'LeasingPayment.grandtotal',
                'class' => 'text-center',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'LeasingPayment.status',
                'class' => 'text-center',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'LeasingPayment.created',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box box-success">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Bayar'),
                '_add_multiple' => array(
                    array(
                        'label' => __('Angsuran'),
                        'url' => array(
                            'controller' => 'leasings',
                            'action' => 'payment_add',
                            'admin' => false,
                        ),
                    ),
                    array(
                        'label' => __('DP'),
                        'url' => array(
                            'controller' => 'leasings',
                            'action' => 'dp_add',
                            'admin' => false,
                        ),
                    ),
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($payments)){
                        foreach ($payments as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'LeasingPayment', 'id');
                            $type = $this->Common->filterEmptyField($value, 'LeasingPayment', 'type');
                            $no_doc = $this->Common->filterEmptyField($value, 'LeasingPayment', 'no_doc');
                            $payment_date = $this->Common->filterEmptyField($value, 'LeasingPayment', 'payment_date');
                            $periode = $this->Common->filterEmptyField($value, 'LeasingPayment', 'payment_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $grandtotal = $this->Common->filterEmptyField($value, 'LeasingPayment', 'grandtotal');
                            $status = $this->Common->filterEmptyField($value, 'LeasingPayment', 'status');
                            $rejected = $this->Common->filterEmptyField($value, 'LeasingPayment', 'rejected');
                            $created = $this->Common->filterEmptyField($value, 'LeasingPayment', 'created');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customCreated = $this->Time->niceShort($created);
                            $customDate = $this->Common->customDate($payment_date, 'd M Y');
                            $customGrandtotal = $this->Common->getFormatPrice($grandtotal);
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $no_doc);
                        echo $this->Html->tag('td', $customDate, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', ($type=='dp')?__('DP'):__('Angsuran'));
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $customGrandtotal, array(
                            'class' => 'text-right',
                        ));

                        if( !empty($rejected) ) {
                            $lblStatus = $this->Html->tag('span', __('Void'), array(
                                'class' => 'label label-danger',
                            ));
                        } else {
                            $lblStatus = $this->Html->tag('span', __('Dibayar'), array(
                                'class' => 'label label-success',
                            ));
                        }

                        echo $this->Html->tag('td', $lblStatus, array(
                            'class' => 'text-right',
                        ));
                        echo $this->Html->tag('td', $customCreated);
                ?>
                <td class="action">
                    <?php
                            if( empty($rejected) ){
                                switch ($type) {
                                    case 'dp':
                                        $action_edit = 'dp_edit';
                                        $action_detail = 'detail_dp_payment';
                                        $action_delete = 'dp_delete';
                                        break;
                                    
                                    default:
                                        $action_edit = 'payment_edit';
                                        $action_detail = 'detail_payment';
                                        $action_delete = 'payment_delete';
                                        break;
                                }
                                
                                echo $this->Html->link('Info', array(
                                    'controller' => 'leasings',
                                    'action' => $action_detail,
                                    $id
                                ), array(
                                    'class' => 'btn btn-info btn-xs'
                                ));
                                echo $this->Html->link(__('Edit'), array(
                                    'controller' => 'leasings',
                                    'action' => $action_edit,
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                ));

                                echo $this->Html->link('Void', array(
                                    'controller' => 'leasings',
                                    'action' => $action_delete,
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs ajaxModal',
                                    'data-action' => 'submit_form',
                                    'title' => __('Void Pembayaran Leasing'),
                                    'closing' => true,
                                    'periode' => $periode,
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