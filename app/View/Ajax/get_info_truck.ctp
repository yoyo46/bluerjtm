<?php 
		echo $this->Html->tag('div', $result['Truck']['capacity'], array(
			'id' => 'truck_capacity',
		));

		echo $this->Html->tag('div', $result['Driver']['name'], array(
			'id' => 'driver_name',
		));
?>