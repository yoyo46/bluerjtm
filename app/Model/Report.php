<?php
class Report extends AppModel {
	var $name = 'Report';

	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id'
		),
	);

	public $hasMany = array(
		'ReportQueue' => array(
			'foreignKey' => 'report_id'
		),
		'ReportDetail' => array(
			'foreignKey' => 'report_id'
		),
	);

	var $validate = array(
		'date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih periode tanggal'
			),
		),
		'start_date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih periode tanggal'
			),
		),
		'end_date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih periode tanggal'
			),
		),
	);
	
	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$status = $this->filterEmptyField($elements, 'status');
		$role = $this->filterEmptyField($elements, 'role');

		$default_options = array(
			'conditions'=> array(
				'Report.status' => 1,
			),
			'order'=> array(
				'Report.created' => 'DESC',
				'Report.id' => 'DESC',
			),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);
		
		if( !empty($status) ) {
			$default_options['conditions']['Report.document_status'] = $status;
		}

		if( !empty($role) ) {
			$default_options['conditions']['Report.report_type_id'] = $role;
		}

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
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

		if( $find == 'paginate' ) {
			if( empty($default_options['limit']) ) {
				$default_options['limit'] = Configure::read('__Site.config_admin_pagination');
			}
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge ( $data, $report_type_id ) {
		if( empty($data['Report']) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					'Report.id' => $report_type_id,
				),
			));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

	public function doSave($data, $dataReport = false ){
		$result = false;
		$default_msg = __('generate laporan');

		if ( !empty($data) ) {
			$flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));

			if( !empty($flag) ) {
				if( !empty($dataReport['data']) ) {
					if( $this->saveAll($data) ) {
						$msg = sprintf(__('Berhasil %s'), $default_msg);
						$id = $this->id;

						$title = $this->filterEmptyField($data, 'Report', 'title');
						$key = '[%id%]';
						$findIdx = strpos($title, $key);

						if( is_numeric($findIdx) ) {
							$title = str_replace($key, __('#%s', $id), $title);
							$this->id = $id;
							$this->set('title', $title);
							$this->save();
						}

						$result = array(
							// 'msg' => $msg,
							'status' => 'success',
							'id' => $id,
							'title' => $title,
							'Log' => array(
								'activity' => $msg,
								'document_id' => $id,
							),
						);
					}else{
						$msg = sprintf(__('Gagal %s'), $default_msg);
						$result = array(
							'msg' => $msg,
							'status' => 'error',
							// 'data' => $data,
						);
					}
				}else{
					$msg = __('Mohon maaf data yang Anda cari tidak ditemukan. Silahkan ganti filter pencarian Anda.');
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						// 'data' => $data,
					);
				}
			}else{
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					// 'data' => $data,
				);
			}
		}

		return $result;
	}

	function _callMergeData($data, $field){
		$priceArr = array();
		
		$value = !empty($data['Search'][$field])?trim($data['Search'][$field]):false;
		$value_min = !empty($data['Search'][$field.'_min'])?urldecode($data['Search'][$field.'_min']):false;
		$value_max = !empty($data['Search'][$field.'_max'])?urldecode($data['Search'][$field.'_max']):false;

		$value_min = !empty($value_min)?trim(str_replace(array(',', '.'), array('', ''), $value_min)):false;
		$value_max = !empty($value_max)?trim(str_replace(array(',', '.'), array('', ''), $value_max)):false;

		if( !empty($value_min) ) {
			$valueArr[] = $value_min;
		}
		if( !empty($value_max) ) {
			$valueArr[] = $value_max;
		}

		if( !empty($valueArr) ) {
			return implode('-', $valueArr);
		} else {
			return $value;
		}
	}

	function _callCheckConditionRange ( $value, $type = false ) {
		$firstString = substr($value, 0, 1);
		
		if( in_array($firstString, array( '>', '<', '>=', '<=' )) ) {
			$value = substr($value, 1);

			switch ($type) {
				case 'price':
					$value = number_format($value, 0, '.', ',');
					break;
			}
		} else {
			$value = explode('-', $value);
			$min = !empty($value[0])?$value[0]:false;
			$max = !empty($value[1])?$value[1]:false;
			$range = array();

			if( !empty($min) ) {
				switch ($type) {
					case 'price':
						$min = number_format($min, 0, '.', ',');
						break;
				}

				$range[] = $min;
			}
			if( !empty($max) ) {
				switch ($type) {
					case 'price':
						$max = number_format($max, 0, '.', ',');
						break;
				}

				$range[] = $max;
			}

			if( !empty($range) ) {
				$value = implode(' s/d ', $range);
			}
		}

		return $value;
	}

	public function _callProcessData( $data = '' ) {
		$mls_id = $this->filterEmptyField($data, 'Search', 'mls_id');
		$subareas = $this->filterEmptyField($data, 'Search', 'subareas');
		$beds = $this->filterEmptyField($data, 'Search', 'beds');
		$baths = $this->filterEmptyField($data, 'Search', 'baths');

		$lot_size = $this->_callMergeData($data, 'lot_size');
		$building_size = $this->_callMergeData($data, 'building_size');
		$price = $this->_callMergeData($data, 'price');

		if( !empty($subareas) ) {
			$subareas = $this->User->Property->PropertyAddress->Subarea->getData('list', array(
				'conditions' => array(
					'Subarea.id' => $subareas,
				),
			));

			if( !empty($subareas) ) {
				$data['Report']['subarea'] = implode(', ', $subareas);
			}
		}

		if( !empty($lot_size) ) {
			$lot_size = $this->_callCheckConditionRange($lot_size).' M2';
			$data['Specification']['L. Tanah'] = $lot_size;
		}
		if( !empty($building_size) ) {
			$building_size = $this->_callCheckConditionRange($building_size).' M2';
			$data['Specification']['L. Bangunan'] = $building_size;
		}
		if( !empty($price) ) {
			$data['Specification']['Harga Properti'] = $this->_callCheckConditionRange($price, 'price');
		}
		if( !empty($beds) ) {
			$data['Specification']['K. Tidur'] = $beds;
		}
		if( !empty($baths) ) {
			$data['Specification']['K. Mandi'] = $baths;
		}

		return $data;
	}

	public function _callRefineParams($data = '', $defaultOptions = NULL){
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
		$status = $this->filterEmptyField($data, 'named', 'status', false, array(
        	'addslashes' => true,
    	));

		if( !empty($keyword) ) {
			$defaultOptions['conditions']['Report.title LIKE'] = '%'.$keyword.'%';
		}

		if( !empty($status) ) {
			switch ($status) {
				case 'progress':
					$defaultOptions['conditions']['Report.document_status NOT'] = 'completed';
					break;
				
				default:
					$defaultOptions['conditions']['Report.document_status'] = $status;
					break;
			}
		}

		return $defaultOptions;
	}

	public function doDelete($id){
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'Report.id' => $id,
			),
		);

		$value_cnt = $this->getData('count', $options);

		if ( !empty($value_cnt) && ($value_cnt == count($id)) ) {
			$default_msg = __('menghapus laporan');
			$options = array(
				'Report.status' => 0,
			);

			$flag = $this->updateAll(array(
				'Report.status' => 0,
			), array(
				'Report.id' => $id,
			));

			if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
				);
			}

		}else{
			$result = array(
				'msg' => __('Gagal menghapus laporan. Data tidak ditemukan'),
				'status' => 'error',
			);
		}
		return $result;
	}
}
?>