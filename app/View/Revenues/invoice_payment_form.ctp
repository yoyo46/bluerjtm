<?php
		$this->Html->addCrumb(__('Pembayaran Invoice'), array(
			'controller' => 'revenues',
			'action' => 'invoice_payments'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('InvoicePayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
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
	        <div class="form-group">
	        	<?php 
						// echo $this->Form->input('invoice_id',array(
						// 	'label'=> __('Kode Invoice *'), 
						// 	'class'=>'form-control invoice-ajax',
						// 	'required' => false,
						// 	'empty' => __('Pilih Kode Invoice'),
						// 	'options' => $list_invoices
						// ));

						echo $this->Form->input('customer_id',array(
							'label'=> __('Customer *'), 
							'class'=>'form-control customer-ajax',
							'required' => false,
							'empty' => __('Pilih Customer'),
							'options' => $list_customer
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('date_payment',array(
							'type' => 'text',
							'label'=> __('Tanggal Pembayaran *'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'placeholder' => __('Tanggal Pembayaran'),
							'value' => (!empty($this->request->data['InvoicePayment']['date_payment'])) ? $this->request->data['InvoicePayment']['date_payment'] : date('d/m/Y')
						));
				?>
	        </div>
	    </div>
	</div>
	<div id="invoice-info" class="<?php echo (!empty($this->request->data) && !empty($invoices)) ? '' : 'hide';?>">
    	<?php
    		echo $this->element('blocks/revenues/info_invoice_payment_detail');
    	?>
    </div>
	<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'invoice_payments', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Form->button(__('Buat Pembayaran Invoice'), array(
		    			'type' => 'submit',
						'class'=> 'btn btn-success btn-lg',
					));
			?>
		</div>
</div>
<?php
		echo $this->Form->end();
?>