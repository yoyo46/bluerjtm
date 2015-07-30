<?php
		$this->Html->addCrumb(__('Karyawan'), array(
            'action' => 'employes',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Employe', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Nama Karyawan *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Karyawan')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('group_id',__('Posisi Karyawan *')); 

				echo $this->Form->input('group_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Posisi Karyawan'),
					'options' => $employe_positions
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
					'type' => 'text',
					'placeholder' => __('Alamat')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('phone',__('No. Telepon *')); 

				echo $this->Form->input('phone',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('No. Telepon')
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
					'action' => 'employes', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>