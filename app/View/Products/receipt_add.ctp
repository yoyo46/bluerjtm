<?php
		$title = __('Penerimaan Barang');
		$urlRoot = array(
			'controller' => 'products',
			'action' => 'receipts',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;

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
						    				echo $this->Form->radio('document_type', array(
										    	'po' => __('PO'),
										    	'spk' => __('SPK External'),
										    	'wht' => __('WHT'),
										    	'production' => __('Produksi'),
									    	), array(
									    		'class' => 'ajax-change',
									    		'href' => $this->Html->url(array(
									    			'controller' => 'products',
									    			'action' => 'receipt_choose_documents',
								    			)),
								    			'data-wrapper-write' => '.wrapper-write-document',
						    					'legend' => false,
											    'separator' => '</label></div></div><div class="col-sm-3"><div class="radio"><label>',
											));
						    		?>
			    				</label>
			    			</div>
		    			</div>
	    			</div>
    			</div>
				<?php
						echo $this->Common->buildInputForm('employe_id', __('Diterima Oleh *'), array(
							'empty' => __('- Pilih Penerima -'),
							'class' => 'form-control chosen-select',
						));
						echo $this->Common->buildInputForm('to_branch_id', __('Gudang Penerima *'), array(
							'empty' => __('- Pilih Gudang -'),
							'class' => 'form-control chosen-select',
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
						echo $this->Common->buildInputForm('note', __('Keterangan'));
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
		echo $this->Form->end();
?>