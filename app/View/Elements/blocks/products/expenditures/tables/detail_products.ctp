<?php 
        $view = !empty($view)?$view:false;
        $value = !empty($value)?$value:false;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'ProductExpenditureDetail');
        $id = $this->Common->filterEmptyField($value, 'ProductExpenditure', 'id', 0);
        $nodoc = $this->Common->filterEmptyField($value, 'Document', 'nodoc');

		$dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'style' => 'width:5%;',
            ),
            'name' => array(
                'name' => __('Nama'),
                'style' => 'width:10%;',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
            'qty_spk' => array(
                'name' => __('Qty SPK'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
        );

        if( empty($view) ) {
            $dataColumns = array_merge($dataColumns, array(
                'qty_out' => array(
                    'name' => __('Qty Out'),
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
            $dataColumns = array_merge($dataColumns, array(
                'qty' => array(
                    'name' => __('Qty Keluar'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
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

        if( $view != 'detail' ) {
            echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                    'controller'=> 'products', 
                    'action' => 'spk_products',
                    $id,
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'title' => __('Daftar Barang'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                    'data-check' => '#document-number',
                    'data-check-alert' => __('Mohon pilih No. SPK terlebih dahulu'),
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
                            $total_spk_qty = 0;
                            $total_qty = 0;
                            $total_out_qty = 0;

                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $id = $this->Common->filterEmptyField($value, 'ProductExpenditureDetail', 'product_id');
                                    $qty = $this->Common->filterEmptyField($value, 'ProductExpenditureDetail', 'qty');
                                    $spk_qty = $this->Common->filterEmptyField($value, 'ProductExpenditureDetail', 'spk_qty', 0);
                                    $out_qty = $this->Common->filterEmptyField($value, 'ProductExpenditureDetail', 'out_qty', 0);
                                    $serial_numbers = $this->Common->filterEmptyField($value, 'ProductExpenditureDetail', 'serial_numbers');
                                    $spk_product_id = $this->Common->filterEmptyField($value, 'ProductExpenditureDetail', 'spk_product_id');
                                    $total_spk_qty += $spk_qty;
                                    $total_qty += $qty;
                                    $total_out_qty += $out_qty;

                                    if( !empty($value['Product']) ) {
                                        $modelName = 'Product';
                                    } else {
                                        $modelName = 'ProductExpenditureDetail';
                                    }

                                    echo $this->element('blocks/products/expenditures/tables/items', array(
                                        'id' => $id,
                                        'modelName' => $modelName,
                                        'value' => $value,
                                        'qty' => $qty,
                                        'serial_numbers' => $serial_numbers,
                                        'key' => $key,
                                        'spk_product_id' => $spk_product_id,
                                        'spk_qty' => $spk_qty,
                                        'out_qty' => $out_qty,
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
                                echo $this->Html->tag('td', $total_spk_qty, array(
                                    'class' => 'text-center total_custom',
                                    'rel' => 'qty-spk',
                                ));
                                
                                if( empty($view) ) {
                                    echo $this->Html->tag('td', $total_out_qty, array(
                                        'class' => 'text-center total_custom',
                                        'rel' => 'qty-out',
                                    ));
                                }

                                echo $this->Html->tag('td', $total_qty, array(
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