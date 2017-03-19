<?php 
        $data = $this->request->data;
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

            echo $this->element('blocks/products/expenditures/forms/search_products');
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

                                $spk_product_id = $this->Common->filterEmptyField($value, 'SpkProduct', 'id');
                                $qty = $this->Common->filterEmptyField($value, 'SpkProduct', 'qty');
                                $out_qty = $this->Common->filterEmptyField($value, 'SpkProduct', 'out_qty', 0);

                                $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $type = $this->Common->filterEmptyField($value, 'Product', 'type');
                                $is_serial_number = $this->Common->filterEmptyField($value, 'Product', 'is_serial_number');
                                $serial_numbers = $this->Common->filterEmptyField($value, 'Product', 'serial_numbers');

                                $unit = $this->Common->filterEmptyField($product, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($product, 'ProductCategory', 'name');

                                $customType = $this->Common->unSlug($type);
                                $qty_remain = $qty - $out_qty;

                                if( !empty($qty_remain) ) {
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>" data-type="single-total">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $code.$this->Form->hidden(sprintf('ProductExpenditureDetail.product_id.%s', $id), array(
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
                                'class' => 'text-center price_custom',
                                'rel' => 'qty-spk',
                            ));
                            echo $this->Html->tag('td', $out_qty, array(
                                'class' => 'text-center hide price_custom',
                                'rel' => 'qty-remain',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('ProductExpenditureDetail.qty.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'input_number text-center price_custom',
                                'attributes' => array(
                                    'rel' => 'qty',
                                    'value' => $qty_remain,
                                ),
                            )), array(
                                'class' => 'hide',
                            ));

                            // if( !empty($is_serial_number) ) {
                                echo $this->Html->tag('td', $this->Common->_callInputForm(__('ProductExpenditureDetailSerialNumber.serial_numbers.%s', $id), array(
                                    'div' => false,
                                    'frameClass' => false,
                                    'class' => 'chosen-select form-control full',
                                    'multiple' => true,
                                    'options' => array(),
                                    // 'data-url' => $this->Html->url(array(
                                    //     'controller' => 'products',
                                    //     'action' => 'scan',
                                    //     'admin' => false,
                                    // )),
                                    // 'options' => $serial_numbers,
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
                                    'class' => 'text-center pick-product-code hide',
                                ));
                            // } else {
                            //     echo $this->Html->tag('td', '-', array(
                            //         'class' => 'text-center hide',
                            //     ));
                            // }

                            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                'class' => 'delete-document btn btn-danger btn-xs',
                                'escape' => false,
                            )), array(
                                'class' => 'actions text-center hide',
                            ));
                            echo $this->Form->hidden(sprintf('ProductExpenditureDetail.spk_product_id.%s', $id), array(
                                'value' => $spk_product_id,
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
                    'urlTitle' => __('Daftar Barang'),
                ),
            ));
    ?>
</div>