<?php 
		$vendors = !empty($vendors)?$vendors:false;
?>
<div class="wrapper-write-document">
	<?php
			echo $this->Common->buildInputForm('PurchaseOrderPayment.vendor_id', __('Supplier *'), array(
				'type' => 'select',
				'empty' => __('- Pilih Supplier -'),
				'id' => 'supplier-val',
				'options' => $vendors,
				'class' => 'form-control empty-change',
				'attributes' => array(
					'data-target' => '.temp-document-picker table tbody',
				),
			));
	?>
</div>