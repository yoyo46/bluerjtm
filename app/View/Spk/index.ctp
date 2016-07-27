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
            'action' => array(
                'name' => __('Action'),
                'width' => '15%',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/receipts/forms/search');
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
                            $note = $this->Common->filterEmptyField($value, 'Spk', 'note');
                            $transaction_status = $this->Common->filterEmptyField($value, 'Spk', 'transaction_status');
                            $document_type = $this->Common->filterEmptyField($value, 'Spk', 'document_type');
                            $nopol = $this->Common->filterEmptyField($value, 'Spk', 'nopol');
                            $est_start_date = $this->Common->filterEmptyField($value, 'Spk', 'est_start_date');
                            $est_end_date = $this->Common->filterEmptyField($value, 'Spk', 'est_end_date');
                            $complete_date = $this->Common->filterEmptyField($value, 'Spk', 'complete_date', false, true, array(
                                'date' => 'd/m/Y',
                            ));

                            $document_type = ucwords($document_type);
                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');
                            $est_date = $this->Common->combineDate($est_start_date, $est_end_date);

                            $customStatus = $this->Spk->_callStatus($value, 'Spk');
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'spk',
                                'action' => 'detail',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            $customAction .= $this->Html->link(__('Edit'), array(
                                'controller' => 'spk',
                                'action' => 'edit',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $customAction .= $this->Html->link(__('Hapus'), array(
                                'controller' => 'spk',
                                'action' => 'toggle',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus SPK ini?'));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $document_type);
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $nopol);
                        echo $this->Html->tag('td', $est_date);
                        echo $this->Html->tag('td', $complete_date);
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
                            'colspan' => '10'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>