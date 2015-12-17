<?php
		$data = $this->request->data;
		$data['LeasingPayment']['payment_date'] = $this->Common->filterEmptyField($data, 'LeasingPayment', 'payment_date', date('d/m/Y'));

		$vendor = $this->Common->filterEmptyField($data, 'Vendor', 'name');
		$coa = $this->Common->filterEmptyField($data, 'Coa', 'name');
		$this->request->data = $data;

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
					    <?php 
					    		if( !empty($id) ) {
									$no_doc = $this->Common->filterEmptyField($data, 'LeasingPayment', 'no_doc');
									$id = $this->Common->filterEmptyField($data, 'LeasingPayment', 'id');
							        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
					    ?>
				        <div class="form-group">
				        	<?php 
		        				echo $this->Html->tag('label', __('No. Referensi'));
		        				echo $this->Html->tag('div', $noref);
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
		        				echo $this->Html->tag('label', __('No. Dokumen'));
		        				echo $this->Html->tag('div', $no_doc);
							?>
				        </div>
				        <?php 
				        		} else {
				        ?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_doc',array(
										'label'=> __('No. Dokumen *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('No. Dokumen'),
									));
							?>
				        </div>
				        <?php 
				        		}
				        ?>
				        <div class="form-group">
							<?php
									if( !empty($id) ) {
				        				echo $this->Html->tag('label', __('Account Kas/Bank'));
				        				echo $this->Html->tag('div', $coa);
				        			} else {
										echo $this->Form->input('coa_id',array(
											'label'=> __('Account Kas/Bank *'), 
											'class'=>'form-control chosen-select',
											'required' => false,
											'empty' => __('Pilih Kas/Bank '),
											'options' => !empty($coas)?$coas:false,
										));
									}
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
				        			if( !empty($id) ) {
				        				echo $this->Html->tag('label', __('Vendor'));
				        				echo $this->Html->tag('div', $vendor);
				        			} else {
										echo $this->Form->input('vendor_id',array(
											'label'=> __('Vendor *'), 
											'class'=>'form-control',
											'required' => false,
											'empty' => __('Pilih Vendor'),
											'id' => 'id-choosen'
										));
									}
							?>
				        </div>
				        <div class="form-group">
							<?php 
									echo $this->Form->input('payment_date',array(
										'type' => 'text',
										'label'=> __('Tgl Bayar'), 
										'class'=>'form-control custom-date leasing-payment-date',
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
				        <?php 
								if( empty($id) ) {
				        ?>
				        <div class="form-group">
				        	<?php 
				        			$attrBrowse = array(
			                            'class' => 'ajaxModal visible-xs browse-docs',
			                            'escape' => false,
			                            'title' => __('Detail Pembayaran'),
			                            'data-action' => 'browse-invoice',
			                            'data-trigger' => '#id-choosen',
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
				        <?php 
				        		}
				        ?>
				    </div>
				</div>
			</div>
		</div>
		<div class="document-pick-info-detail <?php echo !empty($this->request->data['LeasingPaymentDetail']) ? '' : 'hide';?>">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Detail Pembayaran'); ?></h3>
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
					    			echo $this->element('blocks/leasings/forms/leasing_payment_detail');
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

					if( empty($id) ) {
			    		echo $this->Form->button(__('simpan'), array(
							'class'=> 'btn btn-success btn-lg',
							'type' => 'submit'
						));
			    	}
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>