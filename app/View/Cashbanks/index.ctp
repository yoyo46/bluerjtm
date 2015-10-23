<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No Dokumen'),
                'field_model' => 'CashBank.nodoc',
                'display' => true,
            ),
            'receiver' => array(
                'name' => __('Diterima/Dibayar kepada'),
                'field_model' => false,
                'display' => true,
            ),
            'tgl_cash_bank' => array(
                'name' => __('Tgl Transaksi'),
                'field_model' => 'CashBank.tgl_cash_bank',
                'class' => 'text-center',
                'display' => true,
            ),
            'receiving_cash_type' => array(
                'name' => __('Tipe Kas'),
                'field_model' => 'CashBank.receiving_cash_type',
                'class' => 'text-center',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => false,
                'class' => 'text-center',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
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
                            $type = $this->Common->filterEmptyField($value, 'CashBank', 'receiving_cash_type');
                            $is_revised = $this->Common->filterEmptyField($value, 'CashBank', 'is_revised');
                            $completed = $this->Common->filterEmptyField($value, 'CashBank', 'completed');

                            $name_cash = $this->Common->filterEmptyField($value, 'name_cash', false, '-');
                            $customDate = $this->Common->formatDate($tgl, 'd/m/Y');
                            $customType = strtoupper(str_replace('_', ' ', $type));
                            $customStatus = $this->CashBank->_callStatus($value);

                            $content = $this->Html->tag('td', $nodoc);
                            $content .= $this->Html->tag('td', $name_cash);
                            $content .= $this->Html->tag('td', $customDate, array(
                                'class' => 'text-center',
                            ));
                            $content .= $this->Html->tag('td', $customType, array(
                                'class' => 'text-center',
                            ));
                            $content .= $this->Html->tag('td', $customStatus, array(
                                'class' => 'text-center',
                            ));

                            if( !empty($is_revised) ){
                                $link = $this->Html->link(__('Ubah'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'cashbank_edit',
                                    $id,
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-info btn-xs'
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

                            if( empty($completed) ) {
                                $link .= $this->Html->link('Hapus', array(
                                    'controller' => 'cashbanks',
                                    'action' => 'cashbank_delete',
                                    $id
                                ), array(
                                    'escape' => false,
                                    'class' => 'btn btn-danger btn-xs'
                                ), __('Anda yakin ingin menghapus data ini?'));
                            }

                            $content .= $this->Html->tag('td', $link, array(
                                'class' => 'action'
                            ));
                            echo $this->Html->tag('tr', $content);
                        }
                    }else{
                        $content = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
                            'colspan' => 6,
                            'class' => 'alert alert-danger'
                        ));
                        echo $this->Html->tag('tr', $content);
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>