<div class="form-group wrapper-driver">
	<?php 
			echo $this->Common->_callInputForm('Spk.driver_id', array(
				'label' => __('Supir'),
				'empty' => __('- Pilih Supir -'),
                'class'=>'form-control chosen-select',
				'id' => 'driverID',
			));
	?>
</div>
<?php
		if( !empty($ajax_truck_history) ) {
        	echo $this->Html->tag('div', $this->element('blocks/spk/truck_history'), array(
				'class' => 'wrapper-truck-history',
			));
        }
?>