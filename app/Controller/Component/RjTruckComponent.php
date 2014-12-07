<?php
App::uses('Sanitize', 'Utility');
class RjTruckComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		}
		$refine_conditions = array();
		if(!empty($refine)) {
			if( !empty($refine['Truck']['nopol']) ) {
				$refine_conditions['Truck']['nopol'] = $refine['Truck']['nopol'];
			}
			if( !empty($refine['Driver']['name']) ) {
				$refine_conditions['Driver']['name'] = $refine['Driver']['name'];
			}
		}
			
		return $refine_conditions;
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

		return $parameters;
	}
}
?>