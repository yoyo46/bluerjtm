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