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
		$labelName = 'Customer';

        switch ($model) {
            case 'Vendor':
				$labelName = 'Vendor';
                $value = $this->controller->CashBank->Vendor->getData('first', array(
                    'conditions' => array(
                        'Vendor.id' => $receiver_id,
                    )
                ), true, array(
                	'branch' => false,
                ));
                break;
            case 'Employe':
				$labelName = 'Karyawan';
				$fieldName = 'full_name';
                $value = $this->controller->CashBank->Employe->getData('first', array(
                    'conditions' => array(
                        'Employe.id' => $receiver_id,
                    )
                ));

                break;
            case 'Driver':
				$labelName = 'Supir';
				$fieldName = 'driver_name';
                $value = $this->controller->CashBank->Driver->getData('first', array(
                    'conditions' => array(
                        'Driver.id' => $receiver_id,
                    )
                ), true, array(
                	'branch' => false,
                ));

                break;
            default:
				$fieldName = 'customer_code';
                $value = $this->controller->CashBank->Customer->getData('first', array(
                    'conditions' => array(
                        'Customer.id' => $receiver_id,
                    )
                ), true, array(
                	'branch' => false,
                ));

                break;
        }

		return sprintf('%s (%s)', $this->MkCommon->filterEmptyField($value, $model, $fieldName), $labelName);
	}

	function _callCalcBalanceCoa ( $values, $dateFrom = false, $dateTo = false ) {
		if( !empty($values) ) {
            foreach ($values as $key => $value) {
		        $id = $this->MkCommon->filterEmptyField($value, 'Coa', 'id');
		        $level = $this->MkCommon->filterEmptyField($value, 'Coa', 'level');
		        $coa_name = $this->MkCommon->filterEmptyField($value, 'Coa', 'name');
		        $parent_id = $this->MkCommon->filterEmptyField($value, 'Coa', 'parent_id');
		        $childrens = $this->MkCommon->filterEmptyField($value, 'children');

		        if( !empty($childrens) ) {
        			$childrens = $this->_callCalcBalanceCoa($childrens, $dateFrom, $dateTo);
		           	$value['children'] = $childrens;
		        }

		        if( !empty($dateFrom) && !empty($dateTo) ) {
		        	$tmpDateFrom = $dateFrom;
		        	$tmpDateTo = $dateTo;
		        	$value = $this->controller->User->Journal->Coa->getMerge($value, $parent_id, 'Parent');

		            while( $tmpDateFrom <= $tmpDateTo ) {
		                $fieldName = sprintf('month_%s', $tmpDateFrom);
		                
		        		if( $level == 4 ) {
				            $this->controller->User->Journal->virtualFields['balancing'] = 'SUM(Journal.credit) - SUM(Journal.debit)';
				            $summaryBalance = $this->controller->User->Journal->getData('first', array(
				                'conditions' => array(
				                    'Journal.coa_id' => $id,
				                    'DATE_FORMAT(Journal.date, \'%Y-%m\')' => $tmpDateFrom,
				                ),
				                'group' => array(
				                    'Journal.coa_id',
				                ),
				                'contain' => false,
				            ));

				            $balancing = $this->MkCommon->filterEmptyField($summaryBalance, 'Journal', 'balancing', 0);

				            $value['Coa'][$tmpDateFrom]['balancing'] = $balancing;
	            		
		            		$amount = !empty($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']:0;
	    					$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'] = $amount + $balancing;
				        } else if( $level != 1 ) {
		        			$balancing = !empty($value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing'])?$value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing']:0;
		            		$amount = !empty($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']:0;

							$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'] = $amount + $balancing;
				        }

		                $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
		            }
		        }

	            $values[$key] = $value;
            }
        }

        return $values;
	}
}
?>