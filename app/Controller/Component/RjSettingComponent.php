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
			}
			if(!empty($refine)) {
				if( !empty($refine['Customer']['name']) ) {
					$refine_conditions['Customer']['name'] = $refine['Customer']['name'];
				}
			}
			if(!empty($refine)) {
				if( !empty($refine['Customer']['customer_type_id']) ) {
					$refine_conditions['Customer']['customer_type_id'] = $refine['Customer']['customer_type_id'];
				}
			}
			if(!empty($refine)) {
				if( !empty($refine['CustomerType']['name']) ) {
					$refine_conditions['CustomerType']['name'] = $refine['CustomerType']['name'];
				}
			}
			if(!empty($refine)) {
				if( !empty($refine['Vendor']['name']) ) {
					$refine_conditions['Vendor']['name'] = $refine['Vendor']['name'];
				}
			}
			if(!empty($refine)) {
				if( !empty($refine['Company']['name']) ) {
					$refine_conditions['Company']['name'] = $refine['Company']['name'];
				}
			}
			if(!empty($refine)) {
				if( !empty($refine['TipeMotor']['name']) ) {
					$refine_conditions['TipeMotor']['name'] = $refine['TipeMotor']['name'];
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

		return $parameters;
	}
}
?>