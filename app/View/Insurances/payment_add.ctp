<?php
		$data = $this->request->data;
		$data['InsurancePayment']['payment_date'] = $this->Common->filterEmptyField($data, 'InsurancePayment', 'payment_date', date('d/m/Y'));

		$vendor = $this->Common->filterEmptyField($data, 'Vendor', 'name');
		$coa = $this->Common->filterEmptyField($data, 'Coa', 'name');
		$this->request->data = $data;
		$id = !empty($id)?$id:false;

		$errorDetail = $this->Form->error('InsurancePayment.item');

		$this->Html->addCrumb(__('Pembayaran Asuransi'), array(
			'controller' => 'insurances',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('InsurancePayment', array(
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
									$id = $this->Common->filterEmptyField($data, 'InsurancePayment', 'id');
							        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
					    ?>
				        <div class="form-group">
				        	<?php 
		        				echo $this->Html->tag('label', __('No. Referensi'));
		        				echo $this->Html->tag('div', $noref);
							?>
				        </div>
				        <?php 
				        		}
				        ?>
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
									echo $this->Form->input('coa_id',array(
										'label'=> __('Account Kas/Bank *'), 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Kas/Bank '),
										'options' => !empty($coas)?$coas:false,
									));
							?>
				        </div>
				        <?php
								echo $this->Common->buildInputForm('cogs_id', __('Cost Center'), array(
									'label'=> __('Cost Center'), 
									'class'=>'form-control chosen-select',
									'empty' => __('Pilih Cost Center '),
									'options' => $cogs,
								));
				        ?>
				        <div class="form-group">
							<?php 
									echo $this->Form->input('payment_date',array(
										'type' => 'text',
										'label'=> __('Tgl Bayar'), 
										'class'=>'form-control custom-date insurance-payment-date',
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
			                            'class' => 'ajaxModal visible-xs browse-docs',
			                            'escape' => false,
			                            'title' => __('Detail Pembayaran'),
			                            'data-action' => 'browse-invoice',
			                            'data-trigger' => '#id-choosen',
			                            'data-change-message' => __('Mohon pilih supplier terlebih dahulu'),
			                            'url' => $this->Html->url( array(
				                            'controller'=> 'insurances', 
				                            'action' => 'insurance_unpaid',
	                            			'payment_id' => $id,
	                            			'bypass' => true,
				                        ))
			                        );
									$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                        echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Ambil Pembayaran'), 'javascript:', $attrBrowse);

			                        if( !empty($errorDetail) )  {
			                        	echo $this->Html->tag('div', $errorDetail, array(
			                        		'class' => 'mt20',
		                        		));
			                        }
			                ?>
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<div class="document-pick-info-detail <?php echo !empty($this->request->data['InsurancePaymentDetail']) ? '' : 'hide';?>">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Detail Pembayaran'); ?></h3>
			    </div>
			    <div class="box-body table-responsive">
			        <table class="table table-hover">
			        	<thead>
			        		<tr>
			        			<th width="20%"><?php echo __('No. Polis');?></th>
			        			<th><?php echo __('Tgl Polis');?></th>
			        			<th><?php echo __('Asuransi');?></th>
			        			<th><?php echo __('Nama Tertanggung');?></th>
				                <th><?php echo __('Total Premi');?></th>
			                    <th class="text-center"><?php echo __('Action');?></th>
			        		</tr>
			        	</thead>
			        	<tbody>
			                <?php
					    			echo $this->element('blocks/insurances/forms/payment_detail');
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