<?php
App::uses('Sanitize', 'Utility');
class RjLkuComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['Lku']['no_doc']) ) {
					$refine_conditions['Lku']['nodoc'] = $refine['Lku']['no_doc'];
				}
				if( !empty($refine['LkuPayment']['no_doc']) ) {
					$refine_conditions['LkuPayment']['nodoc'] = $refine['LkuPayment']['no_doc'];
				}
				if( !empty($refine['Ksu']['no_doc']) ) {
					$refine_conditions['Ksu']['nodoc'] = $refine['Ksu']['no_doc'];
				}
				if( !empty($refine['KsuPayment']['no_doc']) ) {
					$refine_conditions['KsuPayment']['nodoc'] = $refine['KsuPayment']['no_doc'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['Lku']) && !empty($refine['Lku'])) {
			foreach($refine['Lku'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['LkuPayment']) && !empty($refine['LkuPayment'])) {
			foreach($refine['LkuPayment'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Ksu']) && !empty($refine['Ksu'])) {
			foreach($refine['Ksu'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['KsuPayment']) && !empty($refine['KsuPayment'])) {
			foreach($refine['KsuPayment'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>