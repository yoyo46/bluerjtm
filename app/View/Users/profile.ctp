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
					echo $this->Form->label('first_name', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('first_name',array(
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
					echo $this->Form->label('last_name', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('last_name',array(
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
					echo $this->Form->label('gender', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('gender',array(
						'label'=>false,
						'required' => false,
						'class' => 'form-control',
						'empty' => __('Pilih Jenis Kelamin'),
						'options' => array(
							'male' => __('Pria'),
							'female' => __('Wanita'),
						)
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Tangal Lahir *');
					echo $this->Form->label('birthdate', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('birthdate',array(
						'type' => 'text',
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control custom-date',
					)); 
			?>
			</div>
		</div>

		<div class="form-group">
			<?php 
					$title = __('Password *');
					echo $this->Form->label('old_password', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
				<?php
						echo $this->Form->input('old_password', array(
							'type' => 'password',
							'placeholder' => $title,
							'class' => 'form-control',
							'label' => false,
							'required' => false,
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