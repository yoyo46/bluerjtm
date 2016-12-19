<?php
App::uses('Sanitize', 'Utility');
class RjRevenueComponent extends Component {
	var $components = array('MkCommon'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['Ttuj']['no_ttuj']) ) {
					$refine_conditions['Ttuj']['no_ttuj'] = urlencode($refine['Ttuj']['no_ttuj']);
				}
				if( !empty($refine['Ttuj']['nottuj']) ) {
					$refine_conditions['Ttuj']['nottuj'] = urlencode($refine['Ttuj']['nottuj']);
				}
				if( !empty($refine['Ttuj']['nopol']) ) {
					$refine_conditions['Ttuj']['nopol'] = $refine['Ttuj']['nopol'];
				}
				if( !empty($refine['Ttuj']['type']) ) {
					$refine_conditions['Ttuj']['type'] = $refine['Ttuj']['type'];
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
				if( !empty($refine['Ttuj']['status']) ) {
					$refine_conditions['Ttuj']['status'] = urlencode($refine['Ttuj']['status']);
				}
				if( !empty($refine['Ttuj']['is_draft']) ) {
					$refine_conditions['Ttuj']['is_draft'] = urlencode($refine['Ttuj']['is_draft']);
				}
				if( !empty($refine['Ttuj']['is_commit']) ) {
					$refine_conditions['Ttuj']['is_commit'] = urlencode($refine['Ttuj']['is_commit']);
				}
				if( !empty($refine['Ttuj']['is_arrive']) ) {
					$refine_conditions['Ttuj']['is_arrive'] = urlencode($refine['Ttuj']['is_arrive']);
				}
				if( !empty($refine['Ttuj']['is_bongkaran']) ) {
					$refine_conditions['Ttuj']['is_bongkaran'] = urlencode($refine['Ttuj']['is_bongkaran']);
				}
				if( !empty($refine['Ttuj']['is_balik']) ) {
					$refine_conditions['Ttuj']['is_balik'] = urlencode($refine['Ttuj']['is_balik']);
				}
				if( !empty($refine['Ttuj']['is_pool']) ) {
					$refine_conditions['Ttuj']['is_pool'] = urlencode($refine['Ttuj']['is_pool']);
				}
				if( !empty($refine['Ttuj']['is_sj_not_completed']) ) {
					$refine_conditions['Ttuj']['is_sj_not_completed'] = urlencode($refine['Ttuj']['is_sj_not_completed']);
				}
				if( !empty($refine['Ttuj']['is_sj_completed']) ) {
					$refine_conditions['Ttuj']['is_sj_completed'] = urlencode($refine['Ttuj']['is_sj_completed']);
				}
				if( !empty($refine['Ttuj']['is_revenue']) ) {
					$refine_conditions['Ttuj']['is_revenue'] = urlencode($refine['Ttuj']['is_revenue']);
				}
				if( !empty($refine['Ttuj']['is_not_revenue']) ) {
					$refine_conditions['Ttuj']['is_not_revenue'] = urlencode($refine['Ttuj']['is_not_revenue']);
				}
				if( !empty($refine['Ttuj']['is_completed']) ) {
					$refine_conditions['Ttuj']['is_completed'] = urlencode($refine['Ttuj']['is_completed']);
				}
				if( !empty($refine['Ttuj']['receiver_name']) ) {
					$refine_conditions['Ttuj']['receiver_name'] = urlencode($refine['Ttuj']['receiver_name']);
				}
				if( !empty($refine['Ttuj']['city']) ) {
					$refine_conditions['Ttuj']['city'] = urlencode($refine['Ttuj']['city']);
				}
				if( !empty($refine['Revenue']['no_doc']) ) {
					$refine_conditions['Revenue']['no_doc'] = urlencode($refine['Revenue']['no_doc']);
				}
				if( !empty($refine['Revenue']['customer_id']) ) {
					$refine_conditions['Revenue']['customer'] = urlencode($refine['Revenue']['customer_id']);
				}
				if( !empty($refine['Revenue']['from_date']) ) {
					$refine_conditions['Revenue']['from'] = urlencode($refine['Revenue']['from_date']);
				}
				if( !empty($refine['Revenue']['to_date']) ) {
					$refine_conditions['Revenue']['to'] = urlencode($refine['Revenue']['to_date']);
				}
				if( !empty($refine['Revenue']['transaction_status']) ) {
					$refine_conditions['Revenue']['status'] = urlencode($refine['Revenue']['transaction_status']);
				}
				if( !empty($refine['Revenue']['date']) ) {
					$refine_conditions['Revenue']['date'] = urlencode($refine['Revenue']['date']);
				}
				if( !empty($refine['RevenueDetail']['no_reference']) ) {
					$refine_conditions['RevenueDetail']['no_ref'] = urlencode($refine['RevenueDetail']['no_reference']);
				}
				if( !empty($refine['Invoice']['customer_id']) ) {
					$refine_conditions['Invoice']['customer'] = urlencode($refine['Invoice']['customer_id']);
				}
				if( !empty($refine['Invoice']['company_id']) ) {
					$refine_conditions['Invoice']['company_id'] = urlencode($refine['Invoice']['company_id']);
				}
				if( !empty($refine['Invoice']['from_date']) ) {
					$refine_conditions['Invoice']['from'] = urlencode($refine['Invoice']['from_date']);
				}
				if( !empty($refine['Invoice']['to_date']) ) {
					$refine_conditions['Invoice']['to'] = urlencode($refine['Invoice']['to_date']);
				}
				if( !empty($refine['Invoice']['date']) ) {
					$refine_conditions['Invoice']['date'] = urlencode($refine['Invoice']['date']);
				}
				if( !empty($refine['Invoice']['transaction_status']) ) {
					$refine_conditions['Invoice']['status'] = urlencode($refine['Invoice']['transaction_status']);
				}
				if( !empty($refine['Invoice']['no_invoice']) ) {
					$refine_conditions['Invoice']['no_invoice'] = urlencode($refine['Invoice']['no_invoice']);
				}
				if( !empty($refine['Invoice']['status']) ) {
					$refine_conditions['Invoice']['status'] = urlencode($refine['Invoice']['status']);
				}
				if( !empty($refine['Invoice']['due_15']) ) {
					$refine_conditions['Invoice']['due_15'] = urlencode($refine['Invoice']['due_15']);
				}
				if( !empty($refine['Invoice']['due_30']) ) {
					$refine_conditions['Invoice']['due_30'] = urlencode($refine['Invoice']['due_30']);
				}
				if( !empty($refine['Invoice']['due_above_30']) ) {
					$refine_conditions['Invoice']['due_above_30'] = urlencode($refine['Invoice']['due_above_30']);
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
				if( !empty($refine['Ttuj']['monitoring_customer_id']) ) {
					$refine_conditions['Ttuj']['monitoring_customer_id'] = array_filter($refine['Ttuj']['monitoring_customer_id']);
					$refine_conditions['Ttuj']['monitoring_customer_id'] = implode(',', $refine_conditions['Ttuj']['monitoring_customer_id']);
				}
				if( !empty($refine['Revenue']['customer']) ) {
					$refine_conditions['Revenue']['customer'] = urlencode($refine['Revenue']['customer']);
				}
				if( !empty($refine['Truck']['company_id']) ) {
					$refine_conditions['Truck']['company'] = urlencode($refine['Truck']['company_id']);
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
		if(isset($refine['RevenueDetail']) && !empty($refine['RevenueDetail'])) {
			foreach($refine['RevenueDetail'] as $param => $value) {
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
		if(isset($refine['Truck']) && !empty($refine['Truck'])) {
			foreach($refine['Truck'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}

	function getTtujConditionBrach ( $conditions, $action_type ) {
        if( in_array($action_type, array( 'truk_tiba', 'bongkaran', 'balik' )) ) {
            $conditions['Ttuj.to_city_branch_id'] = Configure::read('__Site.config_branch_id');
        } else if( in_array($action_type, array( 'pool' )) ) {
        	$is_plant = Configure::read('__Site.config_branch_plant');

        	if( !empty($is_plant) ) {
				$this->City = ClassRegistry::init('City');
        		$plantCityId = Configure::read('__Site.Branch.Plant.id');
	            $conditions['Ttuj.from_city_id'] = $plantCityId;
	        } else {
            	$conditions['Ttuj.from_city_id'] = Configure::read('__Site.config_branch_id');
	        }
        } else {
            $conditions['Ttuj.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        return $conditions;
	}

	function _callRefineStatusTTUJ ( $refine, $conditions ) {
		if(!empty($refine['is_draft'])){
            $is_draft = urldecode($refine['is_draft']);
            $conditions['AND']['OR'][] = array(
            	'Ttuj.is_draft' => 1,
            	'Ttuj.status' => 1,
        	);
            $this->controller->request->data['Ttuj']['is_draft'] = $is_draft;
        }
        if(!empty($refine['is_commit'])){
            $is_commit = urldecode($refine['is_commit']);
            $conditions['AND']['OR'][]= array(
                'Ttuj.is_draft' => 0,
                'Ttuj.is_arrive' => 0,
                'Ttuj.is_bongkaran' => 0,
                'Ttuj.is_balik' => 0,
                'Ttuj.is_pool' => 0,
            );
            $this->controller->request->data['Ttuj']['is_commit'] = $is_commit;
        }
        if(!empty($refine['is_arrive'])){
            $is_arrive = urldecode($refine['is_arrive']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_arrive' => 1,
                'Ttuj.is_bongkaran' => 0,
                'Ttuj.is_balik' => 0,
                'Ttuj.is_pool' => 0,
            );
            $this->controller->request->data['Ttuj']['is_arrive'] = $is_arrive;
        }
        if(!empty($refine['is_bongkaran'])){
            $is_bongkaran = urldecode($refine['is_bongkaran']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_bongkaran' => 1,
                'Ttuj.is_balik' => 0,
                'Ttuj.is_pool' => 0,
            );
            $this->controller->request->data['Ttuj']['is_bongkaran'] = $is_bongkaran;
        }
        if(!empty($refine['is_balik'])){
            $is_balik = urldecode($refine['is_balik']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_balik' => 1,
                'Ttuj.is_pool' => 0,
            );
            $this->controller->request->data['Ttuj']['is_balik'] = $is_balik;
        }
        if(!empty($refine['is_pool'])){
            $is_pool = urldecode($refine['is_pool']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_pool' => 1,
            );
            $this->controller->request->data['Ttuj']['is_pool'] = $is_pool;
        }
        if(!empty($refine['is_sj_not_completed'])){
            $is_sj_not_completed = urldecode($refine['is_sj_not_completed']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.status_sj' => array( 'none', 'half' ),
            );
            $this->controller->request->data['Ttuj']['is_sj_not_completed'] = $is_sj_not_completed;
        }
        if(!empty($refine['is_sj_completed'])){
            $is_sj_completed = urldecode($refine['is_sj_completed']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.status_sj' => 'full',
            );
            $this->controller->request->data['Ttuj']['is_sj_completed'] = $is_sj_completed;
        }
        if(!empty($refine['is_revenue'])){
            $is_revenue = urldecode($refine['is_revenue']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_revenue' => 1,
            );
            $this->controller->request->data['Ttuj']['is_revenue'] = $is_revenue;
        }
        if(!empty($refine['is_not_revenue'])){
            $is_not_revenue = urldecode($refine['is_not_revenue']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_revenue' => 0,
            );
            $this->controller->request->data['Ttuj']['is_not_revenue'] = $is_not_revenue;
        }
        if(!empty($refine['is_completed'])){
            $value = urldecode($refine['is_completed']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.completed' => 1,
            );
            $this->controller->request->data['Ttuj']['is_completed'] = $value;
        }

        return $conditions;
	}

    function _callReceiverType ( $value ) {
        if( !empty($value) ) {
            $value = array_unique($value);
            $value = array_filter($value);
            $value = implode(', ', $value);
            $value = $this->MkCommon->unSlug($value);
        }

        return $value;
    }

    function _callGenerateDateTime ( $data, $field = 'tiba' ) {
    	$fieldTgl = 'tgl_'.$field;
    	$fieldJam = 'jam_'.$field;

    	$tgl = $this->MkCommon->filterEmptyField($data, 'Ttuj', $fieldTgl);
    	$jam = $this->MkCommon->filterEmptyField($data, 'Ttuj', $fieldJam);
    	$result = '';

    	if( !empty($tgl) ) {
            $tgl = $this->MkCommon->getDate($tgl);
            $data['Ttuj'][$fieldTgl] = $tgl;

            if( !empty($jam) ) {
                $jam = date('H:i', strtotime($jam));

                $data['Ttuj'][$fieldJam] = $jam;
                $result = sprintf('%s %s', $tgl, $jam);
            }

            switch ($field) {
            	case 'tiba':
                	$data['Ttuj']['is_arrive'] = 1;
            		break;
            	case 'bongkaran':
                	$data['Ttuj']['is_bongkaran'] = 1;
            		break;
            	case 'balik':
                	$data['Ttuj']['is_balik'] = 1;
            		break;
            	case 'pool':
                	$data['Ttuj']['is_pool'] = 1;
            		break;
            }
        }

        $data['Ttuj']['tgljam_'.$field] = $result;

        return $data;
    }

    function _callDataTtujLanjutan ( $data ) {
    	$data['Ttuj']['is_arrive'] = 0;
    	$data['Ttuj']['is_bongkaran'] = 0;
    	$data['Ttuj']['is_balik'] = 0;
    	$data['Ttuj']['is_pool'] = 0;

        $data = $this->_callGenerateDateTime($data, 'tiba');
        $data = $this->_callGenerateDateTime($data, 'bongkaran');
        $data = $this->_callGenerateDateTime($data, 'balik');
        $data = $this->_callGenerateDateTime($data, 'pool');

        return $data;
    }

    function _callShowTglTtuj ( $data ) {
        if( !empty($data['Ttuj']['tgljam_berangkat']) && $data['Ttuj']['tgljam_berangkat'] != '0000-00-00 00:00:00' ) {
	        $data['Ttuj']['tgl_berangkat'] = date('d/m/Y', strtotime($data['Ttuj']['tgljam_berangkat']));
	        $data['Ttuj']['jam_berangkat'] = date('H:i', strtotime($data['Ttuj']['tgljam_berangkat']));
	    }
	    if( !empty($data['Ttuj']['tgljam_tiba']) && $data['Ttuj']['tgljam_tiba'] != '0000-00-00 00:00:00' ) {
	        $data['Ttuj']['tgl_tiba'] = date('d/m/Y', strtotime($data['Ttuj']['tgljam_tiba']));
	        $data['Ttuj']['jam_tiba'] = date('H:i', strtotime($data['Ttuj']['tgljam_tiba']));
	    }
	    if( !empty($data['Ttuj']['tgljam_bongkaran']) && $data['Ttuj']['tgljam_bongkaran'] != '0000-00-00 00:00:00' ) {
	        $data['Ttuj']['tgl_bongkaran'] = date('d/m/Y', strtotime($data['Ttuj']['tgljam_bongkaran']));
	        $data['Ttuj']['jam_bongkaran'] = date('H:i', strtotime($data['Ttuj']['tgljam_bongkaran']));
	    }
	    if( !empty($data['Ttuj']['tgljam_balik']) && $data['Ttuj']['tgljam_balik'] != '0000-00-00 00:00:00' ) {
	        $data['Ttuj']['tgl_balik'] = date('d/m/Y', strtotime($data['Ttuj']['tgljam_balik']));
	        $data['Ttuj']['jam_balik'] = date('H:i', strtotime($data['Ttuj']['tgljam_balik']));
	    }
	    if( !empty($data['Ttuj']['tgljam_pool']) && $data['Ttuj']['tgljam_pool'] != '0000-00-00 00:00:00' ) {
	        $data['Ttuj']['tgl_pool'] = date('d/m/Y', strtotime($data['Ttuj']['tgljam_pool']));
	        $data['Ttuj']['jam_pool'] = date('H:i', strtotime($data['Ttuj']['tgljam_pool']));
	    }

        return $data;
    }

	function _callBeforeViewReportMonitoringSj( $params ) {
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
        $customers = $this->controller->Ttuj->Customer->getData('list', array(
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
            'conditions' => array(
                'Customer.branch_id' => $allow_branch_id,
            ),
        ), true, array(
            'branch' => false,
            'plant' => false,
        ));

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom', false, array(
        	'date' => 'd M Y',
    	));
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo', false, array(
        	'date' => 'd M Y',
    	));

        $period_text = __('Periode %s - %s', $dateFrom, $dateTo);

        $this->controller->set('sub_module_title', __('Laporan Rekap Penerimaan Surat Jalan'));
        $this->controller->set('active_menu', 'report_monitoring_sj_revenue');
        $this->controller->set(compact(
            'period_text', 'customers'
        ));
	}

	function _callBeforeViewInvoicePayment( $data, $value = false ) {
        if( !empty($value) && empty($data) ){
            $data = $value;
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'InvoicePayment' => array(
                        'date_payment',
                    ),
                ),
            ), true);
        }

		$id = $this->MkCommon->filterEmptyField($value, 'InvoicePayment', 'id');
		$customer_id = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'customer_id');
        $values = $this->MkCommon->filterEmptyField($data, 'InvoicePaymentDetail');
		$head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        if( !empty($values) ) {
            $this->controller->InvoicePayment->InvoicePaymentDetail->virtualFields['invoice_has_paid'] = 'SUM(InvoicePaymentDetail.price_pay)';

        	foreach ($values as $key => $value) {
        		$invoice_id = $this->MkCommon->filterEmptyField($value, 'InvoicePaymentDetail', 'invoice_id');

	            $options = array(
	                'conditions' => array(
	                    'Invoice.id' => $invoice_id,
	                    'Invoice.customer_id' => $customer_id,
	                ),
	            );

	            if( empty($id) ) {
	                $options['conditions']['Invoice.complete_paid'] = 0;
	            }

	            $invoice = $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->getdata('first', $options, true, $elementRevenue);

	            if(!empty($invoice)){
                    $invoice_has_paid = $this->controller->InvoicePayment->InvoicePaymentDetail->getData('first', array(
                        'conditions' => array(
                            'InvoicePaymentDetail.invoice_id' => $invoice_id,
                            'InvoicePaymentDetail.invoice_payment_id <>' => $id,
							'InvoicePayment.status' => 1,
							'InvoicePayment.is_canceled' => 0,
						),
						'contain' => array(
							'InvoicePayment',
                        ),
                    ));

                     $value['Invoice'] = $this->MkCommon->filterEmptyField($invoice, 'Invoice');
                     $value['InvoicePaymentDetail']['invoice_has_paid'] = $this->MkCommon->filterEmptyField($invoice_has_paid, 'InvoicePaymentDetail', 'invoice_has_paid');
	            }

        		$values[$key] = $value;
        	}

        	$data['InvoicePaymentDetail'] = $values;
        }

        $this->controller->request->data = $data;
        $customers = $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->getData('list', array(
            'conditions' => array(
                'Invoice.complete_paid' => 0
            ),
            'contain' => array(
                'Customer'
            ),
            'group' => array(
                'Invoice.customer_id'
            ),
            'fields' => array(
                'Invoice.id', 'Customer.id'
            )
        ), true, $elementRevenue);
        $list_customer = $this->controller->Ttuj->Customer->getData('list', array(
            'conditions' => array(
                'Customer.id' => $customers,
            ),
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            ),
        ));
        $coas = $this->controller->InvoicePayment->Coa->getData('list', array(
            'conditions' => array(
                'Coa.status' => 1,
                'Coa.is_cash_bank' => 1
            ),
            'fields' => array(
                'Coa.id', 'Coa.coa_name'
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->controller->set(array(
            'id' => $id,
            'list_customer' => $list_customer,
            'coas' => $coas,
            'data_local' => $value,
        ));
	}

	function _callBeforeSaveInvoicePayment ( $data, $value = false ) {
        $data['InvoicePayment']['branch_id'] = Configure::read('__Site.config_branch_id');
        $data = $this->MkCommon->dataConverter($data, array(
            'price' => array(
                'InvoicePayment' => array(
                    'ppn_total',
                    'pph_total',
                ),
            ),
            'date' => array(
                'InvoicePayment' => array(
                    'date_payment',
                ),
            ),
        ));
        $this->MkCommon->_callAllowClosing($data, 'InvoicePayment', 'date_payment');

        $id = $this->MkCommon->filterEmptyField($value, 'InvoicePayment', 'id');

        if( !empty($id) ){
            $data['InvoicePayment']['id'] = $id;
        }

        $total = 0;
        $totalPpn = 0;
        $totalPpnNominal = 0;
        $totalPph = 0;
        $totalPphNominal = 0;
    	$dataDetail = array();
    	$idx = 0;

        if( !empty($data['InvoicePaymentDetail']['invoice_id']) ){
            foreach ($data['InvoicePaymentDetail']['invoice_id'] as $key => $_invoice_id) {
                $price_pay = !empty($data['InvoicePaymentDetail']['price_pay'][$key])?$data['InvoicePaymentDetail']['price_pay'][$key]:false;
                $price = $this->MkCommon->convertPriceToString($price_pay);

                $ppn = !empty($data['InvoicePaymentDetail']['ppn'][$key])?$data['InvoicePaymentDetail']['ppn'][$key]:false;
                $ppn_total = !empty($data['InvoicePaymentDetail']['ppn_total'][$key])?$this->MkCommon->_callPriceConverter($data['InvoicePaymentDetail']['ppn_total'][$key]):false;
                $pph = !empty($data['InvoicePaymentDetail']['pph'][$key])?$data['InvoicePaymentDetail']['pph'][$key]:false;
                $pph_total = !empty($data['InvoicePaymentDetail']['pph_total'][$key])?$this->MkCommon->_callPriceConverter($data['InvoicePaymentDetail']['pph_total'][$key]):false;

                $dataDetail[$_invoice_id] = array(
                	'InvoicePaymentDetail' => array(
                		'invoice_id' => $_invoice_id,
                		'price_pay' => $price,
                		'ppn' => $ppn,
                		'ppn_nominal' => $ppn_total,
                		'pph' => $pph,
                		'pph_nominal' => $pph_total,
            		),
            	);

                if( empty($price) ){
                    $dataDetail[$_invoice_id]['InvoicePaymentDetail']['error_price'] = true;
                }else{
                    $invoice_has_paid = $this->controller->InvoicePayment->InvoicePaymentDetail->getData('first', array(
                        'conditions' => array(
                            'InvoicePaymentDetail.invoice_id' => $_invoice_id,
                            'InvoicePayment.id <>' => $id,
                            'InvoicePayment.status' => 1,
                            'InvoicePayment.is_canceled' => 0,
                        ),
                        'fields' => array(
                            'SUM(InvoicePaymentDetail.price_pay) as invoice_has_paid'
                        ),
                        'contain' => array(
                            'InvoicePayment'
                        ),
                    ));
        			$head_office = Configure::read('__Site.config_branch_head_office');
			        $elementRevenue = false;

			        if( !empty($head_office) ) {
			            $elementRevenue = array(
			                'branch' => false,
			            );
			        }

                    $invoice_has_paid = (!empty($invoice_has_paid[0]['invoice_has_paid'])) ? $invoice_has_paid[0]['invoice_has_paid'] : 0;
                    $total_paid = $invoice_has_paid + $price;

                    $invoice_data = $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->getData('first', array(
                        'conditions' => array(
                            'Invoice.id' => $_invoice_id,
                        ),
                    ), true, $elementRevenue);
                    
                    if(!empty($invoice_data)){
                        if($total_paid > $invoice_data['Invoice']['total']){
                    		$dataDetail[$_invoice_id]['InvoicePaymentDetail']['over_payment'] = true;
                        }
                    }
                }

                $total += $price;
                $totalPpn += $ppn;
                $totalPpnNominal += $ppn_total;
                $totalPph += $pph;
                $totalPphNominal += $pph_total;
            }
        }
        
        $data['InvoicePayment']['total_payment'] = $total;
        $data['InvoicePayment']['ppn'] = $totalPpn;
        $data['InvoicePayment']['ppn_total'] = $totalPpnNominal;
        $data['InvoicePayment']['pph'] = $totalPph;
        $data['InvoicePayment']['pph_total'] = $totalPphNominal;
        $data['InvoicePayment']['grand_total_payment'] = $total + $totalPpnNominal;
        $data['InvoicePaymentDetail'] = $dataDetail;
        
        return $data;
	}

	function _callAfterSaveInvoicePayment ( $data, $value = false ) {
        $id = $this->MkCommon->filterEmptyField($value, 'InvoicePayment', 'id');

        $invoice_payment_id = $this->controller->InvoicePayment->id;
        $coa_id = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'coa_id');
        $date_payment = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'date_payment');
        $nodoc = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'nodoc');
        $transaction_status = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'transaction_status');
        $grandTotal = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'grand_total_payment');
        $pph_total = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'pph_total');

        $customer_id = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'customer_id');
        $customer = $this->controller->Ttuj->Customer->getMerge(array(), $customer_id, false);
        $customer_name_code = $this->MkCommon->filterEmptyField($customer, 'Customer', 'customer_name_code');
        $customer_code = $this->MkCommon->filterEmptyField($customer, 'Customer', 'code');

        if( $transaction_status == 'posting' ) {
            $this->controller->User->Journal->deleteJournal($invoice_payment_id, array(
                'invoice_payment',
            ));

            if( !empty($grandTotal) ) {
                $titleJournalInv = sprintf(__('Pembayaran invoice oleh customer %s'), $customer_name_code);
                $titleJournalInv = $this->MkCommon->filterEmptyField($data, 'InvoicePayment', 'description', $titleJournalInv);

                $this->controller->User->Journal->setJournal($grandTotal, array(
                    'credit' => 'pembayaran_invoice_coa_id',
                    'debit' => $coa_id,
                ), array(
                    'date' => $date_payment,
                    'document_id' => $invoice_payment_id,
                    'title' => $titleJournalInv,
                    'document_no' => $nodoc,
                    'type' => 'invoice_payment',
                ));
            }
        }

        $details = $this->MkCommon->filterEmptyField($data, 'InvoicePaymentDetail');

        if( $transaction_status == 'posting' ) {
	        if( !empty($details) ) {
            	$this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->InvoicePaymentDetail->virtualFields['invoice_has_paid'] = 'SUM(InvoicePaymentDetail.price_pay)';

	            foreach ($details as $key => $value) {
        			$invoice_id = $this->MkCommon->filterEmptyField($value, 'InvoicePaymentDetail', 'invoice_id');
	                $invoice_has_paid = $this->controller->InvoicePayment->InvoicePaymentDetail->getData('first', array(
	                    'conditions' => array(
		                    'InvoicePayment.transaction_status' => 'posting',
		                    'InvoicePaymentDetail.invoice_id' => $invoice_id,
		                    'InvoicePaymentDetail.status' => 1,
							'InvoicePayment.status' => 1,
							'InvoicePayment.is_canceled' => 0,
		                ),
	                    'contain' => array(
	                        'InvoicePayment',
	                        'Invoice'
	                    )
	                ));
	                $invoice_paid = $this->MkCommon->filterEmptyField($invoice_has_paid, 'InvoicePaymentDetail', 'invoice_has_paid', 0);
	                $invoice_total = $this->MkCommon->filterEmptyField($invoice_has_paid, 'Invoice', 'total', 0);
	                
	                if($invoice_paid >= $invoice_total){
	                    $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->id = $invoice_id;
	                    $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->set(array(
	                        'paid' => 1,
	                        'complete_paid' => 1
	                    ));
	                    $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->save();
	                }else{
	                    $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->id = $invoice_id;
	                    $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->set(array(
	                        'paid' => 1,
	                        'complete_paid' => 0
	                    ));
	                    $this->controller->InvoicePayment->InvoicePaymentDetail->Invoice->save();
	                }
	            }
	        }

            if( !empty($pph_total) ) {
                $coaSetting = $this->controller->User->CoaSetting->getData('first', array(
                    'conditions' => array(
                        'CoaSetting.status' => 1
                    ),
                ));
                $pph_debit_id = $this->MkCommon->filterEmptyField($coaSetting, 'CoaSetting', 'pph_coa_debit_id');

                if( !empty($pph_debit_id) ) {
                    $pph_note = __('Potongan Pph kwitansi No: %s / %s', $nodoc, $customer_code);
                    $dataPph['CashBank'] = array(
                        'branch_id' => Configure::read('__Site.config_branch_id'),
                        'user_id' => $this->controller->user_id,
                        'coa_id' => $pph_debit_id,
                        'receiving_cash_type' => 'out',
                        'receiver_type' => 'Customer',
                        'receiver_id' => $customer_id,
                        'tgl_cash_bank' => $date_payment,
                        'description' => $pph_note,
                        'debit_total' => $pph_total,
                        'transaction_status' => 'posting',
                    );

                    $allowApprovals = $this->controller->User->Employe->EmployePosition->Approval->_callNeedApproval('cash-bank', $pph_total);

                    if( empty($allowApprovals) ) {
                        $dataPph['CashBank']['completed'] = 1;
                    }

                    $this->controller->User->CashBank->create();
                    $this->controller->User->CashBank->set($dataPph);

                    if( $this->controller->User->CashBank->save() ) {
                        $cash_bank_id = $this->controller->User->CashBank->id;
                        $noref = str_pad($cash_bank_id, 6, '0', STR_PAD_LEFT);

                        if( empty($allowApprovals) ) {
                            $this->controller->User->Journal->setJournal($pph_total, array(
                                'credit' => $coa_id,
                                'debit' => 'pph_coa_debit_id',
                            ), array(
                                'date' => $date_payment,
                                'document_id' => $cash_bank_id,
                                'title' => $pph_note,
                                'document_no' => $noref,
                                'type' => 'out',
                            ));
                        }

                        $dataPphDetail['CashBankDetail'] = array(
                            'cash_bank_id' => $cash_bank_id,
                            'coa_id' => $coa_id,
                            'total' => $pph_total,
                        );

                        $this->controller->User->CashBank->CashBankDetail->create();
                        $this->controller->User->CashBank->CashBankDetail->set($dataPphDetail);
                    
                        $this->controller->User->CashBank->CashBankDetail->save();
                    }
                }
            }
	    }

        $this->controller->params['old_data'] = $value;
        $this->controller->params['data'] = $data;

        $noref = str_pad($invoice_payment_id, 6, '0', STR_PAD_LEFT);
        $this->MkCommon->setCustomFlash(__('Berhasil menyimpan Pembayaran Invoice #%s', $noref), 'success'); 
        $this->controller->Log->logActivity( __('Berhasil menyimpan Pembayaran Invoice #%s', $invoice_payment_id), $this->controller->user_data, $this->controller->RequestHandler, $this->controller->params, 0, false, $invoice_payment_id );
        
        $this->controller->redirect(array(
            'action' => 'invoice_payments'
        ));
	}
}
?>