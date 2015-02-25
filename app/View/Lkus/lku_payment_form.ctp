<?php
		$this->Html->addCrumb(__('Pembayaran LKU'), array(
			'controller' => 'lkus',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('LkuPayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
	<div id="step1">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Pembayaran LKU'); ?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_doc',array(
										'label'=> __('No. Dokumen *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('No. Dokumen'),
										'readonly' => (!empty($id)) ? true : false
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('customer_id',array(
										'label'=> __('Customer *'), 
										'class'=>'form-control',
										'required' => false,
										'options' => $ttujs,
										'empty' => __('Pilih Customer'),
										'id' => 'getTtujCustomerInfo'
									));
							?>
				        </div>
				        <div class="form-group">
							<?php 
									echo $this->Form->input('tgl_bayar',array(
										'label'=> __('Tanggal Bayar'), 
										'class'=>'form-control custom-date',
										'type' => 'text',
										'value' => (!empty($this->request->data['LkuPayment']['tgl_bayar'])) ? $this->request->data['LkuPayment']['tgl_bayar'] : date('d/m/Y')
									));
							?>
						</div>
				    </div>
				</div>
			</div>
			<div class="col-sm-12" id="detail-customer-info">
				<?php 
					if(!empty($this->request->data['LkuPaymentDetail'])){
						echo $this->element('blocks/lkus/lkus_info_payment');
					}
				?>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'payments', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Form->submit(__('simpan'), array(
						'class'=> 'btn btn-success'
					));
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>