<?php
App::uses('Sanitize', 'Utility');
class RjTruckComponent extends Component {
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
				if( !empty($refine['Truck']['nopol']) ) {
					$refine_conditions['Truck']['nopol'] = $refine['Truck']['nopol'];
				}
				if( !empty($refine['Truck']['type']) ) {
					$refine_conditions['Truck']['type'] = $refine['Truck']['type'];
				}
				if( !empty($refine['Truck']['no_doc']) ) {
					$refine_conditions['Truck']['no_doc'] = $refine['Truck']['no_doc'];
				}
				if( !empty($refine['Truck']['description']) ) {
					$refine_conditions['Truck']['description'] = $refine['Truck']['description'];
				}
				if( !empty($refine['Driver']['no_id']) ) {
					$refine_conditions['Driver']['no_id'] = $refine['Driver']['no_id'];
				}
				if( !empty($refine['Driver']['name']) ) {
					$refine_conditions['Driver']['name'] = $refine['Driver']['name'];
				}
				if( !empty($refine['Driver']['no_truck']) ) {
					$refine_conditions['Driver']['no_truck'] = $refine['Driver']['no_truck'];
				}
				if( !empty($refine['TruckBrand']['name']) ) {
					$refine_conditions['TruckBrand']['name'] = $refine['TruckBrand']['name'];
				}
				if( !empty($refine['TruckCategory']['name']) ) {
					$refine_conditions['TruckCategory']['name'] = $refine['TruckCategory']['name'];
				}
				if( !empty($refine['TruckFacility']['name']) ) {
					$refine_conditions['TruckFacility']['name'] = $refine['TruckFacility']['name'];
				}
				if( !empty($refine['Truck']['nopol']) ) {
					$refine_conditions['Truck']['nopol'] = $refine['Truck']['nopol'];
				}
				if( !empty($refine['Truck']['from_date']) ) {
					$refine_conditions['Truck']['from'] = strtotime($refine['Truck']['from_date']);
				}
				if( !empty($refine['Truck']['to_date']) ) {
					$refine_conditions['Truck']['to'] = strtotime($refine['Truck']['to_date']);
				}
				if( !empty($refine['Kir']['truck_id']) || !empty($refine['Stnk']['truck_id']) || !empty($refine['Siup']['truck_id']) ) {
					if( !empty($refine['Kir']['truck_id']) ) {
						$refine_conditions['Truck']['truck_id'] = $refine['Kir']['truck_id'];
					} else if( !empty($refine['Stnk']['truck_id']) ) {
						$refine_conditions['Truck']['truck_id'] = $refine['Stnk']['truck_id'];
					} else if( !empty($refine['Siup']['truck_id']) ) {
						$refine_conditions['Truck']['truck_id'] = $refine['Siup']['truck_id'];
					}
				}
				if( !empty($refine['Truck']['month']) ) {
					$refine_conditions['Truck']['month'] = $refine['Truck']['month'];
				}
				if( !empty($refine['Truck']['year']) ) {
					$refine_conditions['Truck']['year'] = $refine['Truck']['year'];
				}
				if( !empty($refine['TruckCustomer']['customer_id']) ) {
					$refine_conditions['TruckCustomer']['alocation'] = $refine['TruckCustomer']['customer_id'];
				}
				if( !empty($refine['Truck']['status_expired']) ) {
					$refine_conditions['Truck']['license_stat'] = $refine['Truck']['status_expired'];
				}
				if( !empty($refine['Truck']['status']) ) {
					$refine_conditions['Truck']['status'] = $refine['Truck']['status'];
				}
				if( !empty($refine['Truck']['capacity']) ) {
					$refine_conditions['Truck']['capacity'] = $refine['Truck']['capacity'];
				}
				if( !empty($refine['Truck']['category']) ) {
					$refine_conditions['Truck']['category'] = $refine['Truck']['category'];
				}
				if( !empty($refine['Truck']['year']) ) {
					$refine_conditions['Truck']['year'] = $refine['Truck']['year'];
				}
				if( !empty($refine['Truck']['alokasi']) ) {
					$refine_conditions['Truck']['alokasi'] = $refine['Truck']['alokasi'];
				}
				if( !empty($refine['Truck']['customer_code']) ) {
					$refine_conditions['Truck']['code'] = $refine['Truck']['customer_code'];
				}
				if( !empty($refine['Truck']['date']) ) {
					$refine_conditions['Truck']['date'] = urlencode($refine['Truck']['date']);
				}
				if( !empty($refine['Truck']['company_id']) ) {
					$refine_conditions['Truck']['company'] = urlencode($refine['Truck']['company_id']);
				}
				if( !empty($refine['Ttuj']['no_ttuj']) ) {
					$refine_conditions['Ttuj']['no_ttuj'] = $refine['Ttuj']['no_ttuj'];
				}
				if( !empty($refine['Truck']['customer_group_id']) ) {
					$customer_group_id = $refine['Truck']['customer_group_id'];

					if( is_array($customer_group_id) ) {
						$customer_group_id = implode(',', $customer_group_id);
					}

					$refine_conditions['Truck']['customer_group_id'] = $customer_group_id;
				}
				if( !empty($refine['Truck']['insurance_status']) ) {
					$refine_conditions['Truck']['insurance_status'] = $refine['Truck']['insurance_status'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['Truck']) && !empty($refine['Truck'])) {
			foreach($refine['Truck'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Driver']) && !empty($refine['Driver'])) {
			foreach($refine['Driver'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckBrand']) && !empty($refine['TruckBrand'])) {
			foreach($refine['TruckBrand'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckCategory']) && !empty($refine['TruckCategory'])) {
			foreach($refine['TruckCategory'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckFacility']) && !empty($refine['TruckFacility'])) {
			foreach($refine['TruckFacility'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TruckCustomer']) && !empty($refine['TruckCustomer'])) {
			foreach($refine['TruckCustomer'] as $param => $value) {
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

	function _callDocumentType ( $type ) {
		switch ($type) {
            case 'stnk':
                $modelName = 'Stnk';
                break;
            case 'stnk_5_thn':
                $modelName = 'Stnk';
                break;
            
            default:
                $modelName = ucwords($type);
                break;
        }

        return $modelName;
	}

	function _callBeforeSave ( $data ) {
        $is_asset = $this->MkCommon->filterEmptyField($data, 'Truck', 'is_asset');
        $result = false;

        if( !empty($is_asset) ) {
	        $nopol = $this->MkCommon->filterEmptyField($data, 'Truck', 'nopol');
	        $asset_group_id = $this->MkCommon->filterEmptyField($data, 'Truck', 'asset_group_id');
	        $nilai_perolehan = $this->MkCommon->filterEmptyField($data, 'Truck', 'nilai_perolehan');
	        $description = $this->MkCommon->filterEmptyField($data, 'Truck', 'description');
	        $tahun_neraca = $this->MkCommon->filterEmptyField($data, 'Truck', 'tahun_neraca');
	        $ak_penyusutan = $this->MkCommon->filterEmptyField($data, 'Truck', 'ak_penyusutan');
	        $purchase_date = $this->MkCommon->filterEmptyField($data, 'Truck', 'purchase_date');

	        $result = array(
	        	'Asset' => array(
	        		'name' => $nopol,
	        		'asset_group_id' => $asset_group_id,
	        		'nilai_perolehan' => $nilai_perolehan,
	        		'ak_penyusutan' => $ak_penyusutan,
	        		'note' => $description,
	        		'neraca_date' => sprintf('%s-01-01', $tahun_neraca),
	        		'purchase_date' => $purchase_date,
        		),
        	);
		}

		return $result;
	}

	function _callTruckCustomer( $customers ) {
        $conditionsTruck = array();
        $conditionsCapacity = array(
        );
    	$truckArr = array();

		if( !empty($customers) ) {
			$params = $this->controller->params;
            $this->controller->Truck->unBindModel(array(
                'hasMany' => array(
                    'TruckCustomer'
                )
            ));

            $this->controller->Truck->bindModel(array(
                'hasOne' => array(
                    'TruckCustomer' => array(
                        'className' => 'TruckCustomer',
                        'foreignKey' => 'truck_id',
                        'conditions' => array(
                            'TruckCustomer.primary' => 1
                        )
                    )
                )
            ), false);

            $company = $this->MkCommon->filterEmptyField($params, 'named', 'company');
            $code = $this->MkCommon->filterEmptyField($params, 'named', 'code');
            $branch = $this->MkCommon->filterEmptyField($params, 'named', 'group_branch');

            if(!empty($company)){
                $company = urldecode($company);
                $conditionsTruck['Truck.company_id'] = $company;
        		$conditionsCapacity['Truck.company_id'] = $company;

                $this->controller->request->data['Truck']['company_id'] = $company;
            }
            if(!empty($code)){
                $code = urldecode($code);
                $code = trim($code);

                $companies = $this->controller->Customer->getData('list', array(
                	'conditions' => array(
                		'Customer.code LIKE' => '%'.$code.'%',
            		),
            		'fields' => array(
            			'Customer.id',
        			),
            	));
                $conditionsTruck['TruckCustomer.customer_id'] = $companies;
                $this->controller->request->data['TruckCustomer']['customer_id'] = $companies;
            }

        	$this->controller->Truck->virtualFields['cnt'] = 'COUNT(Truck.id)';
            $trucks = $this->controller->Truck->getData('all', array(
                'conditions' => $conditionsTruck,
                'contain' => array(
                	'TruckCustomer',
            	),
                'group' => array(
                    'Truck.capacity',
                    'TruckCustomer.customer_id',
                ),
                'order' => array(
                	'TruckCustomer.customer_id',
            	),
            ), true, array(
                'branch' => false,
            ));

            if( !empty($trucks) ) {
                foreach ($trucks as $key => $truck) {
                    $customer_id = $this->MkCommon->filterEmptyField($truck, 'TruckCustomer', 'customer_id', 0);
                    $capacity = $this->MkCommon->filterEmptyField($truck, 'Truck', 'capacity', 0);
                    $qty = $this->MkCommon->filterEmptyField($truck, 'Truck', 'cnt', 0);
                    $truckArr[$customer_id][$capacity] = $qty;
                }
            }

            $customers = $this->controller->Customer->getMergeList($customers, array(
            	'contain' => array(
            		'Branch',
        		),
        	));
        }

        $capacities = $this->controller->Truck->getData('list', array(
            'conditions' => $conditionsCapacity,
            'group' => array(
                'Truck.capacity',
            ),
            'fields' => array(
                'Truck.id',
                'Truck.capacity',
            ),
            'order' => array(
                'Truck.capacity*1' => 'ASC',
            ),
        ), true, array(
            'branch' => false,
        ));
        $companies = $this->controller->Truck->Company->getData('list');

        $this->controller->set(compact(
            'customers', 'truckArr', 'companies',
            'capacities'
        ));
	}

    function _callBeforeViewProfitLoss( $params ) {
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Laporan Profit Loss Per Truk');
        $period_text = false;

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set(compact(
            'period_text'
        ));
    }
}
?>