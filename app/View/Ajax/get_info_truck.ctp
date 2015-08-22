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

		if( !empty($result['Branch']['name']) ) {
			echo $this->Html->tag('div', $result['Branch']['name'], array(
				'id' => 'branch_name',
			));
		}

		if( !empty($result['TruckCategory']['name']) ) {
			echo $this->Html->tag('div', $result['TruckCategory']['name'], array(
				'id' => 'category_name',
			));
		}

		if( !empty($result['TruckFacility']['name']) ) {
			echo $this->Html->tag('div', $result['TruckFacility']['name'], array(
				'id' => 'facility_name',
			));
		}

		if( !empty($result['TruckCustomer']) ) {
			$content = '';

			foreach ($result['TruckCustomer'] as $key => $value) {
				$content .= $this->Html->tag('div', $value['Customer']['customer_name_code'], array(
					'class' => 'col-sm-12',
				));
			}

			echo $this->Html->tag('div', $this->Html->tag('div', $content, array(
				'class' => 'row',
			)), array(
				'id' => 'truck_customers',
			));
		} else {
			echo $this->Html->tag('div', __('Tidak memiliki alokasi'), array(
				'id' => 'truck_customers',
			));
		}
?>