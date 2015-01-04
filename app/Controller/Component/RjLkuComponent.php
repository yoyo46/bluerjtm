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

		return $parameters;
	}
}
?>