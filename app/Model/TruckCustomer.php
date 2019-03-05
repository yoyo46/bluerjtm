<?php
class TruckCustomer extends AppModel {
	var $name = 'TruckCustomer';

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'truck_id',
		),
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customer_id',
		),
		'CustomerNoType' => array(
			'className' => 'CustomerNoType',
			'foreignKey' => 'customer_id',
		),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
	);

	function getMergeTruckCustomer ( $data = false, $truck_id = false, $primary = 'all' ) {
		if( !empty($truck_id) ) {
			$data['Truck']['id'] = $truck_id;
		}

		if( !empty($data['Truck']['id']) && empty($data['TruckCustomer']) ) {
			if( (string)$primary == 'all' || empty($primary) ) {
				$truckCustomers = $this->find('all', array(
					'conditions' => array(
						'TruckCustomer.truck_id'=> $data['Truck']['id'],
					),
					'order' => array(
						'TruckCustomer.id' => 'ASC',
					),
				));

				if( !empty($truckCustomers) ) {
					foreach ($truckCustomers as $key => $truckCustomer) {
	                	$truckCustomer = $this->Customer->getMerge($truckCustomer, $truckCustomer['TruckCustomer']['customer_id']);
	                	$truckCustomers[$key] = $truckCustomer;
					}
					$data['TruckCustomer'] = $truckCustomers;
				}
			} else if( !empty($primary) ) {
				$value = $this->find('first', array(
					'conditions' => array(
						'TruckCustomer.truck_id'=> $truck_id,
						'TruckCustomer.primary'=> 1,
					),
				));

				if( !empty($value) ) {
					$data = array_merge($data, $value);
				}
			}
		}

		return $data;
	}

	/**
	* get data
	*
	* @param string $find - all, list
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	* @param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* @return hasil ditemukan return array, hasil tidak ditemukan return false
	*/
    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $branch = isset($elements['branch'])?$elements['branch']:true;

		$default_options = array(
			'conditions' => array(),
            'order' => array(
                'TruckCustomer.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
		);

		if( !empty($branch) ) {
            $default_options['conditions']['TruckCustomer.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	/**
	* getPerformanceReport
	*
	* @param int $user_id = user id
	* @param string $from_date = tanggal laporan dari tanggal
	* @param string $to_date = tanggal laporan sampai tanggal
	* @param string $period = pilihan waktu "year" dan defaultnya = 1 bulan terakhir 
	* @return $result
	*/
	function getPerformanceReport ( $user_id, $from_date = false, $to_date = false, $period = falase ) {		
		$conditionProperty = array(
			'Property.active'=> 1,
			'Property.status'=> 1,
			'Property.published'=> 1,
			'Property.deleted'=> 0,
			'Property.sold'=> 0,
			'Property.inactive'=> 0,
			'Property.user_id' => $user_id,
		);

		$Property = $this->Property->find('all', array(
			'conditions' => $conditionProperty,
			'fields' => array(
				'Property.id', 'Property.title'
			)
		));

		$result = array();
		if(!empty($Property)){
			switch ($period) {
				case 'year':
					$formatDate = '%Y-%m';
					$groupReport = array(
						'Property.id',
						'MONTH(PropertyLead.created)',
						'YEAR(PropertyLead.created)',
					);
					$orderReport = array(
						'MONTH(PropertyLead.created) ASC',
						'YEAR(PropertyLead.created) ASC',
					);
					break;
				
				default:
					$formatDate = '%Y-%m-%d';
					$groupReport = array(
						'Property.id',
						'DAY(PropertyLead.created)', 
						'MONTH(PropertyLead.created)',
						'YEAR(PropertyLead.created)',
					);
					$orderReport = array(
						'DAY(PropertyLead.created) ASC', 
						'MONTH(PropertyLead.created) ASC',
						'YEAR(PropertyLead.created) ASC',
					);
					break;
			}

			$conditionReport = array();
			foreach ($Property as $key => $value) {
				$conditionReport = array(
					'Property.id' => $value['Property']['id']
				);

				if( !empty($from_date) && !empty($to_date) ) {
					$conditionReport = array_merge($conditionReport, array(
						"DATE_FORMAT(PropertyLead.created, '".$formatDate."')  BETWEEN '".$from_date."' AND '".$to_date."'",
					));
				}
				$reportPerformance = $this->find('all', array(
					'conditions' => $conditionReport,
					'group'=> $groupReport,
					'fields'=> array(
						'DAY(PropertyLead.created) as day', 
						'MONTH(PropertyLead.created) as month', 
						'YEAR(PropertyLead.created) as year',
						'DATE_FORMAT(PropertyLead.created, \''.$formatDate.'\') dt',
						'COUNT(DATE_FORMAT(PropertyLead.created, \''.$formatDate.'\')) cnt',
					),
					'order' => $orderReport,
					'contain' => array(
						'Property'
					)
				));
				$Property[$key]['Property']['report_performance'] = $reportPerformance;
			}
			$result = $Property;
		}
		
		return $result;
	}

    function doSave( $datas, $value = false, $id = false, $truck_id, $is_validate = false ) {
        $result = false;

        if ( !empty($datas) ) {
            if( !empty($truck_id) ) {
                $this->deleteAll(array(
                    'truck_id' => $truck_id
                ));
            }
            
            foreach ($datas as $key => $customer_id) {
                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                $data['TruckCustomer'] = array(
                	'customer_id' => $customer_id,
                	'branch_id' => Configure::read('__Site.config_branch_id'),
            	);

            	if( empty($key) ) {
            		$data['TruckCustomer']['primary']= 1;
            	}

                if( !empty($truck_id) ) {
                    $data['TruckCustomer']['truck_id'] = $truck_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( $flagSave ) {
                        $result = array(
                            'msg' => __('Berhasil menyimpan alokasi customer'),
                            'status' => 'success',
                        );
                    } else {
	                    $result = array(
	                        'msg' => __('Gagal menyimpan alokasi customer'),
	                        'status' => 'error',
	                    );
	                }
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan alokasi customer'),
                        'status' => 'error',
                    );
                }
            }

            if( empty($result) ) {
                $result = array(
                    'msg' => __('Gagal menyimpan alokasi customer'),
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

	function getFirst ( $data = false, $truck_id = false ) {
		if( empty($data['TruckCustomer']) ) {
			$value = $this->find('first', array(
				'conditions' => array(
					'TruckCustomer.truck_id'=> $truck_id,
				),
				'order' => array(
					'TruckCustomer.primary' => 'DESC',
					'TruckCustomer.id' => 'ASC',
				),
			));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		if( !empty($data['TruckCustomer']) ) {
			$customer_id = !empty($data['TruckCustomer']['customer_id'])?$data['TruckCustomer']['customer_id']:false;
        	$data = $this->Customer->getMerge($data, $customer_id);
		}

		return $data;
	}
}
?>