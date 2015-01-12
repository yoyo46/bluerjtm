<?php
		$this->Html->addCrumb(__('Company'), array(
			'controller' => 'settings',
			'action' => 'companies'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo $sub_module_title?></h3>
		    </div>
		    <?php 
				echo $this->Form->create('Company', array(
					'url'=> $this->Html->url( null, true ), 
					'role' => 'form',
					'inputDefaults' => array('div' => false),
					'type' => 'file'
				));
			?>
		    <div class="box-body">
		    	<?php 
						echo $this->Html->tag('div', $this->Form->input('name',array(
							'label'=> __('Nama Company *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Nama Company')
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
						echo $this->Html->tag('div', $this->Form->input('file_upload',array(
							'label'=> __('File'), 
							'type' => 'file',
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('File')
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
							'action' => 'companies', 
						), array(
							'class'=> 'btn btn-default',
						));
		    	?>
		    </div>
			<?php
				echo $this->Form->end();
			?>
		</div>
	</div>
</div>