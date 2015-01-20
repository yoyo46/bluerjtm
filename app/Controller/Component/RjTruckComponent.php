<?php
App::uses('Sanitize', 'Utility');
class RjTruckComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['Truck']['nopol']) ) {
					$refine_conditions['Truck']['nopol'] = $refine['Truck']['nopol'];
				}
				if( !empty($refine['Driver']['name']) ) {
					$refine_conditions['Driver']['name'] = $refine['Driver']['name'];
				}
				if( !empty($refine['TruckBrand']['name']) ) {
					$refine_conditions['TruckBrand']['name'] = $refine['TruckBrand']['name'];
				}
				if( !empty($refine['TruckCategory']['name']) ) {
					$refine_conditions['TruckCategory']['name'] = $refine['TruckCategory']['name'];
				}
				if( !empty($refine['TruckFacility']['name']) ) {
					$refine_conditions['TruckFacility']['name'] = $refine['TruckFacility']['name'];
				}
				if( !empty($refine['Truck']['nopol']) ) {
					$refine_conditions['Truck']['nopol'] = $refine['Truck']['nopol'];
				}
				if( !empty($refine['Truck']['from_date']) ) {
					$refine_conditions['Truck']['from'] = strtotime($refine['Truck']['from_date']);
				}
				if( !empty($refine['Truck']['to_date']) ) {
					$refine_conditions['Truck']['to'] = strtotime($refine['Truck']['to_date']);
				}
				if( !empty($refine['Kir']['truck_id']) || !empty($refine['Stnk']['truck_id']) || !empty($refine['Siup']['truck_id']) ) {
					if( !empty($refine['Kir']['truck_id']) ) {
						$refine_conditions['Truck']['truck_id'] = $refine['Kir']['truck_id'];
					} else if( !empty($refine['Stnk']['truck_id']) ) {
						$refine_conditions['Truck']['truck_id'] = $refine['Stnk']['truck_id'];
					} else if( !empty($refine['Siup']['truck_id']) ) {
						$refine_conditions['Truck']['truck_id'] = $refine['Siup']['truck_id'];
					}
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['Truck']) && !empty($refine['Truck'])) {
			foreach($refine['Truck'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Driver']) && !empty($refine['Driver'])) {
			foreach($refine['Driver'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckBrand']) && !empty($refine['TruckBrand'])) {
			foreach($refine['TruckBrand'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckCategory']) && !empty($refine['TruckCategory'])) {
			foreach($refine['TruckCategory'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckFacility']) && !empty($refine['TruckFacility'])) {
			foreach($refine['TruckFacility'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>