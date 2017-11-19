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
                ),
                'group' => array(
                    'name' => __('Grup'),
                ),
                'type' => array(
                    'name' => __('Tipe'),
                ),
                'stock' => array(
                    'name' => __('Stok'),
                    'class' => 'text-center',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/ajax/forms/searchs/quotation_products');
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
                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $type = $this->Common->filterEmptyField($value, 'Product', 'type');
                                $rate = $this->Common->filterEmptyField($value, 'Product', 'rate');
                                $is_supplier_quotation = $this->Common->filterEmptyField($value, 'Product', 'is_supplier_quotation');
                                $stock = $this->Common->filterEmptyField($value, 'Product', 'product_stock_cnt', 0);

                                $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');
                                
                                $supplier_quotation_detail_id = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'id');
                                $supplier_quotation_id = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'supplier_quotation_id');

                                $customType = $this->Common->unSlug($type);
                                $price = '';
                                $disc = '';
                                $ppn = '';

                                if( $this->Purchase->_callDisabledNoSq($is_supplier_quotation, $supplier_quotation_detail_id) ) {
                                    $disabled = true;
                                } else {
                                    if( !empty($supplier_quotation_id) ) {
                                        $disabled = true;
                                        $price = Common::hashEmptyField($value, 'SupplierQuotationDetail.price');
                                        $disc = Common::hashEmptyField($value, 'SupplierQuotationDetail.disc');
                                        $ppn = Common::hashEmptyField($value, 'SupplierQuotationDetail.ppn');
                                    } else {
                                        $disabled = false;
                                    }
                                }
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>" data-table="po">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $code.$this->Form->hidden(sprintf('PurchaseOrderDetail.product_id.%s', $id), array(
                                'value' => $id,
                            )).$this->Form->hidden('PurchaseOrderDetail.supplier_quotation_detail_id.'.$id, array(
                                'value' => $supplier_quotation_detail_id,
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
                            echo $this->Html->tag('td', $stock, array(
                                'class' => 'removed text-center',
                            ));

                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('PurchaseOrderDetail.note.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                            )), array(
                                'class' => 'hide',
                            ));

                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('PurchaseOrderDetail.qty.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'input_number text-center qty',
                            )), array(
                                'class' => 'hide',
                            ));

                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('PurchaseOrderDetail.price.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'text-right price',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                    'value' => $price,
                                ),
                                'disabled' => $disabled,
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('PurchaseOrderDetail.disc.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'disc text-right',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                    'value' => $this->Common->getFormatPrice($disc, 0, 2),
                                ),
                                'disabled' => $disabled,
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('PurchaseOrderDetail.ppn.%s', $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'ppn text-right',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                    'value' => $this->Common->getFormatPrice($ppn, 0, 2),
                                ),
                                'disabled' => $disabled,
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', 0, array(
                                'class' => 'hide total text-right',
                            ));
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