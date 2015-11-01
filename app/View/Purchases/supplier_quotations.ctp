<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('No Dokumen'),
                'field_model' => 'SupplierQuotation.nodoc',
                'display' => true,
            ),
            'supplier' => array(
                'name' => __('Supplier'),
                'field_model' => false,
                'display' => true,
            ),
            'available_date' => array(
                'name' => __('Tgl Berlaku'),
                'field_model' => false,
                'display' => true,
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'field_model' => false,
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'SupplierQuotation.status',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/purchases/supplier_quotations/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'controller' => 'purchases',
                    'action' => 'supplier_quotation_add',
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
                            $id = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'nodoc');
                            $availableFrom = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'available_from');
                            $availableTo = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'available_to');
                            $transactionDate = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'note');

                            $Vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customAvailable = $this->Common->combineDate($availableFrom, $availableTo );
                            $customDate = $this->Common->formatDate($transactionDate);
                            $customAction = $this->Html->link('Edit', array(
                                'controller' => 'purchases',
                                'action' => 'supplier_quotation_edit',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $customAction .= $this->Html->link(__('Hapus'), array(
                                'controller' => 'purchases',
                                'action' => 'supplier_quotation_toggle',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus barang ini?'));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $code);
                        echo $this->Html->tag('td', $name);
                        echo $this->Html->tag('td', $unit);
                        echo $this->Html->tag('td', $group);
                        echo $this->Html->tag('td', $customType);
                        echo $this->Html->tag('td', $customStatus);
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
                            'colspan' => '7'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>