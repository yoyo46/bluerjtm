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
                'Ttuj.is_sj_completed' => 0,
            );
            $this->controller->request->data['Ttuj']['is_sj_not_completed'] = $is_sj_not_completed;
        }
        if(!empty($refine['is_sj_completed'])){
            $is_sj_completed = urldecode($refine['is_sj_completed']);
            $conditions['AND']['OR'][] = array(
                'Ttuj.is_sj_completed' => 1,
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
}
?>