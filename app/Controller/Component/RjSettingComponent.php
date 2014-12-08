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
				if( !empty($refine['Company']['name']) ) {
					$refine_conditions['Company']['name'] = $refine['Company']['name'];
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
		if(isset($refine['Company']) && !empty($refine['Company'])) {
			foreach($refine['Company'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>