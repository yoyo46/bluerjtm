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
    		'id' => 'inv-form',
		));

		echo $this->Form->hidden('transaction_status', array(
			'id' => 'transaction_status'
		));
?>
<div class="ttuj-form">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Informasi Invoice'); ?></h3>
	    </div>
	    <div class="box-body">
	    	<?php
        			if( !in_array($action, array( 'tarif', 'tarif_name' )) ){
	    	?>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('no_invoice',array(
							'label'=> __('No. Invoice *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('No. Invoice'),
							'readonly' => (!empty($id)) ? true : false,
							'id' => 'no_invoice',
						));
				?>
	        </div>
	        <?php
	    			}
	    	?>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('company_id',array(
							'label'=> __('Company *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Company'),
							'options' => $companies
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('customer_id',array(
							'label'=> __('Customer *'), 
							'class'=>'form-control custom-find-invoice',
							'required' => false,
							'empty' => __('Pilih Customer'),
							'options' => $customers,
						));
				?>
	        </div>
	    	<?php
        			if( !in_array($action, array( 'tarif', 'tarif_name' )) ){
	    	?>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('tarif_type',array(
							'label'=> __('Jenis Invoice *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Jenis Invoice'),
							'options' => array(
								'angkut' => __('Tarif Angkut'),
								'kuli' => __('Tarif Kuli Muat'),
								'asuransi' => __('Tarif Asuransi'),
							),
							'id' => 'invoiceType'
						));
				?>
	        </div>
	        <?php
	    			} else {
	    				echo $this->Form->hidden('tarif_type',array(
							'value' => 'angkut',
							'id' => 'invoiceType'
						));
	    			}
	    	?>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('invoice_date',array(
							'label'=> __('Tgl Invoice *'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'type' => 'text',
							'value' => (!empty($this->request->data['Invoice']['invoice_date'])) ? $this->request->data['Invoice']['invoice_date'] : date('d/m/Y')
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
		    		echo $this->Form->hidden('action_inv', array(
		    			'value' => $action,
		    		));
			?>
		</div>
</div>
<?php
		echo $this->Form->end();
?>