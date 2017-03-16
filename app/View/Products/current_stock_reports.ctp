<?php 

        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $element = 'blocks/products/tables/current_stock_reports';
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode Barang'),
                'field_model' => 'Product.code',
                'align' => 'center',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'code\',width:120',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'name' => array(
                'name' => __('Nama Barang'),
                'field_model' => 'Product.name',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'name\',width:100',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'field_model' => 'ProductUnit.name',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'unit\',width:120',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'total_qty' => array(
                'name' => __('QTY'),
                'field_model' => 'ProductStock.total_qty',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'total_qty\',width:100',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'total_balance' => array(
                'name' => __('Harga Satuan'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'total_balance\',width:100',
                'mainalign' => 'center',
                'align' => 'center',
            ),
            'total' => array(
                'name' => __('Total Harga'),
                'align' => 'center',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'total\',width:80',
                'align' => 'center',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => sprintf('%s - %s', $sub_module_title, $period_text),
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/products/tables/search/current_stock_reports');
?>
<section class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
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
        ?>
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
                    echo $this->Html->tag('tbody', $this->element($element));
            ?>
        </table>
        <?php 
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