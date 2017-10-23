<?php 
        $data = $this->request->data;
        $eksternalClass = Common::_callDisplayToggle('eksternal', $data);
?>
<div id="wrapper-modal-write" class="document-picker">
    <?php 
            $modelName = 'SpkProduct';
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
                                $stock = $this->Common->filterEmptyField($value, 'Product', 'product_stock_cnt', 0);

                                $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                                $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');
                                $group_slug = Common::toSlug($group);

                                $customType = $this->Common->unSlug($type);
                ?>
                <tr class="pick-document" rel="<?php echo $id; ?>" data-type="single-total" data-table="<?php echo $action_type ?>">
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

                            if( $group_slug == 'ban' ) {
                                echo $this->Html->tag('td', $name, array(
                                    'class' => 'removed',
                                ));
                                echo $this->Html->tag('td', $name.$this->Html->link(__('Posisi'), array(
                                    'controller' => 'spk',
                                    'action' => 'wheel_position',
                                    $id,
                                ), array(
                                    'class' => 'wheel-position',
                                    'title' => __('Pilih Posisi Ban'),
                                    'rel' => $id,
                                )), array(
                                    'class' => 'hide',
                                ));
                            } else {
                                echo $this->Html->tag('td', $name);
                            }

                            echo $this->Html->tag('td', $stock, array(
                                'class' => 'hide text-center',
                            ));
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
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.note.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.price.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'text-right price_custom',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                    'rel' => 2,
                                ),
                            )), array(
                                'data-display' => $eksternalClass,
                                'class' => 'hide wrapper-eksternal',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.qty.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'input_number text-center price_custom qty',
                                'attributes' => array(
                                    'rel' => 'qty',
                                ),
                            )), array(
                                'class' => 'hide',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.price_service_type.%s', $modelName, $id), false, array(
                                'frameClass' => false,
                                'class' => 'td-select calc-type',
                                'options' => Common::_callPriceServiceType(),
                            )), array(
                                'data-display' => $eksternalClass,
                                'class' => 'hide wrapper-eksternal',
                            ));
                            echo $this->Html->tag('td', $this->Common->buildInputForm(sprintf('%s.price_service.%s', $modelName, $id), false, array(
                                'type' => 'text',
                                'frameClass' => false,
                                'class' => 'text-right price_custom price',
                                'attributes' => array(
                                    'data-type' => 'input_price_coma',
                                    'rel' => 1,
                                ),
                            )), array(
                                'data-display' => $eksternalClass,
                                'class' => 'hide wrapper-eksternal',
                            ));
                            echo $this->Html->tag('td', '0', array(
                                'rel' => 'grandtotal',
                                'class' => 'total text-right total_row price_custom hide wrapper-eksternal',
                                'data-type' => 'input_price_coma',
                                'data-display' => $eksternalClass,
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