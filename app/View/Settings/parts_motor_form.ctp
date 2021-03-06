<?php
		$this->Html->addCrumb(__('Part Motor'), array(
			'action' => 'parts_motor'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('PartsMotor', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
				echo $this->Form->label('code',__('Kode Part Motor *')); 

				echo $this->Form->input('code',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kode Part Motor')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('name',__('Nama Part Motor *')); 

				echo $this->Form->input('name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Part Motor')
				));
			?>
        </div>
        <div class="form-group">
        	<?php 
				echo $this->Form->label('biaya_claim_unit',__('Biaya Klaim per unit')); 

				echo $this->Form->input('biaya_claim_unit',array(
					'type' => 'text',
					'label'=> false, 
					'class'=>'form-control input_price',
					'required' => false,
					'placeholder' => __('Biaya Klaim per unit')
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
					'action' => 'jenis_perlengkapan', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>