<?php
	$this->Html->addCrumb(__('Data Truk'), array(
		'controller' => 'trucks',
		'action' => 'index'
	));
	$this->Html->addCrumb('Alokasi Truk', array(
		'controller' => 'trucks',
		'action' => 'alocations',
		$truck_id
	));
	$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('TruckAlocation', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->input('city_id', array(
					'label'=> __('Kota *'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Kota'),
					'options' => $cities
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