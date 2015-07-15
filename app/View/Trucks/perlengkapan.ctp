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
							'action_type' => 'perlengkapan'
						));
						echo $this->Html->link('Hapus field', 'javascript:', array(
							'class' => 'delete-custom-field btn btn-danger btn-xs',
							'action_type' => 'perlengkapan'
						));
				?>
			</div>
		</div>
		<div class="list-perlengkapan">
	    	<?php
	    		if( empty($this->request->data) ){
	    	?>
	    	<div id="perlengkapan0" class="seperator">
		    	<div class="row">
		    		<div class="col-sm-9">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.perlengkapan_id.0',array(
										'label'=> __('Perlengkapan 1'), 
										'class'=>'form-control',
										'required' => false,
										'options' => $perlengkapans,
										'empty' => __('Pilih Perlengkapan --'),
									));
							?>
				        </div>
		    		</div>
		    		<div class="col-sm-3">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.qty.0',array(
										'label'=> __('Jumlah Perlengkapan 1'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
		    		</div>
		    	</div>
	    	</div>
	    	<div id="perlengkapan1" class="seperator">
		    	<div class="row">
		    		<div class="col-sm-9">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.perlengkapan_id.1',array(
										'label'=> __('Perlengkapan 2'), 
										'class'=>'form-control',
										'required' => false,
										'options' => $perlengkapans,
										'empty' => __('Pilih Perlengkapan --'),
									));
							?>
				        </div>
		    		</div>
		    		<div class="col-sm-3">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.qty.1',array(
										'label'=> __('Jumlah Perlengkapan 2'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
		    		</div>
		    	</div>
	    	</div>
	    	<div id="perlengkapan2" class="seperator">
		    	<div class="row">
		    		<div class="col-sm-9">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.perlengkapan_id.2',array(
										'label'=> __('Perlengkapan 3'), 
										'class'=>'form-control',
										'required' => false,
										'options' => $perlengkapans,
										'empty' => __('Pilih Perlengkapan --'),
									));
							?>
				        </div>
		    		</div>
		    		<div class="col-sm-3">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.qty.2',array(
										'label'=> __('Jumlah Perlengkapan 3'), 
										'class'=>'form-control',
										'required' => false,
									));
							?>
				        </div>
		    		</div>
		    	</div>
	    	</div>
	    	<?php
	    			} else if( !empty($this->request->data['TruckPerlengkapan']['perlengkapan_id']) ){
	    				foreach ($this->request->data['TruckPerlengkapan']['perlengkapan_id'] as $key => $value) {
	    	?>
	    	<div id="perlengkapan<?php echo $key;?>" class="seperator">
		    	<div class="row">
		    		<div class="col-sm-9">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.perlengkapan_id.'.$key,array(
										'label'=> __('Perlengkapan '.($key+1)), 
										'class'=>'form-control',
										'required' => false,
										'options' => $perlengkapans,
										'empty' => __('Pilih Perlengkapan --'),
										'value' => $value,
									));
									echo $this->Form->error(sprintf('TruckPerlengkapan.%s.perlengkapan_id', $key), array(
										'notempty' => __('Perlengkapan harap dipilih'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
							?>
				        </div>
		    		</div>
		    		<div class="col-sm-3">
		    			<div class="form-group">
				        	<?php 
									echo $this->Form->input('TruckPerlengkapan.qty.'.$key,array(
										'label'=> __('Jumlah Perlengkapan '.($key+1)), 
										'class'=>'form-control input_number',
										'required' => false,
										'value' => !empty($this->request->data['TruckPerlengkapan']['qty'][$key])?$this->request->data['TruckPerlengkapan']['qty'][$key]:0,
									));
									echo $this->Form->error(sprintf('TruckPerlengkapan.%s.qty', $key), array(
										'notempty' => __('Jumlah perlengkapan harap diisi'),
									), array(
										'wrap' => 'div', 
										'class' => 'error-message',
									));
							?>
				        </div>
		    		</div>
		    	</div>
	    	</div>
	    	<?php
	    			}
	    		}
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
<div class="hide">
	<div id="perlengkapan_id">
		<?php 
				echo $this->Form->input('perlengkapan_id',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Perlengkapan --'),
					'options' => $perlengkapans
				));
		?>
	</div>
</div>