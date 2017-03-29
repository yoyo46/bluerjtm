<?php
		$this->Html->addCrumb(__('Supplier'), array(
			'controller' => 'settings',
			'action' => 'vendors'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Vendor', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
    	<?php 
				echo $this->Html->tag('div', $this->Form->input('code',array(
					'label'=> __('Kode Supplier *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kode Supplier')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama Supplier *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Supplier')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('address',array(
					'label'=> __('Alamat *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Address')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('phone_number',array(
					'label'=> __('Telepon *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('phone')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('pic',array(
					'label'=> __('Nama PIC *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama PIC')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('pic_phone',array(
					'label'=> __('Telepon PIC *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('phone')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Common->buildInputForm('top', __('T.O.P'), array(
					'type' => 'text',
					'textGroup' => __('Hari'),
					'column' => 'col-sm-6',
				));
		?>
    </div>
</div>
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Pattern Supplier'); ?></h3>
    </div>
    <div class="box-body">
		<?php 
				echo $this->Html->tag('div', $this->Form->input('pattern',array(
					'label'=> __('Kode Pattern *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kode Pattern')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('last_number',array(
					'label'=> __('No Awal Dokumen *'), 
					'class'=>'form-control input_number',
					'required' => false,
					'placeholder' => __('No Awal Dokumen')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('min_digit',array(
					'label'=> __('Min Digit No Dokumen *'), 
					'class'=>'form-control input_number',
					'required' => false,
				)), array(
					'class' => 'form-group'
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
				'action' => 'vendors', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>