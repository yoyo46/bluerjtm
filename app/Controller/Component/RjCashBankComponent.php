<?php
App::uses('Sanitize', 'Utility');
class RjCashBankComponent extends Component {

	var $components = array(
		'MkCommon'
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['CashBank']['nodoc']) ) {
					$refine_conditions['CashBank']['nodoc'] = $refine['CashBank']['nodoc'];
				}
				if( !empty($refine['CashBank']['date']) ) {
					$refine_conditions['CashBank']['date'] = urlencode($refine['CashBank']['date']);
				}
				if( !empty($refine['CashBank']['receiver']) ) {
					$refine_conditions['CashBank']['receiver'] = $refine['CashBank']['receiver'];
				}
				if( !empty($refine['CashBank']['document_type']) ) {
					$refine_conditions['CashBank']['document_type'] = $refine['CashBank']['document_type'];
				}
				if( !empty($refine['CashBank']['total']) ) {
					$refine_conditions['CashBank']['total'] = $refine['CashBank']['total'];
				}
				if( !empty($refine['CashBank']['note']) ) {
					$refine_conditions['CashBank']['note'] = $refine['CashBank']['note'];
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
				if( !empty($refine['Journal']['date']) ) {
					$refine_conditions['Journal']['date'] = urlencode($refine['Journal']['date']);
				}
				if( !empty($refine['Journal']['document_no']) ) {
					$refine_conditions['Journal']['document_no'] = urlencode($refine['Journal']['document_no']);
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
		if(isset($refine['Journal']) && !empty($refine['Journal'])) {
			foreach($refine['Journal'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}

	function _callReceiverName ( $receiver_id, $model ) {
		$fieldName = 'name';

        switch ($model) {
            case 'Vendor':
                $value = $this->controller->CashBank->Vendor->getData('first', array(
                    'conditions' => array(
                        'Vendor.id' => $receiver_id,
                    )
                ));
                break;
            case 'Employe':
				$fieldName = 'full_name';
                $value = $this->controller->CashBank->Employe->getData('first', array(
                    'conditions' => array(
                        'Employe.id' => $receiver_id,
                    )
                ));

                break;
            default:
				$fieldName = 'customer_code';
                $value = $this->controller->CashBank->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $receiver_id,
                    )
                ));

                break;
        }

		return $this->MkCommon->filterEmptyField($value, $model, $fieldName);
	}
}
?>