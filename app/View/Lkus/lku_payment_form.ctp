<?php
		$this->Html->addCrumb(__('Pembayaran LKU'), array(
			'controller' => 'lkus',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);

		$data_local = !empty($data_local)?$data_local:false;

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
			<div class="col-sm-12">
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi Pembayaran LKU'); ?></h3>
				    </div>
				    <div class="box-body">
					    <?php 
					    		if( !empty($id) ) {
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
									echo $this->Form->input('no_doc',array(
										'label'=> __('No. Dokumen *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('No. Dokumen'),
									));
							?>
				        </div>
						<?php
								echo $this->Html->tag('div', $this->Form->input('coa_id',array(
									'label'=> __('Account Kas/Bank *'), 
									'class'=>'form-control chosen-select',
									'required' => false,
									'empty' => __('Pilih Kas/Bank '),
									'options' => !empty($coas)?$coas:false,
								)), array(
									'class' => 'form-group'
								));
								echo $this->element('blocks/common/forms/cost_center');
						?>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('customer_id',array(
										'label'=> __('Customer *'), 
										'class'=>'form-control chosen-select',
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
										'label'=> __('Tgl Bayar'), 
										'class'=>'form-control custom-date',
										'type' => 'text',
										'value' => (!empty($this->request->data['LkuPayment']['tgl_bayar'])) ? $this->request->data['LkuPayment']['tgl_bayar'] : date('d/m/Y')
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
			                            'class' => 'ajaxModal visible-xs browse-docs',
			                            'escape' => false,
			                            'title' => __('Pembayaran LKU Customer'),
			                            'data-action' => 'browse-invoice',
			                            'data-change' => 'getTtujCustomerInfo',
			                            'url' => $this->Html->url( array(
				                            'controller'=> 'ajax', 
				                            'action' => 'getTtujCustomerInfo',
	                            			'payment_id' => $id,
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
		<div class="document-pick-info-detail <?php echo (!empty($this->request->data) && !empty($lku_details)) ? '' : 'hide';?>">
			<div class="box box-primary">
			    <div class="box-header">
			        <h3 class="box-title"><?php echo __('Detail Info Pembayaran LKU'); ?></h3>
			    </div>
			    <div class="box-body table-responsive">
			        <table class="table table-hover">
			        	<thead>
			        		<tr>
			        			<th><?php echo __('No LKU');?></th>
			        			<th><?php echo __('Tgl LKU');?></th>
				                <th><?php echo __('TTUJ');?></th>
				                <th><?php echo __('Supir');?></th>
				                <th><?php echo __('Nopol');?></th>
				                <th><?php echo __('Tipe Motor');?></th>
				                <th><?php echo __('Parts Motor');?></th>
				                <th><?php echo __('Total');?></th>
				                <th><?php echo __('Telah Dibayar');?></th>
				                <th><?php echo __('Bayar');?></th>
			                    <th class="text-center"><?php echo __('Action');?></th>
			        		</tr>
			        	</thead>
			        	<tbody class="ttuj-info-table">
			                <?php
					    		echo $this->element('blocks/lkus/info_lku_payment_detail');
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
					$this->Common->_getButtonPostingUnposting( $data_local, 'LkuPayment', array( 'Commit', 'Draft' ) );
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>