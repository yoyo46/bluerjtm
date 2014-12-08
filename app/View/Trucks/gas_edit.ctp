<?php
	$this->Html->addCrumb(__('Data Truk'), array(
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
		echo $this->Form->create('Gases', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->input('premium', array(
					'type' => 'text',
					'label'=> __('Harga Premium *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Harga Premium')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->input('solar', array(
					'type' => 'text',
					'label'=> __('Harga Solar *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Harga Solar')
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