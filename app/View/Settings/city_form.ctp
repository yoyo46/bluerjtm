<?php
		$this->Html->addCrumb(__('Kota'), array(
			'action' => 'cities'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('City', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('code',array(
						'label'=> __('Kode'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Kode Kota')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('alias',array(
						'label'=> __('Nama Singkatan *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Nama Singkatan')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Nama Kota *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Nama Kota')
					));
			?>
        </div>
        <div class="form-group">
	        <div class="checkbox aset-handling">
                <label>
                    <?php 
							echo $this->Form->checkbox('is_head_office',array(
								'label'=> false, 
								'required' => false,
							)).sprintf(__('Head Office ? %s'), $this->Html->tag('small', __('( Fitur ini hanya berlaku untuk satu kota yg dipilih )')));
					?>
                </label>
            </div>
        </div>
        <div class="form-group">
	        <div class="checkbox-options">
	        	<div class="checkbox">
	                <label>
	                	<?php echo $this->Form->checkbox('is_branch').' Cabang?';?>
	                </label>
	            </div>
	        </div>
	    </div>
	    <div class="form-group">
	        <div class="checkbox-options">
	        	<div class="checkbox">
	                <label>
	                	<?php echo $this->Form->checkbox('is_plant').' Plant?';?>
	                </label>
	            </div>
	        </div>
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
					'action' => 'cities', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>