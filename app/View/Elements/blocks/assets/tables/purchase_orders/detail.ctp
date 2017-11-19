<?php 
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'PurchaseOrderAsset');

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
        );

        if( empty($view) ) {
            $dataColumns = array_merge($dataColumns, array(
                'action' => array(
                    'name' => __('Action'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        }

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="form-added temp-document-picker">
    <?php 
            if( empty($view) ) {
                echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Tambah Barang'), '#', array(
                    'escape' => false,
                    'class' => 'btn btn-success field-added btn-xs',
                )), array(
                    'class' => "form-group",
                ));
            }
    ?>
    <div class="temp-document-picker document-calc">
    	<div class="box box-success">
    	    <?php 
    	            echo $this->element('blocks/common/box_header', array(
    	                'title' => __('Informasi Asset'),
    	            ));
    	    ?>
    	    <div class="box-body table-responsive">
    	        <table class="table table-hover form-added" id="wrapper-write">
    		        <?php
    	                    if( !empty($fieldColumn) ) {
    	                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
    	                    }
    	            ?>
    	        	<tbody class="field-content">
                        <?php
                                $grandtotal = 0;

                                if(!empty($dataDetail)){
                                    foreach ($dataDetail as $key => $value) {
                                        $price = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'price');

                                        $grandtotal += $price;
                                        $customTotal = $this->Common->getFormatPrice($price);

                                        echo $this->element('blocks/assets/tables/purchase_orders/items', array(
                                            'modelName' => 'PurchaseOrderDetail',
                                            'value' => $value,
                                            'total' => $customTotal,
                                            'idx' => $key,
                                        ));
                                    }
                                } else {
                                    echo $this->element('blocks/assets/tables/purchase_orders/items', array(
                                        'idx' => 0,
                                    ));
                                }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="grandtotal">
                            <?php
                                    $customGrandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);

                                    echo $this->Html->tag('td', __('Grand Total'), array(
                                        'colspan' => 3,
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
</div>