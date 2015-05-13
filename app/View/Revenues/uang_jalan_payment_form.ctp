<?php
		$this->Html->addCrumb(__('Pembayaran Uang Jalan'), array(
			'controller' => 'revenues',
			'action' => 'uang_jalan_payments'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('UangJalanPayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Informasi Uang Jalan'); ?></h3>
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
                    	echo $this->Form->label('ttuj_id', __('No. TTUJ *'));
	        	?>
	        	<div class="row">
	        		<div class="col-sm-10">
	        			<?php 
								echo $this->Form->input('ttuj_id',array(
									'label'=> false, 
									'class'=>'form-control ttuj-ajax',
									'required' => false,
									'empty' => __('Pilih TTUJ'),
									'id' => 'ttuj-val'
								));
						?>
	        		</div>
	        	</div>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('date_payment',array(
							'type' => 'text',
							'label'=> __('Tanggal Pembayaran *'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'placeholder' => __('Tanggal Pembayaran'),
							'value' => (!empty($this->request->data['UangJalanPayment']['date_payment'])) ? $this->request->data['UangJalanPayment']['date_payment'] : date('d/m/Y')
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
                            'title' => __('TTUJ'),
                            'data-action' => 'browse-ttuj-invoice',
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