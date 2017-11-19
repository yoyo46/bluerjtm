<?php 
        $value = !empty($value)?$value:false;
        $data = $this->request->data;
        $id = $this->Common->filterEmptyField($value, 'PurchaseOrderPayment', 'id', 0);
        $dataDetail = $this->Common->filterEmptyField($data, 'PurchaseOrderPaymentDetail');

        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No Dokumen'),
                'style' => 'width:15%;',
            ),
            'date' => array(
                'name' => __('Tanggal'),
                'class' => 'text-center',
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'total' => array(
                'name' => __('Total PO'),
                'class' => 'text-center',
                'style' => 'width:15%;',
            ),
            'paid' => array(
                'name' => __('Telah Dibayar'),
                'class' => 'text-center',
                'style' => 'width:15%;',
            ),
            'unpaid' => array(
                'name' => __('Total'),
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
                echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil PO'), $this->Html->url( array(
                    'controller'=> 'purchases', 
                    'action' => 'po_documents',
                    'payment_id' => $id,
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'title' => __('Daftar PO'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                    'data-check' => '#supplier-val',
                    'data-check-named' => 'vendor_id',
                    'data-check-alert' => __('Mohon pilih supplier terlebih dahulu'),
                )), array(
                'class' => "form-group",
            ));
            }
    ?>
    <div class="temp-document-picker document-calc">
    	<div class="box box-success">
    	    <?php 
    	            echo $this->element('blocks/common/box_header', array(
    	                'title' => __('Informasi PO'),
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
                                        $price = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'price');

                                        $grandtotal += $price;
                                        $customTotal = $this->Common->getFormatPrice($price);

                                        echo $this->element('blocks/purchases/payments/tables/items', array(
                                            'modelName' => 'PurchaseOrderPaymentDetail',
                                            'value' => $value,
                                            'total' => $customTotal,
                                            'idx' => $key,
                                        ));
                                    }
                                }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="grandtotal">
                            <?php
                                    $customGrandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);

                                    echo $this->Html->tag('td', __('Grand Total'), array(
                                        'colspan' => 5,
                                        'class' => 'text-right',
                                    ));
                                    echo $this->Html->tag('td', $customGrandtotal, array(
                                        'class' => 'text-right total_custom',
                                        'data-decimal' => 2,
                                        'rel' => 'price',
                                    ));
                            ?>
                        </tr>
                    </tfoot>
    	    	</table>
    	    </div>
    	</div>
    </div>
</div>