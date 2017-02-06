<?php 
		$vendors = !empty($vendors)?$vendors:false;
		$type = !empty($type)?$type:false;
?>
<div class="wrapper-write-document">
	<?php 
			echo $this->Common->buildInputForm('ProductReceipt.vendor_id', __('Supplier *'), array(
				'type' => 'select',
				'empty' => __('- Pilih Supplier -'),
				'id' => 'supplier-val',
				'class' => 'form-control chosen-select',
				'options' => $vendors,
			));
	?>
	<div class="form-group">
		<?php
			echo $this->Form->label('document_number', __('No. Dok Ref *'));
		?>
		<div class="row">
			<div class="col-sm-10">
				<?php
						echo $this->Common->buildInputForm('ProductReceipt.document_number', false, array(
							'frameClass' => false,
							'id' => 'document-number',
							'attributes' => array(
								'placeholder' => __('No. Dok Ref'),
								'readonly' => true,
							),
							'fieldError' => 'ProductReceipt.document_id',
						));
						echo $this->Html->tag('span', '', array(
							'id' => 'available-date',
						));
				?>
			</div>
			<div class="col-sm-2 hidden-xs">
				<?php 
						$attrBrowse = array(
                            'class' => 'ajaxCustomModal btn bg-maroon',
                            'escape' => false,
                            'title' => __('Ambil Data'),
                            'data-check' => '#supplier-val',
                            'data-check-title' => 'supplier',
                    		'data-check-alert' => __('Mohon pilih supplier terlebih dahulu'),
                        );
    					$urlBrowse = array(
                            'controller'=> 'products', 
                            'action' => 'receipt_documents',
                            $type,
                            'admin' => false,
                        );
                        echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
                ?>
			</div>
		</div>
	</div>
</div>