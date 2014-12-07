<?php
	$this->Html->addCrumb(__('Company'), array(
		'controller' => 'trucks',
		'action' => 'companies'
	));
	$this->Html->addCrumb($sub_module_title);
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
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Nama Perusahaan *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Perusahaan')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('address',__('Alamat *')); 

				echo $this->Form->input('address',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Address')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('phone_number',__('Telepon *')); 

				echo $this->Form->input('phone_number',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('phone')
				));
			?>
        </div>
    </div>

    <div class="box-footer">
    	<?php
    		echo $this->Form->button(__('Submit'), array(
				'div' => false, 
				'class'=> 'btn btn-primary',
				'type' => 'submit',
			));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>