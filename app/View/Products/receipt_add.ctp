<?php
		$title = __('Penerimaan Barang');
		$urlRoot = array(
			'controller' => 'products',
			'action' => 'receipts',
			'admin' => false,
		);
		$data = $this->request->data;
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;
		$documentTypes = array(
	    	'po' => __('PO'),
	    	'wht' => __('WHT'),
	    	'production' => __('Produksi'),
    	);

    	$document_type = Common::hashEmptyField($data, 'ProductReceipt.document_type');
    	$last_transaction_date = Common::hashEmptyField($data, 'ProductReceipt.last_transaction_date', null, array(
    		'date' => 'd/m/Y',
		));

    	if( !empty($spk_internal_policy) && $spk_internal_policy == 'receipt' ) {
    		$documentTypes['spk'] = __('SPK External');
    	}

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

        echo $this->element('blocks/cashbanks/tables/list_approvals', array(
        	'urlBack' => false,
        	'modelName' => 'ProductReceipt',
    	));

		echo $this->Form->create('ProductReceipt', array(
			'class' => 'receipt-form',
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => $title,
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('transaction_date', __('Tgl Penerimaan *'), array(
							'type' => 'text',
		                    'textGroup' => $this->Common->icon('calendar'),
		                    'class' => 'form-control pull-right custom-date',
		                    'attributes' => array(
								'error' => array(
									'checkDocDate' => __('Tgl penerimaan tidak boleh lebih kecil dari tgl penerimaan sebelumnya - %s', $last_transaction_date),
								),
	                    	),
						));
				?>
				<div class="form-group">
					<?php 
							echo $this->Form->label('document_type', __('Tipe Penerimaan *'), array(
			                    'class' => 'control-label',
							));
					?>
					<div class="row">
			    		<div class="col-sm-3">
							<div class="radio">
			    				<label>
						    		<?php 
						    				echo $this->Form->radio('document_type', $documentTypes, array(
									    		'class' => 'ajax-change',
									    		'href' => $this->Html->url(array(
									    			'controller' => 'products',
									    			'action' => 'receipt_choose_documents',
								    			)),
								    			'data-wrapper-write' => '.wrapper-write-document',
						    					'legend' => false,
											    'separator' => '</label></div></div><div class="col-sm-3"><div class="radio"><label>',
					    						'data-match' => '[[\'.wrapper-warehouse\', [\'po\',\'production\',\'spk\'], \'slide\']]',
					    						'data-trigger' => 'handle-toggle',
											));
						    		?>
			    				</label>
			    			</div>
		    			</div>
	    			</div>
    			</div>
				<?php
						echo $this->Common->buildInputForm('employe_id', __('Diterima Oleh *'), array(
							'type' => 'select',
							'empty' => __('- Pilih Penerima -'),
							'class' => 'form-control chosen-select',
						));
						echo $this->Html->tag('div', $this->Common->buildInputForm('to_branch_id', __('Gudang Penerima *'), array(
							'type' => 'select',
							'empty' => __('- Pilih Gudang -'),
							'class' => 'form-control chosen-select',
						)), array(
							'class' => 'wrapper-warehouse',
						));
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-warning">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => __('Informasi Dokumen'),
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
        				echo $this->element('blocks/products/receipts/forms/receipt_choose_document');
						echo $this->Common->buildInputForm('note', __('Keterangan'), array(
							'type' => 'textarea',
						));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php     	
        echo $this->element('blocks/products/receipts/tables/detail_products');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));
			
			if( empty($view) ) {
				$this->Common->_getButtonPostingUnposting( $value, 'ProductReceipt', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->hidden('session_id');
		echo $this->Form->end();
?>