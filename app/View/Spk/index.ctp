<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No SPK'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl SPK'),
            ),
            'document_type' => array(
                'name' => __('Jenis'),
            ),
            'vendor' => array(
                'name' => __('Supplier'),
            ),
            'nolaka' => array(
                'name' => __('LAKA'),
            ),
            'nopol' => array(
                'name' => __('NoPol'),
            ),
            'est' => array(
                'name' => __('Estimasi'),
            ),
            'finish' => array(
                'name' => __('Tgl Selesai'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'payment_status' => array(
                'name' => __('Pembayaran'),
                'class' => 'text-center',
            ),
        );

        // if( !empty($spk_internal_policy) && $spk_internal_policy == 'receipt' ) {
        //     $dataColumns = array_merge($dataColumns, array(
        //         'receipt_status' => array(
        //             'name' => __('Status Penerimaan'),
        //             'class' => 'text-center',
        //         ),
        //     ));
        // }

        $dataColumns = array_merge($dataColumns, array(
            'action' => array(
                'name' => __('Action'),
                'width' => '15%',
                'class' => 'text-left',
            ),
        ));

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/spk/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'spk',
                    'action' => 'add',
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
                            $id = $this->Common->filterEmptyField($value, 'Spk', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'Spk', 'nodoc', '-');
                            $transactionDate = $this->Common->filterEmptyField($value, 'Spk', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'Spk', 'note', '-');
                            $document_type = $this->Common->filterEmptyField($value, 'Spk', 'document_type');
                            $nopol = $this->Common->filterEmptyField($value, 'Spk', 'nopol', '-');
                            $nolaka = $this->Common->filterEmptyField($value, 'Laka', 'nodoc', '-');
                            $estimation_date = $this->Common->filterEmptyField($value, 'Spk', 'estimation_date', '-', true, array(
                                'date' => 'd M Y',
                            ));
                            $complete_date = $this->Common->filterEmptyField($value, 'Spk', 'complete_date', '-', true, array(
                                'date' => 'd M Y',
                            ));
                            $receipt_status = $this->Common->filterEmptyField($value, 'Spk', 'receipt_status', 'none');
                            $transaction_status = $this->Common->filterEmptyField($value, 'Spk', 'transaction_status');

                            if( $document_type == 'eksternal' ) {
                                $paymentStatus = $this->Common->_callTransactionStatus($value, 'Spk', 'payment_status');
                            } else {
                                $paymentStatus = 'N/A';
                            }

                            $document_type = ucwords($document_type);
                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name', '-');

                            $customStatus = $this->Common->_callTransactionStatus($value, 'Spk');
                            $customDate = $this->Common->formatDate($transactionDate, 'd M Y');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'spk',
                                'action' => 'detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( $receipt_status == 'none' && $transaction_status == 'open' ) {
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'spk',
                                    'action' => 'edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                $customAction .= $this->Html->link(__('Void'), array(
                                    'controller' => 'spk',
                                    'action' => 'toggle',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => __('Anda yakin ingin membatalkan SPK ini?'),
                                ));
                            }

                            if( in_array($transaction_status, array( 'closed' )) ) {
                                $customAction .= $this->Html->link(__('Selesai'), array(
                                    'controller' => 'spk',
                                    'action' => 'completed',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-success btn-xs ajaxCustomModal',
                                    'title' => __('SPK Telah Selesai'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $document_type);
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $nolaka);
                        echo $this->Html->tag('td', $nopol);
                        echo $this->Html->tag('td', $estimation_date);
                        echo $this->Html->tag('td', $complete_date);
                        echo $this->Html->tag('td', $note);
                        echo $this->Html->tag('td', $customStatus, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $paymentStatus, array(
                            'class' => 'text-center',
                        ));

                        echo $this->Html->tag('td', $customAction, array(
                            'class' => 'action text-left',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
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