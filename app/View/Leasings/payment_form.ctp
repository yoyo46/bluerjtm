<?php
		$this->Html->addCrumb(__('Pembayaran Leasing'), array(
			'controller' => 'leasings',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('LeasingPayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
	<div id="step1">
		<div class="row">
			<div class="col-sm-12">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Pembayaran'); ?></h3>
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
						<?php
								echo $this->Html->tag('div', $this->Form->input('coa_id',array(
									'label'=> __('Account Kas/Bank *'), 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Kas/Bank '),
									'options' => !empty($coas)?$coas:false,
								)), array(
									'class' => 'form-group'
								));
						?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('vendor_id',array(
										'label'=> __('Vendor *'), 
										'class'=>'form-control',
										'required' => false,
										'empty' => __('Pilih Vendor'),
										'id' => 'vendor-choosen'
									));
							?>
				        </div>
				        <div class="form-group">
							<?php 
									echo $this->Form->input('payment_date',array(
										'type' => 'text',
										'label'=> __('Tgl Bayar'), 
										'class'=>'form-control custom-date',
									));
							?>
						</div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('note',array(
										'type' => 'textarea',
										'label'=> __('Keterangan'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
				        			$attrBrowse = array(
			                            'class' => 'ajaxModal visible-xs',
			                            'escape' => false,
			                            'title' => __('Detail Pembayaran'),
			                            'data-action' => 'browse-invoice',
			                            'data-trigger' => '#vendor-choosen',
			                            'data-change-message' => __('Mohon pilih vendor terlebih dahulu'),
			                            'url' => $this->Html->url( array(
				                            'controller'=> 'leasings', 
				                            'action' => 'leasings_unpaid',
				                        ))
			                        );
									$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                        echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Ambil Pembayaran'), 'javascript:', $attrBrowse);
			                ?>
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<div class="invoice-info-detail <?php echo (!empty($this->request->data) && !empty($lku_details)) ? '' : 'hide';?>">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Detail Info Pembayaran'); ?></h3>
			    </div>
			    <div class="box-body table-responsive">
			        <table class="table table-hover">
			        	<thead>
			        		<tr>
			        			<th width="20%"><?php echo __('No Kontrak');?></th>
			        			<th><?php echo __('Tgl Jth Tempo');?></th>
				                <th><?php echo __('Pokok');?></th>
				                <th><?php echo __('Bunga');?></th>
				                <th><?php echo __('Denda');?></th>
				                <th><?php echo __('Total');?></th>
			                    <th class="text-center"><?php echo __('Action');?></th>
			        		</tr>
			        	</thead>
			        	<tbody>
			                <?php
					    			echo $this->element('blocks/leasings/forms/leasing_payment_detail.ctp');
					    	?>
			        	</tbody>
			    	</table>
			    </div>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'payments', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Form->button(__('simpan'), array(
						'class'=> 'btn btn-success btn-lg',
						'type' => 'submit'
					));
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>