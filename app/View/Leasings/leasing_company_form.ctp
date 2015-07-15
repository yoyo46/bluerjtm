<?php
		$this->Html->addCrumb(__('Leasing'), array(
			'action' => 'index'
		));
		$this->Html->addCrumb(__('Perusahaan Leasing'), array(
			'action' => 'leasing_companies'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('LeasingCompany', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Nama Perusahaan Leasing *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Nama Leasing')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('phone',array(
						'label'=> __('Telepon'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Telepon')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('address',array(
						'label'=> __('Alamat'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Alamat'),
						'type' => 'textarea'
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
					'action' => 'leasing_companies', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>