<?php 
		$info = !empty($info)?$info:false;

		if( !empty($info) ) {
?>
    <div class="form-group">
		<?php 
				echo $this->Form->input('Revenue.date_revenue',array(
					'type' => 'text',
					'label'=> __('Tgl Revenue *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tgl Revenue'),
					'readonly' => true,
					'value' => (!empty($this->request->data['Revenue']['date_revenue'])) ? $this->request->data['Revenue']['date_revenue'] : date('d/m/Y')
				));
		?>
	</div>
<?php 
		}
?>
<div class="form-group">
	<?php 
			echo $this->Form->input('Ttuj.nopol',array(
				'label'=> __('Nopol'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => true
			));
	?>
</div>
<div class="form-group">
	<?php 
			echo $this->Form->input('Ttuj.truck_capacity',array(
				'label'=> __('Kapasitas'), 
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
					echo $this->Form->hidden('Ttuj.from_city_id',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
						'id' => 'fromCityId'
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
					echo $this->Form->hidden('Ttuj.to_city_id',array(
						'label'=> false, 
						'class'=>'form-control',
						'readonly' => true,
						'id' => 'toCityId'
					));
			?>
		</div>
	</div>
</div>