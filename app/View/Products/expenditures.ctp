<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No Dokumen'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl Pengeluaran'),
                'class' => 'text-center',
            ),
            'spk' => array(
                'name' => __('No. SPK'),
            ),
            'type' => array(
                'name' => __('Jenis'),
            ),
            'nopol' => array(
                'name' => __('NoPol'),
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
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/expenditures/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'products',
                    'action' => 'expenditure_add',
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
                        $types = Configure::read('__Site.Spk.type');

                        foreach ($values as $key => $value) {
                            $spk = $this->Common->filterEmptyField($value, 'Spk');

                            $id = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'nodoc', '-');
                            $document_type = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'document_type');
                            $transactionDate = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'note');
                            $transaction_status = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'transaction_status');

                            $document_number = $this->Common->filterEmptyField($spk, 'nodoc', false, '-');
                            $nopol = $this->Common->filterEmptyField($spk, 'nopol', false, '-');

                            $staff = $this->Common->filterEmptyField($value, 'Staff', 'name');
                            $employe = $this->Common->filterEmptyField($value, 'Employe', 'full_name');
                            $type = $this->Common->filterEmptyField($types, $document_type);

                            $customStatus = $this->Common->_callTransactionStatus($value, 'ProductExpenditure');
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');
                            $noref = $this->Common->getNoRef($id);

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'products',
                                'action' => 'expenditure_detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( in_array($transaction_status, array( 'unposting', 'revised' )) ){
                                $customAction .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'products',
                                    'action' => 'expenditure_edit',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( $transaction_status <> 'void' ) {
                                $customAction .= $this->Html->link(__('Void'), array(
                                    'controller' => 'products',
                                    'action' => 'expenditure_toggle',
                                    $id,
                                    'void',
                                    'admin' => false,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'data-alert' => __('Anda yakin ingin membatalkan Pengeluaran ini?'),
                                ));
                            }
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $customDate, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $document_number);
                        echo $this->Html->tag('td', $type);
                        echo $this->Html->tag('td', $nopol);
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
                            'colspan' => '9'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>