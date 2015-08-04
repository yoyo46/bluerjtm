<?php
		$this->Html->addCrumb(__('Group'), array(
            'controller' => 'users',
            'action' => 'groups'
        ));

		$this->Html->addCrumb(__('Action Module'), array(
			'controller' => 'users',
			'action' => 'action_modules'
		));
		
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('BranchModule', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('name',array(
						'label'=> __('Nama Module *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Nama Module')
					));
			?>
        </div>
        <div class="form-group" id="parent_branch" style="display:<?php echo empty($this->request->data['BranchModule']['parent_id']) ? 'block' : 'none';?>">
        	<?php 
					echo $this->Form->input('branch_parent_module_id',array(
						'label'=> __('Parent Module Group *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('Parent Module Group'),
						'options' => $parent_modules,
						'empty' => __('Pilih Parent Module Group')
					));
			?>
        </div>
        <div class="form-group">
	        <div class="checkbox-options">
	        	<div class="checkbox" id="handle-module">
	                <label>
	                	<?php 
	                		$checked = 'checked';
	                		if(!empty($this->request->data) && !empty($this->request->data['BranchModule']['parent_id'])){
	                			$checked = false;
	                		}
	                		echo $this->Form->checkbox('is_parent', array(
	                			'checked' => $checked,
	                		)).' Parent Module?';
	                	?>
	                </label>
	            </div>
	        </div>
	    </div>
	    <div class="box-action-module" style="display:<?php echo !empty($this->request->data['BranchModule']['parent_id']) ? 'block' : 'none';?>">
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('parent_id',array(
							'label'=> __('Parent Module *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Parent Module'),
							'options' => $parent_group,
							'empty' => __('Pilih Parent Module')
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('type',array(
							'label'=> __('Tipe Modul *'), 
							'class'=>'form-control order-handle',
							'required' => false,
							'empty' => __('Pilih Tipe Modul'),
							'options' => array(
								'lihat' => 'Lihat',
								'tambah' => 'Tambah',
								'ubah' => 'Ubah',
								'hapus' => 'Hapus',
								'void' => 'Void',
								'non-aktif' => 'Non-aktif',
							)
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('controller',array(
							'label'=> __('Controller *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Nama Controller')
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('action',array(
							'label'=> __('Action *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Nama Action')
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('extend_action',array(
							'label'=> __('Parameter Tambahan'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Parameter Tambahan')
						));
				?>
	        </div>
	    </div>
	    <div class="form-group">
        	<?php 
					echo $this->Form->input('order',array(
						'label'=> __('order *'), 
						'class'=>'form-control',
						'id' => 'order-control',
						'required' => false,
						'placeholder' => __('order'),
						'readonly' => !empty($this->request->data['BranchModule']['parent_id']) ? true : false
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
					'controller' => 'users',
					'action' => 'action_modules'
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>