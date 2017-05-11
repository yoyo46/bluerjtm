<?php 
        $data = $this->request->data;
        $nodoc = !empty($nodoc)?$nodoc:false;
        $transaction_id = !empty($transaction_id)?$transaction_id:0;
?>
<div id="wrapper-modal-write" class="document-picker">
    <?php 
            $dataColumns = array(
                'check-box' => array(
                    'name' => $this->Form->checkbox('checkbox_all', array(
                        'class' => 'checkAll'
                    )),
                    'class' => 'text-center',
                ),
                'code' => array(
                    'name' => __('Kode'),
                ),
                'name' => array(
                    'name' => __('Nama'),
                ),
                'unit' => array(
                    'name' => __('Satuan'),
                    'class' => 'text-center',
                ),
                'group' => array(
                    'name' => __('Grup'),
                ),
                'type' => array(
                    'name' => __('Tipe'),
                ),
                'qty' => array(
                    'name' => __('Qty'),
                    'class' => 'text-center',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/expenditures/forms/search_products', array(
                'urlForm' => array(
                    'controller' => 'products',
                    'action' => 'search',
                    'retur_document_products',
                    $transaction_id,
                    $nodoc,
                    'po',
                    'admin' => false,
                ),
                'urlReset' => array(
                    'controller' => 'products',
                    'action' => 'retur_document_products',
                    $transaction_id,
                    $nodoc,
                    'po',
                    'admin' => false,
                ),
            ));
    ?>
    <div class="box-body table-responsive">
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
                                $product = $this->Common->filterEmptyField($value, 'Product');

                                $detail_id = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'id');
                                $total_qty = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'total_qty');
                                $qty = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');
                                $retur_qty = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'retur_qty', 0);

                                $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $type = $this->Common->filterEmptyField($value, 'Product', 'type');

                                $unit = $this->Common->filterEmptyField($product, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($product, 'ProductCategory', 'name');

                                $customType = $this->Common->unSlug($type);
                                $targetQty = __('input-qty-%s', $id);
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>" data-type="single-total">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $code.$this->Form->hidden(sprintf('ProductReturDetail.product_id.%s', $id), array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $name);
                            echo $this->Html->tag('td', $unit, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $group, array(
                                'class' => 'removed',
                            ));
                            echo $this->Html->tag('td', $customType, array(
                                'class' => 'removed',
                            ));
                            echo $this->Html->tag('td', $total_qty, array(
                                'class' => 'text-center price_custom',
                                'rel' => 'qty-doc',
                            ));
                            echo $this->Html->tag('td', $retur_qty, array(
                                'class' => 'text-center hide price_custom',
                                'rel' => 'qty-remain',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('ProductReturDetail.qty.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => __('input_number text-center price_custom %s', $targetQty),
                                'attributes' => array(
                                    'rel' => 'qty',
                                    'value' => $qty,
                                ),
                            )), array(
                                'class' => 'hide',
                            ));

                            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            )), array(
                                'class' => 'actions text-center hide',
                            ));
                            echo $this->Form->hidden(sprintf('ProductReturDetail.document_detail_id.%s', $id), array(
                                'value' => $detail_id,
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
            echo $this->element('pagination', array(
                'options' => array(
                    'urlClass' => 'ajaxCustomModal',
                    'urlTitle' => __('Daftar Barang'),
                ),
            ));
    ?>
</div>