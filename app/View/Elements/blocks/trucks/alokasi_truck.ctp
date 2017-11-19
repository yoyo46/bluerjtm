<?php 
		$content = '';

		foreach ($truckCustomers as $key => $value) {
			if( !empty($value['TruckMutationOldCustomer']['customer_name']) ) {
				$customer_name = $value['TruckMutationOldCustomer']['customer_name'];
			} else if( !empty($value['TruckMutationCustomer']['customer_name']) ) {
				$customer_name = $value['TruckMutationCustomer']['customer_name'];
			} else if( !empty($value['Customer']['customer_name_code']) ) {
				$customer_name = $value['Customer']['customer_name_code'];
			} else {
				$customer_name = '-';
			}
			
			$content .= $this->Html->tag('div', $customer_name, array(
				'class' => 'col-sm-12',
			));
		}

		echo $this->Html->tag('div', $this->Html->tag('div', $content, array(
			'class' => 'row',
		)), array(
			'id' => 'truck_customers',
		));
?>