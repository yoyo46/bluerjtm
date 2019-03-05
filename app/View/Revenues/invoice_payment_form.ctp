<?php
		$this->Html->addCrumb(__('Pembayaran Invoice'), array(
			'controller' => 'revenues',
			'action' => 'invoice_payments'
		));
		$this->Html->addCrumb($sub_module_title);

		$data = $this->request->data;
		$view = !empty($view)?$view:false;
		$customer_name_code = $this->Common->filterEmptyField($data, 'Customer', 'customer_name_code');
		$coa_name = $this->Common->filterEmptyField($data, 'Coa', 'coa_name');
		$cogs_name = $this->Common->filterEmptyField($data, 'Cogs', 'cogs_name', '-');

		echo $this->Form->create('InvoicePayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form" id="invoice-payment-form">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Informasi Invoice'); ?></h3>
	    </div>
	    <div class="box-body">
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('nodoc',array(
							'label'=> __('No. Dokumen *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('No. Dokumen'),
						));
				?>
	        </div>
	        <?php 
            		if( !empty($view) ) {
            			echo $this->Html->tag('div', 
            				$this->Html->tag('label', __('Customer')).
            				$this->Html->tag('div', $customer_name_code, array(
            					'class' => 'form-control',
        					)), array(
        					'class' => 'form-group',
    					));
					} else {
						echo $this->Common->_callInputForm('customer_id',array(
							'label' => __('Customer *'),
							'class'=>'form-control chosen-select customer-ajax',
							'required' => false,
							'empty' => __('Pilih Customer'),
							'options' => $list_customer,
							'id' => 'customer-val'
						));
					}

            		if( !empty($view) ) {
            			echo $this->Html->tag('div', 
            				$this->Html->tag('label', __('Account Kas/Bank')).
            				$this->Html->tag('div', $coa_name, array(
            					'class' => 'form-control',
        					)), array(
        					'class' => 'form-group',
    					));
					} else {
						echo $this->Common->_callInputForm('coa_id',array(
							'label'=> __('Account Kas/Bank *'), 
							'class'=>'form-control chosen-select',
							'required' => false,
							'empty' => __('Pilih Kas/Bank'),
							'options' => $coas
						));
					}

					if( !empty($view) ) {
            			echo $this->Html->tag('div', 
            				$this->Html->tag('label', __('Cost Center')).
            				$this->Html->tag('div', $cogs_name, array(
            					'class' => 'form-control',
        					)), array(
        					'class' => 'form-group',
    					));
					} else {
						echo $this->element('blocks/common/forms/cost_center');
					}

            		if( !empty($view) ) {
						$date_payment = $this->Common->filterEmptyField($data, 'InvoicePayment', 'date_payment', date('d M Y'));

            			echo $this->Html->tag('div', 
            				$this->Html->tag('label', __('Tgl Pembayaran')).
            				$this->Html->tag('div', $date_payment, array(
            					'class' => 'form-control',
        					)), array(
        					'class' => 'form-group',
    					));
					} else {
						$date_payment = $this->Common->filterEmptyField($data, 'InvoicePayment', 'date_payment', date('d/m/Y'));
						
						echo $this->Common->_callInputForm('date_payment',array(
							'type' => 'text',
							'label'=> __('Tgl Pembayaran *'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'placeholder' => __('Tgl Pembayaran'),
							'value' => $date_payment,
						));
					}
			?>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('description',array(
							'type' => 'textarea',
							'label'=> __('Keterangan'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Keterangan'),
						));
				?>
	        </div>
	        <?php 
	        		if( empty($view) ) {
	        			$attrBrowse = array(
                            'class' => 'ajaxModal visible-xs browse-docs',
                            'escape' => false,
                            'title' => __('Invoice Customer'),
                            'data-action' => 'browse-invoice',
                            'data-change' => 'getTtujInfoRevenue',
                            'url' => $this->Html->url( array(
	                            'controller'=> 'ajax', 
	                            'action' => 'getInfoInvoicePaymentDetail',
	                            'payment_id' => $id,
	                        ))
                        );
						$attrBrowse['class'] = 'btn bg-maroon ajaxModal';

                        echo $this->Html->tag('div',
                        	$this->Html->link(__('%s Ambil Tagihan', $this->Common->icon('plus-square')), 'javascript:', $attrBrowse), array(
                    		'class' => 'form-group',
                		));
                    }
            ?>
	    </div>
	</div>
	<div class="document-pick-info-detail <?php echo !empty($data['InvoicePaymentDetail']) ? '' : 'hide';?>">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Detail Info Pembayaran Invoice'); ?></h3>
		    </div>
		    <div class="box-body table-responsive document-calc">
		        <table class="table table-hover">
		        	<thead>
		        		<tr>
		        			<th><?php echo __('No.Invoice');?></th>
		                    <th><?php echo __('Tgl');?></th>
		                    <th class="text-center"><?php echo __('Periode');?></th>
		                    <th class="text-center"><?php echo __('Total');?></th>
		                    <th class="text-center"><?php echo __('Dibayar');?></th>
		                    <th class="text-center" width="10%"><?php echo __('Inv Dibayar');?></th>
		                    <th class="text-center" width="15%"><?php echo __('PPN');?></th>
		                    <th class="text-center" width="15%"><?php echo __('Pph');?></th>
		                    <th class="text-center" width="10%"><?php echo __('Total');?></th>
					        <?php 
					        		if( empty($view) ) {
				                        echo $this->Html->tag('th',__('Action'), array(
				                    		'class' => 'text-center',
				                		));
				                    }
				            ?>
		        		</tr>
		        	</thead>
		        	<tbody class="ttuj-info-table">
		                <?php
				    		echo $this->element('blocks/revenues/info_invoice_payment_detail');
				    	?>
		        	</tbody>
		    	</table>
		    </div>
		</div>
    </div>
	<div class="box-footer text-center action">
		<?php
	    		echo $this->Html->link(__('Kembali'), array(
					'action' => 'invoice_payments', 
				), array(
					'class'=> 'btn btn-default',
				));

				if( empty($view) ) {
					$this->Common->_getButtonPostingUnposting( $data_local, 'InvoicePayment', array( 'Commit', 'Draft' ) );
				}
		?>
	</div>
</div>
<?php
		echo $this->Form->end();
?>