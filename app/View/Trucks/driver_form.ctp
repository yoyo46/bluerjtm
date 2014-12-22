<?php
		$this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
		$this->Html->addCrumb(__('Supir'), array(
			'controller' => 'trucks',
			'action' => 'drivers'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Driver', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'type' => 'file'
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Supir'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<?php
		    		if(!empty($this->request->data['Driver']['photo'])){
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Common->getImage('drivers', $this->request->data['Driver']['photo'], true, 'small');
					?>
		        </div>
		        <?php
		    		}
		    	?>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('photo',array(
								'type' => 'file',
								'label'=> __('Foto Supir *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Foto Supir')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('name',array(
								'label'=> __('Nama Supir *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Supir')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('alias',array(
								'label'=> __('Nama Panggilan'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Nama Panggilan')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('address',array(
								'label'=> __('Alamat *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('Address')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('phone',array(
							'label'=> __('Telepon *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Telepon')
						));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
						echo $this->Form->input('phone_2',array(
							'label'=> __('Telepon 2'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Telepon 2')
						));
					?>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Dokumen'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('identity_number',array(
								'label'=> __('No. Identitas *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. Identitas')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('no_sim',array(
								'label'=> __('No. SIM *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. SIM')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('expired_date_sim',array(
								'label'=> __('Tgl Berakhir SIM *'), 
								'class'=>'form-control custom-date',
								'required' => false,
								'placeholder' => __('Tgl Berakhir SIM'),
								'type' => 'text'
							));
					?>
		        </div>
		    </div>
		</div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Form->button(__('Submit'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'drivers', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>