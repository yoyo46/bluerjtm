<?php 
		if( !empty($step) ) {
?>
<script type="text/javascript">
	window.location.hash = '<?php echo $step; ?>';
</script>
<?php
		}

		$data = $this->request->data;
		$id = !empty($id)?$id:false;
		$id = $this->Common->filterEmptyField($data, 'Laka', 'id', $id);

		$this->Html->addCrumb(__('LAKA'), array(
			'controller' => 'lakas',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Laka', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
    		'type' => 'file'
		));
?>
<div class="laka-form">
	<div id="step1">
		<div class="row">
			<div class="col-sm-6">
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Data Supir dan Armada'); ?></h3>
				    </div>
				    <div class="box-body">
				    	<?php 
				    			if( !empty($id) ) {
        							$noref = str_pad($id, 6, '0', STR_PAD_LEFT);
				    	?>
						<div class="form-group">
							<?php
									echo $this->Form->label('id', __('No. Referensi'));
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
										'placeholder' => __('No. Dokumen')
									));
							?>
						</div>
				        <div class="form-group">
				        	<?php 
		        					$attrBrowse = array(
	                                    'class' => 'ajaxModal visible-xs browse-docs',
	                                    'escape' => false,
	                                    'title' => __('Data Truk'),
	                                    'data-action' => 'browse-form',
	                                    'data-change' => 'laka-driver-change',
	                                );
		        					$urlBrowse = array(
	                                    'controller'=> 'ajax', 
	                                    // 'action' => 'getTtujs',
	                                    'action' => 'getTrucks',
	                                    'laka',
	                                );
			                    	echo $this->Form->label('truck_id', __('Nopol Truk * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
			                ?>
			                <div class="row">
			                    <div class="col-sm-10">
						        	<?php 
											echo $this->Form->input('truck_id',array(
												'label'=> false, 
												'class'=>'form-control chosen-select',
												'required' => false,
												'empty' => __('Pilih Nopol Truk'),
												'options' => $trucks,
												'id' => 'laka-driver-change'
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
									echo $this->Form->input('driver_name',array(
										'label'=> __('Nama Supir *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('Nama Supir'),
										'readonly' => true,
										'id' => 'laka-driver-name'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_sim',array(
										'label'=> __('No SIM *'), 
										'placeholder' => __('No SIM'),
										'class'=>'form-control',
										'required' => false,
										'type' => 'text',
										'id' => 'laka-no-sim',
										'readonly' => true
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
				        			$urlBrowse = $this->Html->url(array(
	                                    'controller'=> 'ajax', 
	                                    'action' => 'getTtujs',
	                                    'laka',
	                                ));
		        					$attrBrowse = array(
	                                    'class' => 'ajaxModal visible-xs browse-docs',
	                                    'escape' => false,
	                                    'title' => __('Data TTUJ'),
	                                    'data-action' => 'browse-form',
	                                    'data-change' => 'laka-ttuj-change',
	                                    'url' => $urlBrowse,
	                                );

			                    	echo $this->Form->label('ttuj_id', __('No. TTUJ').$this->Html->link('<i class="fa fa-plus-square"></i>', 'javascript:', $attrBrowse));
			                ?>
				        	<div class="row">
			                    <div class="col-sm-10" id="ttuj-form">
						        	<?php 
											echo $this->Form->input('ttuj_id',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'empty' => __('Pilih No TTUJ'),
												'options' => $ttujs,
												'id' => 'laka-ttuj-change',
												'readonly' => (empty($this->request->data['Laka']['truck_id'])) ? true : false
											));
									?>
			                    </div>
		        				<div class="col-sm-2 hidden-xs" >
			                        <?php 
		        							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                                echo $this->Html->link('<i class="fa fa-plus-square"></i>', 'javascript:', $attrBrowse);
			                        ?>
			                    </div>
			                </div>
				        </div>
				        <div class="form-group" id="data-supir-pengganti">
				        	<?php 
									echo $this->Form->input('Laka.change_driver_id',array(
										// 'type' => 'text',
										'label'=> __('Supir Pengganti'), 
										'empty'=> __('Pilih Supir Pengganti'), 
										'class'=>'form-control supir-pengganti-val',
										'options' => $driverPenggantis,
										'required' => false,
										'id' => 'laka-driver-change-id',
										// 'value' => !empty($this->request->data['Laka']['change_driver_name']) ? $this->request->data['Laka']['change_driver_name']: '',
										'disabled' => !empty($this->request->data['Laka']['ttuj_id']) ? true: false
									));

									// if ($this->Form->isFieldError('change_driver_id')) {
									//     echo $this->Form->error('change_driver_id');
									// }
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('lokasi_laka',array(
										'label'=> __('Lokasi LAKA *'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text',
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->label('from_city_name', __('Asal dan Tujuan *'));
							?>
							<div class="row" id="city-laka">
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('from_city_name', array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'readonly' => true,
											));
											echo $this->Form->hidden('from_city_id');
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('to_city_name', array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'readonly' => true,
											));
											echo $this->Form->hidden('to_city_id');
									?>
								</div>
							</div>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('tgl_laka',array(
										'label'=> __('Tgl LAKA *'), 
										'class'=>'form-control custom-date',
										'required' => false,
										'type' => 'text',
										'value' => (!empty($this->request->data['Laka']['tgl_laka'])) ? $this->request->data['Laka']['tgl_laka'] : date('d/m/Y')
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('status_muatan',array(
										'label'=> __('Status Muatan *'), 
										'class'=>'form-control',
										'required' => false,
										'empty' => __('Pilih Status'),
										'options' => array(
											'ada' => 'Ada',
											'kosong' => 'Kosong'
										)
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('total_laka',array(
										'type' => 'text',
										'label'=> __('Biaya LAKA'), 
										'class'=>'form-control input_price',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('total',array(
										'type' => 'text',
										'label'=> __('Ditanggung Supir'), 
										'class'=>'form-control input_price',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('driver_condition',array(
										'label'=> __('Kondisi Supir dan kenek *'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('truck_condition',array(
										'label'=> __('Kondisi Armada dan Muatan *'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				    </div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Data dan Informasi Pihak Lain (jika ada)');?></h3>
				    </div>
				    <div class="box-body">
				    	<div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.name_second',array(
										'label'=> __('Nama Pihak Kedua'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				    	<div class="form-group">
				        	<?php 
									echo $this->Form->label('LakaDetail.place_birth', __('Tempat dan Tanggal lahir'));
							?>
							<div class="row" id="city-laka">
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('LakaDetail.place_birth',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'placeholder' => __('Tempat Lahir'),
											));
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('LakaDetail.date_birth',array(
												'label'=> false, 
												'class'=>'form-control custom-date',
												'required' => false,
												'placeholder' => __('Tanggal lahir'),
												'type' => 'text'
											));
									?>
								</div>
							</div>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.age',array(
										'label'=> __('Usia'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.gender',array(
										'label'=> __('Jenis Kelamin'), 
										'class'=>'form-control',
										'required' => false,
										'options' => array(
											'male' => 'Pria',
											'female' => 'Wanita'
										)
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.address',array(
										'label'=> __('Alamat'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.no_indentity',array(
										'label'=> __('NO. Identitas (KTM/SIM)'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.job',array(
										'label'=> __('Pekerjaan'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.man_conditions',array(
										'label'=> __('Kondisi Orang'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.nopol',array(
										'label'=> __('Nopol'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.type_transport',array(
										'label'=> __('Jenis Truk'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.brand',array(
										'label'=> __('Merek dan Tahun'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.condition_transport',array(
										'label'=> __('Kondisi Truk'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.second_party',array(
										'label'=> __('Pihak Terkait lain'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'textarea'
									));
							?>
				        </div>
				    </div>
				</div>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'index', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Html->link(__('Next'), '#step2', array(
						'class'=> 'btn btn-success',
						'id' => 'nextLaka',
						'allow' => true,
					));
			?>
		</div>
	</div>
	<div id="step2">
		<div class="row">
			<div class="col-sm-6">
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Data dan Informasi Kejadian'); ?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('description_laka',array(
										'label'=> __('Deskripsi LAKA *'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('ilustration',array(
										'label'=> __('Ilustrasi LAKA'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				        <div class="form-group" id="laka-media-form">
				        	<?php
				        		if(!empty($this->request->data['LakaMedias'])){
				        	?>
				        	<div class="laka-image-file">
				        		<?php
			        				$list = '';
			        				foreach ($this->request->data['LakaMedias'] as $key => $value) {
			        					if(!empty($value['name'])){
			        						$photo = $this->Common->photo_thumbnail(array(
												'save_path' => Configure::read('__Site.laka_photo_folder'), 
												'src' => !empty($value['name'])?$value['name']:false, 
												'thumb'=>true,
												'size' => 'pm',
												'thumb' => true,
											));
											$link = $this->Html->link('<i class="fa fa-times-circle"></i>', array(
												'controller' => 'ajax',
												'action' => 'delete_laka_media',
												!empty($value['id'])?$value['id']:false,
											), array(
												'escape' => false,
												'class' => 'delete-custom-field time-circle-laka',
												'action_type' => 'delete-image-laka',
												'title' => __('Hapus Media')
											));
				        					$list .= $this->Html->tag('li', $photo.$link, array(
				        						'class' => 'relative'
				        					));
			        					}
			        					
			        				}

			        				if(!empty($list)){
			        					echo $this->Html->tag('ul', $list);
			        				}
				        		?>
				        	</div>
				        	<div class="clear"></div>
				        	<?php 
				        		}
				        			echo $this->Form->label('LakaMedias.name.', 'LAKA Media');
				        	?>
				        	<span class="action-media-laka pull-right text-right">
				        		<?php
					        			echo $this->Html->link('Tambah <i class="fa fa-plus"></i>', 'javascript:', array(
					        				'escape' => false,
					        				'class' => 'add-custom-field btn btn-info btn-xs',
					        				'action_type' => 'file-laka'
					        			));

					        			echo $this->Html->link('Hapus <i class="fa fa-times"></i>', 'javascript:', array(
					        				'escape' => false,
					        				'class' => 'delete-custom-field btn btn-danger btn-xs',
					        				'action_type' => 'file-laka'
					        			));
					        	?>
				        	</span>
				        	<div class="clear"></div>
				        	<div class="laka-form-media">
				        		<?php
										echo $this->Form->input('LakaMedias.name.',array(
											'type' => 'file',
											'label'=> false, 
											'class'=>'form-control',
											'required' => false
										));
								?>
				        	</div>
				        </div>
				    </div>
				</div>
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Data Kerusakan Properti (jika ada)');?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.property_type',array(
										'label'=> __('Jenis Properti'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.property_owner',array(
										'label'=> __('Pemilik Properti'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('LakaDetail.property_condition',array(
										'label'=> __('Kondisi Properti'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'textarea'
									));
							?>
				        </div>
				    </div>
				</div>
			</div>
			
			<div class="col-sm-6">
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Laporan Internal');?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('responden_name',array(
										'label'=> __('Nama Responden'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('responden_position',array(
										'label'=> __('Jabatan'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('responden_no_telp',array(
										'label'=> __('No Telepon'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('operator',array(
										'label'=> __('Nama Pengurus'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('operator_location',array(
										'label'=> __('Lokasi Pengurus'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
				        <?php echo $this->Html->tag('h3', __('Kelengkapan (tandai yang sudah ada)'));?>
				        <div class="checkbox-options">
				        	<?php
				        		foreach ($material as $key => $value) {
				        	?>
				        	<div class="checkbox">
	                            <label>
	                            	<?php echo $this->Form->checkbox('Laka.completeness.'.$key).' '.$value;?>
	                            </label>
	                        </div>
				        	<?php
				        		}
				        	?>
				        </div>
				        <?php echo $this->Html->tag('h3', __('Asuransi (tandai yang ada)'));?>
				        <div class="checkbox-options">
				        	<?php
				        		foreach ($insurance as $key => $value) {
				        	?>
				        	<div class="checkbox">
	                            <label>
	                            	<?php echo $this->Form->checkbox('Laka.completeness_insurance.'.$key).' '.$value;?>
	                            </label>
	                        </div>
				        	<?php
				        		}
				        	?>
				        </div>
				    </div>
				</div>
				<?php
						if(!empty($id)){
				?>
				<div class="box">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Status LAKA'); ?></h3>
				    </div>
				    <div class="box-body">
				    	<?php 
				    		echo $this->Html->tag('p', __('Status selesai digunakan jika proses kejadian LAKA sudah selesai.'));
				    	?>
				    	<div class="form-group">
				    		<div class="checkbox">
		                        <label class="completed-handle">
		                        	<?php 
		                        		echo $this->Form->checkbox('completed').' Proses LAKA sudah selesai?';
		                        	?>
		                        </label>
		                    </div>
				    	</div>
	                    <div id="desc-complete" class="<?php echo !empty($this->request->data['Laka']['completed']) ? '' : 'hide';?>">
				    		<div class="form-group">
		                    	<?php 
										echo $this->Form->input('completed_date',array(
											'label'=> __('Tgl Selesai *'), 
											'class'=>'form-control custom-date',
											'required' => false,
											'type' => 'text',
											'value' => (!empty($this->request->data['Laka']['completed_date'])) ? $this->request->data['Laka']['completed_date'] : date('d/m/Y')
										));

										if ($this->Form->isFieldError('completed')) {
										    echo $this->Form->error('completed');
										}
								?>
	                    	</div>
				    		<div class="form-group">
		                    	<?php 
										echo $this->Form->input('complete_desc',array(
											'label'=> __('Keterangan *'), 
											'class'=>'form-control',
											'required' => false,
											'type' => 'textarea'
										));

										if ($this->Form->isFieldError('completed')) {
										    echo $this->Form->error('completed');
										}
								?>
	                    	</div>
	                    </div>
				    </div>
				</div>
				<?php
						}
				?>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), '#step1', array(
						'class'=> 'btn btn-default',
						'id' => 'backLaka',
						'allow' => true,
					));

					echo $this->Form->button(__('Simpan'), array(
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