<?php 
        $dataColumns = array(
            'noretur' => array(
                'name' => __('No. Dokumen'),
            ),
            'nodoc' => array(
                'name' => __('No. Dok Ref'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl Retur'),
            ),
            'supplier' => array(
                'name' => __('Supplier'),
            ),
            'employe' => array(
                'name' => __('Dicek Oleh'),
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
                'width' => '15%',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/retur/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'products',
                    'action' => 'retur_add',
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
                            $id = $this->Common->filterEmptyField($value, 'ProductRetur', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'ProductRetur', 'nodoc', '-');
                            $transactionDate = $this->Common->filterEmptyField($value, 'ProductRetur', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'ProductRetur', 'note');
                            $transaction_status = $this->Common->filterEmptyField($value, 'ProductRetur', 'transaction_status');
                            $document_number = $this->Common->filterEmptyField($value, 'ProductRetur', 'document_number', '-');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');
                            $employe = $this->Common->filterEmptyField($value, 'Employe', 'full_name');

                            $customStatus = $this->Common->_callTransactionStatus($value, 'ProductRetur');
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');
                            $noref = $this->Common->getNoRef($id);

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'products',
                                'action' => 'retur_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting', 'revised' )) ){
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'products',
                                    'action' => 'retur_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( empty($qty_use) && $transaction_status <> 'void' ) {
                                $customAction .= $this->Html->link(__('Void'), array(
                                    'controller' => 'products',
                                    'action' => 'retur_toggle',
                                    $id,
                                    'void',
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => __('Anda yakin ingin membatalkan Retur ini?'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        // echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $document_number);
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $employe);
                        echo $this->Html->tag('td', $note);
                        echo $this->Html->tag('td', $customStatus, array(
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
                            'colspan' => '9'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>