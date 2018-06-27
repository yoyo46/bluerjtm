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
            case 'Supplier':
				$labelName = 'Supplier';
                $model = 'Vendor';
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
                ), array(
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

    function _callCalcProfitLoss ( $values, $dateFrom = false, $dateTo = false, $data_action = false ) {
        if( !empty($values) ) {
            // $this->controller->loadModel('ViewCoaProfitLoss');

            $this->controller->User->Journal->virtualFields['balancing'] = 'CASE WHEN Coa.type = \'debit\' THEN SUM(Journal.debit) - SUM(Journal.credit) ELSE SUM(Journal.credit) - SUM(Journal.debit) END';
            // $this->controller->ViewCoaProfitLoss->virtualFields['code'] = 'CONCAT(ViewCoaProfitLoss.coa_id, \'-\', ViewCoaProfitLoss.date)';
            // $this->controller->ViewCoaProfitLoss->virtualFields['balancing'] = 'ViewCoaProfitLoss.summary';

            // $coaProfilLoss = $this->controller->ViewCoaProfitLoss->find('list', array(
            //     'conditions' => array(
            //         'ViewCoaProfitLoss.date' => $dateFrom,
            //         // 'ViewCoaProfitLoss.date >=' => $dateFrom,
            //         // 'ViewCoaProfitLoss.date <=' => $dateTo,
            //     ),
            //     'fields' => array(
            //         'ViewCoaProfitLoss.code',
            //         'ViewCoaProfitLoss.balancing',
            //     ),
            // ));

            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Coa', 'id');
                $level = $this->MkCommon->filterEmptyField($value, 'Coa', 'level');
                $coa_name = $this->MkCommon->filterEmptyField($value, 'Coa', 'name');
                $parent_id = $this->MkCommon->filterEmptyField($value, 'Coa', 'parent_id');
                $type = $this->MkCommon->filterEmptyField($value, 'Coa', 'type');
                $childrens = $this->MkCommon->filterEmptyField($value, 'children');

                if( !empty($childrens) ) {
                    $childrens = $this->_callCalcProfitLoss($childrens, $dateFrom, $dateTo, $data_action);
                    $value['children'] = $childrens;
                }

                if( !empty($dateFrom) && !empty($dateTo) ) {
                    $tmpDateFrom = $dateFrom;
                    $tmpDateTo = $dateTo;
                    $value = $this->controller->User->Journal->Coa->getMerge($value, $parent_id, 'Parent');

                    while( $tmpDateFrom <= $tmpDateTo ) {
                        $fieldName = sprintf('month_%s', $tmpDateFrom);
                        
                        if( $level == 4 ) {
                            // $summaryBalance = $this->controller->ViewCoaSummary->find('first', array(
                            //     'conditions' => array(
                            //         'ViewCoaSummary.date' => $tmpDateFrom,
                            //     ),
                            // ));

                            if( $data_action == 'excel' ) {
                                $summaryBalance = $this->controller->User->Journal->getData('first', array(
                                    'conditions' => array(
                                        'Journal.coa_id' => $id,
                                        'DATE_FORMAT(Journal.date, \'%Y-%m\')' => $tmpDateFrom,
                                    ),
                                    'contain' => array(
                                        'Coa',
                                    ),
                                    'group' => array(
                                        'Journal.coa_id',
                                    ),
                                ), true, array(
                                    'type' => 'active',
                                ));

                                $balancing = Common::hashEmptyField($summaryBalance, 'Journal.balancing', 0);
                            } else {
                                $balancing = 0;
                            }

                            // $balancing = Common::hashEmptyField($coaProfilLoss, $id.'-'.$tmpDateFrom, 0);
                            $value['Coa'][$tmpDateFrom]['balancing'] = $balancing;
                        
                            $amount = !empty($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']:0;
                            $values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'] = $amount + $balancing;
                        } else if( $level == 1 ) {
                            $balancing = !empty($value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing'])?$value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing']:0;
                            $amount = !empty($values['TotalCoa'][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$tmpDateFrom]['balancing']:0;

                            $values['TotalCoa'][$tmpDateFrom]['balancing'] = $amount + $balancing;
                        } else {
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

	function _callCalcBalanceSheet ( $values, $dateFrom = false, $dateTo = false ) {
		if( !empty($values) ) {
            foreach ($values as $key => $value) {
		        $id = $this->MkCommon->filterEmptyField($value, 'Coa', 'id');
		        $level = $this->MkCommon->filterEmptyField($value, 'Coa', 'level');
		        $coa_name = $this->MkCommon->filterEmptyField($value, 'Coa', 'name');
		        $parent_id = $this->MkCommon->filterEmptyField($value, 'Coa', 'parent_id');
		        $childrens = $this->MkCommon->filterEmptyField($value, 'children');

		        if( !empty($childrens) ) {
        			$childrens = $this->_callCalcBalanceSheet($childrens, $dateFrom, $dateTo);
		           	$value['children'] = $childrens;
		        }

		        if( !empty($dateFrom) && !empty($dateTo) ) {
		        	// $tmpDateFrom = $dateFrom;
		        	// $tmpDateTo = $dateTo;
		        	$value = $this->controller->User->Journal->Coa->getMerge($value, $parent_id, 'Parent');

		     //        while( $tmpDateFrom <= $tmpDateTo ) {
		     //            $fieldName = sprintf('month_%s', $tmpDateFrom);
		                
		     //    		if( $level == 4 ) {
				   //          $value['Coa'][$tmpDateFrom]['balancing'] = 0;
	            		
		     //        		$amount = !empty($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']:0;
	    		// 			$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'] = $amount + $balancing;
				   //      } else if( $level == 1 ) {
		     //    			$balancing = !empty($value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing'])?$value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing']:0;
		     //        		$amount = !empty($values['TotalCoa'][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$tmpDateFrom]['balancing']:0;

							// $values['TotalCoa'][$tmpDateFrom]['balancing'] = $amount + $balancing;
				   //      } else {
		     //    			$balancing = !empty($value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing'])?$value['children']['TotalCoa'][$id][$tmpDateFrom]['balancing']:0;
		     //        		$amount = !empty($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'])?$values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']:0;

							// $values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'] = $amount + $balancing;
				   //      }

		     //            $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
		     //        }
		        }

	            $values[$key] = $value;
            }
        }

        return $values;
	}

    function _callBeforeSaveCoaSetting ( $data, $id = false ) {
        if( !empty($data) ) {
            $dataSave = array();
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'CoaSettingDetail');

            if( !empty($dataDetail) ) {
                $values = array_filter($dataDetail);
                unset($data['CoaSettingDetail']);

                foreach ($values as $type => $coas) {
                    $coa_id = !empty($coas['coa_id'])?$coas['coa_id']:false;
                    $coa_setting_id = !empty($coas['id'])?$coas['id']:false;

                    if( !empty($coa_id) ) {
	                    $detail['CoaSettingDetail'] = array(
	                        'id' => $coa_setting_id,
	                        'user_id' => Configure::read('__Site.config_user_id'),
	                        'coa_id' => $coa_id,
	                        'label' => $type,
	                    );
                    	$dataSave[] = $detail;
	                }
                }
            }

            if( !empty($dataSave) ) {
                $data['CoaSettingDetail'] = $dataSave;
            }
        }

        return $data;
    }

    function _callBeforeRenderCoaSetting ( $data, $values ) {
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'CoaSettingDetail', 'id');
                $label = $this->MkCommon->filterEmptyField($value, 'CoaSettingDetail', 'label');
                $coa_id = $this->MkCommon->filterEmptyField($value, 'CoaSettingDetail', 'coa_id');

                $data['CoaSettingDetail'][$label]['id'] = $id;
                $data['CoaSettingDetail'][$label]['label'] = $label;
                $data['CoaSettingDetail'][$label]['coa_id'] = $coa_id;
            }
        }

        $coas = $this->controller->User->Coa->getData('list', array(
            'conditions' => array(
                'Coa.level' => 4,
                'Coa.status' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));
        $this->MkCommon->_layout_file('select');

        $this->controller->set(compact(
            'coas'
        ));

        return $data;
    }

    function _callBeforeSaveClosing ( $data ) {
        $value = $this->MkCommon->filterEmptyField($data, 'Journal');
        $closingMonth = $this->MkCommon->filterEmptyField($value, 'periode', 'month');
        $closingYear = $this->MkCommon->filterEmptyField($value, 'periode', 'year');
        $closingPeriod = sprintf('%s-%s-01', $closingYear, $closingMonth);

        $data = array(
        	'CoaClosingQueue' => array(
        		'branch_id' => Configure::read('__Site.config_branch_id'),
        		'user_id' => $this->controller->user_id,
        		'periode' => $closingPeriod,
    		),
    	);

        return $data;
    }

	function _callBeforeViewCashFlow( $params, $data_action ) {
        $coas = $this->controller->User->Journal->Coa->getData('list', array(
        	'fields' => array(
        		'Coa.id', 'Coa.coa_name',
    		),
    	), array(
        	'status' => 'cash_bank_child',
    	));
     //    $coas = $this->controller->User->Journal->Coa->_callOptGroup( false, false, false, false, array(
     //    	'status' => 'cash_bank_child',
    	// ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom', false, array(
        	'date' => 'd M Y',
    	));
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo', false, array(
        	'date' => 'd M Y',
    	));

        $period_text = __('Periode %s - %s', $dateFrom, $dateTo);
        $this->controller->set('sub_module_title', __('Laporan Cash Flow'));
        $this->controller->set('active_menu', 'cash_flows');
        $this->MkCommon->_callBeforeViewReport($data_action, array(
            'layout_file' => array(
                'select',
            ),
        ));
        $this->controller->set(compact(
            'period_text', 'coas', 'data_action',
            'dateFrom', 'dateTo'
        ));
	}

    function _callBeforeRenderGeneralLedger ( $value = false ) {
        $data = $this->controller->request->data;

        if( empty($data) && !empty($value) ) {
            $data = $value;
        }

        $transaction_date = $this->MkCommon->filterEmptyField($data, 'GeneralLedger', 'transaction_date', date('Y-m-d'));
        $data['GeneralLedger']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);

        $this->controller->request->data = $data;
        
        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas(false, false, array(
            'status' => 'non-cashbank',
        ));
        $this->MkCommon->_layout_file('select');
        $this->controller->set(compact(
        	'coas'
    	));
    }

    function _callBeforeSaveGeneralLedger ( $data, $value = false ) {
        if( !empty($data) ) {
            $this->MkCommon->_callAllowClosing($data, 'GeneralLedger', 'transaction_date');
            
            $id = $this->MkCommon->filterEmptyField($value, 'GeneralLedger', 'id');

            $dataSave = array();
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'GeneralLedger' => array(
                        'transaction_date',
                    ),
                )
            ));
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'GeneralLedgerDetail');

            $data['GeneralLedger']['id'] = $id;
            $data['GeneralLedger']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['GeneralLedger']['user_id'] = Configure::read('__Site.config_user_id');

            if( !empty($dataDetail) ) {
                $grandtotal_debit = 0;
                $grandtotal_credit = 0;
                $dataArr = array();

                foreach ($dataDetail['coa_id'] as $key => $coa_id) {
                    $debit = $this->MkCommon->filterIssetField($dataDetail, 'debit', $key);
                    $credit = $this->MkCommon->filterIssetField($dataDetail, 'credit', $key);

                    $debit = $this->MkCommon->_callPriceConverter($debit);
                    $credit = $this->MkCommon->_callPriceConverter($credit);

                    $dataArr[] = array(
                        'GeneralLedgerDetail' => array(
                            'coa_id' => $coa_id,
                            'debit' => $debit,
                            'credit' => $credit,
                        ),
                    );
                    $grandtotal_debit += $debit;
                    $grandtotal_credit += $credit;
                }

                $data['GeneralLedger']['debit_total'] = $grandtotal_debit;
                $data['GeneralLedger']['credit_total'] = $grandtotal_credit;
            }

            if( !empty($dataArr) ) {
                $data['GeneralLedgerDetail'] = $dataArr;
            }
        }

        return $data;
    }
}
?>