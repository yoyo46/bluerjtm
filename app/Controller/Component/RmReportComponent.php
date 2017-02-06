<?php
/*
	- Performance
	- Messages
	- Transaksi
	- User Membership
*/
class RmReportComponent extends Component {
	public $components = array(
		'MkCommon',
		'PhpExcel.PhpExcel',
		'RjImage',
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function _callAddBeforeSave( $data, $report_type_id = 'performance' ) {
		$dataSave = array();

		if( !empty($data) ) {
			$dataSearches = Common::hashEmptyField($data, 'Search');
			$dataBranch = Common::hashEmptyField($data, 'GroupBranch', array());
        	$params_named = Common::hashEmptyField($this->controller->params->params, 'named', array());

			if( !empty($dataSearches) ) {
				$dataSearches = array_merge($dataSearches, $dataBranch);
			}
			if( !empty($params_named) ) {
				$dataSearches = array_merge($dataSearches, $params_named);
			}

			$title = Common::hashEmptyField($dataSearches, 'title', $report_type_id);

			$dataSave['Report'] = array(
				'user_id' => $this->controller->user_id,
				'report_type_id' => $report_type_id,
				'session_id' => String::uuid(),
			);

			$dataSearches = Common::_callUnset($dataSearches, array(
				'title',
			));

			if( !empty($dataSearches) ) {
				foreach ($dataSearches as $field => $value) {
					if( is_array($value) ) {
						$value = array_filter($value);

						// if( in_array($field, array( 'type', 'status', 'subareas' )) ) {
						// 	$result_value = array();

						// 	foreach ($value as $key => $val) {
						// 		$result_value[] = $key;
						// 	}

						// 	$value = $result_value;
						// }

						$search_value = implode(',', $value);
						
						if( !empty($value) ) {
							$value = @serialize($value);
						}
					} else {
						$search_value = $value;
					}

					if( !empty($value) ) {
						// switch ($field) {
						// 	case 'date':
						// 		$values = Common::dataConverter($values,array(
						// 			'daterange' => array(
						// 				'date',
						// 			),
						// 		), true);

						// 		$start_date = Common::hashEmptyField($values, 'date.start_date');
						// 		$end_date = Common::hashEmptyField($values, 'date.end_date');
						// 		$date = Common::getCombineDate($start_date, $end_date);

						// 		$title = str_replace(array( '[%periode_date%]' ), array( $date ), $title);
						// 		break;
						// }

						$dataSave['ReportDetail'][] = array(
							// 'title' => $titleField,
							'field' => $field,
							'value' => $value,
						);
						$dataSave['Search'][$field] = $search_value;
					}
				}
			}

			$dataSave['Report']['title'] = $title;
		}

		return $dataSave;
	}

	function _callDataDriver_reports ( $params, $limit = 30, $offset = 0 ) {
		$this->controller->loadModel('Driver');
        $params_named = Common::hashEmptyField($this->controller->params->params, 'named', array());
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));

        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');
		$options = array(
            'conditions' => array(
                'Driver.branch_id' => $allow_branch_id,
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );

		// if( !empty($last_id) ) {
		// 	$options['conditions']['Driver.id <'] = $last_id;
		// }

		$options = $this->controller->Driver->_callRefineParams($params, $options);

        if(!empty($params['named'])){
            $refine = $params['named'];
            $options = $this->MkCommon->getConditionGroupBranch( $refine, 'Driver', $options );
        }

		$this->controller->paginate	= $this->controller->Driver->getData('paginate', $options, array(
            'branch' => false,
		));
		$data = $this->controller->paginate('Driver');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Driver.id');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
                $id = Common::hashEmptyField($value, 'Driver.id');
                $is_resign = Common::hashEmptyField($value, 'Driver.is_resign');
                $status = Common::hashEmptyField($value, 'Driver.status');
                $date_resign = Common::hashEmptyField($value, 'Driver.date_resign', '-', array(
					'date' => 'd/m/Y',
				));
                $value = $this->controller->Driver->Truck->getByDriver( $value, $id );

				$value = $this->controller->Driver->getMergeList($value, array(
					'contain' => array(
						'JenisSim',
						'DriverRelation',
						'Branch',
					),
				));

                if( !empty($is_resign) ) {
                    $lblStatus = __('Resign - %s', $date_resign);
                } else if( empty($status) ) {
                    $lblStatus = __('Non-Aktif');
                } else if( !empty($status) ) {
                    $lblStatus = __('Aktif');
                } else {
                    $lblStatus = '-';
                }

				$result[$key] = array(
					__('Cabang') => array(
						'text' =>Common::hashEmptyField($value, 'Branch.code'),
						// 'width' => 12,
                		'field_model' => 'Branch.name',
					),
					__('No. ID') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.no_id'),
						// 'width' => 20,
                		'field_model' => 'Driver.no_id',
					),
					__('Truk') => array(
						'text' =>Common::hashEmptyField($value, 'Truck.nopol', '-'),
						// 'width' => 15,
                		'field_model' => 'Truck.nopol',
					),
					__('Nama Lengkap') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.driver_name'),
						// 'width' => 20,
                		'field_model' => 'Driver.driver_name',
					),
					__('No. KTP') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.identity_number'),
						// 'width' => 20,
                		'field_model' => 'Driver.identity_number',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Alamat') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.address', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.address',
					),
					__('Kota') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.city', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.city',
					),
					__('Provinsi') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.provinsi', '-'),
						// 'width' => 25,
                		'field_model' => 'Driver.provinsi',
					),
					__('No. HP') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.no_hp', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.no_hp',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('No. Telp') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.phone', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.phone',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Tempat Lahir') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.tempat_lahir', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.tempat_lahir',
					),
					__('Tgl Lahir') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.birth_date', '-', array(
							'date' => 'd/m/Y',
						)),
						// 'width' => 20,
                		'field_model' => 'Driver.birth_date',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Jenis SIM') => array(
						'text' =>Common::hashEmptyField($value, 'JenisSim.name', '-'),
						// 'width' => 15,
                		'field_model' => 'JenisSim.name',
					),
					__('No. SIM') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.no_sim', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.no_sim',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Tgl Berakhir SIM') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.expired_date_sim', '-', array(
							'date' => 'd/m/Y',
						)),
						// 'width' => 15,
                		'field_model' => 'Driver.expired_date_sim',
					),
					__('Nama Kontak Darurat') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.kontak_darurat_name', '-'),
						// 'width' => 20,
                		'field_model' => 'Driver.kontak_darurat_name',
					),
					__('No. Hp Kontak Darurat') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.kontak_darurat_no_hp', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.kontak_darurat_no_hp',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Telp Kontak Darurat') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.kontak_darurat_phone', '-'),
						// 'width' => 15,
                		'field_model' => 'Driver.kontak_darurat_phone',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Hubungan') => array(
						'text' =>Common::hashEmptyField($value, 'DriverRelation.name', '-'),
						// 'width' => 20,
                		'field_model' => 'DriverRelation.name',
					),
					__('Tgl Diterima') => array(
						'text' =>Common::hashEmptyField($value, 'Driver.join_date', '-', array(
							'date' => 'd/m/Y',
						)),
						// 'width' => 15,
                		'field_model' => 'Driver.join_date',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Status') => array(
						'text' => $lblStatus,
						// 'width' => 15,
                		'field_model' => 'Driver.status',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'Driver',
		);
	}

	function _callProcess( $modelName, $id, $value, $data ) {
		$dataSave = false;
		$file = false;
		
		$last_id = Common::hashEmptyField($data, 'last_id');
		$data = Common::hashEmptyField($data, 'data');
		$dataSave = array();
		$dataQueue = array();

		if( !empty($data) ) {
			$last_data = end($data);
			$currency_total_data = Common::hashEmptyField($value, 'Report.total_data');
			$previously_fetched_data = Common::hashEmptyField($value, 'Report.fetched_data');
			
			$paging = Common::hashEmptyField($this->controller->params->params, 'paging.'.$modelName);
			$total_current = Common::hashEmptyField($paging, 'current');
			$total_data = Common::hashEmptyField($paging, 'count');
			$limit = Common::hashEmptyField($paging, 'limit');
			$fetched_data = $previously_fetched_data + $total_current;

			$dataQueue['ReportQueue']['last_id'] = $last_id;
			$dataQueue['ReportQueue']['fetched_data'] = $total_current;
			$dataQueue['ReportQueue']['total_data'] = $fetched_data;

			$dataSave['Report']['id'] = $id;
			$dataSave['Report']['fetched_data'] = $fetched_data;
			$dataSave['Report']['on_progress'] = 0;

			if( empty($currency_total_data) ) {
				$dataSave['Report']['total_data'] = $total_data;
				$currency_total_data = $total_data;
			}
			if( $fetched_data >= $currency_total_data ) {
				$dataSave['Report']['document_status'] = 'completed';
			} else {
				$dataSave['Report']['document_status'] = 'progress';
			}

			$file = $this->_callFileCreate($value);
			$dataSave['Report']['filename'] = Common::hashEmptyField($file, 'filename');
		}

		return array(
			'dataSave' => $dataSave,
			'dataQueue' => $dataQueue,
			'file' => $file,
		);
	}

	function _callSaveDataExport ( $title, $report, $data, $value ) {
		$last_id = Common::hashEmptyField($data, 'last_id');
		$data = Common::hashEmptyField($data, 'data');
		$result = false;

		if( !empty($data) ) {
			$dataSave = Common::hashEmptyField($value, 'dataSave');
			$dataQueue = Common::hashEmptyField($value, 'dataQueue');
			$filename_path = Common::hashEmptyField($value, 'file.filename_path');

			$start_date = Common::hashEmptyField($report, 'Report.start_date');
			$end_date = Common::hashEmptyField($report, 'Report.end_date');
			$periods = Common::getCombineDate($start_date, $end_date);
			$titles = array(
				'title' => $title,
				'periods' => $periods,
			);

			$this->exportExcel($titles, $data, $filename_path);

			if( !empty($dataSave) ) {
				$dataSave['ReportQueue'][] = $dataQueue;
				$result = $this->controller->Report->saveAll($dataSave, array('deep' => true));
			}
		}

		return $result;
	}

	function _callFileCreate( $value ) {
		$prefix = Common::hashEmptyField($value, 'Report.session_id');
		$filename = Common::hashEmptyField($value, 'Report.filename');
		$path = Configure::read('__Site.report_folder');

		if( empty($filename) ) {
			$filename = sprintf('%s.xlsx', $prefix);
			$path = $this->RjImage->generatePathFolder($filename, $path);
		} else {
			$path = array(
				'filename' => $filename,
				'filename_path' => $this->RjImage->_callGetFolderUploadPath($filename, $path),
			);
		}

		return $path;
	}

	function exportExcel( $titles, $data, $path = false ) {
		if( file_exists($path) ) {
			$this->PhpExcel->loadWorksheet($path);
			$this->PhpExcel->_xls->getActiveSheet();
			$theader = false;
		} else {
			$this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);;
			$this->PhpExcel->setRow(3);
			$theader = true;
		}

		// get table report data
		$this->processReportTableData( $titles, $data, $theader );
		// $this->PhpExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->PhpExcel->_xls->getActiveSheet();
		$this->PhpExcel->save($path);
	}

	function processReportTableData( $titles, $data, $theader = true ) {
		$table = array();
		$idx = 64; // Acii A
		$dimensi = 64; // Acii A

		if( !empty($data[0]) ) {
			foreach ($data[0] as $label => $value) {
				$text = Common::hashEmptyField($value, 'text');
				// $width = Common::hashEmptyField($value, 'width');

				$dataArr = Common::_callUnset($value, array(
					'text',
					'horizontal',
				));

				$table[] = array_merge($dataArr, array(
					'label' => $label,
					// 'width' => $width,
				));

				if( $idx >= 90 ) {
					$dimensi++;
				} else {
					$idx++;
				}
			}
		}

		$cell_end = chr($idx);

		if( $dimensi > 64 ) {
			$dimensi_chr = chr($dimensi);
			$cell_end = __('A%s', $dimensi_chr);
		}

		if( !empty($theader) ) {
			$title = Common::hashEmptyField($titles, 'title');
			$periods = Common::hashEmptyField($titles, 'periods');
			$this->PhpExcel->setReportHeader($title, $periods, 'A1', 'A2', sprintf('A1:%s1', $cell_end), sprintf('A2:%s2', $cell_end));
			
			$bold = true;
		} else {
			$table = array();
			$bold = false;
		}

		// add heading with different font and bold text
		$this->PhpExcel->addTableHeader($table, array(
			'name' => 'Calibri',
			'bold' => $bold,
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'fill_color' => 'd70601',
			'text_color' => 'FFFFFF',
		), $cell_end);

		if( !empty($data) ) {
			foreach ($data as $label => $values) {
				$dataTable = array();

				if( !empty($values) ) {
					foreach ($values as $key => $value) {
						$text = Common::hashEmptyField($value, 'text', '');
						$excel = Common::hashEmptyField($value, 'excel');

						if( !empty($excel) ) {
							$dataTable[] = array(
								'text' => $text,
								'options' => $excel,
							);
						} else {
							$dataTable[] = $text;
						}
					}
				}

				if( !empty($dataTable) ) {
				    $this->PhpExcel->addTableRow($dataTable);
				}
			}
		}
	}

	function _callDetailBeforeView ( $value ) {
		$last_id = Common::hashEmptyField($value, 'Report.last_id');
		$report_type_id = Common::hashEmptyField($value, 'Report.report_type_id');

		$data = $this->_callDataSearch($value);
		$values = $this->_callAdminUserBeforeSave($data, $this->controller->limit_paging, 'view');

		$this->controller->set(array(
			'values' => $values, 
			'value' => $value, 
		));
	}

    function _callDataSearch ( $data ) {
		$details = Common::hashEmptyField($data, 'ReportDetail');
		$result['Search']['report_type_id'] = Common::hashEmptyField($data, 'Report.report_type_id');

    	if( !empty($details) ) {
			foreach ($details as $key => &$detail) {
				$value_name = false;
				$field = Common::hashEmptyField($detail, 'ReportDetail.field');
				$val = Common::hashEmptyField($detail, 'ReportDetail.value');
				
				$vals = @unserialize($val);

				if ($vals === false) {
				    $vals = $val;
				}

				if( !empty($vals) && is_array($vals) ) {
					$vals = implode(',', $vals);
				} else {
					$vals = $val;
				}
				
				$result['Search'][$field] = $vals;
			}
		}

		return $result;
    }

	function _callAdminUserBeforeSave( $data, $limit = false, $type = false ) {
		$dataReport = array();
		$render = Common::hashEmptyField($data, 'Search', 'report_type_id');

		if( empty($limit) ) {
			$limit = $this->controller->limit;
		}

		$funcName = __('_callData%s', $render);
		$dataReport = $this->$funcName($data, false, $limit, $type);

		return array(
			'data' => $dataReport,
			'type' => $render,
		);
	}
}
?>