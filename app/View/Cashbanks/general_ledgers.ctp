<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('No Dokumen'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl Transaksi'),
                'class' => 'text-center',
            ),
            // 'debit' => array(
            //     'name' => __('Debit'),
            //     'class' => 'text-right',
            // ),
            'total' => array(
                'name' => __('Total'),
                'class' => 'text-right',
            ),
            'note' => array(
                'name' => __('Keterangan'),
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
        echo $this->element('blocks/cashbanks/searchs/general_ledgers');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'controller' => 'cashbanks',
                    'action' => 'general_ledger_add',
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
                            $id = $this->Common->filterEmptyField($value, 'GeneralLedger', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'GeneralLedger', 'nodoc');
                            $transactionDate = $this->Common->filterEmptyField($value, 'GeneralLedger', 'transaction_date', false, true, array(
                                'date' => 'd M Y',
                            ));
                            $periode = $this->Common->filterEmptyField($value, 'GeneralLedger', 'transaction_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $note = $this->Common->filterEmptyField($value, 'GeneralLedger', 'note');
                            $status = $this->Common->filterEmptyField($value, 'GeneralLedger', 'status');
                            $transaction_status = $this->Common->filterEmptyField($value, 'GeneralLedger', 'transaction_status');
                            $debit_total = $this->Common->filterEmptyField($value, 'GeneralLedger', 'debit_total', 0, true, array(
                                'price' => array(
                                    'decimal' => 2,
                                ),
                            ));
                            $credit_total = $this->Common->filterEmptyField($value, 'GeneralLedger', 'credit_total', 0, true, array(
                                'price' => array(
                                    'decimal' => 2,
                                ),
                            ));
                            $allow_closing = $this->Common->filterEmptyField($value, 'GeneralLedger', 'AllowClosing');

                            $customStatus = $this->Common->_callTransactionStatus($value, 'GeneralLedger');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'cashbanks',
                                'action' => 'general_ledger_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( !empty($allow_closing) ) {
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'general_ledger_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            // if( in_array($transaction_status, array( 'unposting', 'revised' )) ){
                            if( !empty($allow_closing) && !in_array($transaction_status, array( 'void' )) ){
                                $customAction .= $this->Html->link(__('Hapus'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'general_ledger_toggle',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs ajaxModal',
                                    'data-action' => 'submit_form',
                                    'title' => __('Hapus'),
                                    'closing' => true,
                                    'periode' => $periode,
                                ), __('Anda yakin ingin menghapus data ini?'));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $transactionDate, array(
                            'class' => 'text-center',
                        ));
                        // echo $this->Html->tag('td', $debit_total, array(
                        //     'class' => 'text-right',
                        // ));
                        echo $this->Html->tag('td', $credit_total, array(
                            'class' => 'text-right',
                        ));
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