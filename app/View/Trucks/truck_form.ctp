<?php
		$this->Html->addCrumb(__('Truk'), array(
			'controller' => 'trucks',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Truck', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'type' => 'file'
		));

		$data = $this->request->data;
        $is_asset = $this->Common->filterEmptyField($data, 'Truck', 'is_asset');
        $asset_id = $this->Common->filterEmptyField($data, 'Asset', 'id');

		if( !empty($id) ) {
			$disabled = true;
		} else {
			$disabled = false;
		}

		if( !empty($is_asset) ) {
			$assetDisabled = false;
		} else {
			$assetDisabled = true;
		}
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Utama')?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
		    			if( !empty($data_local) ) {
		    	?>
		    	<div class="form-group">
		        	<?php 
							echo $this->Form->input('id',array(
								'label'=> __('Nomor ID'), 
								'class'=>'form-control',
								'required' => false,
								'type' => 'text',
								'disabled' => true,
								'value' => str_pad($data_local['Truck']['id'], 4, '0', STR_PAD_LEFT),
							));
					?>
		        </div>
		        <?php 
		        		}
		        ?>
		    	<div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('nopol', __('Nopol *'), array(
		    					'disabled' => $disabled,
	    					));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('truck_brand_id', __('Merek Truk *'), array(
								'options' => $truck_brands,
								'empty' => __('Pilih Merek Truk'),
	    					));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('truck_category_id', __('Jenis Truk *'), array(
								'options' => $truck_categories,
								'empty' => __('Pilih Jenis Truk'),
		    					'disabled' => $disabled,
	    					));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('truck_facility_id', __('Fasilitas Truk *'), array(
								'options' => $truck_facilities,
								'empty' => __('Pilih Fasilitas Truk'),
		    					'disabled' => $disabled,
	    					));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('no_rangka', __('No Rangka *'));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('no_machine', __('No Mesin *'));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('company_id', __('Pemilik Truk *'), array(
								'empty' => __('Pilih Pemilik Truk'),
	    					));
					?>
		        </div>
		        <div class="form-group">
        			<?php 
                            $attrBrowse = array(
                                'class' => 'ajaxModal visible-xs browse-docs',
                                'escape' => false,
								'title' => __('Supir Truk'),
								'data-action' => 'browse-form',
								'data-change' => 'driverID',
                            );
                            $urlBrowse = array(
                                'controller'=> 'ajax', 
								'action' => 'getDrivers',
								!empty($data_local['Truck']['driver_id'])?$data_local['Truck']['driver_id']:false,
                            );
							echo $this->Form->label('driver_id', __('Supir Truk ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
					?>
		        	<div class="row">
		        		<div class="col-sm-10">
		        			<?php 
				    				echo $this->Common->buildForm('driver_id', false, array(
										'options' => $drivers,
										'empty' => __('Pilih Supir Truk'),
										'id' => 'driverID',
				    					'disabled' => $disabled,
			    					));
							?>
		        		</div>
        				<div class="col-sm-2 hidden-xs">
	                        <?php 
        							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
	                                echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
	                        ?>
	                    </div>
		        	</div>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('capacity', __('Kapasitas *'), array(
		    					'disabled' => $disabled,
	    					));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
		    				echo $this->Common->buildForm('tahun', __('Tahun *'), array(
								'options' => $years,
								'empty' => __('Pilih Tahun'),
	    					));
					?>
		        </div>
			        <?php 
			        		if( !empty($allowEditAsset) ) {
			        ?>
		        <div class="form-group">
			        <div class="checkbox aset-handling">
		                <label>
		                    <?php 
									echo $this->Form->checkbox('is_asset',array(
										'label'=> false, 
										'required' => false,
										'class' => 'aset-handling-form',
									)).__('ini adalah aset?');
							?>
		                </label>
		            </div>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('tahun_neraca',__('Tahun Neraca *')); 

							echo $this->Form->input('tahun_neraca',array(
								'label'=> false, 
								'class'=>'form-control neraca-form',
								'required' => false,
								'empty' => __('Pilih Tahun Neraca'),
								'options' => $years,
								'disabled' => $assetDisabled,
							));
					?>
		        </div>
		        <?php
							// if( !empty($id) && !empty($asset_id) ) {
							// 	echo $this->Html->tag('div', $this->Html->link(__('Lihat Detail Asset'), array(
       //                              'controller' => 'assets',
       //                              'action' => 'edit',
       //                              $asset_id,
       //                          ), array(
       //                              'escape' => false,
       //                              'target' => '_blank',
       //                          )), array(
       //                              'class' => 'form-group',
       //                          ));
							// } else if( empty($id) ) {
							// 	echo $this->Common->buildInputForm('purchase_date', __('Tanggal pembelian *'), array(
							// 		'type' => 'text',
							// 		'class' => 'custom-date form-control neraca-form',
							// 		'fieldError' => 'Asset.purchase_date',
							// 		'disabled' => $assetDisabled,
							// 	));
	 					// 		echo $this->Common->buildInputForm('asset_group_id', __('Group Asset *'), array(
							// 		'empty' => __('Pilih Group Asset'),
							// 		'disabled' => $assetDisabled,
							// 		'class'=>'form-control neraca-form',
							// 		'fieldError' => 'Asset.asset_group_id',
							// 	));
							// 	echo $this->Common->buildInputForm('nilai_perolehan', __('Nilai perolehan *'), array(
							// 		'type' => 'text',
							// 		'class' => 'input_price_coma form-control neraca-form',
							// 		'fieldError' => 'Asset.nilai_perolehan',
							// 		'disabled' => $assetDisabled,
							// 	));
							// 	echo $this->Common->buildInputForm('ak_penyusutan', __('Ak. Penyusutan *'), array(
							// 		'type' => 'text',
							// 		'class' => 'input_price_coma form-control neraca-form',
							// 		'fieldError' => 'Asset.ak_penyusutan',
							// 		'disabled' => $assetDisabled,
							// 	));
							// }
		        		}
		        ?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('description',array(
								'type' => 'textarea',
								'label'=> __('Keterangan'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
			</div>    	
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Dokumen')?></h3>
		    </div>
		    <div class="box-body">
		    	<?php
		    			if(!empty($this->request->data['Truck']['photo']) && !is_array($this->request->data['Truck']['photo'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Truck']['photo'], 
								'thumb'=>true,
								'size' => 'pm',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('photo',array(
								'type' => 'file',
								'label'=> __('Foto Truk'), 
								'class'=>'form-control',
								'required' => false
							));
					?>
		        </div>
		    	<div class="form-group">
		        	<?php 
						echo $this->Form->label('atas_nama',__('Atas Nama')); 

						echo $this->Form->input('atas_nama',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->label('bpkb',__('BPKB')); 

						echo $this->Form->input('bpkb',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->label('tgl_bpkb',__('Tgl BPKB')); 

						echo $this->Form->input('tgl_bpkb',array(
							'type' => 'text',
							'label'=> false, 
							'class'=>'form-control custom-date',
							'required' => false,
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->label('no_stnk',__('No STNK')); 

						echo $this->Form->input('no_stnk',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('tgl_stnk',array(
							'label'=> __('Tgl Perpanjang STNK 1Thn'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'type' => 'text',
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('tgl_stnk_plat',array(
								'label'=> __('Tgl Perpanjang STNK 5Thn'), 
								'class'=>'form-control custom-date',
								'required' => false,
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('bbnkb',array(
								'label'=> __('Biaya BBNKB'), 
								'class'=>'form-control input_price',
								'required' => false,
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('pkb',array(
								'label'=> __('Biaya PKB'), 
								'class'=>'form-control input_price',
								'required' => false,
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('swdkllj',array(
								'label'=> __('Biaya SWDKLLJ'), 
								'class'=>'form-control input_price',
								'required' => false,
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('tgl_siup',array(
							'label'=> __('Tgl Perpanjang Ijin Usaha'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'type' => 'text',
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('siup',array(
								'label'=> __('Biaya Ijin Usaha'), 
								'class'=>'form-control input_price',
								'required' => false,
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('tgl_kir',array(
							'label'=> __('Tgl Perpanjang KIR'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'type' => 'text',
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('kir',array(
								'label'=> __('Biaya KIR'), 
								'class'=>'form-control input_price',
								'required' => false,
								'type' => 'text',
							));
					?>
		        </div>
		   	</div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="col-sm-6">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Alokasi Truk')?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php
		        			if( empty($id) ) {
				        		echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah Alokasi'), 'javascript:', array(
									'class' => 'add-custom-field btn btn-success btn-xs',
									'action_type' => 'alocation',
									'escape' => false,
								));
				        	}
		        	?>
		        </div>
		        <div id="box-field-input">
		        	<div id="main-alocation">
		        		<div class="list-alocation">
			        		<?php 
									echo $this->Form->label('TruckCustomer.customer_id.',__('Alokasi *')); 
			        		?>
			        		<div class="row">
			        			<div class="col-sm-11">
			        				<div class="form-group">
					        			<?php 
												echo $this->Form->input('TruckCustomer.customer_id.',array(
													'label'=> false, 
													'class'=> 'form-control',
													'required' => false,
													'empty' => __('Pilih'),
													'options' => $customers,
													'value' => (!empty($this->request->data['TruckCustomer']['customer_id'][0])) ? $this->request->data['TruckCustomer']['customer_id'][0] : '',
													'disabled'=> $disabled, 
												));
										?>
							        </div>
			        			</div>
			        			<div class="col-sm-1 no-pleft">
							        <?php
											echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
												'class' => 'delete-custom-field has-danger',
												'action_type' => 'alocation',
												'escape' => false
											));
							        ?>
			        			</div>
			        		</div>
		        		</div>
		        	</div>
		        </div>
		        <div id="advance-box-field-input">
		        	<?php
			        		if(!empty($this->request->data['TruckCustomer']['customer_id'])){
			        			foreach ($this->request->data['TruckCustomer']['customer_id'] as $key => $value) {
			        				if($key != 0 && !empty($value)){
		        	?>
	        		<div class="list-alocation">
		        		<?php 
								echo $this->Form->label('TruckCustomer.customer_id.',__('Alokasi *')); 
		        		?>
		        		<div class="row">
		        			<div class="col-sm-11">
					        	<div class="form-group">
				        			<?php 
										echo $this->Form->input('TruckCustomer.customer_id.',array(
											'label'=> false, 
											'class'=> 'form-control',
											'required' => false,
											'empty' => __('Pilih'),
											'options' => $customers,
											'value' => $value,
											'disabled'=> $disabled, 
										));
									?>
						        </div>
		        			</div>
		        			<div class="col-sm-1 no-pleft">
						        <?php
										echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
											'class' => 'delete-custom-field has-danger',
											'action_type' => 'alocation',
											'escape' => false
										));
						        ?>
		        			</div>
		        		</div>
	        		</div>
		        	<?php
			        				}
			        			}
			        		}
		        	?>
		        </div>
		   	</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Emergency Call')?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
			        <div class="checkbox aset-handling">
		                <label>
		                    <?php 
								echo $this->Form->checkbox('is_gps',array(
									'label'=> false, 
									'required' => false,
								)).__('Dilengkapi GPS?');
							?>
		                </label>
		            </div>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->label('emergency_name', __('Nama Panggilan darurat')); 

						echo $this->Form->input('emergency_name',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->label('emergency_call', __('Telepon darurat')); 

						echo $this->Form->input('emergency_call',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
							'type' => 'text',
						));
					?>
		        </div>
		   	</div>
		</div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Form->button(__('Simpan'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
	echo $this->Form->end();
?>