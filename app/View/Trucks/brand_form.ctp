<?php
	$this->Html->addCrumb(__('Merek Truk'), array(
		'controller' => 'trucks',
		'action' => 'brands'
	));
	$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('TruckBrand', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Merek *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Merek')
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