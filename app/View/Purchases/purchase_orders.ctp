<?php 
        $dataColumns = array(
            'transaction_date' => array(
                'name' => __('Tgl PO'),
            ),
            'code' => array(
                'name' => __('No Dokumen'),
                'field_model' => 'PurchaseOrder.nodoc',
            ),
            'supplier' => array(
                'name' => __('Supplier'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'PurchaseOrder.status',
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/purchases/purchase_orders/forms/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'controller' => 'purchases',
                    'action' => 'purchase_order_add',
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
                            $id = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'nodoc');
                            $transactionDate = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'transaction_date');
                            $note = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'note');
                            $status = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'status');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customStatus = $this->Purchase->_callStatus($value, 'PurchaseOrder');
                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');

                            $customAction = $this->Html->link(__('Detail'), array(
                                'controller' => 'purchases',
                                'action' => 'purchase_order_edit',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $customAction .= $this->Html->link(__('Hapus'), array(
                                'controller' => 'purchases',
                                'action' => 'purchase_order_toggle',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus PO ini?'));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $vendor);
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
                            'colspan' => '7'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>