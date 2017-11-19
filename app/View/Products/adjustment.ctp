<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('No Doc'),
                'width' => '15%',
            ),
            'transaction_date' => array(
                'name' => __('Tgl'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'total' => array(
                'name' => __('Total'),
                'class' => 'text-center',
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
        echo $this->element('blocks/products/adjustment/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add_multiple' => array(
                    array(
                        'label' => __('Tambah'),
                        'url' => array(
                            'action' => 'adjustment_add',
                        ),
                    ),
                    array(
                        'label' => __('Import'),
                        'url' => array(
                            'action' => 'adjustment_import',
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
                            $id = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'nodoc');
                            $transactionDate = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'note', '-');
                            $total = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'total', '-');
                            $transaction_status = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'transaction_status');
                            $disabled_void = Common::hashEmptyField($value, 'ProductAdjustment.disabled_void');

                            $customStatus = $this->Common->_callTransactionStatus($value, 'ProductAdjustment');
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'action' => 'adjustment_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting')) ){
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'action' => 'adjustment_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                ));
                            }

                            if( $transaction_status <> 'void' && empty($disabled_void) ) {
                                $customAction .= $this->Html->link(__('Void'), array(
                                    'action' => 'adjustment_toggle',
                                    $id,
                                    'void',
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => __('Anda yakin ingin membatalkan dokumen ini?'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $note);
                        echo $this->Html->tag('td', $total, array(
                            'class' => 'text-center',
                        ));
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