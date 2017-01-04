<?php 
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'field_model' => 'CashBank.id',
                'class' => 'text-center',
                'style' => 'width: 10%;'
            ),
            'nodoc' => array(
                'name' => __('No. Doc'),
                'field_model' => 'CashBank.nodoc',
                'class' => 'text-center',
                'style' => 'width: 10%;'
            ),
            'receiver' => array(
                'name' => __('Diterima/Dibayar Kpd'),
                'style' => 'width: 20%;'
            ),
            'tgl_cash_bank' => array(
                'name' => __('Tgl Transaksi'),
                'field_model' => 'CashBank.tgl_cash_bank',
                'class' => 'text-center',
            ),
            'receiving_cash_type' => array(
                'name' => __('Tipe Kas'),
                'field_model' => 'CashBank.receiving_cash_type',
                'class' => 'text-center',
                'style' => 'width: 8%;'
            ),
            'description' => array(
                'name' => __('Keterangan'),
                'style' => 'width: 20%;'
            ),
            'grand_total' => array(
                'name' => __('Total'),
                'class' => 'text-center',
                'style' => 'width: 15%;'
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb(__('Kas/Bank'));
        echo $this->element('blocks/cashbanks/search_index');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'cashbanks',
                        'action' => 'cashbank_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div><!-- /.box-header -->
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
                            $id = $this->Common->filterEmptyField($value, 'CashBank', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'CashBank', 'nodoc');
                            $tgl = $this->Common->filterEmptyField($value, 'CashBank', 'tgl_cash_bank');
                            $periode = $this->Common->filterEmptyField($value, 'CashBank', 'tgl_cash_bank', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $type = $this->Common->filterEmptyField($value, 'CashBank', 'receiving_cash_type');
                            $grand_total = $this->Common->filterEmptyField($value, 'CashBank', 'grand_total');
                            $description = $this->Common->filterEmptyField($value, 'CashBank', 'description');

                            $is_revised = $this->Common->filterEmptyField($value, 'CashBank', 'is_revised');
                            $completed = $this->Common->filterEmptyField($value, 'CashBank', 'completed');
                            $is_rejected = $this->Common->filterEmptyField($value, 'CashBank', 'is_rejected');
                            $transaction_status = $this->Common->filterEmptyField($value, 'CashBank', 'transaction_status');

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $name_cash = $this->Common->filterEmptyField($value, 'name_cash', false, '-');
                            $customDate = $this->Common->formatDate($tgl, 'd/m/Y');
                            $customType = strtoupper(str_replace('_', ' ', $type));
                            $customStatus = $this->CashBank->_callStatus($value);
                            $customGrandTotal = $this->Common->getFormatPrice($grand_total, 0, 2);

                            $content = $this->Html->tag('td', $noref);
                            $content .= $this->Html->tag('td', $nodoc);
                            $content .= $this->Html->tag('td', $name_cash);
                            $content .= $this->Html->tag('td', $customDate, array(
                                'class' => 'text-center',
                            ));
                            $content .= $this->Html->tag('td', $customType, array(
                                'class' => 'text-center',
                            ));
                            $content .= $this->Html->tag('td', $description);
                            $content .= $this->Html->tag('td', $customGrandTotal, array(
                                'class' => 'text-right',
                            ));
                            $content .= $this->Html->tag('td', $customStatus, array(
                                'class' => 'text-center',
                            ));

                            if( ( !empty($is_revised) || $transaction_status == 'unposting' ) && empty($is_rejected) ){
                                $link = $this->Html->link(__('Edit'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'cashbank_edit',
                                    $id,
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-info btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-btn-replace' => array(
                                        'controller' => 'cashbanks',
                                        'action' => 'detail',
                                        $id
                                    ),
                                    'data-btn-replace-class' => 'btn btn-primary btn-xs',
                                    'data-btn-replace-label' => __('Detail'),
                                ));
                            } else {
                                $link = $this->Html->link('Detail', array(
                                    'controller' => 'cashbanks',
                                    'action' => 'detail',
                                    $id
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            // if( empty($completed) ) {
                            if( empty($is_rejected) ) {
                                $link .= $this->Html->link(__('Void'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'cashbank_delete',
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
                            'colspan' => 9,
                            'class' => 'alert alert-warning text-center'
                        ));
                        echo $this->Html->tag('tr', $content);
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>