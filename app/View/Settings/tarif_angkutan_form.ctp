<?php
		$this->Html->addCrumb(__('Tarif Angkutan'), array(
			'action' => 'tarif_angkutan'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('TarifAngkutan', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<?php 
			echo $this->Form->input('from_city_id',array(
				'label'=> __('Kota awal *'), 
				'class'=>'form-control',
				'required' => false,
				'options' => $cities,
				'empty' => __('pilih kota awal'),
				'div' => array(
					'class' => 'form-group'
				)
			));

			echo $this->Form->input('to_city_id',array(
				'label'=> __('Kota tujuan *'), 
				'class'=>'form-control',
				'required' => false,
				'options' => $cities,
				'empty' => __('pilih kota tujuan'),
				'div' => array(
					'class' => 'form-group'
				)
			));

			echo $this->Form->input('customer_id',array(
				'label'=> __('Customer *'), 
				'class'=>'form-control',
				'required' => false,
				'options' => $customers,
				'empty' => __('pilih customer'),
				'div' => array(
					'class' => 'form-group'
				)
			));

			echo $this->Form->input('capacity',array(
				'label'=> __('Kapasitas *'), 
				'class'=>'form-control input_number',
				'required' => false,
				'placeholder' => __('Kapasitas'),
				'div' => array(
					'class' => 'form-group'
				)
			));

			echo $this->Form->input('tarif',array(
				'label'=> __('Tarif angkutan *'), 
				'type' => 'text',
				'class'=>'form-control input_number',
				'required' => false,
				'placeholder' => __('Tarif angkutan'),
				'div' => array(
					'class' => 'form-group'
				)
			));
			
			echo $this->Form->input('jenis_unit',array(
				'label'=> __('Jenis angkutan *'), 
				'class'=>'form-control',
				'required' => false,
				'options' => array(
					'per_unit' => 'per unit',
					'per_truck' => 'per truk',
				),
				'empty' => __('pilih jenis angkutan'),
				'div' => array(
					'class' => 'form-group'
				)
			));

			echo $this->Form->input('group_motor_id',array(
				'label'=> __('Grup motor *'), 
				'class'=>'form-control',
				'required' => false,
				'options' => $group_motors,
				'empty' => __('pilih group motor'),
				'div' => array(
					'class' => 'form-group'
				)
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
					'action' => 'tarif_angkutan', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>