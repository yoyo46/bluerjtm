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
				if( !empty($refine['Ttuj']['driver_name']) ) {
					$refine_conditions['Ttuj']['driver_name'] = $refine['Ttuj']['driver_name'];
				}
				if( !empty($refine['Ttuj']['date']) ) {
					$refine_conditions['Ttuj']['date'] = urlencode($refine['Ttuj']['date']);
				}
				if( !empty($refine['Ttuj']['from']['month']) ) {
					$refine_conditions['Ttuj']['fromMonth'] = urlencode($refine['Ttuj']['from']['month']);
				}
				if( !empty($refine['Ttuj']['from']['year']) ) {
					$refine_conditions['Ttuj']['fromYear'] = urlencode($refine['Ttuj']['from']['year']);
				}
				if( !empty($refine['Ttuj']['to']['month']) ) {
					$refine_conditions['Ttuj']['toMonth'] = urlencode($refine['Ttuj']['to']['month']);
				}
				if( !empty($refine['Ttuj']['to']['year']) ) {
					$refine_conditions['Ttuj']['toYear'] = urlencode($refine['Ttuj']['to']['year']);
				}
				if( !empty($refine['Revenue']['no_doc']) ) {
					$refine_conditions['Revenue']['no_doc'] = urlencode($refine['Revenue']['no_doc']);
				}
				if( !empty($refine['Invoice']['customer_id']) ) {
					$refine_conditions['Invoice']['customer'] = urlencode($refine['Invoice']['customer_id']);
				}
				if( !empty($refine['Invoice']['from_date']) ) {
					$refine_conditions['Invoice']['from'] = urlencode($refine['Invoice']['from_date']);
				}
				if( !empty($refine['Invoice']['to_date']) ) {
					$refine_conditions['Invoice']['to'] = urlencode($refine['Invoice']['to_date']);
				}
				if( !empty($refine['InvoicePayment']['date_from']) ) {
					$refine_conditions['InvoicePayment']['from'] = urlencode($refine['InvoicePayment']['date_from']);
				}
				if( !empty($refine['InvoicePayment']['date_to']) ) {
					$refine_conditions['InvoicePayment']['to'] = urlencode($refine['InvoicePayment']['date_to']);
				}
				if( !empty($refine['InvoicePayment']['nodoc']) ) {
					$refine_conditions['InvoicePayment']['nodoc'] = urlencode($refine['InvoicePayment']['nodoc']);
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
		if(isset($refine['Revenue']) && !empty($refine['Revenue'])) {
			foreach($refine['Revenue'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Invoice']) && !empty($refine['Invoice'])) {
			foreach($refine['Invoice'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['InvoicePayment']) && !empty($refine['InvoicePayment'])) {
			foreach($refine['InvoicePayment'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
}
?>