<?php 
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'field_model' => 'Debt.id',
                'style' => 'width: 10%;'
            ),
            'nodoc' => array(
                'name' => __('No. Doc'),
                'field_model' => 'Debt.nodoc',
                'style' => 'width: 10%;'
            ),
            'transaction_date' => array(
                'name' => __('Tgl Transaksi'),
                'field_model' => 'Debt.transaction_date',
                'class' => 'text-center',
                'style' => 'width: 10%;'
            ),
            'coa_id' => array(
                'name' => __('Account Kas/Bank'),
            ),
            'staff' => array(
                'name' => __('Karyawan'),
                'style' => 'width: 20%;'
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'width: 15%;'
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

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/debt/search/index');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'debt',
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
                            $id = Common::hashEmptyField($value, 'Debt.id');
                            $nodoc = Common::hashEmptyField($value, 'Debt.nodoc');
                            $tgl = Common::hashEmptyField($value, 'Debt.transaction_date');
                            $periode = Common::hashEmptyField($value, 'Debt.transaction_date', false, false, array(
                                'date' => 'Y-m',
                            ));
                            $grand_total = Common::hashEmptyField($value, 'Debt.total');
                            $note = Common::hashEmptyField($value, 'Debt.note', '-');
                            $coa_code = Common::hashEmptyField($value, 'Coa.coa_name');

                            $transaction_status = Common::hashEmptyField($value, 'Debt.transaction_status');
                            $draft_paid_status = Common::hashEmptyField($value, 'Debt.draft_paid_status');

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $customDate = $this->Common->formatDate($tgl, 'd M Y');
                            $statusDebt = $this->Common->_callTransactionStatus($value, 'Debt', 'transaction_status');
                            $customGrandTotal = $this->Common->getFormatPrice($grand_total, 0, 2);
                            
                            $staff_names = Set::extract('/DebtDetail/ViewStaff/name_code', $value);
                            $staff_name = !empty($staff_names)?implode(', ', $staff_names):'-';

                            $content = $this->Html->tag('td', $noref);
                            $content .= $this->Html->tag('td', $nodoc);
                            $content .= $this->Html->tag('td', $customDate, array(
                                'class' => 'text-center',
                            ));
                            $content .= $this->Html->tag('td', $coa_code);
                            $content .= $this->Html->tag('td', $staff_name);
                            $content .= $this->Html->tag('td', $note);
                            $content .= $this->Html->tag('td', $customGrandTotal, array(
                                'class' => 'text-right',
                            ));
                            $content .= $this->Html->tag('td', $statusDebt, array(
                                'class' => 'text-center',
                            ));

                            if( $transaction_status == 'unposting' ){
                                $link = $this->Html->link(__('Edit'), array(
                                    'controller' => 'debt',
                                    'action' => 'edit',
                                    $id,
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-info btn-xs',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-btn-replace' => array(
                                        'controller' => 'debt',
                                        'action' => 'detail',
                                        $id
                                    ),
                                    'data-btn-replace-class' => 'btn btn-primary btn-xs',
                                    'data-btn-replace-label' => __('Detail'),
                                ));
                            } else {
                                $link = $this->Html->link('Detail', array(
                                    'controller' => 'debt',
                                    'action' => 'detail',
                                    $id
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( $transaction_status <> 'void' && $draft_paid_status == 'none' ) {
                                $link .= $this->Html->link(__('Void'), array(
                                    'controller' => 'debt',
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