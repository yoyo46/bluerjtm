<?php
		$this->Html->addCrumb(__('Customer'), array(
			'controller' => 'settings',
			'action' => 'customers'
		));
		$this->Html->addCrumb($sub_module_title);
		echo $this->element('blocks/settings/customers');
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('CustomerPattern', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
    	<?php 
				echo $this->Html->tag('div', $this->Form->input('pattern',array(
					'label'=> __('Kode Pattern *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kode Pattern')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('last_number',array(
					'label'=> __('No Awal Dokumen *'), 
					'class'=>'form-control input_number',
					'required' => false,
					'placeholder' => __('No Awal Dokumen')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('min_digit',array(
					'label'=> __('Min Digit No Dokumen *'), 
					'class'=>'form-control input_number',
					'required' => false,
				)), array(
					'class' => 'form-group'
				));
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
					'action' => 'customers', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>