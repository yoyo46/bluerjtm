<?php 
        $dataColumns = array(
            'transaction_date' => array(
                'name' => __('Tgl Quotation'),
            ),
            'code' => array(
                'name' => __('No Dokumen'),
            ),
            'supplier' => array(
                'name' => __('Supplier'),
            ),
            'available_date' => array(
                'name' => __('Tgl Berlaku'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
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
        echo $this->element('blocks/purchases/supplier_quotations/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'controller' => 'purchases',
                    'action' => 'supplier_quotation_add',
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
                            $id = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'nodoc');
                            $availableFrom = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'available_from');
                            $availableTo = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'available_to');
                            $transactionDate = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'note');
                            $status = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'status');
                            $transaction_status = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'transaction_status');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customAvailable = $this->Common->getCombineDate($availableFrom, $availableTo );
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');
                            $customStatus = $this->Common->_callTransactionStatus($value, 'SupplierQuotation');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'purchases',
                                'action' => 'supplier_quotation_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting', 'revised' )) ){
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'purchases',
                                    'action' => 'supplier_quotation_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                                $customAction .= $this->Html->link(__('Hapus'), array(
                                    'controller' => 'purchases',
                                    'action' => 'supplier_quotation_toggle',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => __('Anda yakin ingin menghapus quotation ini?'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $customAvailable);
                        echo $this->Html->tag('td', $note);
                        echo $this->Html->tag('td', $customStatus, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customAction, array(
                            'class' => 'action text-center',
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