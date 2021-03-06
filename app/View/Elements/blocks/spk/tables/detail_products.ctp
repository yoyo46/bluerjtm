<?php 
        $view = !empty($view)?$view:false;
        $data = $this->request->data;

        $dataDetail = Common::hashEmptyField($data, 'SpkProduct');
        // $eksternalClass = Common::_callDisplayToggle('eksternal', $data);
        $priceClass = Common::_callDisplayToggle('price', $data);
        $document_type = Common::hashEmptyField($data, 'Spk.document_type');

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
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'width:12%;',
            ),
            'price' => array(
                'name' => __('Harga Satuan'),
                'class' => __('text-center wrapper-price %s', $priceClass),
                'style' => 'width:15%;',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'class' => 'text-center',
                'style' => 'width:10%;',
            ),
            // 'price_service_type' => array(
            //     'name' => __('Jenis Harga'),
            //     'class' => __('text-center wrapper-eksternal %s', $eksternalClass),
            //     'style' => 'width:10%;',
            // ),
            // 'price_service' => array(
            //     'name' => __('Harga Jasa'),
            //     'class' => __('text-center wrapper-eksternal %s', $eksternalClass),
            //     'style' => 'width:15%;',
            // ),
            'grandtotal' => array(
                'name' => __('Total'),
                'class' => __('text-center wrapper-price %s', $priceClass),
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
        
        if( $view != 'detail' ) {
            echo $this->Form->error('Spk.product', null, array(
                'class' => 'error-message form-group',
            ));
        }
?>
<div class="temp-document-picker document-calc" rel="spk">
	<div class="box box-success">
	    <?php 
	            echo $this->element('blocks/common/box_header', array(
	                'title' => __('Informasi Barang'),
	            ));
	    ?>
	    <div class="box-body">
            <?php 
                    if( $view != 'detail' ) {
                        echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                                'controller'=> 'ajax', 
                                'action' => 'products',
                                'spk',
                                'admin' => false,
                            )), array(
                                'escape' => false,
                                'allow' => true,
                                'title' => __('Daftar Barang'),
                                'class' => 'btn bg-maroon ajaxCustomModal',
                                'data-form' => '#form-spk',
                                'data-check' => '#spk-document-type',
                                'data-check-named' => 'document_type',
                                'data-check-alert' => __('Mohon pilih jenis spk terlebih dahulu'),
                            )), array(
                            'class' => 'form-group mb30',
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
                                // $total_price_service = 0;
                                $total_price = 0;
                                $grandtotal = 0;

                                if(!empty($dataDetail)){
                                    foreach ($dataDetail as $key => $value) {
                                        $product = Common::hashEmptyField($value, 'Product');

                                        $product_id = Common::hashEmptyField($product, 'id');
                                        $code = Common::hashEmptyField($product, 'code');
                                        $name = Common::hashEmptyField($product, 'name');
                                        $unit = Common::hashEmptyField($product, 'ProductUnit.name');
                                        $stock = Common::hashEmptyField($product, 'product_stock_cnt', 0);

                                        $note = Common::hashEmptyField($value, 'SpkProduct.note');
                                        $qty = Common::hashEmptyField($value, 'SpkProduct.qty', 0);
                                        // $price_service = Common::hashEmptyField($value, 'SpkProduct.price_service', 0);
                                        $price = Common::hashEmptyField($value, 'SpkProduct.price', 0);
                                        
                                        $document_status = Common::hashEmptyField($value, 'SpkProduct.document_status');
                                        $draft_document_status = Common::hashEmptyField($value, 'SpkProduct.draft_document_status');
                                        $receipt_status = Common::hashEmptyField($value, 'SpkProduct.receipt_status');
                                        $retur_status = Common::hashEmptyField($value, 'SpkProduct.retur_status');

                                        // $price_service_type = Common::hashEmptyField($value, 'SpkProduct.price_service_type', 'borongan');
                                        $group = Common::hashEmptyField($value, 'Product.ProductCategory.name');
                                        $group_slug = Common::toSlug($group);

                                        // switch ($price_service_type) {
                                        //     case 'satuan':
                                        //         $customTotal = $price_service*$qty;
                                        //         break;
                                            
                                        //     default:
                                        //         $customTotal = $price_service;
                                        //         break;
                                        // }
                                        $customTotal = $price*$qty;

                                        $total_qty += $qty;
                                        // $total_price_service += $price_service;
                                        $total_price += $price;
                                        $grandtotal += $customTotal;

                        ?>
                        <tr class="pick-document" rel="<?php echo $product_id; ?>">
                            <?php
                                    echo $this->Html->tag('td', $code.$this->Form->hidden('SpkProduct.product_id.'.$product_id, array(
                                        'value' => $product_id,
                                    )));

                                    if( $group_slug == 'ban' ) {
                                        $tires = Common::hashEmptyField($value, 'SpkProductTire', array());
                                        $tires = Common::hashEmptyField($value, 'SpkProduct.SpkProductTire', $tires);
                                        $tire_count = count($tires);

                                        echo $this->Html->tag('td', $name.$this->Html->link(!empty($tire_count)?__('Set (%s Ban)', $tire_count):__('Posisi'), array(
                                            'controller' => 'spk',
                                            'action' => 'wheel_position',
                                            $product_id,
                                            'view' => $view,
                                        ), array(
                                            'class' => 'wheel-position wrapper-internal',
                                            'title' => __('Pilih Posisi Ban'),
                                            'rel' => $product_id,
                                            'style' => ($document_type == 'internal')?'':'display:none;',
                                        )).$this->Form->error(__('SpkProduct.%s.empty_tire', $key)));

                                        if( !empty($tires) ) {
                                            foreach ($tires as $key => $tire) {
                                                $position = Common::hashEmptyField($tire, 'SpkProductTire.position');
                                                $position = Common::hashEmptyField($tire, 'position', $position);

                                                echo $this->Form->hidden('SpkProduct.tire_position.'.$product_id.'.', array(
                                                    'value' => $position,
                                                    'class'=> 'wheel-position-input',
                                                    'rel' => $product_id,
                                                ));
                                            }
                                        }
                                    } else {
                                        echo $this->Html->tag('td', $name);
                                    }

                                    echo $this->Html->tag('td', $stock, array(
                                        'class' => 'text-center',
                                    ));
                                    echo $this->Html->tag('td', $unit, array(
                                        'class' => 'text-center',
                                    ));
                                    
                                    if( empty($view) ) {
                                        echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.note.%s', $product_id), array(
                                            'type' => 'text',
                                            'frameClass' => false,
                                            'value' => $note,
                                            'fieldError' => __('SpkProduct.%s.note', $key),
                                        )));

                                        echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.price.%s', $product_id), array(
                                            'type' => 'text',
                                            'frameClass' => false,
                                            'class' => 'text-right price_custom price',
                                            'data-type' => 'input_price_coma',
                                            'rel' => 2,
                                            'value' => $this->Common->getFormatPrice($price, 0, 2),
                                            'fieldError' => __('SpkProduct.%s.price', $key),
                                        )), array(
                                            'class' => __('wrapper-price %s', $priceClass),
                                        ));

                                        echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.qty.%s', $product_id), array(
                                            'type' => 'text',
                                            'frameClass' => false,
                                            'class' => 'input_number text-center price_custom qty',
                                            'rel' => 'qty',
                                            'value' => $qty,
                                            'fieldError' => __('SpkProduct.%s.qty', $key),
                                        )));

                                        // echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.price_service_type.%s', $product_id), array(
                                        //     'frameClass' => false,
                                        //     'class' => 'td-select calc-type',
                                        //     'value' => $price_service_type,
                                        //     'options' => Common::_callPriceServiceType(),
                                        // )), array(
                                        //     'class' => __('wrapper-eksternal %s', $eksternalClass),
                                        // ));

                                        // echo $this->Html->tag('td', $this->Common->_callInputForm(__('SpkProduct.price_service.%s', $product_id), array(
                                        //     'type' => 'text',
                                        //     'frameClass' => false,
                                        //     'class' => 'text-right price_custom price',
                                        //     'data-type' => 'input_price_coma',
                                        //     'rel' => 1,
                                        //     'value' => $this->Common->getFormatPrice($price_service, 0, 2),
                                        //     'fieldError' => __('SpkProduct.%s.price_service', $key),
                                        // )), array(
                                        //     'class' => __('wrapper-eksternal %s', $eksternalClass),
                                        // ));
                                    } else {
                                        // $price_service_type = Common::hashEmptyField(Common::_callPriceServiceType(), $price_service_type);

                                        echo $this->Html->tag('td', $note);

                                        echo $this->Html->tag('td', $this->Html->tag('div', $this->Common->getFormatPrice($price, 0, 2), array(
                                            'class' => 'text-center',
                                            'class' => 'text-right price_custom',
                                            'data-type' => 'input_price_coma',
                                            'rel' => 2,
                                        )), array(
                                            'class' => __('wrapper-price %s', $priceClass),
                                        ));

                                        echo $this->Html->tag('td', $qty, array(
                                            'class' => 'text-center price_custom',
                                            'rel' => 'qty',
                                        ));

                                        // echo $this->Html->tag('td', $price_service_type, array(
                                        //     'class' => __('wrapper-eksternal %s', $eksternalClass),
                                        // ));

                                        // echo $this->Html->tag('td', $this->Html->tag('div', $this->Common->getFormatPrice($price_service, 0, 2), array(
                                        //     'class' => 'text-center',
                                        //     'rel' => 1,
                                        //     'class' => 'text-right price_custom',
                                        //     'data-type' => 'input_price_coma',
                                        // )), array(
                                        //     'class' => __('wrapper-eksternal %s', $eksternalClass),
                                        // ));
                                    }

                                    echo $this->Html->tag('td', $this->Common->getFormatPrice($customTotal, '0', 2), array(
                                        'rel' => 'grandtotal',
                                        'class' => __('wrapper-price %s total text-right total_row price_custom', $priceClass),
                                        'data-type' => 'input_price_coma',
                                    ));

                                    if( empty($view) ) {
                                        echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                                            'class' => 'delete-document btn btn-danger btn-xs',
                                            'escape' => false,
                                        )), array(
                                            'class' => 'text-center',
                                        ));
                                    }
                            ?>
                        </tr>
                        <?php
                                        echo $this->Form->hidden('SpkProduct.document_status.'.$product_id, array(
                                            'value' => $document_status,
                                        ));
                                        echo $this->Form->hidden('SpkProduct.draft_document_status.'.$product_id, array(
                                            'value' => $draft_document_status,
                                        ));
                                        echo $this->Form->hidden('SpkProduct.receipt_status.'.$product_id, array(
                                            'value' => $receipt_status,
                                        ));
                                        echo $this->Form->hidden('SpkProduct.retur_status.'.$product_id, array(
                                            'value' => $retur_status,
                                        ));
                                    }
                                }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="grandtotal">
                            <?php
                                    // $price_service = $this->Common->getFormatPrice($total_price_service, 0, 2);
                                    $price = $this->Common->getFormatPrice($total_price, 0, 2);
                                    $grandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);

                                    echo $this->Html->tag('td', __('Total'), array(
                                        'colspan' => 5,
                                        'class' => 'text-right',
                                    ));
                                    // echo $this->Html->tag('td', $price, array(
                                    //     'class' => __('text-right total_custom wrapper-eksternal %s', $eksternalClass),
                                    //     'data-decimal' => 2,
                                    //     'rel' => 2,
                                    // ));
                                    echo $this->Html->tag('td', '', array(
                                        'class' => __('wrapper-price %s', $priceClass),
                                    ));
                                    echo $this->Html->tag('td', $total_qty, array(
                                        'class' => 'text-center total_custom',
                                        'data-decimal' => 0,
                                        'rel' => 'qty',
                                    ));
                                    // echo $this->Html->tag('td', '', array(
                                    //     'class' => __('wrapper-eksternal %s', $eksternalClass),
                                    //     'colspan' => 2,
                                    // ));
                                    // echo $this->Html->tag('td', $price_service, array(
                                    //     'class' => __('text-right total_custom wrapper-eksternal %s', $eksternalClass),
                                    //     'data-decimal' => 2,
                                    //     'rel' => 1,
                                    // ));
                                    echo $this->Html->tag('td', $grandtotal, array(
                                        'class' => __('text-right total_custom wrapper-price %s', $priceClass),
                                        'data-decimal' => 2,
                                        'rel' => 'grandtotal',
                                    ));
                                    
                                    if( empty($view) ) {
                                        echo $this->Html->tag('td', '&nbsp;');
                                    }
                            ?>
                        </tr>
                    </tfoot>
    	    	</table>
            </div>
	    </div>
	</div>
</div>