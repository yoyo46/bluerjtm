<?php
App::uses('Sanitize', 'Utility');
class RjSettingComponent extends Component {

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
				if( !empty($refine['City']['code']) ) {
					$refine_conditions['City']['code'] = $refine['City']['code'];
				}
				if( !empty($refine['City']['name']) ) {
					$refine_conditions['City']['name'] = $refine['City']['name'];
				}
				if( !empty($refine['City']['is_branch']) ) {
					$refine_conditions['City']['branch'] = $refine['City']['is_branch'];
				}
				if( !empty($refine['City']['is_plant']) ) {
					$refine_conditions['City']['plant'] = $refine['City']['is_plant'];
				}
				if( !empty($refine['City']['is_head_office']) ) {
					$refine_conditions['City']['head_office'] = $refine['City']['is_head_office'];
				}
			
				if( !empty($refine['GroupClassification']['name']) ) {
					$refine_conditions['GroupClassification']['name'] = $refine['GroupClassification']['name'];
				}
			
				if( !empty($refine['Region']['name']) ) {
					$refine_conditions['Region']['name'] = $refine['Region']['name'];
				}
			
				if( !empty($refine['Customer']['code']) ) {
					$refine_conditions['Customer']['code'] = $refine['Customer']['code'];
				}
			
				if( !empty($refine['Customer']['name']) ) {
					$refine_conditions['Customer']['name'] = $refine['Customer']['name'];
				}
			
				if( !empty($refine['Customer']['customer_type_id']) ) {
					$refine_conditions['Customer']['customer_type_id'] = $refine['Customer']['customer_type_id'];
				}
			
				if( !empty($refine['Customer']['customer_group_id']) ) {
					$refine_conditions['Customer']['customer_group_id'] = $refine['Customer']['customer_group_id'];
				}
			
				if( !empty($refine['CustomerType']['name']) ) {
					$refine_conditions['CustomerType']['name'] = $refine['CustomerType']['name'];
				}
			
				if( !empty($refine['Vendor']['name']) ) {
					$refine_conditions['Vendor']['name'] = $refine['Vendor']['name'];
				}
			
				if( !empty($refine['Company']['code']) ) {
					$refine_conditions['Company']['code'] = $refine['Company']['code'];
				}
				if( !empty($refine['Company']['name']) ) {
					$refine_conditions['Company']['name'] = $refine['Company']['name'];
				}
				if( !empty($refine['Company']['is_rjtm']) ) {
					$refine_conditions['Company']['rjtm'] = $refine['Company']['is_rjtm'];
				}
				if( !empty($refine['UangJalan']['from_city']) ) {
					$refine_conditions['UangJalan']['from'] = $refine['UangJalan']['from_city'];
				}
				if( !empty($refine['UangJalan']['to_city']) ) {
					$refine_conditions['UangJalan']['to'] = $refine['UangJalan']['to_city'];
				}
				if( !empty($refine['UangJalan']['noref']) ) {
					$refine_conditions['UangJalan']['noref'] = $refine['UangJalan']['noref'];
				}
			
				if( !empty($refine['TipeMotor']['name']) ) {
					$refine_conditions['TipeMotor']['name'] = $refine['TipeMotor']['name'];
				}
			
				if( !empty($refine['TipeMotor']['code']) ) {
					$refine_conditions['TipeMotor']['code'] = $refine['TipeMotor']['code'];
				}
			
				if( !empty($refine['Perlengkapan']['name']) ) {
					$refine_conditions['Perlengkapan']['name'] = $refine['Perlengkapan']['name'];
				}
			
				if( !empty($refine['Branch']['name']) ) {
					$refine_conditions['Branch']['name'] = $refine['Branch']['name'];
				}
			
				if( !empty($refine['CustomerGroup']['name']) ) {
					$refine_conditions['CustomerGroup']['name'] = $refine['CustomerGroup']['name'];
				}
			
				if( !empty($refine['JenisSim']['name']) ) {
					$refine_conditions['JenisSim']['name'] = $refine['JenisSim']['name'];
				}
			
				if( !empty($refine['JenisPerlengkapan']['name']) ) {
					$refine_conditions['JenisPerlengkapan']['name'] = $refine['JenisPerlengkapan']['name'];
				}
			
				if( !empty($refine['TarifAngkutan']['customer_name']) ) {
					$refine_conditions['TarifAngkutan']['customer_name'] = $refine['TarifAngkutan']['customer_name'];
				}
			
				if( !empty($refine['TarifAngkutan']['name']) ) {
					$refine_conditions['TarifAngkutan']['name'] = $refine['TarifAngkutan']['name'];
				}
			
				if( !empty($refine['TarifAngkutan']['jenis_unit']) ) {
					$refine_conditions['TarifAngkutan']['jenis_unit'] = $refine['TarifAngkutan']['jenis_unit'];
				}
			
				if( !empty($refine['Bank']['name']) ) {
					$refine_conditions['Bank']['name'] = $refine['Bank']['name'];
				}

				if( !empty($refine['CalendarColor']['name']) ) {
					$refine_conditions['CalendarColor']['name'] = $refine['CalendarColor']['name'];
				}

				if( !empty($refine['PartsMotor']['name']) ) {
					$refine_conditions['PartsMotor']['name'] = $refine['PartsMotor']['name'];
				}

				if( !empty($refine['UangKuli']['city']) ) {
					$refine_conditions['UangKuli']['city'] = $refine['UangKuli']['city'];
				}

				if( !empty($refine['UangJalan']['name']) ) {
					$refine_conditions['UangJalan']['name'] = $refine['UangJalan']['name'];
				}

				if( !empty($refine['UangJalan']['capacity']) ) {
					$refine_conditions['UangJalan']['capacity'] = $refine['UangJalan']['capacity'];
				}

				if( !empty($refine['Approval']['module']) ) {
					$refine_conditions['Approval']['module'] = $refine['Approval']['module'];
				}

				if( !empty($refine['Approval']['position']) ) {
					$refine_conditions['Approval']['position'] = $refine['Approval']['position'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['City']) && !empty($refine['City'])) {
			foreach($refine['City'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Region']) && !empty($refine['Region'])) {
			foreach($refine['Region'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Customer']) && !empty($refine['Customer'])) {
			foreach($refine['Customer'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['CustomerType']) && !empty($refine['CustomerType'])) {
			foreach($refine['CustomerType'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['CustomerGroup']) && !empty($refine['CustomerGroup'])) {
			foreach($refine['CustomerGroup'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Vendor']) && !empty($refine['Vendor'])) {
			foreach($refine['Vendor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Company']) && !empty($refine['Company'])) {
			foreach($refine['Company'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}if(isset($refine['TipeMotor']) && !empty($refine['TipeMotor'])) {
			foreach($refine['TipeMotor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Perlengkapan']) && !empty($refine['Perlengkapan'])) {
			foreach($refine['Perlengkapan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		// if(isset($refine['Branch']) && !empty($refine['Branch'])) {
		// 	foreach($refine['Branch'] as $param => $value) {
		// 		if($value) {
		// 			$parameters[trim($param)] = rawurlencode($value);
		// 		}
		// 	}
		// }
		if(isset($refine['JenisSim']) && !empty($refine['JenisSim'])) {
			foreach($refine['JenisSim'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['JenisPerlengkapan']) && !empty($refine['JenisPerlengkapan'])) {
			foreach($refine['JenisPerlengkapan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['GroupClassification']) && !empty($refine['GroupClassification'])) {
			foreach($refine['GroupClassification'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['TarifAngkutan']) && !empty($refine['TarifAngkutan'])) {
			foreach($refine['TarifAngkutan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['Bank']) && !empty($refine['Bank'])) {
			foreach($refine['Bank'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}
		if(isset($refine['CalendarColor']) && !empty($refine['CalendarColor'])) {
			foreach($refine['CalendarColor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		if(isset($refine['PartsMotor']) && !empty($refine['PartsMotor'])) {
			foreach($refine['PartsMotor'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		if(isset($refine['UangKuli']) && !empty($refine['UangKuli'])) {
			foreach($refine['UangKuli'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		if(isset($refine['UangJalan']) && !empty($refine['UangJalan'])) {
			foreach($refine['UangJalan'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		if(isset($refine['Approval']) && !empty($refine['Approval'])) {
			foreach($refine['Approval'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}
	
    public function _processRefine( $conditions = false, $params = false ) {
        if( is_array($conditions) ) {
            if(!empty($params['named'])) {
                $refine = $params['named'];

                if(!empty($refine['name'])) {
                    $value = urldecode($refine['name']);
                    $conditions['Branch.name LIKE'] = '%'.$value.'%';
                    $this->controller->request->data['Branch']['name'] = $value;
                }

                if(!empty($refine['city'])) {
                    $value = urldecode($refine['city']);
                    $conditions['City.name LIKE'] = '%'.$value.'%';
                    $this->controller->request->data['City']['name'] = $value;
                }
            }

        }

        return $conditions;
    }
    
    public function processRequest( $data = false ) {
        $params = array();

        if(!empty($data['Branch'])) {
            $value = $data['Branch'];
            $params['name'] = !empty($value['name'])?urlencode($value['name']):false;
            $params['city'] = !empty($value['city'])?urlencode($value['city']):false;
        }

        return $params;
    }

    function _callBeforeRenderCogsSetting ( $data, $values ) {
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'CogsSetting', 'id');
                $label = $this->MkCommon->filterEmptyField($value, 'CogsSetting', 'label');
                $cogs_id = $this->MkCommon->filterEmptyField($value, 'CogsSetting', 'cogs_id');

                $data['CogsSetting'][$label]['id'] = $id;
                $data['CogsSetting'][$label]['label'] = $label;
                $data['CogsSetting'][$label]['cogs_id'] = $cogs_id;
            }
        }

        $cogs = $this->controller->User->Cogs->_callOptGroup();
        $this->MkCommon->_layout_file('select');

        $this->controller->set(compact(
            'cogs'
        ));

        return $data;
    }
}
?>