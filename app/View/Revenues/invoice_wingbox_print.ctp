<?php
        $company = Common::hashEmptyField($invoice, 'Company.name');
		$customer = Common::hashEmptyField($invoice, 'Customer.name');
        $customer_address = Common::hashEmptyField($invoice, 'Customer.address');
        $no_invoice = Common::hashEmptyField($invoice, 'Invoice.no_invoice');
		$invoice_year = Common::hashEmptyField($invoice, 'Invoice.invoice_date', NULL, array(
            'date' => 'Y',
        ));

        $element = 'blocks/revenues/prints/invoice_wingbox_print';


        $dataColumns = array(
            'no' => array(
                'name' => __('NO'),
                'style' => 'text-align: center;vertical-align: middle;background-color: #ccc; border: 1px solid #ddd;',
                'rowspan' => 3,
            ),
            'no_do' => array(
                'name' => $this->Html->tag('div', __('No DO'), array(
                    'style' => 'width: 80px;',
                )),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                'rowspan' => 3,
            ),
            'tujuan_kirim' => array(
                'name' => __('Tujuan Kirim'),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                // 'fix_column' => true,
                'child' => array(
                    'dealer' => array(
                        'name' => $this->Html->tag('div', __('Nama PT/CV'), array(
                            'style' => 'width: 150px;',
                        )),
                        'rowspan' => 2,
                        'style' => 'background-color: #ccc; border: 1px solid #ddd;',
                    ),
                    'city' => array(
                        'name' => $this->Html->tag('div', __('Kota'), array(
                            'style' => 'width: 150px;',
                        )),
                        'rowspan' => 2,
                        'style' => 'background-color: #ccc; border: 1px solid #ddd;',
                    ),
                ),
            ),
            'ttuj_date' => array(
                'name' => $this->Html->tag('div', __('Tgl Kirim'), array(
                    'style' => 'width: 80px;',
                )),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                'rowspan' => 3,
            ),
            'tgljam_tiba' => array(
                'name' => $this->Html->tag('div', __('Tgl Truk Sampai'), array(
                    'style' => 'width: 80px;',
                )),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                'rowspan' => 3,
            ),
            'tgl_terima' => array(
                'name' => $this->Html->tag('div', __('Tgl Terima'), array(
                    'style' => 'width: 80px;',
                )),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                'rowspan' => 3,
            ),
            'no_sj' => array(
                'name' => $this->Html->tag('div', __('No. Surat Jalan SMII'), array(
                    'style' => 'width: 150px;',
                )),
                'rowspan' => 3,
                'style' => 'background-color: #ccc; border: 1px solid #ddd;',
            ),
            'armada' => array(
                'name' => __('Armada'),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                'child' => array(
                    'truck_type' => array(
                        'name' => $this->Html->tag('div', __('Type Truck'), array(
                            'style' => 'width: 100px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                    'nopol' => array(
                        'name' => $this->Html->tag('div', __('No. Pol'), array(
                            'style' => 'width: 100px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                ),
            ),
            'biaya_biaya' => array(
                'name' => __('BIAYA-BIAYA'),
                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                'colspan' => 7,
                'child' => array(
                    'qty' => array(
                        'name' => $this->Html->tag('div', __('Jml Muatan<br>( dlm Kg )'), array(
                            'style' => 'width: 80px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                    'price_per_unit' => array(
                        'name' => $this->Html->tag('div', __('Biaya<br>( per Kg )'), array(
                            'style' => 'width: 100px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                    'price' => array(
                        'name' => $this->Html->tag('div', __('Biaya Utama'), array(
                            'style' => 'width: 100px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                    'multi_drop' => array(
                        'name' => $this->Html->tag('div', __('+ Multi Drop'), array(
                            'style' => 'width: 80px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                    'night_charges' => array(
                        'name' => $this->Html->tag('div', __('Overnight Charges'), array(
                            'style' => 'width: 80px;',
                        )),
                        'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                        'rowspan' => 2,
                    ),
                    'lain_lain_cost' => array(
                        'name' => __('Lain-Lain Cost'),
                        'style' => 'background-color: #ccc; border: 1px solid #ddd;',
                        'child' => array(
                            'extra_charges' => array(
                                'name' => $this->Html->tag('div', __('Over Tonase<br>( Kg )'), array(
                                    'style' => 'width: 80px;',
                                )),
                                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                            ),
                            'total' => array(
                                'name' => $this->Html->tag('div', __('Rp'), array(
                                    'style' => 'width: 100px;',
                                )),
                                'style' => 'text-align:center;background-color: #ccc; border: 1px solid #ddd;',
                            ),
                        ),
                    ),
                ),
            ),
            'no_invoice' => array(
                'name' => $this->Html->tag('div', __('No. Invoice Transporter'), array(
                    'style' => 'width: 120px;',
                )),
                'rowspan' => 3,
                'style' => 'background-color: #ccc; border: 1px solid #ddd;',
            ),
            'cc' => array(
                'name' => __('CC'),
                'rowspan' => 3,
                'style' => 'background-color: #ccc; border: 1px solid #ddd;',
            ),
            'note' => array(
                'name' => $this->Html->tag('div', __('Remark'), array(
                    'style' => 'width: 100px;',
                )),
                'rowspan' => 3,
                'style' => 'background-color: #ccc; border: 1px solid #ddd;',
            ),
        );

        if( !empty($action_print) ){
            $fieldColumn = $this->Common->_generateShowHideColumn($dataColumns, 'field-table', false, array(
                // 'style' => 'background-color: #ccc; border: 1px solid #ddd; color: #333; font-weight: bold; padding: 0 10px;',
            ));

            echo $this->element(sprintf('blocks/common/tables/export_%s', $action_print), array(
                'tableContent' => $this->element($element, array(
	            	'fieldColumn' => $fieldColumn,
	        	)),
                'filename' => $sub_module_title,
                'sub_module_title' => false,
                'print_by' => false,
            ));
        } else {
    		$this->Html->addCrumb(__('Invoice'), array(
    			'controller' => 'revenues',
    			'action' => 'invoices'
			));
    		$this->Html->addCrumb($sub_module_title);
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

			$print = $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
				'class' => 'btn btn-default hidden-print print-window pull-right',
				'escape' => false
			));
            $print .= $this->Common->_getPrint();

            echo $this->Html->tag('div', $print, array(
            	'class' => 'action-print',
        	));
?>

<div class="header-title">
	<?php 
			echo $this->Html->tag('h3', __('DETAIL SUMMARY COST DELIVERY %s', $invoice_year), array(
				'class' => 'header-invoice',
				'style' => 'text-align:center;'
			));
	?>
    <div class="sub-title" style="margin-bottom: 20px;">
    <?php 
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => $this->Html->tag('strong', __('Nama Vendor')),
                'value' => $company,
            ));
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => $this->Html->tag('strong', __('Nama Customer')),
                'value' => $customer,
            ));
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => $this->Html->tag('strong', __('Alamat')),
                'value' => $customer_address,
            ));
    ?>
</div>
	<div class="clear"></div>
</div>
<?php
            
            echo $this->Html->tag('div', $this->element($element, array(
            	'fieldColumn' => $fieldColumn,
        	)), array(
            	'class' => 'invoice-print table-responsive',
        	));

        	if( empty($preview) ) {
?>
<div class="box-footer text-center action hidden-print">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
                'controller' => 'revenues', 
				'action' => 'invoices', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php 
			}
		}
?>