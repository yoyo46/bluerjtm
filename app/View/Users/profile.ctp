<?php
	$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php
		echo $this->Form->create('User', array(
			'url'=> array(
				'controller'=> 'users', 
				'action'=> 'profile'
			),
			'class'=> 'form-horizontal'
		));
	?>
	<div class="box-body">
		<div class="form-group">
			<?php 
					$title = __('Email *');
					echo $this->Form->label('email', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('email',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Nama Depan *');
					echo $this->Form->label('Employe.first_name', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('Employe.first_name',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Nama Belakang');
					echo $this->Form->label('Employe.last_name', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('Employe.last_name',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Telepon');
					echo $this->Form->label('Employe.phone', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('Employe.phone',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Jenis Kelamin *');
					echo $this->Form->label('Employe.gender_id', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('Employe.gender_id',array(
						'label'=>false,
						'required' => false,
						'class' => 'form-control',
						'empty' => __('Pilih Jenis Kelamin'),
						'options' => array(
							1 => __('Pria'),
							2 => __('Wanita'),
						)
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Alamat *');
					echo $this->Form->label('Employe.address', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('Employe.address',array(
						'type' => 'textarea',
						'label'=>false,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Tangal Lahir *');
					echo $this->Form->label('Employe.birthdate', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('Employe.birthdate',array(
						'type' => 'text',
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control custom-date',
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
	    	?>
	    </div>
	</div>
	<?php
		echo $this->Form->end();
	?>
</div>