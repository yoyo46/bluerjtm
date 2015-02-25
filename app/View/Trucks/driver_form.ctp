<?php
		$this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
		$this->Html->addCrumb(__('Supir'), array(
			'controller' => 'trucks',
			'action' => 'drivers'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Driver', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'type' => 'file'
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Data Pribadi'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<?php
		    			if(!empty($this->request->data['Driver']['photo']) && !is_array($this->request->data['Driver']['photo'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.profile_photo_folder'), 
								'src' => $this->request->data['Driver']['photo'], 
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
								'label'=> __('Foto Supir *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Foto Supir')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('no_id',array(
								'label'=> __('No. ID *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. ID'),
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('name',array(
								'label'=> __('Nama Lengkap *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Lengkap')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('alias',array(
								'label'=> __('Nama Panggilan'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Panggilan')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('identity_number',array(
								'label'=> __('No. KTP *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. KTP')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('address',array(
								'label'=> __('Alamat Rumah *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Address KTP')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('city',array(
								'label'=> __('Kota *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Kota')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('provinsi',array(
								'label'=> __('Provinsi *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Provinsi')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('no_hp',array(
							'label'=> __('No. HP *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('No. HP')
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('phone',array(
							'label'=> __('No. Telp'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('No. Telp')
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('tempat_lahir',array(
								'label'=> __('Tempat Lahir *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Tempat Lahir')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('tgl_lahir', __('Tgl Lahir *'));
					?>
					<div class="row">
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->day('tgl_lahir', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Hari'),
										'id' => 'day',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->month('tgl_lahir', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Bulan'),
										'id' => 'month',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->year('tgl_lahir', 1949, date('Y') - 12, array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'empty' => __('Tahun'),
										'id' => 'year',
										'required' => false,
									));
							?>
						</div>
					</div>
		        	<?php 
							echo $this->Form->error('birth_date', array(
								'notempty' => __('Tgl Lahir harap dipilih'),
								'date' => __('Tgl Lahir tidak benar'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
		        	?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Data SIM'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('jenis_sim_id',array(
								'label'=> __('Jenis SIM *'), 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih Jenis SIM --'),
								'options' => $jenisSims,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('no_sim',array(
								'label'=> __('No. SIM *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. SIM')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('tgl_expire_sim', __('Tgl Berakhir SIM *'));
					?>
					<div class="row">
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->day('tgl_expire_sim', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Hari'),
										'id' => 'day',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->month('tgl_expire_sim', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Bulan'),
										'id' => 'month',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->year('tgl_expire_sim', date('Y')-15, date('Y')+5, array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'empty' => __('Tahun'),
										'id' => 'year',
										'required' => false,
									));
							?>
						</div>
					</div>
		        	<?php 
							echo $this->Form->error('expired_date_sim', array(
								'notempty' => __('Tgl Berakhir SIM harap dipilih'),
								'date' => __('Tgl Berakhir SIM tidak benar'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
		        	?>
		        </div>
		    </div>
		</div>
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Data Kontak Darurat'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('kontak_darurat_name',array(
								'label'=> __('Nama Lengkap *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Lengkap')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('kontak_darurat_no_hp',array(
								'label'=> __('No. Hp *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. Hp'),
								'type' => 'text'
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('kontak_darurat_phone',array(
								'label'=> __('No. Telp'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. Telp'),
								'type' => 'text'
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('driver_relation_id',array(
								'label'=> __('Hubungan *'), 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih Hubungan --'),
								'options' => $driverRelations
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Penerimaan'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('tgl_penerimaan', __('Tgl Penerimaan *'));
					?>
					<div class="row">
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->day('tgl_penerimaan', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Hari'),
										'id' => 'day',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->month('tgl_penerimaan', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Bulan'),
										'id' => 'month',
										'required' => false,
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->year('tgl_penerimaan', 1949, date('Y'), array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'empty' => __('Tahun'),
										'id' => 'year',
										'required' => false,
									));
							?>
						</div>
					</div>
		        	<?php 
							echo $this->Form->error('join_date', array(
								'notempty' => __('Tgl Penerimaan harap dipilih'),
								'date' => __('Tgl Penerimaan tidak benar'),
							), array(
								'wrap' => 'div', 
								'class' => 'error-message',
							));
		        	?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('branch_id',array(
								'label'=> __('Cabang Penerimaan *'), 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih Cabang'),
							));
					?>
		        </div>
		    </div>
		</div>
		<?php
			if(!empty($id)){
		?>
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Resign'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<?php echo $this->Html->tag('p', __('Dengan mengisi tanggal resign, maka, supir akan secara otomatis tidak akan tampil lagi atau menjadi non-aktif statusnya.'));?>
		    	<div class="form-group">
		    		<div class="checkbox">
                        <label class="date-resign-handle">
                        	<?php 
                        		echo $this->Form->checkbox('is_resign').' Resign?';
                        	?>
                        </label>
                    </div>
		    	</div>
		        <div class="form-group <?php echo (!empty($this->request->data['Driver']['is_resign'])) ? '' : 'hide';?>" id="resign-date">
		        	<?php 
							echo $this->Form->label('date_resign', __('Tgl Resign *'));
					?>
					<div class="row">
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->day('date_resign', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Hari'),
										'id' => 'day',
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->month('date_resign', array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'required' => false,
										'empty' => __('Bulan'),
										'id' => 'month',
									));
							?>
						</div>
						<div class="col-sm-4">
				        	<?php 
									echo $this->Form->year('date_resign', 1949, date('Y'), array(
										'label'=> false, 
										'class'=>'form-control selectbox-date',
										'empty' => __('Tahun'),
										'id' => 'year',
									));
							?>
						</div>
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
    		echo $this->Form->button(__('Submit'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'drivers', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>