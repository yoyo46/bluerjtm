<?php 
        $dataColumns = array(
            'transaction_date' => array(
                'name' => __('Tgl Penjualan'),
            ),
            'code' => array(
                'name' => __('No Dokumen'),
            ),
            'coa' => array(
                'name' => __('Bank'),
            ),
            'transfer_date' => array(
                'name' => __('Tgl Transfer'),
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
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/assets/searchs/sells');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'assets',
                    'action' => 'sell_add',
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
                            $id = $this->Common->filterEmptyField($value, 'AssetSell', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'AssetSell', 'nodoc');
                            $transactionDate = $this->Common->filterEmptyField($value, 'AssetSell', 'transaction_date');
                            $periode = $this->Common->filterEmptyField($value, 'AssetSell', 'transaction_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $transDate = $this->Common->filterEmptyField($value, 'AssetSell', 'transfer_date');
                            $note = $this->Common->filterEmptyField($value, 'AssetSell', 'note');
                            $grandtotal = $this->Common->filterEmptyField($value, 'AssetSell', 'grandtotal');
                            $transaction_status = $this->Common->filterEmptyField($value, 'AssetSell', 'transaction_status');
                            $bank = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');

                            $customStatus = $this->Common->_callTransactionStatus($value, 'AssetSell');
                            $customDate = $this->Common->formatDate($transactionDate, 'd M Y');
                            $customTransDate = $this->Common->formatDate($transDate, 'd M Y');
                            $grandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'assets',
                                'action' => 'sell_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting' )) ) {
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'assets',
                                    'action' => 'sell_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                ));
                                $customAction .= $this->Html->link(__('Hapus'), array(
                                    'controller' => 'assets',
                                    'action' => 'sell_toggle',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-alert' => __('Anda yakin ingin menghapus transaksi ini?'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $bank);
                        echo $this->Html->tag('td', $customTransDate);
                        echo $this->Html->tag('td', $note);
                        echo $this->Html->tag('td', $grandtotal, array(
                            'class' => 'text-right',
                        ));
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
                            'colspan' => '8'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>