<?php 
        $dataColumns = array(
            'transaction_date' => array(
                'name' => __('Tgl PO'),
            ),
            'code' => array(
                'name' => __('No PO'),
            ),
            'supplier' => array(
                'name' => __('Supplier'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'grandtotal' => array(
                'name' => __('Grandtotal'),
                'class' => 'text-center',
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'penerimaan' => array(
                'name' => __('Penerimaan'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'width' => '15%',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/purchases/purchase_orders/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add_multiple' => array(
                    array(
                        'label' => __('Barang'),
                        'url' => array(
                            'controller' => 'purchases',
                            'action' => 'purchase_order_add',
                            'admin' => false,
                        ),
                    ),
                    array(
                        'label' => __('Asset'),
                        'url' => array(
                            'controller' => 'assets',
                            'action' => 'purchase_order_add',
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
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'nodoc');
                            $transactionDate = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'transaction_date');
                            $periode = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'transaction_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $note = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'note');
                            $grandtotal = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'grandtotal');
                            $is_asset = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'is_asset');
                            $transaction_status = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'transaction_status');
                            $receipt_status = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'receipt_status');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customStatus = $this->Common->_callTransactionStatus($value, 'PurchaseOrder');
                            $customReceipt = $this->Common->_callStatusReceipt($value, 'PurchaseOrder');
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');
                            $grandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);

                            if( !empty($is_asset) ) {
                                $controller = 'assets';
                            } else {
                                $controller = 'purchases';
                            }

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => $controller,
                                'action' => 'purchase_order_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting', 'revised' )) ){
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => $controller,
                                    'action' => 'purchase_order_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                ));
                            }
                            if( in_array($transaction_status, array( 'unposting', 'revised', 'posting', 'approved' )) && $receipt_status == 'none' ){
                                $customAction .= $this->Html->link(__('Void'), array(
                                    'controller' => 'purchases',
                                    'action' => 'purchase_order_toggle',
                                    $id,
                                    'void',
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-alert' => __('Anda yakin ingin membatalkan PO ini?'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $note);
                        echo $this->Html->tag('td', $grandtotal, array(
                            'class' => 'text-right',
                        ));
                        echo $this->Html->tag('td', $customStatus, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customReceipt, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customAction, array(
                            'class' => 'action',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>