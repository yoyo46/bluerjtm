<?php 
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'SupplierQuotationDetail');

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
            'rate-price' => array(
                'name' => __('Ref. Harga'),
                'class' => 'text-center',
                'style' => 'width:15%;',
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
<div class="temp-document-picker document-calc <?php echo $tableClass; ?>">
	<div class="box box-primary">
	    <?php 
	            echo $this->element('blocks/common/box_header', array(
	                'title' => __('Informasi Barang'),
	            ));
	    ?>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
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
                                    $product_id = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'product_id');
                                    $price = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'price');
                                    $disc = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'disc');
                                    $ppn = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail', 'ppn');

                                    $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                                    $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                                    $rate = $this->Common->filterEmptyField($value, 'Product', 'rate');

                                    $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                                    $customRate = $this->Common->getFormatPrice($rate, '-');

                                    $total = ( $price - $disc ) + $ppn;
                                    $grandtotal += $total;

                                    $customTotal = $this->Common->getFormatPrice($total);
                    ?>
                    <tr class="pick-document" rel="<?php echo $product_id; ?>">
                        <?php
                                echo $this->Html->tag('td', $code.$this->Form->hidden('SupplierQuotationDetail.product_id.'.$product_id, array(
                                    'value' => $product_id,
                                )));
                                echo $this->Html->tag('td', $name);
                                echo $this->Html->tag('td', $unit);
                                echo $this->Html->tag('td', $customRate, array(
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $this->Common->buildInputForm('SupplierQuotationDetail.price.'.$product_id, false, array(
                                    'type' => 'text',
                                    'frameClass' => false,
                                    'class' => 'input_price text-right price',
                                    'attributes' => array(
                                        'value' => $price,
                                    ),
                                )));
                                echo $this->Html->tag('td', $this->Common->buildInputForm('SupplierQuotationDetail.disc.'.$product_id, false, array(
                                    'type' => 'text',
                                    'frameClass' => false,
                                    'class' => 'disc input_price text-right',
                                    'attributes' => array(
                                        'value' => $disc,
                                    ),
                                )));
                                echo $this->Html->tag('td', $this->Common->buildInputForm('SupplierQuotationDetail.ppn.'.$product_id, false, array(
                                    'type' => 'text',
                                    'frameClass' => false,
                                    'class' => 'ppn input_price text-right',
                                    'attributes' => array(
                                        'value' => $ppn,
                                    ),
                                )));
                                echo $this->Html->tag('td', $customTotal, array(
                                    'class' => 'total text-right',
                                ));
                                echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                    'class' => 'delete-document btn btn-danger btn-xs',
                                    'escape' => false,
                                )));
                        ?>
                    </tr>
                    <?php
                                }
                            }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="grandtotal">
                        <?php
                                $customGrandtotal = $this->Common->getFormatPrice($grandtotal);

                                echo $this->Html->tag('td', __('Grand Total'), array(
                                    'colspan' => 7,
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