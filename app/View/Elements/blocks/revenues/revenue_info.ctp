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
			echo $this->Form->hidden('Ttuj.truck_id',array(
				'label'=> false, 
				'class'=>'form-control',
				'readonly' => true,
				'id' => 'TruckId'
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
			echo $this->Form->label('Revenue.from_city_id', __('Tujuan Dari'));
	?>
	<div class="row">
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Revenue.from_city_id',array(
						'id' => 'fromCityId',
						'label'=> false, 
						'class'=>'form-control chosen-select from-city-revenue-change',
						'readonly' => true,
						'empty' => __('Dari Kota --'),
						'options' => !empty($toCities)?$toCities:false,
					));
			?>
		</div>
		<div class="col-sm-6">
			<?php 
					echo $this->Form->input('Revenue.to_city_id',array(
						'id' => 'toCityId',
						'label'=> false, 
						'class'=>'form-control chosen-select',
						'readonly' => true,
						'empty' => __('Kota Tujuan --'),
						'options' => !empty($toCities)?$toCities:false,
					));
			?>
		</div>
	</div>
</div>