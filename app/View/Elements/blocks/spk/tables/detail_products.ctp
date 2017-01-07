<?php 
        $view = !empty($view)?$view:false;
        $data = $this->request->data;

        $dataDetail = $this->Common->filterEmptyField($data, 'SpkProduct');
        $eksternalClass = $this->Spk->_callDisplayToggle('eksternal', $data);

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
            'qty' => array(
                'name' => __('Qty'),
                'class' => 'text-center',
                'style' => 'width:10%;',
            ),
            'stock' => array(
                'name' => __('Stok'),
                'class' => 'text-center',
                'style' => 'width:7%;',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
                'style' => 'width:7%;',
            ),
            'price_service' => array(
                'name' => __('Harga Jasa'),
                'class' => __('text-center wrapper-eksternal %s', $eksternalClass),
                'style' => 'width:15%;',
            ),
            'price' => array(
                'name' => __('Harga Barang'),
                'class' => __('text-center wrapper-eksternal %s', $eksternalClass),
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
                    'spk',
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'title' => __('Daftar Barang'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                    'data-form' => '#form-spk',
                )), array(
                'class' => "form-group",
            ));
            echo $this->Form->error('Spk.product', null, array(
                'class' => 'error-message form-group',
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
	        	<tbody>
                    <?php
                            $total_qty = 0;
                            $total_price_service = 0;
                            $total_price = 0;

                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $product = $this->Common->filterEmptyField($value, 'Product');

                                    $product_id = $this->Common->filterEmptyField($product, 'id');
                                    $code = $this->Common->filterEmptyField($product, 'code');
                                    $name = $this->Common->filterEmptyField($product, 'name');
                                    $stock = $this->Common->filterEmptyField($product, 'product_stock_cnt');
                                    $unit = $this->Common->filterEmptyField($product, 'ProductUnit', 'name');

                                    $qty = $this->Common->filterEmptyField($value, 'SpkProduct', 'qty', 0);
                                    $price_service = $this->Common->filterEmptyField($value, 'SpkProduct', 'price_service', 0);
                                    $price = $this->Common->filterEmptyField($value, 'SpkProduct', 'price', 0);

                                    $total_qty += $qty;
                                    $total_price_service += $price_service;
                                    $total_price += $price;

                    ?>
                    <tr class="pick-document" rel="<?php echo $product_id; ?>">
                        <?php
                                echo $this->Html->tag('td', $code.$this->Form->hidden('SpkProduct.product_id.'.$product_id, array(
                                    'value' => $product_id,
                                )));
                                echo $this->Html->tag('td', $name);
                                echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.qty.%s', $product_id), array(
                                    'type' => 'text',
                                    'frameClass' => false,
                                    'class' => 'input_number text-center price_custom',
                                    'rel' => 'qty',
                                    'value' => $qty,
                                    'fieldError' => __('SpkProduct.%s.qty', $key),
                                )));
                                echo $this->Html->tag('td', $stock, array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', $unit, array(
                                    'class' => 'text-center',
                                ));
                                echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.price_service.%s', $product_id), array(
                                    'type' => 'text',
                                    'frameClass' => false,
                                    'class' => 'text-right price_custom',
                                    'data-type' => 'input_price_coma',
                                    'rel' => 1,
                                    'value' => $this->Common->getFormatPrice($price_service, 0, 2),
                                    'fieldError' => __('SpkProduct.%s.price_service', $key),
                                )), array(
                                    'class' => __('wrapper-eksternal %s', $eksternalClass),
                                ));
                                echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.price.%s', $product_id), array(
                                    'type' => 'text',
                                    'frameClass' => false,
                                    'class' => 'text-right price_custom',
                                    'data-type' => 'input_price_coma',
                                    'rel' => 2,
                                    'value' => $this->Common->getFormatPrice($price, 0, 2),
                                    'fieldError' => __('SpkProduct.%s.price', $key),
                                )), array(
                                    'class' => __('wrapper-eksternal %s', $eksternalClass),
                                ));
                                echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                    'class' => 'delete-document btn btn-danger btn-xs',
                                    'escape' => false,
                                )), array(
                                    'class' => 'text-center',
                                ));
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
                                $price_service = $this->Common->getFormatPrice($total_price_service, 0, 2);
                                $price = $this->Common->getFormatPrice($total_price, 0, 2);

                                echo $this->Html->tag('td', __('Total'), array(
                                    'colspan' => 2,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $total_qty, array(
                                    'class' => 'text-center total_custom',
                                    'data-decimal' => 0,
                                    'rel' => 'qty',
                                ));
                                echo $this->Html->tag('td', '&nbsp;', array(
                                    'colspan' => 2,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $price_service, array(
                                    'class' => __('text-right total_custom wrapper-eksternal %s', $eksternalClass),
                                    'data-decimal' => 2,
                                    'rel' => 1,
                                ));
                                echo $this->Html->tag('td', $price, array(
                                    'class' => __('text-right total_custom wrapper-eksternal %s', $eksternalClass),
                                    'data-decimal' => 2,
                                    'rel' => 2,
                                ));
                        ?>
                    </tr>
                </tfoot>
	    	</table>
	    </div>
	</div>
</div>