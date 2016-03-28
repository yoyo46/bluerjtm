<?php 
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'PurchaseOrderDetail');

		$dataColumns = array(
            'name' => array(
                'name' => __('Nama Asset'),
                'style' => 'width:15%;',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'class' => 'text-center',
                'style' => 'width:20%;',
            ),
            'asset_group' => array(
                'name' => __('Group Asset'),
                'class' => 'text-center',
                'style' => 'width:15%;',
            ),
            'price' => array(
                'name' => __('Harga Pembelian'),
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

        echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Tambah Barang'), $this->Html->url( array(
                'controller'=> 'ajax', 
                'action' => 'products',
                'po',
                'admin' => false,
            )), array(
                'escape' => false,
                'title' => __('Daftar Barang'),
                'class' => 'btn bg-maroon ajaxCustomModal',
                'class' => 'btn btn-success add-custom-field btn-xs',
                'action_type' => 'revenue-detail'
            )), array(
            'class' => "form-group",
        ));
?>
<div class="temp-document-picker document-calc">
	<div class="box box-success">
	    <?php 
	            echo $this->element('blocks/common/box_header', array(
	                'title' => __('Informasi Asset'),
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

                                    $customTotal = $this->Common->getFormatPrice($total);
                                    $disabled = false;

                                    if( !empty($supplier_quotation_detail_id) ) {
                                        $disabled = true;
                                    }

                                    echo $this->element('blocks/purchases/purchase_orders/tables/detail_items', array(
                                        'modelName' => 'PurchaseOrderDetail',
                                        'sq_detail_id' => $supplier_quotation_detail_id,
                                        'value' => $value,
                                        'total' => $customTotal,
                                        'disabled' => $disabled,
                                        'id' => $key,
                                    ));
                                }
                            } else {
                                echo $this->element('blocks/assets/tables/purchase_orders/items', array(
                                    'id' => 0,
                                ));
                            }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="grandtotal">
                        <?php
                                $customGrandtotal = $this->Common->getFormatPrice($grandtotal);

                                echo $this->Html->tag('td', __('Grand Total'), array(
                                    'colspan' => 3,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $customGrandtotal, array(
                                    'class' => 'text-right total',
                                ));
                        ?>
                    </tr>
                </tfoot>
	    	</table>
	    </div>
	</div>
</div>