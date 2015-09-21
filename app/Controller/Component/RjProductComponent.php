<?php
App::uses('Sanitize', 'Utility');
class RjProductComponent extends Component {
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['ProductCategory']['name']) ) {
					$refine_conditions['ProductCategory']['name'] = $refine['ProductCategory']['name'];
				}
				if( !empty($refine['ProductCategory']['parent']) ) {
					$refine_conditions['ProductCategory']['parent'] = $refine['ProductCategory']['parent'];
				}
				if( !empty($refine['ProductBrand']['name']) ) {
					$refine_conditions['ProductBrand']['name'] = $refine['ProductBrand']['name'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['ProductCategory']) && !empty($refine['ProductCategory'])) {
			foreach($refine['ProductCategory'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['ProductBrand']) && !empty($refine['ProductBrand'])) {
			foreach($refine['ProductBrand'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>