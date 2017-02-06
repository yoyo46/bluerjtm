<?php 
        $view = !empty($view)?$view:false;
        $data = $this->request->data;
        $ppn_include = $this->Common->filterEmptyField($data, 'PurchaseOrder', 'ppn_include');
        $dataDetail = $this->Common->filterEmptyField($data, 'PurchaseOrderDetail');

        if( !empty($dataDetail) ) {
            $tableClass = '';
        } else {
            $tableClass = 'hide';
        }

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
            'note' => array(
                'name' => __('Ket.'),
                'class' => 'text-center',
                'style' => 'width:15%;',
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

        if( $view != 'detail' ) {
            echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                    'controller'=> 'ajax', 
                    'action' => 'products',
                    'po',
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'title' => __('Daftar Barang'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                    'data-check' => '#supplier-quotation',
                    'data-check-named' => 'no_sq',
                )), array(
                'class' => "form-group",
            ));
        }
?>
<div class="temp-document-picker document-calc <?php echo $tableClass; ?>">
	<div class="box box-success">
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
                            $grandtotal = 0;

                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $supplier_quotation_detail_id = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'supplier_quotation_detail_id');

                                    $total = $this->Purchase->calculate($value, $ppn_include);
                                    $grandtotal += $total;

                                    $customTotal = $this->Common->getFormatPrice($total, '', 2);

                                    if( !empty($value['PurchaseOrderDetail']['code']) ) {
                                        $code = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'code');
                                        $name = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'name');
                                        $unit = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'unit');
                                        $is_supplier_quotation = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'is_supplier_quotation');
                                    } else {
                                        $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                        $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                        $is_supplier_quotation = $this->Common->filterEmptyField($value, 'Product', 'is_supplier_quotation');
                                        $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                                    }

                                    echo $this->element('blocks/purchases/purchase_orders/tables/detail_items', array(
                                        'modelName' => 'PurchaseOrderDetail',
                                        'supplier_quotation_detail_id' => $supplier_quotation_detail_id,
                                        'value' => $value,
                                        'total' => $customTotal,
                                        'code' => $code,
                                        'name' => $name,
                                        'unit' => $unit,
                                        'idx' => $key,
                                        'disabled' => $this->Purchase->_callDisabledNoSq($is_supplier_quotation, $supplier_quotation_detail_id),
                                    ));
                                }
                            }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="grandtotal">
                        <?php
                                $customGrandtotal = $this->Common->getFormatPrice($grandtotal, '', 2);

                                echo $this->Html->tag('td', __('Grand Total'), array(
                                    'colspan' => 8,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $customGrandtotal, array(
                                    'class' => 'text-right total',
                                    'data-decimal' => 2,
                                ));
                        ?>
                    </tr>
                </tfoot>
	    	</table>
	    </div>
	</div>
</div>