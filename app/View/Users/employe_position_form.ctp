<?php
		$this->Html->addCrumb(__('Karyawan'), array(
            'action' => 'employes',
        ));
		$this->Html->addCrumb(__('Posisi Karyawan'), array(
            'action' => 'employe_positions',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('EmployePosition', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Posisi Karyawan *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Posisi Karyawan')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('code',__('Kode Posisi *')); 

				echo $this->Form->input('code',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kode Posisi')
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
					'action' => 'employe_positions', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>