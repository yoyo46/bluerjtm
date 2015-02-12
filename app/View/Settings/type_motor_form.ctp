<?php
		$this->Html->addCrumb(__('Tipe Motor'), array(
            'action' => 'type_motors',
        ));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('TipeMotor', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Nama Tipe Motor *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Tipe Motor')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('code_motor_id',__('Kode Motor *')); 

				echo $this->Form->input('code_motor_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Kode Motor'),
					'options' => $code_motors
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('group_motor_id',__('Grup Motor*')); 

				echo $this->Form->input('group_motor_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Grup Motor'),
					'options' => $group_motors
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
					'action' => 'type_motors', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>