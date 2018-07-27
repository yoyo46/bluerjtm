<?php
		$urlBack = array(
			'action' => 'branches'
		);
		
		$this->Html->addCrumb(__('Cabang'), $urlBack);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Branch', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));

		$branch_cities = !empty($branch_cities)?$branch_cities:false;
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Info Cabang');?></h3>
		    </div>
		    <div class="box-body">
    			<?php 
		    			echo $this->Common->buildForm('code', __('Kode Cabang *'));
		    			echo $this->Common->buildForm('name', __('Nama Cabang *'));
		    			echo $this->Common->buildForm('city_id', __('Kota *'), array(
		    				'class' => 'chosen-select',
		    				'empty' => __('Pilih Kota'),
						));
		    			echo $this->Common->buildForm('address', __('Alamat *'));
		    			echo $this->Common->buildForm('coa_id', __('Account Kas/Bank *'), array(
		    				'class' => 'chosen-select',
		    				'empty' => __('Pilih COA'),
	    				));
		    	?>
			    <div class="form-group">
			        <div class="checkbox-options">
			        	<div class="checkbox">
			                <label>
			                	<?php echo $this->Form->checkbox('is_plant').' Plant?';?>
			                </label>
			            </div>
			        </div>
			    </div>
		        <div class="form-group">
			        <div class="checkbox aset-handling">
		                <label>
		                    <?php 
									echo $this->Form->checkbox('is_head_office',array(
										'label'=> false, 
										'required' => false,
									)).sprintf(__('Head Office ? %s'), $this->Html->tag('small', __('( Fitur ini hanya berlaku untuk satu kota yg dipilih )')));
							?>
		                </label>
		            </div>
		        </div>
			    <div class="form-group">
			        <div class="checkbox-options">
			        	<div class="checkbox">
			                <label>
			                	<?php echo $this->Form->checkbox('is_cost_center_readonly').' Cost Center readonly?';?>
			                </label>
			            </div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-warning">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Info Kontak');?></h3>
		    </div>
		    <div class="box-body">
    			<?php 
		    			echo $this->Common->buildForm('phone', __('No Telepon *'));
		    			echo $this->Common->buildForm('fax', __('Fax'));
		    	?>
			</div>
		</div>
	</div>
	<div class="col-sm-6 branch-list-city">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Tujuan bongkar dari cabang?'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		            <?php
		                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
		                        'class' => 'add-custom-field btn btn-success btn-xs',
		                        'action_type' => 'branch_city',
		                        'escape' => false
		                    ));
		            ?>
		        </div>
		        <div id="box-branch-city">
		        	<?php 
		        			if( !empty($this->request->data['BranchCity']['branch_city_id']) ) {
								foreach ($this->request->data['BranchCity']['branch_city_id'] as $key => $branch_city_id) {
					?>
					<div class="row list-branch-city" rel="<?php echo $key; ?>">
					    <div class="col-sm-10">
							<div class="form-group">
					            <?php
					                    echo $this->Form->input('BranchCity.branch_city_id.', array(
					                        'label' => __('Cabang'),
					                        'empty' => __('Pilih Cabang'),
					                        'class' => 'form-control',
					                        'required' => false,
					                        'error' => false,
					                        'options' => $branch_cities,
					                        'value' => !empty($this->request->data['BranchCity']['branch_city_id'][$key])?$this->request->data['BranchCity']['branch_city_id'][$key]:false,
					                    ));
					            ?>
					    	</div>
					    </div>
					    <div class="col-sm-2">
					        <?php
					        		echo $this->Html->tag('label', '&nbsp;', array(
					        			'class' => 'block',
				        			));
					                echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
					                    'class' => 'delete-custom-field btn btn-danger btn-xs',
					                    'escape' => false,
					                    'action_type' => 'branch_city'
					                ));
					        ?>
					    </div>
					</div>
					<?php
								}
							}
		        	?>
		        </div>
		   	</div>
		</div>
	</div>
</div>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Pengaturan Cost Center'); ?></h3>
    </div>
    <div class="box-body">
        <div class="row mt10">
    		<div class="col-sm-6">
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.LkuKsuPayment.cogs_id', __('Pembayaran LKU / KSU'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.LkuKsuPayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.LkuKsuPayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.LakaPayment.cogs_id', __('Pembayaran LAKA'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.LakaPayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.LakaPayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.LeasingPayment.cogs_id', __('Pembayaran Leasing'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.LeasingPayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.LeasingPayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.PurchaseOrderPayment.cogs_id', __('Pembayaran PO/SPK'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.PurchaseOrderPayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.PurchaseOrderPayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.AssetSell.cogs_id', __('Penjualan Asset'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.AssetSell.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.AssetSell.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.TtujPayment.cogs_id', __('Pembayaran Uang Jalan/Komisi'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.TtujPayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.TtujPayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.TtujPaymentCost.cogs_id', __('Pembayaran Biaya TTUJ'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.TtujPaymentCost.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.TtujPaymentCost.id');
							?>
						</div>
					</div>
		        </div>
    		</div>
    		<div class="col-sm-6">
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.Cashbank.cogs_id', __('Kas/Bank'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.Cashbank.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.Cashbank.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.Revenue.cogs_id', __('Revenue'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.Revenue.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.Revenue.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.GeneralLedger.cogs_id', __('Jurnal Umum'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.GeneralLedger.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.GeneralLedger.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.DocumentPayment.cogs_id', __('Pembayaran Surat-Surat Truk'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.DocumentPayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.DocumentPayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.InsurancePayment.cogs_id', __('Pembayaran Asuransi'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.InsurancePayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.InsurancePayment.id');
							?>
						</div>
					</div>
		        </div>
		        <div class="form-group">
					<div class="row">
						<div class="col-sm-4 text-right">
					    	<?php 
									echo $this->Form->label('CogsSetting.InvoicePayment.cogs_id', __('Pembayaran invoice'));
							?>
						</div>
						<div class="col-sm-8">
				        	<?php 
									echo $this->Form->input('CogsSetting.InvoicePayment.cogs_id',array(
										'label'=> false, 
										'class'=>'form-control chosen-select',
										'required' => false,
										'empty' => __('Pilih Cost Center'),
										'options' => $cogs,
									));
									echo $this->Form->hidden('CogsSetting.InvoicePayment.id');
							?>
						</div>
					</div>
		        </div>
    		</div>
    	</div>
    </div>
</div>
<?php
		if( !empty($id) ) {
			echo $this->element('blocks/settings/branch_coas');
		}
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Form->button(__('Simpan'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
    		echo $this->Html->link(__('Kembali'), $urlBack, array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
	echo $this->Form->end();
?>
<div class="hide">
	<div id="branch_city_input">
		<?php
                echo $this->Form->input('branch_city_id', array(
                    'label'=> false, 
                    'class'=>'form-control',
                    'required' => false,
                    'empty' => false,
                    'empty' => __('Pilih Cabang'),
               	 	'options' => $branch_cities,
                ));
        ?>
	</div>
</div>