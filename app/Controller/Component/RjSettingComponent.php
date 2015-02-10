<?php
App::uses('Sanitize', 'Utility');
class RjSettingComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['City']['name']) ) {
					$refine_conditions['City']['name'] = $refine['City']['name'];
				}
			
				if( !empty($refine['GroupClassification']['name']) ) {
					$refine_conditions['GroupClassification']['name'] = $refine['GroupClassification']['name'];
				}
			
				if( !empty($refine['Region']['name']) ) {
					$refine_conditions['Region']['name'] = $refine['Region']['name'];
				}
			
				if( !empty($refine['Customer']['name']) ) {
					$refine_conditions['Customer']['name'] = $refine['Customer']['name'];
				}
			
				if( !empty($refine['Customer']['customer_type_id']) ) {
					$refine_conditions['Customer']['customer_type_id'] = $refine['Customer']['customer_type_id'];
				}
			
				if( !empty($refine['Customer']['customer_group_id']) ) {
					$refine_conditions['Customer']['customer_group_id'] = $refine['Customer']['customer_group_id'];
				}
			
				if( !empty($refine['CustomerType']['name']) ) {
					$refine_conditions['CustomerType']['name'] = $refine['CustomerType']['name'];
				}
			
				if( !empty($refine['Vendor']['name']) ) {
					$refine_conditions['Vendor']['name'] = $refine['Vendor']['name'];
				}
			
				if( !empty($refine['Company']['name']) ) {
					$refine_conditions['Company']['name'] = $refine['Company']['name'];
				}
			
				if( !empty($refine['TipeMotor']['name']) ) {
					$refine_conditions['TipeMotor']['name'] = $refine['TipeMotor']['name'];
				}
			
				if( !empty($refine['Perlengkapan']['name']) ) {
					$refine_conditions['Perlengkapan']['name'] = $refine['Perlengkapan']['name'];
				}
			
				if( !empty($refine['Branch']['name']) ) {
					$refine_conditions['Branch']['name'] = $refine['Branch']['name'];
				}
			
				if( !empty($refine['CustomerGroup']['name']) ) {
					$refine_conditions['CustomerGroup']['name'] = $refine['CustomerGroup']['name'];
				}
			
				if( !empty($refine['JenisSim']['name']) ) {
					$refine_conditions['JenisSim']['name'] = $refine['JenisSim']['name'];
				}
			
				if( !empty($refine['JenisPerlengkapan']['name']) ) {
					$refine_conditions['JenisPerlengkapan']['name'] = $refine['JenisPerlengkapan']['name'];
				}
			
				if( !empty($refine['TarifAngkutan']['customer_name']) ) {
					$refine_conditions['TarifAngkutan']['customer_name'] = $refine['TarifAngkutan']['customer_name'];
				}
			
				if( !empty($refine['TarifAngkutan']['name']) ) {
					$refine_conditions['TarifAngkutan']['name'] = $refine['TarifAngkutan']['name'];
				}
			
				if( !empty($refine['Bank']['name']) ) {
					$refine_conditions['Bank']['name'] = $refine['Bank']['name'];
				}

				if( !empty($refine['CalendarColor']['name']) ) {
					$refine_conditions['CalendarColor']['name'] = $refine['CalendarColor']['name'];
				}

				if( !empty($refine['PartsMotor']['name']) ) {
					$refine_conditions['PartsMotor']['name'] = $refine['PartsMotor']['name'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['City']) && !empty($refine['City'])) {
			foreach($refine['City'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Region']) && !empty($refine['Region'])) {
			foreach($refine['Region'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Customer']) && !empty($refine['Customer'])) {
			foreach($refine['Customer'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['CustomerType']) && !empty($refine['CustomerType'])) {
			foreach($refine['CustomerType'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['CustomerGroup']) && !empty($refine['CustomerGroup'])) {
			foreach($refine['CustomerGroup'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Vendor']) && !empty($refine['Vendor'])) {
			foreach($refine['Vendor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Company']) && !empty($refine['Company'])) {
			foreach($refine['Company'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}if(isset($refine['TipeMotor']) && !empty($refine['TipeMotor'])) {
			foreach($refine['TipeMotor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Perlengkapan']) && !empty($refine['Perlengkapan'])) {
			foreach($refine['Perlengkapan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Branch']) && !empty($refine['Branch'])) {
			foreach($refine['Branch'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['JenisSim']) && !empty($refine['JenisSim'])) {
			foreach($refine['JenisSim'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['JenisPerlengkapan']) && !empty($refine['JenisPerlengkapan'])) {
			foreach($refine['JenisPerlengkapan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['GroupClassification']) && !empty($refine['GroupClassification'])) {
			foreach($refine['GroupClassification'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TarifAngkutan']) && !empty($refine['TarifAngkutan'])) {
			foreach($refine['TarifAngkutan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Bank']) && !empty($refine['Bank'])) {
			foreach($refine['Bank'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['CalendarColor']) && !empty($refine['CalendarColor'])) {
			foreach($refine['CalendarColor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		if(isset($refine['PartsMotor']) && !empty($refine['PartsMotor'])) {
			foreach($refine['PartsMotor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>