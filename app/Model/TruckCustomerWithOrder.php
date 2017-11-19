<?php
class TruckCustomerWithOrder extends AppModel {
	var $name = 'TruckCustomerWithOrder';
    var $useTable = 'truck_customers';

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'truck_id',
		),
	);
}
?>