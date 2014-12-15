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
    <div class="box-body">
        <div class="form-group">
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
        <div class="form-group">
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
        <div class="form-group">
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