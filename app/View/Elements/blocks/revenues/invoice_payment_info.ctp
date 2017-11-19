<div class="form-group">
	<?php 
			echo $this->Form->input('Invoice.total',array(
				'label'=> __('Total Pembayaran Invoice'), 
				'class'=>'form-control',
				'required' => false,
				'placeholder' => __('Total Pembayaran Invoice'),
				'readonly' => true,
				'value' => !empty($invoice_real['Invoice']['total']) ? $this->Number->currency($invoice_real['Invoice']['total'], Configure::read('__Site.config_currency_code'), array('places' => 0)) : 0,
				'type' => 'text',
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('InvoicePayment.total_payment_before',array(
				'label'=> __('Total Pembayaran Sebelumnya'), 
				'class'=>'form-control',
				'required' => false,
				'placeholder' => __('Total Pembayaran'),
				'readonly' => true,
				'value' => (!empty($invoice[0]['total_payment'])) ? $this->Number->currency($invoice[0]['total_payment'], Configure::read('__Site.config_currency_code'), array('places' => 0)) : 0,
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('InvoicePayment.total_payment',array(
				'label'=> __('Total Pembayaran *'), 
				'class'=>'form-control',
				'required' => false,
				'placeholder' => __('Total Pembayaran'),
			));
	?>
</div>