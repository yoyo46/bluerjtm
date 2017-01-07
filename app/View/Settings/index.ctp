<?php
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Setting', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'type' => 'file',
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Icon Monitoring'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<?php
		    			if(!empty($this->request->data['Setting']['icon_berangkat']) && !is_array($this->request->data['Setting']['icon_berangkat'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Setting']['icon_berangkat'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_berangkat',array(
								'type' => 'file',
								'label'=> __('Icon Truk Berangkat'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['icon_tiba']) && !is_array($this->request->data['Setting']['icon_tiba'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Setting']['icon_tiba'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_tiba',array(
								'type' => 'file',
								'label'=> __('Icon Truk Tiba'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['icon_bongkaran']) && !is_array($this->request->data['Setting']['icon_bongkaran'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Setting']['icon_bongkaran'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_bongkaran',array(
								'type' => 'file',
								'label'=> __('Icon Truk Bongkaran'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['icon_balik']) && !is_array($this->request->data['Setting']['icon_balik'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Setting']['icon_balik'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_balik',array(
								'type' => 'file',
								'label'=> __('Icon Truk Balik'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['icon_pool']) && !is_array($this->request->data['Setting']['icon_pool'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Setting']['icon_pool'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_pool',array(
								'type' => 'file',
								'label'=> __('Icon Truk Pool'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['icon_laka']) && !is_array($this->request->data['Setting']['icon_laka'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $this->request->data['Setting']['icon_laka'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_laka',array(
								'type' => 'file',
								'label'=> __('Icon Truk LAKA'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Perusahaan'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('company_name',array(
								'label'=> __('Nama Perusahaan *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Perusahaan')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('company_address',array(
								'label'=> __('Alamat Perusahaan'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Alamat Perusahaan')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('company_email',array(
								'label'=> __('Email Perusahaan'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Email Perusahaan')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('company_phone',array(
								'label'=> __('Telepon Perusahaan'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Telepon Perusahaan')
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['favicon']) && !is_array($this->request->data['Setting']['favicon'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.profile_photo_folder'), 
								'src' => $this->request->data['Setting']['favicon'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_favicon',array(
								'type' => 'file',
								'label'=> __('Favicon *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Favicon')
							));
					?>
		        </div>
		    	<?php
		    			if(!empty($this->request->data['Setting']['logo']) && !is_array($this->request->data['Setting']['logo'])){
		    				$photo = $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.profile_photo_folder'), 
								'src' => $this->request->data['Setting']['logo'], 
								'thumb'=>true,
								'size' => 's',
								'thumb' => true,
							));

							echo $this->Html->tag('div', $photo, array(
								'class' => 'form-group',
							));
		    			}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('img_logo',array(
								'type' => 'file',
								'label'=> __('Logo Perusahaan *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Logo Perusahaan')
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi PPN & PPh'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="row">
		    		<div class="col-sm-4">
		    			<div class="form-group">
				    		<?php 
				    				echo $this->Form->label('SettingGeneral.ppn', __('PPN'));
				    		?>
		                    <div class="input-group">
						    	<?php 
										echo $this->Form->input('SettingGeneral.ppn',array(
											'type' => 'text',
											'label'=> false, 
											'class'=>'form-control input_number',
											'required' => false,
										));
						    			echo $this->Html->tag('span', __('%'), array(
						    				'class' => 'input-group-addon'
					    				));
								?>
							</div>
						</div>
		    			<div class="form-group">
				    		<?php 
				    				echo $this->Form->label('SettingGeneral.pph', __('PPh'));
				    		?>
		                    <div class="input-group">
						    	<?php 
										echo $this->Form->input('SettingGeneral.pph',array(
											'type' => 'text',
											'label'=> false, 
											'class'=>'form-control input_number',
											'required' => false,
										));
						    			echo $this->Html->tag('span', __('%'), array(
						    				'class' => 'input-group-addon'
					    				));
								?>
							</div>
						</div>
		    		</div>
		    	</div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Notifikasi Pembayaran'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="row">
		    		<div class="col-sm-6">
		    			<div class="form-group">
				    		<?php 
				    				echo $this->Form->label('SettingGeneral.leasing_expired_day', __('Pembayaran Leasing'));
				    		?>
		                    <div class="input-group">
						    	<?php 
										echo $this->Form->input('SettingGeneral.leasing_expired_day',array(
											'type' => 'text',
											'label'=> false, 
											'class'=>'form-control input_number',
											'required' => false,
										));
						    			echo $this->Html->tag('span', __('Hari Sebelum'), array(
						    				'class' => 'input-group-addon'
					    				));
								?>
							</div>
						</div>
		    		</div>
		    	</div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Pengaturan SPK'); ?></h3>
		    </div>
		    <div class="box-body">
	    		<?php 
						echo $this->Form->input('SettingGeneral.spk_internal_policy',array(
							'label'=> __('Ketentuan SPK Internal'), 
							'class'=>'form-control',
							'required' => false,
							'div' => 'form-group',
							'empty' => __('Pilih Ketentuan SPK'),
							'options' => array(
								'receipt' => __('Ada Penerimaan barang bekas'),
								'no_receipt' => __('Tidak ada penerimaan barang bekas'),
							),
						));
						echo $this->Form->input('SettingGeneral.spk_internal_status',array(
							'label'=> __('Status SPK Internal'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Ketentuan SPK'),
							'div' => 'form-group',
							'options' => array(
								'closed_expenditured' => __('"Closed" ketika barang keluar'),
								'closed_receipt' => __('"Closed" ketika barang bekas diterima'),
								'closed_all_condition' => __('Semua kondisi terpenuhi'),
							),
						));
				?>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Pengaturan Transaksi'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="row">
	    			<?php 
	    					echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('SettingGeneral.lock_closing_bank', array(
                                'type' => 'checkbox',
                                'label'=> false,
                                'required' => false,
                                'value' => 1,
                                'div' => false,
                            )).__('Kunci semua transaksi setelah Closing Bank')), array(
                                'class' => 'checkbox',
                            )), array(
                                'class' => 'col-sm-12',
                            ));
	    			?>
		    	</div>
		    </div>
		</div>
	</div>
	<?php
	/*
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Pembayaran'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('bank_name',array(
								'label'=> __('Nama Bank *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Bank')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('bank_branch',array(
								'label'=> __('Cabang Bank *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Cabang Bank')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('bank_account_number',array(
								'label'=> __('No. Rek *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. Rek')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('bank_account_name',array(
								'label'=> __('Atas Nama *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Atas Nama')
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Kwitansi'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('pusat',array(
								'label'=> __('Kota Pusat *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Kota Pusat')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('billing_name',array(
								'label'=> __('Nama Billing *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Billing')
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
	*/
	?>
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