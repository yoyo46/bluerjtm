<div id="wrapper-modal-write">
    <?php 
            $modelName = false;
            $dataColumns = array(
                'nodoc' => array(
                    'name' => __('No Dokumen'),
                ),
                'date' => array(
                    'name' => __('Tanggal'),
                    'class' => 'text-center',
                ),
                'note' => array(
                    'name' => __('Keterangan'),
                ),
                'qty' => array(
                    'name' => __('Total Qty'),
                    'class' => 'text-center',
                ),
                'total' => array(
                    'name' => __('Grandtotal'),
                    'class' => 'text-center',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/retur/forms/search_po');
    ?>
    <div class="box-body table-responsive browse-form">
        <table class="table table-hover">
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
                                $transaction_date = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'transaction_date');
                                $note = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'note', '-');
                                $total_qty = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'total_qty');
                                $grandtotal = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'grandtotal', 0, false, array(
                                    'price' => true,
                                ));

                                $transaction_date = $this->Common->formatDate($transaction_date, 'd/m/Y');
                ?>
                <tr data-value="<?php echo $nodoc; ?>" data-change="#document-number" data-form=".retur-form" data-wrapper-write=".wrapper-table-documents" data-duplicate="false" data-id="<?php echo $id; ?>" >
                    <?php
                            echo $this->Html->tag('td', $nodoc.$this->Form->hidden('PurchaseOrderPaymentDetail.purchase_order_id.', array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $transaction_date, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $note);
                            echo $this->Html->tag('td', $total_qty, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $grandtotal, array(
                                'class' => 'text-right',
                            ));
                    ?>
                </tr>
                <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                            'colspan' => 7,
                            'class' => 'text-center alert alert-warning',
                        )));
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php
            if(!empty($values)){
                echo $this->element('pagination', array(
                    'options' => array(
                        'urlClass' => 'ajaxCustomModal',
                        'urlTitle' => __('Daftar PO'),
                    ),
                ));
            }
    ?>
</div>