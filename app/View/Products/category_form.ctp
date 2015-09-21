<?php
		$this->Html->addCrumb(__('Grup Barang'), array(
			'action' => 'categories'
		));
		$this->Html->addCrumb($sub_module_title);

		$categories = !empty($categories)?$categories:false;
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('ProductCategory', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
	    	<?php 
		        	echo $this->Form->input('parent_id',array(
						'label'=> __('Parent Grup'), 
						'class'=>'form-control',
						'required' => false,
		            	'empty' => __('Grup Utama'),
		            	'options' => $categories,
					));
	    	?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Nama Grup *'), 
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
					'action' => 'categories', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>