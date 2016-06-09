<?php 
        $view = !empty($view)?$view:false;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'ProductReceiptDetail');

		$dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
            'name' => array(
                'name' => __('Nama'),
                'style' => 'width:10%;',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
            'serial_number' => array(
                'name' => __('No Seri'),
                'class' => 'text-center',
                'style' => 'width:10%;',
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
                    'controller'=> 'products', 
                    'action' => 'receipt_document_products',
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'title' => __('Daftar Barang'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                )), array(
                'class' => "form-group",
            ));
        }
?>
<div class="temp-document-picker document-calc">
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
	        	<tbody class="wrapper-table-documents">
                    <?php
                            $grandtotal = 0;

                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $qty = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'qty');
                                    $grandtotal += $qty;

                                    $code = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'code');
                                    $name = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'name');
                                    $unit = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'unit');

                                    echo $this->element('blocks/products/receipts/tables/detail_items', array(
                                        'modelName' => 'ProductReceiptDetail',
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
                                echo $this->Html->tag('td', __('Total'), array(
                                    'colspan' => 2,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $grandtotal, array(
                                    'class' => 'text-right total',
                                ));
                        ?>
                    </tr>
                </tfoot>
	    	</table>
	    </div>
	</div>
</div>