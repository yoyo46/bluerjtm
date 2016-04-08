<?php
		$this->Html->addCrumb(__('Company'), array(
			'controller' => 'settings',
			'action' => 'companies'
		));
		$this->Html->addCrumb($sub_module_title);

		$data = $this->request->data;
		$is_rjtm = isset($data['Company']['is_rjtm'])?$data['Company']['is_rjtm']:true;
		$is_invoice = isset($data['Company']['is_invoice'])?$data['Company']['is_invoice']:true;
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Company', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'type' => 'file'
		));
	?>
    <div class="box-body">
    	<?php
    			if(!empty($this->request->data['Company']['logo']) && !is_array($this->request->data['Company']['logo'])){
    				$logo = $this->Common->photo_thumbnail(array(
						'save_path' => Configure::read('__Site.general_photo_folder'), 
						'src' => $this->request->data['Company']['logo'], 
						'thumb'=>true,
						'size' => 'pm',
						'thumb' => true,
					));

					echo $this->Html->tag('div', $logo, array(
						'class' => 'form-group',
					));
    			}
    	?>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('logo',array(
						'type' => 'file',
						'label'=> __('Logo Company'), 
						'class'=>'form-control',
						'required' => false
					));
			?>
        </div>
    	<?php 
				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama Company *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Company')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('address',array(
					'label'=> __('Alamat *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Address')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('phone_number',array(
					'label'=> __('Telepon *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nomor Telepon')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('fax',array(
					'label'=> __('Fax'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nomor Fax')
				)), array(
					'class' => 'form-group'
				));
				// echo $this->Html->tag('div', $this->Form->input('file_upload',array(
				// 	'label'=> __('File'), 
				// 	'type' => 'file',
				// 	'class'=>'form-control',
				// 	'required' => false,
				// 	'placeholder' => __('File'),
				// )), array(
				// 	'class' => 'form-group'
				// ));
		?>
	    <div class="form-group">
	        <div class="checkbox-options">
	        	<div class="checkbox">
	                <label>
	                	<?php
	                			echo $this->Form->checkbox('is_rjtm', array(
	                				'checked' => $is_rjtm,
                				)).__(' Uang Jalan via RJTM?');
            			?>
	                </label>
	            </div>
	        </div>
	    </div>
	    <div class="form-group">
	        <div class="checkbox-options">
	        	<div class="checkbox">
	                <label>
	                	<?php
	                			echo $this->Form->checkbox('is_invoice', array(
	                				'checked' => $is_invoice,
                				)).__(' Tampil dipilihan Invoice?');
            			?>
	                </label>
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
					'action' => 'companies', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>
