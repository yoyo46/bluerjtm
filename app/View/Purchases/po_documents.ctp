<div id="wrapper-modal-write">
    <?php 
            $modelName = false;
            $dataColumns = array(
                'check-box' => array(
                    'name' => '',
                    'class' => 'text-center',
                ),
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
                'total' => array(
                    'name' => __('Total'),
                    'class' => 'text-center',
                ),
                'paid' => array(
                    'name' => __('Sudah Dibayar'),
                    'class' => 'text-center',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/purchases/payments/forms/search_po');
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover document-picker">
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
                                $note = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'note');
                                $grandtotal = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'grandtotal');
                                $total_paid = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'total_paid');
                                $total_remain = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'total_remain');

                                if( !empty($total_remain) ) {
                                    $transaction_date = $this->Common->formatDate($transaction_date, 'd/m/Y');
                                    $grandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);
                                    $total_paid = $this->Common->getFormatPrice($total_paid, 0, 2);
                                    $totalRemainFormat = $this->Common->getFormatPrice($total_remain, 0, 2);
                ?>
                <tr class="pick-document item" rel="<?php echo $id; ?>" data-type="single-total">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $nodoc.$this->Form->hidden('PurchaseOrderPaymentDetail.purchase_order_id.', array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $transaction_date, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $note);
                            echo $this->Html->tag('td', $grandtotal, array(
                                'class' => 'removed text-right',
                            ));
                            echo $this->Html->tag('td', $total_paid, array(
                                'class' => 'removed text-right',
                            ));
                            echo $this->Html->tag('td', $totalRemainFormat.$this->Form->hidden('PurchaseOrder.total_remain.', array(
                                'value' => $total_remain,
                            )), array(
                                'class' => 'hide text-right',
                            ));

                            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderPaymentDetail.price.', false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'text-right price',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                ),
                            )), array(
                                'class' => 'text-center hide',
                            ));
                            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            )), array(
                                'class' => 'actions hide text-center',
                            ));
                    ?>
                </tr>
                <?php
                            }
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
            echo $this->element('pagination', array(
                'options' => array(
                    'urlClass' => 'ajaxCustomModal',
                    'urlTitle' => __('Daftar PO'),
                ),
            ));
    ?>
</div>