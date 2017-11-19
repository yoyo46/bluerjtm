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
				if( !empty($refine['Laka']['type']) ) {
					$refine_conditions['Laka']['type'] = $refine['Laka']['type'];
				}
				if( !empty($refine['Laka']['date']) ) {
					$refine_conditions['Laka']['date'] = urlencode($refine['Laka']['date']);
				}
				if( !empty($refine['Ttuj']['no_ttuj']) ) {
					$refine_conditions['Ttuj']['no_ttuj'] = urlencode($refine['Ttuj']['no_ttuj']);
				}
				if( !empty($refine['Laka']['status']) ) {
					$refine_conditions['Laka']['status'] = $refine['Laka']['status'];
				}
				if( !empty($refine['Laka']['insurance']) ) {
					$refine_conditions['Laka']['insurance'] = $refine['Laka']['insurance'];
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