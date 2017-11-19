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
			echo $this->element('blocks/trucks/alokasi_truck', array(
				'truckCustomers' => $result['TruckCustomer'],
			));
		} else {
			echo $this->Html->tag('div', __('Tidak memiliki alokasi'), array(
				'id' => 'truck_customers',
			));
		}
?>