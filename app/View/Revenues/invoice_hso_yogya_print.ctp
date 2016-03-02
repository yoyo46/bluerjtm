<?php
        $tarif_name = !empty($tarif_name)?$tarif_name:false;
		$customer = $this->Common->filterEmptyField($invoice, 'Customer', 'name');
		$no_invoice = $this->Common->filterEmptyField($invoice, 'Invoice', 'no_invoice');
        $element = 'blocks/revenues/prints/invoice_hso_yogya_print';
        $dataColumns = array(
            'no' => array(
                'name' => __('NO'),
				'style' => 'text-align:center;width: 5%;',
            ),
            'date' => array(
                'name' => __('TANGGAL'),
				'style' => 'text-align:center;width: 10%;',
            ),
            'dealer' => array(
                'name' => __('DEALER'),
				'style' => 'text-align:center;width: 12%;',
            ),
            'nopol' => array(
                'name' => __('NOPOL'),
				'style' => 'text-align:center;width: 10%;',
            ),
            'nobstk' => array(
                'name' => __('No BSTK'),
				'style' => 'text-align:center;width: 13%;',
            ),
            'qty' => array(
                'name' => __('QTY'),
				'style' => 'text-align:center;width: 10%;',
            ),
            'price' => array(
                'name' => __('HARGA'),
				'style' => 'text-align:center;width: 10%;',
            ),
            'jml' => array(
                'name' => __('JUMLAH'),
				'style' => 'text-align:center;width: 10%;',
            ),
            'ekspedisi' => array(
                'name' => __('EKSPEDISI'),
				'style' => 'text-align:center;width: 10%;',
            ),
            'noref' => array(
                'name' => __('NO REF'),
				'style' => 'text-align:center;width: 10%;',
            ),
        );

        if( !empty($action_print) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $action_print), array(
                'tableContent' => $this->element($element, array(
	            	'fieldColumn' => $fieldColumn,
	        	)),
                'filename' => $sub_module_title,
                'sub_module_title' => false,
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
			echo $this->Html->tag('h3', $customer, array(
				'class' => 'header-invoice pull-left',
				'style' => 'font-size: 18px;font-weight: 700;margin-top: 0;margin-bottom: 20px;'
			));
	?>
	<div class="no-invoice pull-right">
		<?php 
				echo $this->Html->tag('p', $no_invoice, array(
					'style' => 'font-size: 16px;font-weight: 700;margin: 0 0 5px;'
				));
				echo $this->Html->tag('p', $tarif_name, array(
					'style' => 'font-size: 16px;font-weight: 700;margin: 0;'
				));
		?>
	</div>
	<div class="clear"></div>
</div>
<?php
            
            echo $this->Html->tag('div', $this->element($element, array(
            	'fieldColumn' => $fieldColumn,
        	)), array(
            	'class' => 'invoice-print',
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