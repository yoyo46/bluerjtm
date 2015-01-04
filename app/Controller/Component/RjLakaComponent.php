<?php
App::uses('Sanitize', 'Utility');
class RjLakaComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['Laka']['nopol']) ) {
					$refine_conditions['Laka']['nopol'] = $refine['Laka']['nopol'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['Laka']) && !empty($refine['Laka'])) {
			foreach($refine['Laka'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>