<?php
App::uses('Sanitize', 'Utility');
class RjCashBankComponent extends Component {

	var $components = array(
		'MkCommon'
	); 
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['CashBank']['nodoc']) ) {
					$refine_conditions['CashBank']['nodoc'] = $refine['CashBank']['nodoc'];
				}
				if( !empty($refine['CashBank']['receiving_cash_type']) ) {
					$refine_conditions['CashBank']['cash'] = $refine['CashBank']['receiving_cash_type'];
				}
				if( !empty($refine['CashBank']['date_from']) ) {
					$refine_conditions['CashBank']['from'] = strtotime($this->MkCommon->getDate($refine['CashBank']['date_from']));
				}
				if( !empty($refine['CashBank']['date_to']) ) {
					$refine_conditions['CashBank']['to'] = strtotime($this->MkCommon->getDate($refine['CashBank']['date_to']));
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['CashBank']) && !empty($refine['CashBank'])) {
			foreach($refine['CashBank'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>