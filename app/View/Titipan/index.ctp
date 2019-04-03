<?php 
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'field_model' => 'Titipan.id',
                'style' => 'width: 10%;'
            ),
            'nodoc' => array(
                'name' => __('No. Doc'),
                'field_model' => 'Titipan.nodoc',
                'style' => 'width: 15%;'
            ),
            'transaction_date' => array(
                'name' => __('Tgl Transaksi'),
                'field_model' => 'Titipan.transaction_date',
                'class' => 'text-center',
                'style' => 'width: 10%;'
            ),
            'ttuj_payment_nodoc' => array(
                'name' => __('No. Pemb. Komisi'),
                'style' => 'width: 15%;'
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'field_model' => 'Titipan.note',
                'class' => 'text-left',
            ),
            'grandtotal' => array(
                'name' => __('Total'),
                'class' => 'text-center',
                'style' => 'width: 15%;'
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
                'style' => 'width: 10%;'
            ),
            'action' => array(
                'name' => __('Action'),
                'style' => 'width: 10%;'
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/titipan/search/index');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'titipan',
                    'action' => 'add',
                    'admin' => false,
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table red table-hover sorting">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = Common::hashEmptyField($value, 'Titipan.id');
                            $nodoc = Common::hashEmptyField($value, 'Titipan.nodoc');
                            $ttuj_payment_nodoc = Common::hashEmptyField($value, 'TtujPayment.nodoc');
                            $tgl = Common::hashEmptyField($value, 'Titipan.transaction_date');
                            $periode = Common::hashEmptyField($value, 'Titipan.transaction_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $grand_total = Common::hashEmptyField($value, 'Titipan.grandtotal');
                            $note = Common::hashEmptyField($value, 'Titipan.note', '-');

                            $transaction_status = Common::hashEmptyField($value, 'Titipan.transaction_status');

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $customDate = $this->Common->formatDate($tgl, 'd M Y');
                            $statusTitipan = $this->Common->_callTransactionStatus($value, 'Titipan', 'transaction_status');
                            $customGrandTotal = $this->Common->getFormatPrice($grand_total, 0, 2);

                            if( !empty($ttuj_payment_nodoc) ) {
                                $ttuj_payment_id = Common::hashEmptyField($value, 'TtujPayment.id');
                                $ttuj_payment_id = str_pad($ttuj_payment_id, 6, '0', STR_PAD_LEFT);

                                $tmp_ttuj_payment_nodoc = $this->Html->link($ttuj_payment_nodoc, array(
                                    'controller' => 'revenues',
                                    'action' => 'ttuj_payments',
                                    'uang_jalan_commission',
                                    'noref' => $ttuj_payment_id,
                                ), array(
                                    'target' => '_blank',
                                ));
                            } else {
                                $tmp_ttuj_payment_nodoc = '-';
                            }

                            $content = $this->Html->tag('td', $noref);
                            $content .= $this->Html->tag('td', $nodoc);
                            $content .= $this->Html->tag('td', $customDate, array(
                                'class' => 'text-center',
                            ));
                            $content .= $this->Html->tag('td', $tmp_ttuj_payment_nodoc);
                            $content .= $this->Html->tag('td', $note);
                            $content .= $this->Html->tag('td', $customGrandTotal, array(
                                'class' => 'text-right',
                            ));
                            $content .= $this->Html->tag('td', $statusTitipan, array(
                                'class' => 'text-center',
                            ));

                            if( $transaction_status == 'unposting' ){
                                $link = $this->Html->link(__('Edit'), array(
                                    'controller' => 'titipan',
                                    'action' => 'edit',
                                    $id,
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-info btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-btn-replace' => array(
                                        'controller' => 'titipan',
                                        'action' => 'detail',
                                        $id
                                    ),
                                    'data-btn-replace-class' => 'btn btn-primary btn-xs',
                                    'data-btn-replace-label' => __('Detail'),
                                ));
                            } else {
                                $link = $this->Html->link('Detail', array(
                                    'controller' => 'titipan',
                                    'action' => 'detail',
                                    $id
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( $transaction_status <> 'void' && empty($ttuj_payment_nodoc) ) {
                                $link .= $this->Html->link(__('Void'), array(
                                    'controller' => 'titipan',
                                    'action' => 'delete',
                                    $id
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-alert' => __('Anda yakin ingin void data ini?'),
                                ));
                            }

                            $content .= $this->Html->tag('td', $link, array(
                                'class' => 'action'
                            ));
                            echo $this->Html->tag('tr', $content);
                        }
                    }else{
                        $content = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
                            'colspan' => 6,
                            'class' => 'alert alert-warning text-center'
                        ));
                        echo $this->Html->tag('tr', $content);
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>