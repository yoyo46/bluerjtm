<?php
		$this->Html->addCrumb(__('Bank'), array(
			'action' => 'banks'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Bank', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Nama Bank *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Nama Bank')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('branch',array(
						'label'=> __('Cabang *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Cabang')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('account_number',array(
						'label'=> __('No. Rek *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('No. Rek')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('account_name',array(
						'label'=> __('Atas Nama *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Atas Nama')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('coa_id',array(
						'label'=> __('No. Akun'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih No. Akun'),
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
					'action' => 'banks', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>