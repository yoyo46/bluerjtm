<?php 
        $view = !empty($view)?$view:false;
        $data = $this->request->data;

        $dataDetail = $this->Common->filterEmptyField($data, 'SpkProduction');

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
                'name' => __('Harga Satuan'),
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
            echo $this->Form->error('Spk.production', null, array(
                'class' => 'error-message form-group',
            ));
        }
?>
<div class="temp-document-picker document-calc" rel="productions">
	<div class="box box-success">
	    <?php 
	            echo $this->element('blocks/common/box_header', array(
	                'title' => __('Informasi Barang yg dihasilkan'),
	            ));
	    ?>
	    <div class="box-body">
            <?php 
                    if( $view != 'detail' ) {
                        echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                                'controller'=> 'ajax', 
                                'action' => 'products',
                                'productions',
                                'admin' => false,
                            )), array(
                                'escape' => false,
                                'title' => __('Daftar Barang'),
                                'class' => 'btn bg-maroon ajaxCustomModal',
                                'data-form' => '#form-spk',
                            )), array(
                            'class' => "form-group mb30",
                        ));
                    }
            ?>
            <div class="table-responsive">
    	        <table class="table table-hover" id="wrapper-write">
    		        <?php
    	                    if( !empty($fieldColumn) ) {
    	                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
    	                    }
    	            ?>
    	        	<tbody>
                        <?php
                                $total_qty = 0;
                                $total_price = 0;

                                if(!empty($dataDetail)){
                                    foreach ($dataDetail as $key => $value) {
                                        $product = $this->Common->filterEmptyField($value, 'Product');

                                        $product_id = $this->Common->filterEmptyField($product, 'id');
                                        $code = $this->Common->filterEmptyField($product, 'code');
                                        $name = $this->Common->filterEmptyField($product, 'name');
                                        $unit = $this->Common->filterEmptyField($product, 'ProductUnit', 'name');

                                        $qty = $this->Common->filterEmptyField($value, 'SpkProduction', 'qty', 0);
                                        $price = $this->Common->filterEmptyField($value, 'SpkProduction', 'price', 0);

                                        $total_qty += $qty;
                                        $total_price += $price;

                        ?>
                        <tr class="pick-document" rel="<?php echo $product_id; ?>">
                            <?php
                                    echo $this->Html->tag('td', $code.$this->Form->hidden('SpkProduction.product_id.'.$product_id, array(
                                        'value' => $product_id,
                                    )));
                                    echo $this->Html->tag('td', $name);
                                    echo $this->Html->tag('td', $unit, array(
                                        'class' => 'text-center',
                                    ));
                                    
                                    if( empty($view) ) {
                                        echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduction.qty.%s', $product_id), array(
                                            'type' => 'text',
                                            'frameClass' => false,
                                            'class' => 'input_number text-center price_custom',
                                            'rel' => 'qty',
                                            'value' => $qty,
                                            'fieldError' => __('SpkProduction.%s.qty', $key),
                                        )));
                                    } else {
                                        echo $this->Html->tag('td', $qty, array(
                                            'class' => 'text-center price_custom',
                                            'rel' => 'qty',
                                        ));
                                    }
                                    
                                    if( empty($view) ) {
                                        echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduction.price.%s', $product_id), array(
                                            'type' => 'text',
                                            'frameClass' => false,
                                            'class' => 'input_price_coma text-right price_custom',
                                            'rel' => 'price',
                                            'value' => $price,
                                            'fieldError' => __('SpkProduction.%s.price', $key),
                                            'data-decimal' => 2,
                                        )));
                                    } else {
                                        echo $this->Html->tag('td', $price, array(
                                            'class' => 'text-right',
                                            'rel' => 'price',
                                        ));
                                    }

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
                                    echo $this->Html->tag('td', __('Total'), array(
                                        'colspan' => 3,
                                        'class' => 'text-right',
                                    ));
                                    echo $this->Html->tag('td', $total_qty, array(
                                        'class' => 'text-center total_custom',
                                        'data-decimal' => 0,
                                        'rel' => 'qty',
                                    ));
                                    echo $this->Html->tag('td', $this->Common->getFormatPrice($total_price, 0, 2), array(
                                        'class' => 'text-right total_custom',
                                        'data-decimal' => 2,
                                        'rel' => 'price',
                                    ));
                                    echo $this->Html->tag('td', '&nbsp;');
                            ?>
                        </tr>
                    </tfoot>
    	    	</table>
            </div>
	    </div>
	</div>
</div>