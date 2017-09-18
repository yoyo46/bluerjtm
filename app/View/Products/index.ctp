<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'field_model' => 'Product.code',
            ),
            'name' => array(
                'name' => __('Nama'),
                'field_model' => 'Product.name',
                'width' => '20%',
            ),
            'type' => array(
                'name' => __('Tipe'),
                'field_model' => 'Product.type',
            ),
            'unit' => array(
                'name' => __('Satuan'),
            ),
            'group' => array(
                'name' => __('Grup'),
            ),
            'min_stock' => array(
                'name' => __('Min. Stok'),
                'field_model' => 'Product.min_stock',
                'class' => 'text-center',
            ),
            'stock' => array(
                'name' => __('Stok'),
                'field_model' => 'Product.product_stock_cnt',
                'class' => 'text-center',
            ),
            'sq' => array(
                'name' => __('Harus ada SQ ?'),
                'field_model' => 'Product.is_supplier_quotation',
                'class' => 'text-center',
            ),
            'sn' => array(
                'name' => __('SN ?'),
                'field_model' => 'Product.is_serial_number',
                'class' => 'text-center',
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Product.status',
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add_multiple' => array(
                    array(
                        'label' => __('Produk'),
                        'url' => array(
                            'controller' => 'products',
                            'action' => 'add',
                            'admin' => false,
                        ),
                    ),
                    array(
                        'label' => __('Import'),
                        'url' => array(
                            'controller' => 'products',
                            'action' => 'import',
                            'admin' => false,
                        ),
                    ),
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
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
                            $created = $this->Common->filterEmptyField($value, 'Product', 'created');
                            $status = $this->Common->filterEmptyField($value, 'Product', 'status');
                            $is_supplier_quotation = $this->Common->filterEmptyField($value, 'Product', 'is_supplier_quotation');
                            $is_serial_number = $this->Common->filterEmptyField($value, 'Product', 'is_serial_number');

                            $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                            $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');
                            
                            $min_stock = $this->Common->filterEmptyField($value, 'Product', 'min_stock');
                            $product_stock_cnt = $this->Common->filterEmptyField($value, 'Product', 'product_stock_cnt', '-');

                            if( !empty($min_stock) && $min_stock >= $product_stock_cnt ) {
                                $tr_bg = 'alert alert-danger';
                            } else {
                                $min_stock = '-';
                                $tr_bg = '';
                            }

                            $customCreated = $this->Common->formatDate($created, 'd/m/Y');
                            
                            $customType = str_replace('_', ' ', $type);
                            $customType = ucwords($customType);

                            $customStatus = $this->Common->getCheckStatus($status);
                            $sq = $this->Common->getCheckStatus($is_supplier_quotation);
                            $sn = $this->Common->getCheckStatus($is_serial_number);
                            $customAction = $this->Html->link('Edit', array(
                                'controller' => 'products',
                                'action' => 'edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $customAction .= $this->Html->link(__('Hapus'), array(
                                'controller' => 'products',
                                'action' => 'toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'data-alert' => __('Anda yakin ingin menghapus barang ini?'),
                            ));
            ?>
            <tr class="<?php echo $tr_bg; ?>">
                <?php 
                        echo $this->Html->tag('td', $code);
                        echo $this->Html->tag('td', $name);
                        echo $this->Html->tag('td', $customType);
                        echo $this->Html->tag('td', $unit);
                        echo $this->Html->tag('td', $group);
                        echo $this->Html->tag('td', $min_stock, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $product_stock_cnt, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $sq, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $sn, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customStatus, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customAction, array(
                            'class' => 'action',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>