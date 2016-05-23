<?php 
		$dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'class' => 'text-center',
                'style' => 'width:7%;',
            ),
            'name' => array(
                'name' => __('Nama'),
                'style' => 'width:15%;',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
                'style' => 'width:7%;',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'class' => 'text-center',
                'style' => 'width:10%;',
            ),
            'price' => array(
                'name' => __('Harga'),
                'class' => 'text-center',
                'style' => 'width:15%;',
            ),
            'disc' => array(
                'name' => __('Potongan'),
                'class' => 'text-center',
                'style' => 'width:10%;',
            ),
            'ppn' => array(
                'name' => __('Pajak'),
                'class' => 'text-center',
                'style' => 'width:10%;',
            ),
            'total' => array(
                'name' => __('Total'),
                'class' => 'text-center',
                'style' => 'width:15%;',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box box-primary">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => __('Informasi Barang'),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover" id="wrapper-write">
	        <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
        	<tbody>
                <?php
                        if(!empty($values)){
                            foreach ($values as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'id');

                                $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');

                                echo $this->element('blocks/purchases/purchase_orders/tables/detail_items', array(
                                    'modelName' => 'SupplierQuotationDetail',
                                    'supplier_quotation_detail_id' => $id,
                                    'value' => $value,
                                    'code' => $code,
                                    'name' => $name,
                                    'unit' => $unit,
                                    'idx' => $key,
                                ));
                            }
                        }
                ?>
            </tbody>
            <tfoot>
                <tr class="grandtotal">
                    <?php
                            echo $this->Html->tag('td', __('Grand Total'), array(
                                'colspan' => 7,
                                'class' => 'text-right',
                            ));
                            echo $this->Html->tag('td', '', array(
                                'class' => 'text-right total',
                            ));
                    ?>
                </tr>
            </tfoot>
    	</table>
    </div>
</div>