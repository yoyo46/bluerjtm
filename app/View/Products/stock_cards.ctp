<?php 
        $period_text = !empty($period_text)?$period_text:false;
        $title = __('%s<br>%s', $sub_module_title, $period_text);

        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $element = 'blocks/products/tables/stock_cards';
        $dataColumns = array(
            'transaction_date' => array(
                'name' => __('Tanggal'),
                'field_model' => 'ProductHistory.transaction_date',
            ),
            'nodoc' => array(
                'name' => __('No. Referensi'),
                'field_model' => 'Document.nodoc',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'field_model' => 'ProductUnit.name',
                'style' => 'text-align: center;',
            ),
            'in' => array(
                'name' => __('Masuk'),
                'style' => 'text-align: center;',
            ),
            'price_in' => array(
                'name' => __('Harga Satuan'),
                'style' => 'text-align: right;',
            ),
            'total_in' => array(
                'name' => __('Total'),
            ),
            'out' => array(
                'name' => __('Keluar'),
                'style' => 'text-align: center;',
            ),
            'price_out' => array(
                'name' => __('Harga Satuan'),
                'style' => 'text-align: right;',
            ),
            'total_out' => array(
                'name' => __('Total'),
                'style' => 'text-align: right;',
            ),
            'saldo' => array(
                'name' => __('Saldo'),
                'style' => 'text-align: center;',
            ),
            'price' => array(
                'name' => __('Harga Satuan'),
                'style' => 'text-align: right;',
            ),
            'total' => array(
                'name' => __('Total'),
                'style' => 'text-align: right;',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/tables/search/stock_cards');
?>
<section class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'escape' => false,
                    'class' => 'ajaxLink',
                    'data-request' => '#form-report',
                ),
                '_ajax' => true,
            ));
    ?>
    <div class="table-responsive">
        <?php 
                if(!empty($values)){
                    foreach ($values as $key => $product) {
                        if(!empty($product)){
                            foreach ($product as $key => $branch) {
                                if( !empty($branch['ProductHistory']) ) {
                                    $branch_name = Common::hashEmptyField($branch, 'Branch.full_name');
                                    $product_name = Common::hashEmptyField($branch, 'Product.full_name');
        ?>
        <div class="wrapper-product">
            <?php 
                    echo $this->Html->tag('div', __('Cabang: %s', $branch_name), array(
                        'class' => 'product-branch',
                    ));
                    echo $this->Html->tag('div', $product_name, array(
                        'class' => 'product-name',
                    ));
            ?>
        </div>
        <table id="tt" class="table table-bordered">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <?php 
                    echo $this->Html->tag('tbody', $this->element($element, array(
                        'values' => $branch,
                    )));
            ?>
        </table>
        <?php   
                                }
                            }
                        }
                    }
                } else {
                    echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                    ));
                }
        ?>
    </div><!-- /.box-body -->
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            echo $this->Html->tag('div', $this->element('pagination'), array(
                'class' => 'pagination-report'
            ));
    ?>
</div>
<?php 
        }
?>