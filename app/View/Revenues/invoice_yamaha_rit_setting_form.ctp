<?php
		$this->Html->addCrumb(__('Pengaturan Yamaha Per RIT'), array(
			'action' => 'units'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('SettingInvoiceYamahaRit', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Judul *'), 
						'class'=>'form-control',
						'required' => false,
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('percent',array(
						'label'=> __('Tarif dlm Persen (%) *'), 
						'class'=>'form-control',
						'required' => false,
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
					'action' => 'invoice_yamaha_rit_setting', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>