<?php
		$this->Html->addCrumb(__('Truk'), array(
			'controller' => 'trucks',
			'action' => 'index'
		));
		$this->Html->addCrumb(__('Data Truk'), array(
			'controller' => 'trucks',
			'action' => 'edit',
			$truck_id
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->element('blocks/trucks/info_truck');
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Perlengkapan', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
	<?php 
		echo $this->Html->link('Tambah field', 'javascript:', array(
			'class' => 'add-custom-field btn btn-success'
		));
		echo $this->Html->link('Hapus field', 'javascript:', array(
			'class' => 'delete-custom-field btn btn-danger'
		));
	?>
    <div class="box-body" id="box-field-input">
    	<?php
    		if( empty($this->request->data) ){
    	?>
    	<div class="form-group" id="perlengkapan1">
        	<?php 
				echo $this->Form->label('name.0', __('perlengkapan 1')); 

				echo $this->Form->input('name.',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('perlengkapan')
				));
			?>
        </div>
        <div class="form-group"  id="perlengkapan2">
        	<?php 
				echo $this->Form->label('name.1', __('perlengkapan 2')); 

				echo $this->Form->input('name.',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('perlengkapan')
				));
			?>
        </div>
        <div class="form-group"  id="perlengkapan1">
        	<?php 
				echo $this->Form->label('name.2', __('perlengkapan 3')); 

				echo $this->Form->input('name.',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('perlengkapan')
				));
			?>
        </div>
    	<?php
    		}else{
    			foreach ($this->request->data['Perlengkapan']['name'] as $key => $value) {
    	?>
    	<div class="form-group"  id="perlengkapan<?php echo $key;?>">
        	<?php 
				echo $this->Form->label('Perlengkapan.name.'.$key, __('perlengkapan '.($key+1))); 

				echo $this->Form->input('Perlengkapan.name.'.$key,array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('perlengkapan'),
					'value' => $value
				));
			?>
        </div>	
    	<?php
    			}
    		}
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
</div>