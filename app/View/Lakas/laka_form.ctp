<?php 
		if( !empty($step) ) {
?>
<script type="text/javascript">
	window.location.hash = '<?php echo $step; ?>';
</script>
<?php
		}

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
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Data Supir dan Armada'); ?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('ttuj_id',array(
										'label'=> __('Nama Supir *'), 
										'class'=>'form-control',
										'required' => false,
										'empty' => __('Pilih Nama Supir'),
										'options' => $ttujs,
										'id' => 'laka-driver-change'
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_sim',array(
										'label'=> __('No SIM *'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text',
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('tgl_laka',array(
										'label'=> __('Tgl LAKA *'), 
										'class'=>'form-control custom-date',
										'required' => false,
										'type' => 'text',
									));
							?>
				        </div>
				        <div class="form-group" id="nopol-laka">
				        	<?php 
									echo $this->Form->input('nopol',array(
										'label'=> __('Nopol Armada *'), 
										'class'=>'form-control',
										'required' => false,
										'type' => 'text',
										'readonly' => true
									));
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
											echo $this->Form->input('from_city_name',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'placeholder' => __('Dari Kota'),
												'readonly' => true,
											));
									?>
								</div>
								<div class="col-sm-6">
									<?php 
											echo $this->Form->input('to_city_name',array(
												'label'=> false, 
												'class'=>'form-control',
												'required' => false,
												'placeholder' => __('Ke Kota'),
												'readonly' => true,
											));
									?>
								</div>
							</div>
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
									echo $this->Form->input('driver_condition',array(
										'label'=> __('Kondisi Supir dan kenek'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('truck_condition',array(
										'label'=> __('Kondisi Armada dan Muatan'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				    </div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="box box-primary">
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
										'label'=> __('Jenis Kendaraan'), 
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
										'label'=> __('Kondisi Kendaraan'), 
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
						'action' => 'ttuj', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Html->link(__('Next'), '#step2', array(
						'class'=> 'btn btn-success',
						'id' => 'nextLaka'
					));
			?>
		</div>
	</div>
	<div id="step2">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Data dan Informasi Kejadian'); ?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('description_laka',array(
										'label'=> __('Deskripsi LAKA'), 
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
				        <?php
				    			if(!empty($this->request->data['Laka']['ilustration_photo']) && !is_array($this->request->data['Laka']['ilustration_photo'])){
				    				$photo = $this->Common->photo_thumbnail(array(
										'save_path' => Configure::read('__Site.laka_photo_folder'), 
										'src' => $this->request->data['Laka']['ilustration_photo'], 
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
									echo $this->Form->input('ilustration_photo',array(
										'type' => 'file',
										'label'=> __('Foto Ilustrasi LAKA'), 
										'class'=>'form-control',
										'required' => false
									));
							?>
				        </div>
				    </div>
				</div>
				<div class="box box-primary">
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
				<div class="box box-primary">
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
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), '#step1', array(
						'class'=> 'btn btn-default',
						'id' => 'backLaka'
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