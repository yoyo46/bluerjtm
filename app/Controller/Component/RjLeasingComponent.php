<?php
App::uses('Sanitize', 'Utility');
class RjLeasingComponent extends Component {
	
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
				if( !empty($refine['Leasing']['no_contract']) ) {
					$refine_conditions['Leasing']['nocontract'] = $refine['Leasing']['no_contract'];
				}
				if( !empty($refine['LeasingCompany']['name']) ) {
					$refine_conditions['LeasingCompany']['name'] = $refine['LeasingCompany']['name'];
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
		if(isset($refine['Leasing']) && !empty($refine['Leasing'])) {
			foreach($refine['Leasing'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['LeasingCompany']) && !empty($refine['LeasingCompany'])) {
			foreach($refine['LeasingCompany'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>