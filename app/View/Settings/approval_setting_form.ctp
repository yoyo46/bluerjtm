<?php
		$this->Html->addCrumb(__('Approval Setting'), array(
			'controller' => 'settings', 
			'action'=> 'approval_setting',
			'admin' => false,
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Approval', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('List Approval Kas/Bank'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('approval_module_id',array(
						'label'=> __('Modul *'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Modul'),
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('group_id',array(
						'label'=> __('Posisi yg Mengajukan *'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Posisi'),
					));
			?>
        </div>
    	<div class="form-group action">
    		<?php 
	                echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah List Approval'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-success add-custom-field btn-xs',
	                	'action_type' => 'auth-cash-bank'
	                ));
	        ?>
    	</div>
	</div>
</div>
<div class="list-approval-setting">
	<?php 
	        if( !empty($this->request->data['ApprovalDetail']['min_amount']) ) {
	            foreach ($this->request->data['ApprovalDetail']['min_amount'] as $key => $min_amount) {
					echo $this->element('blocks/settings/approval_setting', array(
						'idx' => $key,
					));
	            }
	        } else {
				echo $this->element('blocks/settings/approval_setting', array(
					'idx' => 0,
				));
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
				'controller' => 'settings', 
				'action'=> 'approval_setting',
				'admin' => false,
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
	echo $this->Form->end();
?>
<div id="form-authorize" class="hide">
	<?php 
			echo $this->Form->input('ApprovalDetailPosition.group_id.', array(
				'label' => false,
				'empty' => __('Pilih Posisi'),
				'options' => $employePositions,
				'class' => 'form-control approval-position',
				'div' => false
			));
	?>
</div>