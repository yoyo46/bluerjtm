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
                    'adjustment_products',
                    'admin' => false,
                ),
                'urlReset' => array(
                    'controller' => 'products',
                    'action' => 'adjustment_products',
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
                                $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                                $total_qty = $this->Common->filterEmptyField($value, 'Product', 'product_stock_cnt', 0);

                                $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $type = $this->Common->filterEmptyField($value, 'Product', 'type');
                                $is_serial_number = $this->Common->filterEmptyField($value, 'Product', 'is_serial_number');
                                $serial_numbers = $this->Common->filterEmptyField($value, 'Product', 'serial_numbers');

                                $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');

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
                            echo $this->Html->tag('td', $code.$this->Form->hidden(sprintf('ProductAdjustmentDetail.product_id.%s', $id), array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $name);
                            echo $this->Html->tag('td', $unit, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('ProductAdjustmentDetail.note.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $group, array(
                                'class' => 'removed',
                            ));
                            echo $this->Html->tag('td', $customType, array(
                                'class' => 'removed',
                            ));
                            echo $this->Html->tag('td', $total_qty, array(
                                'class' => 'text-center price_custom',
                                'rel' => 'qty-stock',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('ProductAdjustmentDetail.qty.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'input_number text-center price_custom',
                                'attributes' => array(
                                    'rel' => 'qty',
                                    'data-adjutment' => '.qty-difference',
                                ),
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', '0', array(
                                'class' => __('text-center hide qty-difference %s', $targetQty),
                                'data-target' => '.price_custom[rel=qty-stock]',
                                'data-minus' => '.price_custom[rel=qty]',
                                'data-disabled' => '.price_custom[rel=price]',
                                'data-display' => '[[\'.serial-number-in\', \'plus\'],[\'.serial-number-out\', \'minus\']]',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('ProductAdjustmentDetail.price.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'input_price text-right price_custom',
                                'attributes' => array(
                                    'rel' => 'price',
                                ),
                            )), array(
                                'class' => 'hide',
                            ));

                            if( !empty($is_serial_number) ) {
                                $lblSerialNumber = __('Masukan No. Seri %s', $this->Common->icon('plus-square', false, 'i', 'ml5'));

                                echo $this->Html->tag('td', $this->Html->tag('div', 
                                    $this->Html->link($lblSerialNumber, array(
                                        'controller'=> 'products', 
                                        'action' => 'adjust_serial_numbers',
                                        $id,
                                        'admin' => false,
                                        'bypass' => true,
                                    ), array(
                                        'escape' => false,
                                        'allow' => true,
                                        'class' => sprintf('ajaxCustomModal browse-docs serial-number-fill-%s', $id),
                                        'title' => __('Serial Number'),
                                        'data-action' => 'browse-form',
                                        'data-form' => '.adjust-form',
                                        'data-picker' => sprintf('.%s', $targetQty),
                                    )), array(
                                    'class' => 'serial-number-in display-adjust',
                                )).
                                $this->Html->tag('div', $this->Common->_callInputForm(__('ProductAdjustmentDetailSerialNumber.serial_numbers.%s', $id), array(
                                    'div' => false,
                                    'frameClass' => false,
                                    'class' => 'chosen-select form-control full',
                                    'multiple' => true,
                                    'options' => array(),
                                )).$this->Html->link($this->Common->icon('plus-square'), array(
                                    'controller'=> 'products', 
                                    'action' => 'stocks',
                                    $id,
                                    'admin' => false,
                                ), array(
                                    'escape' => false,
                                    'allow' => true,
                                    'class' => 'ajaxCustomModal browse-docs',
                                    'title' => __('Stok Barang'),
                                    'data-action' => 'browse-form',
                                )), array(
                                    'style' => 'display: none;',
                                    'class' => 'serial-number-out display-adjust pick-product-code',
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