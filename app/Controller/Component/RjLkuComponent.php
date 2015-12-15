<?php
App::uses('Sanitize', 'Utility');
class RjLkuComponent extends Component {

	var $components = array('MkCommon');
	
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
				if( !empty($refine['Lku']['from_date']) ) {
					$refine_conditions['Lku']['from'] = $this->MkCommon->getDate($refine['Lku']['from_date']);
				}
				if( !empty($refine['Lku']['to_date']) ) {
					$refine_conditions['Lku']['to'] = $this->MkCommon->getDate($refine['Lku']['to_date']);
				}
				if( !empty($refine['Lku']['date']) ) {
					$refine_conditions['Lku']['date'] = urlencode($refine['Lku']['date']);
				}
				if( !empty($refine['Lku']['no_ttuj']) ) {
					$refine_conditions['Lku']['no_ttuj'] = $refine['Lku']['no_ttuj'];
				}
				if( !empty($refine['Lku']['customer_id']) ) {
					$refine_conditions['Lku']['customer'] = $refine['Lku']['customer_id'];
				}
				if( !empty($refine['Ksu']['no_doc']) ) {
					$refine_conditions['Ksu']['nodoc'] = $refine['Ksu']['no_doc'];
				}
				if( !empty($refine['KsuPayment']['no_doc']) ) {
					$refine_conditions['KsuPayment']['nodoc'] = $refine['KsuPayment']['no_doc'];
				}
				if( !empty($refine['Ksu']['from_date']) ) {
					$refine_conditions['Ksu']['from'] = $this->MkCommon->getDate($refine['Ksu']['from_date']);
				}
				if( !empty($refine['Ksu']['to_date']) ) {
					$refine_conditions['Ksu']['to'] = $this->MkCommon->getDate($refine['Ksu']['to_date']);
				}
				if( !empty($refine['Ksu']['no_ttuj']) ) {
					$refine_conditions['Ksu']['no_ttuj'] = $refine['Ksu']['no_ttuj'];
				}
				if( !empty($refine['Ksu']['customer_id']) ) {
					$refine_conditions['Ksu']['customer'] = $refine['Ksu']['customer_id'];
				}
				if( !empty($refine['Ksu']['atpm']) ) {
					$refine_conditions['Ksu']['atpm'] = $refine['Ksu']['atpm'];
				}
				if( !empty($refine['Ksu']['closing']) ) {
					$refine_conditions['Ksu']['closing'] = $refine['Ksu']['closing'];
				}
				if( !empty($refine['Ksu']['paid']) ) {
					$refine_conditions['Ksu']['paid'] = $refine['Ksu']['paid'];
				}
				if( !empty($refine['Ksu']['half_paid']) ) {
					$refine_conditions['Ksu']['half_paid'] = $refine['Ksu']['half_paid'];
				}
				if( !empty($refine['Lku']['driver_name']) ) {
					$refine_conditions['Lku']['driver_name'] = $refine['Lku']['driver_name'];
				}
				if( !empty($refine['Lku']['nopol']) ) {
					$refine_conditions['Lku']['nopol'] = $refine['Lku']['nopol'];
				}
				if( !empty($refine['Lku']['type']) ) {
					$refine_conditions['Lku']['type'] = $refine['Lku']['type'];
				}
				if( !empty($refine['Lku']['status']) ) {
					$refine_conditions['Lku']['status'] = $refine['Lku']['status'];
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

	function getTtujConditions () {
		return array(
            'OR' => array(
                array(
                    'Ttuj.is_bongkaran' => 1,
                    'Ttuj.is_draft' => 0,
                    'Ttuj.status' => 1,
                ),
                array(
                    'Ttuj.id' => !empty($data_local['Lku']['ttuj_id']) ? $data_local['Lku']['ttuj_id'] : false
                )
            ),
        );
	}
}
?>