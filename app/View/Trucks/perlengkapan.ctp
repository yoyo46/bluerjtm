<?php
		$this->Html->addCrumb(__('Truk'), array(
			'controller' => 'trucks',
			'action' => 'index'
		));
		$this->Html->addCrumb(__('Data Truk'), array(
			'controller' => 'trucks',
			'action' => 'edit',
			$truck_id
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->element('blocks/trucks/info_truck');
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('TruckPerlengkapan', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body" id="box-field-input">
    	<div class="form-group">
			<div class="action">
				<?php 
						echo $this->Html->link('Tambah field', 'javascript:', array(
							'class' => 'add-custom-field btn btn-success btn-xs',
							'type_action' => 'perlengkapan'
						));
						echo $this->Html->link('Hapus field', 'javascript:', array(
							'class' => 'delete-custom-field btn btn-danger btn-xs',
							'type_action' => 'perlengkapan'
						));
				?>
			</div>
		</div>
    	<?php
    		if( empty($this->request->data) ){
    	?>
    	<div class="form-group" id="perlengkapan1">
        	<?php 
				echo $this->Form->label('perlengkapan_id.0', __('Perlengkapan 1')); 

				echo $this->Form->input('perlengkapan_id.0',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'options' => $perlengkapans,
					'empty' => __('Pilih Perlengkapan --'),
				));
			?>
        </div>
        <div class="form-group"  id="perlengkapan2">
        	<?php 
				echo $this->Form->label('perlengkapan_id.1', __('Perlengkapan 2')); 

				echo $this->Form->input('perlengkapan_id.',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'options' => $perlengkapans,
					'empty' => __('Pilih Perlengkapan --'),
				));
			?>
        </div>
        <div class="form-group"  id="perlengkapan1">
        	<?php 
				echo $this->Form->label('perlengkapan_id.2', __('Perlengkapan 3')); 

				echo $this->Form->input('perlengkapan_id.',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'options' => $perlengkapans,
					'empty' => __('Pilih Perlengkapan --'),
				));
			?>
        </div>
    	<?php
    			} else if( !empty($this->request->data['TruckPerlengkapan']['perlengkapan_id']) ){
    				foreach ($this->request->data['TruckPerlengkapan']['perlengkapan_id'] as $key => $value) {
    	?>
    	<div class="form-group"  id="perlengkapan<?php echo $key;?>">
        	<?php 
				echo $this->Form->label('TruckPerlengkapan.perlengkapan_id.'.$key, __('Perlengkapan '.($key+1))); 

				echo $this->Form->input('TruckPerlengkapan.perlengkapan_id.'.$key,array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'value' => $value,
					'empty' => __('Pilih Perlengkapan --'),
					'options' => $perlengkapans,
				));
			?>
        </div>	
    	<?php
    			}
    		}
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
					'action' => 'index', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>