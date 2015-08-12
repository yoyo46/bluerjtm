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
                    	echo $this->Form->label('customer_id', __('Customer *'));
	        	?>
	        	<div class="row">
	        		<div class="col-sm-10">
	        			<?php 
								echo $this->Form->input('customer_id',array(
									'label'=> false, 
									'class'=>'form-control customer-ajax',
									'required' => false,
									'empty' => __('Pilih Customer'),
									'options' => $list_customer,
									'id' => 'customer-val'
								));
						?>
	        		</div>
	        	</div>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('coa_id',array(
							'label'=> __('Account Kas/Bank *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Kas/Bank'),
							'options' => $coas
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('date_payment',array(
							'type' => 'text',
							'label'=> __('Tgl Pembayaran *'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'placeholder' => __('Tgl Pembayaran'),
							'value' => (!empty($this->request->data['InvoicePayment']['date_payment'])) ? $this->request->data['InvoicePayment']['date_payment'] : date('d/m/Y')
						));
				?>
	        </div>
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
	        <div class="form-group">
	        	<?php 
	        			$attrBrowse = array(
                            'class' => 'ajaxModal visible-xs',
                            'escape' => false,
                            'title' => __('Invoice Customer'),
                            'data-action' => 'browse-invoice',
                            'data-change' => 'getTtujInfoRevenue',
                            'url' => $this->Html->url( array(
	                            'controller'=> 'ajax', 
	                            'action' => 'getInfoInvoicePaymentDetail',
	                        ))
                        );
						$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                        echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Ambil Tagihan'), 'javascript:', $attrBrowse);
                ?>
	        </div>
	    </div>
	</div>
	<div class="invoice-info-detail <?php echo (!empty($this->request->data) && !empty($invoices)) ? '' : 'hide';?>">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Detail Info Pembayaran Invoice'); ?></h3>
		    </div>
		    <div class="box-body table-responsive">
		        <table class="table table-hover">
		        	<thead>
		        		<tr>
		        			<th><?php echo __('No.Invoice');?></th>
		                    <th><?php echo __('Tgl Invoice');?></th>
		                    <th class="text-center"><?php echo __('Periode');?></th>
		                    <th class="text-center"><?php echo __('Total');?></th>
		                    <th class="text-center"><?php echo __('Telah Dibayar');?></th>
		                    <th class="text-center" width="15%"><?php echo __('Invoice Dibayar');?></th>
		                    <th class="text-center"><?php echo __('Action');?></th>
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
	    		echo $this->Form->button(__('Simpan'), array(
	    			'type' => 'submit',
					'class'=> 'btn btn-success btn-lg',
				));
		?>
	</div>
</div>
<?php
		echo $this->Form->end();
?>