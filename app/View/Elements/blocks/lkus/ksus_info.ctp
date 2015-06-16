<div class="form-group">
	<?php 
			echo $this->Form->input('Ttuj.customer_name',array(
				'label'=> __('Customer'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => true
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('Ttuj.driver_name',array(
				'label'=> __('Nama Supir'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => true
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('DriverPenganti.driver_name',array(
				'label'=> __('Nama Supir Pengganti'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => true
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->label('Ttuj.from_city_name', __('Tujuan Dari'));
	?>
	<div class="row">
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Ttuj.from_city_name',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true
					));
			?>
		</div>
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Ttuj.to_city_name',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
					));
			?>
		</div>
	</div>
</div>
<div class="form-group">
	<?php
		echo $this->Form->label('UangJalan.distance', __('Jarak'));
	?>
	<div class="input-group">
		<?php 
				echo $this->Form->input('UangJalan.distance',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'readonly' => true,
				));
		?>
		<span class="input-group-addon" id="basic-addon2">Km</span>
	</div>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('Ttuj.nopol',array(
				'label'=> __('Truk'), 
				'class'=>'form-control',
				'readonly' => true,
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('Ttuj.truck_capacity',array(
				'label'=> __('Kapasitas Truk'), 
				'class'=>'form-control',
				'readonly' => true,
			));
	?>
</div>