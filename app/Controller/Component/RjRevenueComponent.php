<?php
App::uses('Sanitize', 'Utility');
class RjRevenueComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['Ttuj']['no_ttuj']) ) {
					$refine_conditions['Ttuj']['no_ttuj'] = $refine['Ttuj']['no_ttuj'];
				}
				if( !empty($refine['Ttuj']['nottuj']) ) {
					$refine_conditions['Ttuj']['nottuj'] = $refine['Ttuj']['nottuj'];
				}
				if( !empty($refine['Ttuj']['nopol']) ) {
					$refine_conditions['Ttuj']['nopol'] = $refine['Ttuj']['nopol'];
				}
				if( !empty($refine['Ttuj']['customer']) ) {
					$refine_conditions['Ttuj']['customer'] = $refine['Ttuj']['customer'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['Ttuj']) && !empty($refine['Ttuj'])) {
			foreach($refine['Ttuj'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>