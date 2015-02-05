<?php
App::uses('Sanitize', 'Utility');
class RjUserComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['User']['name']) ) {
					$refine_conditions['User']['name'] = $refine['User']['name'];
				}
				if( !empty($refine['User']['email']) ) {
					$refine_conditions['User']['email'] = $refine['User']['email'];
				}
				if( !empty($refine['Group']['name']) ) {
					$refine_conditions['Group']['name'] = $refine['Group']['name'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(!empty($refine['User'])) {
			foreach($refine['User'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(!empty($refine['Group'])) {
			foreach($refine['Group'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>