<?php
		$this->Html->addCrumb(__('Truk'), array(
			'controller' => 'trucks',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Truck', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<?php echo $this->Html->tag('h3', __('Informasi Utama'));?>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('nopol',__('Nopol *')); 

				echo $this->Form->input('nopol',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nopol')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('truck_brand_id',__('Merek Truk *')); 

				echo $this->Form->input('truck_brand_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Merek Truk'),
					'options' => $truck_brands
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('truck_category_id',__('Jenis Truk *')); 

				echo $this->Form->input('truck_category_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Jenis Truk'),
					'options' => $truck_categories
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('company_id',__('Pemilik Truk *')); 

				echo $this->Form->input('company_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Pemilik Truk'),
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('driver_id',__('Supir Truk *')); 

				echo $this->Form->input('driver_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Perusahaan Truk'),
					'options' => $drivers
				));
			?>
        </div>
        <?php echo $this->Html->tag('h3', __('Informasi Dokumen'));?>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('no_contract',__('No Kontrak *')); 

				echo $this->Form->input('no_contract',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('No Kontrak')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('bpkb',__('BPKB *')); 

				echo $this->Form->input('bpkb',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('BPKB')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('atas_nama',__('Pemilik Truk *')); 

				echo $this->Form->input('atas_nama',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Pemilik Truk')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('no_rangka',__('No Rangka *')); 

				echo $this->Form->input('no_rangka',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('No Rangka')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('no_stnk',__('No STNK *')); 

				echo $this->Form->input('no_stnk',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('No STNK')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('no_machine',__('No Mesin *')); 

				echo $this->Form->input('no_machine',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('No Mesin')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('capacity',__('Kapasitas *')); 

				echo $this->Form->input('capacity',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kapasitas')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('tahun',__('Tahun *')); 

				echo $this->Form->input('tahun',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Tahun'),
					'options' => $years
				));
			?>
        </div>
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
					'disabled' => (!empty($this->request->data['Truck']['is_asset'])) ? false : 'disabled'
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('tgl_bpkb',__('Tanggal BPKB *')); 

				echo $this->Form->input('tgl_bpkb',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal BPKB')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('description',__('Keterangan')); 

				echo $this->Form->input('description',array(
					'type' => 'textarea',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
				));
			?>
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
</div>