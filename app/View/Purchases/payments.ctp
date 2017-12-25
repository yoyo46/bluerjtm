<?php 
        $dataColumns = array(
            'transaction_date' => array(
                'name' => __('Tgl Pembayaran'),
            ),
            'code' => array(
                'name' => __('No Dokumen'),
            ),
            'supplier' => array(
                'name' => __('Supplier'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'grandtotal' => array(
                'name' => __('Total Dibayar'),
                'class' => 'text-center',
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/purchases/payments/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'purchases',
                    'action' => 'payment_add',
                    'admin' => false,
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
                            $id = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'nodoc');
                            $transactionDate = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'transaction_date');
                            $periode = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'transaction_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $note = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'note');
                            $grandtotal = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'grandtotal');
                            $transaction_status = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'transaction_status');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');
                            $grandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);
                            $customStatus = $this->Common->_callTransactionStatus($value, 'PurchaseOrderPayment');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'purchases',
                                'action' => 'payment_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting' )) ) {
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'purchases',
                                    'action' => 'payment_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                ));
                            }
                            if( !in_array($transaction_status, array( 'void' )) ) {
                                $customAction .= $this->Html->link('Void', array(
                                    'controller' => 'purchases',
                                    'action' => 'payment_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs ajaxModal',
                                    'data-action' => 'submit_form',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'title' => __('Void Pembayaran PO/SPK')
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
                        echo $this->Html->tag('td', $customAction, array(
                            'class' => 'action text-right',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '7'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>