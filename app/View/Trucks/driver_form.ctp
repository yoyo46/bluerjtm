<?php
		$this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
		$this->Html->addCrumb(__('Supir'), array(
			'controller' => 'trucks',
			'action' => 'drivers'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('Driver', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->label('name',__('Supir name *')); 

					echo $this->Form->input('name',array(
						'label'=> false, 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Supir Name')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('identity_number',__('No. Identitas *')); 

					echo $this->Form->input('identity_number',array(
						'label'=> false, 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('No. Identitas')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('address',__('Alamat *')); 

					echo $this->Form->input('address',array(
						'label'=> false, 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Address')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('phone',__('Telepon *')); 

				echo $this->Form->input('phone',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Telepon')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('phone_2',__('Telepon 2')); 

				echo $this->Form->input('phone_2',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Telepon 2')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('uang_makan',__('Uang makan *')); 

				echo $this->Form->input('uang_makan',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Uang makan')
				));
			?>
        </div>
    </div>

    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Submit'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), array(
					'action' => 'drivers', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>