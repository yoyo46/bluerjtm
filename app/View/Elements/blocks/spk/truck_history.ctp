<?php
		if( !empty($current_truck_id) && !empty($current_truck) ) {
        	$current_nopol = Common::hashEmptyField($current_truck, 'Truck.nopol');
        	
			echo $this->Html->link(__(' ( History Perbaikan )'), array(
                'controller' => 'spk',
                'action' => 'history',
                $current_truck_id,
            ), array(
                'class' => 'ajaxCustomModal',
                'title' => __('History Perbaikan - %s', $current_nopol),
            ));
		}
?>