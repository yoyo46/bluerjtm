<?php
		$this->Html->addCrumb(__('Pembayaran KSU'), array(
			'controller' => 'lkus',
			'action' => 'ksu_payments'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('KsuPayment', array(
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
				        <h3 class="box-title"><?php echo __('Informasi Pembayaran KSU'); ?></h3>
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
										'id' => 'getTtujCustomerInfoKsu'
									));
							?>
				        </div>
				        <div class="form-group">
							<?php 
									echo $this->Form->input('tgl_bayar',array(
										'label'=> __('Tanggal Bayar'), 
										'class'=>'form-control custom-date',
										'type' => 'text',
										'value' => (!empty($this->request->data['KsuPayment']['tgl_bayar'])) ? $this->request->data['KsuPayment']['tgl_bayar'] : date('d/m/Y')
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
			                            'title' => __('Pembayaran KSU Customer'),
			                            'data-action' => 'browse-invoice',
			                            'data-change' => 'getTtujCustomerInfoKsu',
			                            'url' => $this->Html->url( array(
				                            'controller'=> 'ajax', 
				                            'action' => 'getTtujCustomerInfoKsu',
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
		<div class="invoice-info-detail <?php echo (!empty($this->request->data) && !empty($ksu_details)) ? '' : 'hide';?>">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Detail Info Pembayaran KSU'); ?></h3>
			    </div>
			    <div class="box-body table-responsive">
			        <table class="table table-hover">
			        	<thead>
			        		<tr>
			        			<th width="20%"><?php echo __('No LKU');?></th>
				                <th><?php echo __('TTUJ');?></th>
				                <th><?php echo __('Nopol Truk');?></th>
				                <th><?php echo __('Perlengkapan');?></th>
				                <th><?php echo __('Total');?></th>
				                <th><?php echo __('Telah Dibayar');?></th>
				                <th><?php echo __('Bayar');?></th>
			                    <th class="text-center"><?php echo __('Action');?></th>
			        		</tr>
			        	</thead>
			        	<tbody class="ttuj-info-table">
			                <?php
					    		echo $this->element('blocks/lkus/info_ksu_payment_detail');
					    	?>
			        	</tbody>
			    	</table>
			    </div>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'ksu_payments', 
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