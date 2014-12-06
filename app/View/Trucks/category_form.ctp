<?php
	$this->Html->addCrumb(__('Categories'), array(
		'controller' => 'trucks',
		'action' => 'categories'
	));
	$this->Html->addCrumb($module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('TruckCategory', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Category name *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Category Name')
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