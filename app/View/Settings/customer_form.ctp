<?php
		$this->Html->addCrumb(__('Customer'), array(
			'controller' => 'settings',
			'action' => 'customers'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Customer', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<?php 
				echo $this->Html->tag('div', $this->Form->input('code',array(
					'label'=> __('Kode Customer *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Kode Customer')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('customer_type_id',array(
					'label'=> __('Tipe Customer *'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Tipe Customer')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('customer_group_id',array(
					'label'=> __('Grup Customer *'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Grup Customer')
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama Customer *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama Customer')
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
		?>
		<div class="row">
			<div class="col-sm-3">
		        <div class="form-group">
		        	<?php 
						echo $this->Form->label('target_rit',__('Target Rit / Bln *')); 

						echo $this->Form->input('target_rit',array(
							'type' => 'text',
							'label'=> false, 
							'class'=>'form-control input_number',
							'required' => false,
							'placeholder' => __('Target Rit / Bln'),
						));
					?>
		        </div>
			</div>
		</div>
	    <div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<?php 
							echo $this->Form->label('term_of_payment', __('Term Of Payment *'));
					?>
			        <div class="input-group">
				    	<?php 
								echo $this->Form->input('term_of_payment',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'type' => 'text',
								));
				    			echo $this->Html->tag('span', __('Hari'), array(
				    				'class' => 'input-group-addon'
			    				));
						?>
					</div>
				</div>
			</div>
		</div>
		<?php 
				echo $this->Html->tag('div', $this->Form->input('bank_id',array(
					'label'=> __('Bank'), 
					'class'=>'form-control',
					'required' => false,
					'empty' => __('Pilih Bank')
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
					'action' => 'customers', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>