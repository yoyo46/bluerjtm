<?php
		$title = __('Retur Barang');
		$urlRoot = array(
			'controller' => 'products',
			'action' => 'retur',
			'admin' => false,
		);
		$data = $this->request->data;
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;
		$documentTypes = array(
	    	'po' => __('PO'),
    	);

    	$document_type = Common::hashEmptyField($data, 'ProductRetur.document_type');
    	$last_transaction_date = Common::hashEmptyField($data, 'ProductRetur.last_transaction_date', null, array(
    		'date' => 'd/m/Y',
		));

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		if( !empty($document_type) ) {
			$document_type = Common::hashEmptyField($documentTypes, $document_type);
		}

		echo $this->Form->create('ProductRetur', array(
			'class' => 'retur-form',
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
						echo $this->Common->buildInputForm('transaction_date', __('Tgl Retur *'), array(
							'type' => 'text',
		                    'textGroup' => $this->Common->icon('calendar'),
		                    'class' => 'form-control pull-right custom-date',
		                    'attributes' => array(
								'error' => array(
									'checkDocDate' => __('Tgl Retur tidak boleh lebih kecil dari tgl retur sebelumnya - %s', $last_transaction_date),
									'validateDate' => __('Tgl Retur tidak boleh lebih kecil dari tgl %s', $document_type),
								),
	                    	),
						));
				?>
				<div class="form-group">
					<?php 
							echo $this->Form->label('document_type', __('Tipe Retur *'), array(
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
									    			'action' => 'retur_choose_documents',
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
						echo $this->Common->buildInputForm('employe_id', __('Dicek Oleh *'), array(
							'type' => 'select',
							'empty' => __('- Pilih Karyawan -'),
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
        				echo $this->element('blocks/products/retur/forms/choose_document');
						echo $this->Common->buildInputForm('note', __('Keterangan'), array(
							'type' => 'textarea',
						));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php     	
        echo $this->element('blocks/products/retur/tables/detail_products');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));
			
			if( empty($view) ) {
				$this->Common->_getButtonPostingUnposting( $value, 'ProductRetur', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->hidden('session_id');
		echo $this->Form->end();
?>