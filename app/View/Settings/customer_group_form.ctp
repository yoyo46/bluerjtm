<?php
		$this->Html->addCrumb(__('Grup Customer'), array(
			'controller' => 'settings',
			'action' => 'customer_groups'
		));
		$this->Html->addCrumb($sub_module_title);
		echo $this->Form->create('CustomerGroup', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="row">
	<div class="col-sm-12">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo $sub_module_title?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('name',array(
								'label'=> __('Grup Customer *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Grup Customer')
							));
					?>
		        </div>
		    </div>
		</div>
    </div>
	<div class="col-sm-12">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Pattern Customer'); ?></h3>
		    </div>
		    <div class="box-body">
				<?php 
						echo $this->Html->tag('div', $this->Form->input('CustomerGroupPattern.pattern',array(
							'label'=> __('Kode Pattern *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Kode Pattern')
						)), array(
							'class' => 'form-group'
						));
						echo $this->Html->tag('div', $this->Form->input('CustomerGroupPattern.last_number',array(
							'label'=> __('No Awal Dokumen *'), 
							'class'=>'form-control input_number',
							'required' => false,
							'placeholder' => __('No Awal Dokumen')
						)), array(
							'class' => 'form-group'
						));
						echo $this->Html->tag('div', $this->Form->input('CustomerGroupPattern.min_digit',array(
							'label'=> __('Min Digit No Dokumen *'), 
							'class'=>'form-control input_number',
							'required' => false,
						)), array(
							'class' => 'form-group'
						));
				?>
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
				'action' => 'customer_groups', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
	echo $this->Form->end();
?>