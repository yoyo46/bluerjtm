<?php 
        $view = !empty($view)?$view:false;
        $value = !empty($value)?$value:false;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'ProductReceiptDetail');
        $nodoc = $this->Common->filterEmptyField($value, 'Document', 'nodoc');
        $id = $this->Common->filterEmptyField($value, 'ProductReceipt', 'id', 0);
        $document_type = $this->Common->filterEmptyField($value, 'ProductReceipt', 'document_type');

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
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
            ),
        );

        if( empty($view) ) {
            $dataColumns = array_merge($dataColumns, array(
                'qty_doc' => array(
                    'name' => __('Qty PO'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
                'qty_in' => array(
                    'name' => __('Qty Diterima'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
                'qty' => array(
                    'name' => __('Qty'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        } else {
            switch ($document_type) {
                case 'spk':
                    $lblQty = __('SPK');
                    break;
                
                default:
                    $lblQty = __('PO');
                    break;
            }
            $dataColumns = array_merge($dataColumns, array(
                'qty_doc' => array(
                    'name' => __('Qty %s', $lblQty),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
                'qty' => array(
                    'name' => __('Qty Diterima'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
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
        ));

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

        if( empty($view) ) {
            echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                    'controller'=> 'products', 
                    'action' => 'receipt_document_products',
                    $id,
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'allow' => true,
                    'title' => __('Daftar Barang'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                    'data-form' => '.receipt-form',
                    'data-check' => '#document-number',
                    'data-check-encode' => 'true',
                    'data-check-alert' => __('Mohon pilih No. Dokumen terlebih dahulu'),
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
	        	<tbody class="wrapper-table-documents" data-remove="true" rel="<?php echo $nodoc; ?>">
                    <?php
                            $grandtotal = 0;
                            $total_doc_qty = 0;
                            $total_in_qty = 0;


                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $id = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'product_id');
                                    $doc_qty = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'doc_qty', 0);
                                    $in_qty = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'in_qty', 0);
                                    $qty = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'qty');
                                    $code = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'code');
                                    $document_detail_id = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'document_detail_id');
                                    $serial_number = $this->Common->filterEmptyField($value, 'ProductReceiptDetailSerialNumber');


                                    $grandtotal += $qty;
                                    $total_doc_qty += $doc_qty;
                                    $total_in_qty += $in_qty;

                                    if( !empty($code) ) {
                                        $modelName = 'ProductReceiptDetail';
                                    } else {
                                        $modelName = 'Product';
                                    }
                                    if( empty($serial_number) ) {
                                        $serial_number = $this->Common->filterEmptyField($value, 'ProductReceiptDetail', 'serial_number');
                                    }

                                    echo $this->element('blocks/products/receipts/tables/items', array(
                                        'id' => $id,
                                        'modelName' => $modelName,
                                        'value' => $value,
                                        'doc_qty' => $doc_qty,
                                        'in_qty' => $in_qty,
                                        'qty' => $qty,
                                        'key' => $key,
                                        'serial_number' => $serial_number,
                                        'document_detail_id' => $document_detail_id,
                                    ));
                                }
                            }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="grandtotal">
                        <?php
                                echo $this->Html->tag('td', __('Total'), array(
                                    'colspan' => 3,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $total_doc_qty, array(
                                    'class' => 'text-center total_custom',
                                    'rel' => 'qty-doc',
                                ));
                                
                                if( empty($view) ) {
                                    echo $this->Html->tag('td', $total_in_qty, array(
                                        'class' => 'text-center total_custom',
                                        'rel' => 'qty-remain',
                                    ));
                                }

                                echo $this->Html->tag('td', $grandtotal, array(
                                    'class' => 'text-center total_custom',
                                    'rel' => 'qty',
                                ));

                                echo $this->Html->tag('td', '&nbsp;');
                        ?>
                    </tr>
                </tfoot>
	    	</table>
	    </div>
	</div>
</div>