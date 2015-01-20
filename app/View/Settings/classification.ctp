<?php
		$this->Html->addCrumb(__('Klasifikasi'));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('GroupClassification', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('GroupClassification.name.1', array(
						'label'=> __('Klasifikasi 1'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('GroupClassification.name.2', array(
						'label'=> __('Klasifikasi 2'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('GroupClassification.name.3', array(
						'label'=> __('Klasifikasi 3'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('GroupClassification.name.4', array(
						'label'=> __('Klasifikasi 4'), 
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
    	?>
    </div>
	<?php
			echo $this->Form->end();
	?>
</div>