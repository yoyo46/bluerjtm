<?php
		$this->Html->addCrumb(__('Perlengkapan'), array(
			'controller' => 'settings',
			'action' => 'perlengkapan'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Perlengkapan', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<?php 
				echo $this->Html->tag('div', $this->Form->input('jenis_perlengkapan_id',array(
					'label'=> __('Jenis Perlengkapan *'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Jenis Perlengkapan --'),
				)), array(
					'class' => 'form-group'
				));

				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama Perlengkapan *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Perlengkapan')
				)), array(
					'class' => 'form-group'
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
					'action' => 'perlengkapan', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>