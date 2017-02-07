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
                'serial_number' => array(
                    'name' => __('Serial Number'),
                ),
                'transaction_date' => array(
                    'name' => __('Tgl Masuk'),
                    'class' => 'text-center',
                ),
                'qty' => array(
                    'name' => __('Qty'),
                    'class' => 'text-center',
                ),
                'price' => array(
                    'name' => __('Harga/Unit'),
                    'class' => 'text-right',
                ),
            );

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/search_stocks', array(
                'urlForm' => array(
                    'controller' => 'products',
                    'action' => 'search',
                    'stocks',
                    $id,
                    'admin' => false,
                ),
                'urlReset' => array(
                    'controller' => 'products',
                    'action' => 'stocks',
                    $id,
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
                                $id = $this->Common->filterEmptyField($value, 'ProductStock', 'id');
                                $qty = $this->Common->filterEmptyField($value, 'ProductStock', 'qty_total');
                                $serial_number = $this->Common->filterEmptyField($value, 'ProductStock', 'serial_number');
                                $transaction_date = $this->Common->filterEmptyField($value, 'ProductStock', 'transaction_date', '-', false, array(
                                    'date' => 'd M Y'
                                ));
                                $price = $this->Common->filterEmptyField($value, 'ProductStock', 'price', '-', false, array(
                                    'price' => array(
                                        'decimal' => 2,
                                        'empty' => '-',
                                    ),
                                ));
                                $product_id = $this->Common->filterEmptyField($value, 'ProductStock', 'product_id');
                ?>
                <tr class="pick-document" rel="<?php echo $product_id; ?>" data-type="select-multiple">
                    <?php
                            echo $this->Html->tag('td', $this->Form->checkbox('document_id.'.$id, array(
                                'class' => 'check-option',
                                'value' => $serial_number,
                            )), array(
                                'class' => 'removed check-box text-center',
                            ));
                            echo $this->Html->tag('td', $serial_number);
                            echo $this->Html->tag('td', $transaction_date, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $qty, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $price, array(
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
            echo $this->element('pagination', array(
                'options' => array(
                    'urlClass' => 'ajaxCustomModal',
                    'urlTitle' => __('Stok Barang'),
                ),
            ));
    ?>
</div>