<?php 
		if( !empty($result['Truck']['capacity']) ) {
			echo $this->Html->tag('div', $result['Truck']['capacity'], array(
				'id' => 'truck_capacity',
			));
		}

		if( !empty($result['Driver']['name']) ) {
			echo $this->Html->tag('div', $result['Driver']['name'], array(
				'id' => 'driver_name',
			));
		}
?>