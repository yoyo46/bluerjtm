<?php
		$this->Html->addCrumb(__('Invoice'), array(
			'controller' => 'revenues',
			'action' => 'invoices'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Invoice', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));

		echo $this->Form->hidden('transaction_status', array(
			'id' => 'transaction_status'
		));
?>
<div class="ttuj-form">
	<div id="step1">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Invoice'); ?></h3>
				    </div>
				    <div class="box-body">
				    	<?php
				    		if($action != 'tarif'){
				    	?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_invoice',array(
										'label'=> __('No. Invoice *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('No. Invoice'),
										'readonly' => (!empty($id)) ? true : false
									));
							?>
				        </div>
				        <?php
				    		}
				    	?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('customer_id',array(
										'label'=> __('Customer *'), 
										'class'=>'form-control custom-find-invoice',
										'required' => false,
										'empty' => __('Pilih Customer'),
										'options' => $customers
									));
							?>
				        </div>
				        <div id="invoice-info">
				        	<?php
				        		echo $this->element('blocks/revenues/invoice_info');
				        	?>
				        </div>
				    </div>
				</div>
			</div>
		</div>
	</div>
	<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'invoices', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Form->button(__('Buat Invoice'), array(
		    			'type' => 'submit',
						'class'=> 'btn btn-success btn-lg btn-invoice',
					));
		    		echo $this->Html->link(__('Preview Invoice'), 'javascript:', array(
		    			'rel' => $action,
		    			'class' => 'btn btn-primary',
		    			'id' => 'preview-invoice'
		    		));
			?>
		</div>
</div>
<?php
		echo $this->Form->end();
?>