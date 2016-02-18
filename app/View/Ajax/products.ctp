<div id="wrapper-modal-write">
    <?php 
            $modelName = false;
            $dataColumns = array(
                'check-box' => array(
                    'name' => '',
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
            );

            switch ($action_type) {
                case 'sq':
                    $dataColumns = array_merge($dataColumns, array(
                        'rate-price' => array(
                            'name' => __('Ref. Harga'),
                        ),
                    ));
                    $modelName = 'SupplierQuotationDetail';
                    break;
                case 'po':
                    $modelName = 'PurchaseOrderDetail';
                    break;
            }

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/ajax/forms/searchs/quotation_products');
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
                                $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $type = $this->Common->filterEmptyField($value, 'Product', 'type');
                                $rate = $this->Common->filterEmptyField($value, 'Product', 'rate');

                                $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');

                                $customType = $this->Common->unSlug($type);
                                $customRate = $this->Common->getFormatPrice($rate, '-');
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $id,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $code.$this->Form->hidden(sprintf('%s.product_id.%s', $modelName, $id), array(
                                'value' => $id,
                            )));
                            echo $this->Html->tag('td', $name);
                            echo $this->Html->tag('td', $unit);
                            echo $this->Html->tag('td', $group, array(
                                'class' => 'removed',
                            ));
                            echo $this->Html->tag('td', $customType, array(
                                'class' => 'removed',
                            ));

                            switch ($action_type) {
                                case 'sq':
                                    echo $this->Html->tag('td', $customRate, array(
                                        'class' => 'text-right',
                                    ));
                                    break;
                                case 'po':
                                    echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.qty.%s', $modelName, $id), false, array(
                                        'type' => 'text',
                                        'frameClass' => false,
                                        'class' => 'input_number text-right qty',
                                    )), array(
                                        'class' => 'hide',
                                    ));
                                    break;
                            }

                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.price.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'input_price text-right price',
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.disc.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'disc input_price text-right',
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.ppn.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'ppn input_price text-right',
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
                                'class' => 'actions hide',
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