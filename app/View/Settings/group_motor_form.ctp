<?php
		$this->Html->addCrumb(__('Tipe Motor'), array(
            'action' => 'type_motors',
        ));
		$this->Html->addCrumb(__('Grup Motor'), array(
            'action' => 'group_motors',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('GroupMotor', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Grup Motor *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Grup Motor')
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
	    		echo $this->Common->rule_link(__('Kembali'), array(
					'action' => 'group_motors', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>