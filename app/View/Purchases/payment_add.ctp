<?php
		$title = __('Pembayaran PO');
		$urlRoot = array(
			'controller' => 'purchases',
			'action' => 'payments',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;
		$data = $this->request->data;

		$vendor_id = Common::hashEmptyField($value, 'PurchaseOrderPayment.vendor_id');
		$this->request->data['PurchaseOrderPayment']['document_type'] = Common::hashEmptyField($data, 'PurchaseOrderPayment.document_type', 'po');

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		$documentTypes = array(
	    	'po' => __('PO'),
	    	'spk' => __('SPK Eksternal'),
    	);

		echo $this->Form->create('PurchaseOrderPayment');
?>
<div class="box box-primary">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
    ?>
    <div class="box-body">
    	<?php 
				echo $this->Common->buildInputForm('transaction_date', __('Tgl Dibayar *'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'class' => 'form-control pull-right custom-date',
				));
				echo $this->Common->buildInputForm('coa_id', __('Account Kas/Bank *'), array(
					'empty' => __('- Pilih Kas/Bank -'),
					'class' => 'form-control chosen-select',
				));
		?>
		<div class="form-group">
			<?php 
					echo $this->Form->label('document_type', __('Tipe Dokumen *'), array(
	                    'class' => 'control-label',
					));
			?>
			<div class="row">
	    		<div class="col-sm-2">
					<div class="radio">
	    				<label>
				    		<?php 
				    				echo $this->Form->radio('document_type', $documentTypes, array(
							    		'class' => 'ajax-change',
							    		'href' => $this->Html->url(array(
							    			'action' => 'payment_choose_documents',
						    			)),
						    			'data-wrapper-write' => '.wrapper-write-document',
										'data-empty' => '.temp-document-picker table tbody',
			    						'data-trigger' => 'handle-toggle-click',
				    					'legend' => false,
									    'separator' => '</label></div></div><div class="col-sm-2"><div class="radio"><label>',
			    						'data-match' => '[[\'.wrapper-po\', [\'po\']],[\'.wrapper-spk\', [\'spk\']]]',
									));
				    		?>
	    				</label>
	    			</div>
    			</div>
			</div>
		</div>
		<?php
        		echo $this->element('blocks/purchases/payments/forms/payment_choose_documents');
				echo $this->Common->buildInputForm('note', __('Keterangan'));
		?>
	</div>
</div>
<?php     	
        echo $this->element('blocks/purchases/payments/tables/detail');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));

			if( empty($view) ) {
				$this->Common->_getButtonPostingUnposting( $value, 'PurchaseOrderPayment', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->end();
?>