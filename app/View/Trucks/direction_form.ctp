<?php
	$this->Html->addCrumb(__('Direction'), array(
		'controller' => 'trucks',
		'action' => 'cities'
	));
	$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Direction', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('from_city_id',__('Dari Kota *')); 

				echo $this->Form->input('from_city_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Kota awal'),
					'options' => $cities
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('to_city_id',__('Ke Kota *')); 

				echo $this->Form->input('to_city_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Kota tujuan'),
					'options' => $cities
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('distance',__('Jumlah Jarak Tempuh *')); 

				echo $this->Form->input('distance',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Jarak Tempuh'),
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('gas',__('Jumlah Bahan Bakar *')); 

				echo $this->Form->input('gas',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Bahan Bakar'),
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