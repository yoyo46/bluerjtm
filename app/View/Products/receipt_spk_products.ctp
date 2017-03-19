<?php 
        $data = $this->request->data;
        $modelName = !empty($modelName)?$modelName:'SpkProduct';
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
                    'receipt_document_products',
                    $transaction_id,
                    $nodoc,
                    $document_type,
                    'admin' => false,
                ),
                'urlReset' => array(
                    'controller' => 'products',
                    'action' => 'receipt_document_products',
                    $transaction_id,
                    $nodoc,
                    $document_type,
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

                                $detail_id = $this->Common->filterEmptyField($value, $modelName, 'id');
                                $qty = $this->Common->filterEmptyField($value, $modelName, 'qty');
                                $in_qty = $this->Common->filterEmptyField($value, $modelName, 'in_qty', 0);

                                $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $type = $this->Common->filterEmptyField($value, 'Product', 'type');
                                $is_serial_number = $this->Common->filterEmptyField($value, 'Product', 'is_serial_number');
                                $serial_numbers = $this->Common->filterEmptyField($value, 'Product', 'serial_numbers');

                                $unit = $this->Common->filterEmptyField($product, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($product, 'ProductCategory', 'name');

                                $customType = $this->Common->unSlug($type);
                                $targetQty = __('input-qty-%s', $id);

                                $qty_remain = $qty - $in_qty;
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>" data-type="single-total">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $code.$this->Form->hidden(sprintf('ProductReceiptDetail.product_id.%s', $id), array(
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
                            echo $this->Html->tag('td', $qty, array(
                                'class' => 'text-center price_custom hide',
                                'rel' => 'qty-doc',
                            ));
                            echo $this->Html->tag('td', $in_qty, array(
                                'class' => 'text-center hide price_custom',
                                'rel' => 'qty-in',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('ProductReceiptDetail.qty.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => __('input_number text-center price_custom %s', $targetQty),
                                'attributes' => array(
                                    'rel' => 'qty',
                                    'value' => $qty_remain,
                                ),
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $qty_remain, array(
                                'class' => 'text-center removed',
                            ));

                            if( !empty($is_serial_number) ) {
                                $lblSerialNumber = __('Masukan No. Seri %s', $this->Common->icon('plus-square', false, 'i', 'ml5'));
                                echo $this->Html->tag('td', $this->Html->link($lblSerialNumber, array(
                                    'controller'=> 'products', 
                                    'action' => 'receipt_serial_numbers',
                                    $id,
                                    'admin' => false,
                                    'bypass' => true,
                                ), array(
                                    'escape' => false,
                                    'allow' => true,
                                    'class' => sprintf('ajaxCustomModal browse-docs serial-number-fill-%s', $id),
                                    'title' => __('Serial Number'),
                                    'data-action' => 'browse-form',
                                    'data-form' => '.receipt-form',
                                    'data-picker' => sprintf('.%s', $targetQty),
                                )), array(
                                    'class' => 'text-center hide',
                                ));
                            } else {
                                echo $this->Html->tag('td', __('Automatic'), array(
                                    'class' => 'text-center hide',
                                ));
                            }

                            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            )), array(
                                'class' => 'actions text-center hide',
                            ));
                            echo $this->Form->hidden(sprintf('ProductReceiptDetail.document_detail_id.%s', $id), array(
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