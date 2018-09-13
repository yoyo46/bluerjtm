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
		'RjImage', 'RjProduct',
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

						switch ($field) {
							case 'from':
								if( count($value) == 1 ) {
									$field = 'year';
									$value = $search_value = implode(',', $value);
								} else {
									$from_month = Common::hashEmptyField($value, 'month');
									$from_year = Common::hashEmptyField($value, 'year');

									if( !empty($from_month) && !empty($from_year) ) {
										$field = 'dateFrom';
										$value = $search_value = __('%s-%s', $from_year, $from_month);
									}
								}
								break;
							case 'to':
								$to_year = Common::hashEmptyField($value, 'year');
								$to_month = Common::hashEmptyField($value, 'month');

								if( !empty($to_year) && !empty($to_month) ) {
									$field = 'dateTo';
									$value = $search_value = __('%s-%s', $to_year, $to_month);
								}
								break;
							default:
								$search_value = implode(',', $value);
								
								if( !empty($value) ) {
									$value = @serialize($value);
								}
								break;
						}
					} else {
						$search_value = $value;
					}

					if( !empty($value) ) {
						switch ($field) {
							case 'date':
								$tmp = Common::_callDateRangeConverter($value);

								$start_date = Common::hashEmptyField($tmp, 'start_date');
								$end_date = Common::hashEmptyField($tmp, 'end_date');
								$date = Common::getCombineDate($start_date, $end_date);
								$dataSave['Report']['start_date'] = $start_date;
								$dataSave['Report']['end_date'] = $end_date;

								$title = str_replace(array( '[%periode_date%]' ), array( $date ), $title);
								break;
						}

						$dataSave['ReportDetail'][] = array(
							// 'title' => $titleField,
							'field' => $field,
							'value' => $value,
						);
						$dataSave['Search'][$field] = !empty($search_value)?$search_value:false;
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
		$params = $this->MkCommon->_callRefineParams($params);

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

	function _callDataCurrent_stock_reports ( $params, $limit = 30, $offset = 0 ) {
		$this->controller->loadModel('Product');

        $params_named = Common::hashEmptyField($params, 'named', array());
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
            'contain' => array(
                'ProductStock',
            ),
            'group' => array(
                'Product.id',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );

        $this->controller->Product->unBindModel(array(
            'hasMany' => array(
                'ProductStock'
            )
        ));
        $this->controller->Product->bindModel(array(
            'hasOne' => array(
                'ProductStock' => array(
                    'className' => 'ProductStock',
                    'foreignKey' => 'product_id',
                ),
            )
        ), false);
        $this->controller->Product->ProductStock->virtualFields['total_qty'] = 'SUM(ProductStock.qty - ProductStock.qty_use)';
        $this->controller->Product->ProductStock->virtualFields['total_balance'] = 'SUM(ProductStock.price*(ProductStock.qty - ProductStock.qty_use))';

		$options = $this->controller->Product->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductStock', $options );

		$this->controller->paginate	= $this->controller->Product->getData('paginate', $options, array(
            'branch' => false,
		));
		$data = $this->controller->paginate('Product');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Product.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'Product.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
		        $value = $this->controller->Product->getMergeList($value, array(
		            'contain' => array(
		                'ProductUnit',
		            ),
		        ));

                $id = Common::hashEmptyField($value, 'Product.id');
                $code = Common::hashEmptyField($value, 'Product.code');
                $name = Common::hashEmptyField($value, 'Product.name');
                $unit = Common::hashEmptyField($value, 'ProductUnit.name');
                $qty = Common::hashEmptyField($value, 'ProductStock.total_qty', 0);
                $total = Common::hashEmptyField($value, 'ProductStock.total_balance', 0);

                if( !empty($qty) ) {
                    $price = $total / $qty;
                } else {
                    $price = 0;
                }

                $totalQty += $qty;
                $totalPrice += $price;
                $grandtotal += $total;

				$result[$key] = array(
					__('Kode Barang') => array(
						'text' => $code,
                		'field_model' => 'Product.code',
					),
					__('Nama Barang') => array(
						'text' => $name,
                		'field_model' => 'Product.name',
					),
					__('Satuan') => array(
						'text' => $unit,
                		'field_model' => 'ProductUnit.name',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('QTY') => array(
						'text' => $qty,
                		'field_model' => 'ProductStock.total_qty',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga Satuan') => array(
						'text' => $this->MkCommon->getFormatPrice($price, 0, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total Harga') => array(
						'text' => $this->MkCommon->getFormatPrice($total, 0, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			}

			if( empty($nextPage) ) {
				$result[$key+1] = array(
					__('Kode Barang') => array(
                		'field_model' => 'Product.code',
					),
					__('Nama Barang') => array(
                		'field_model' => 'Product.name',
					),
					__('Satuan') => array(
						'text' => __('Total'),
                		'field_model' => 'ProductUnit.name',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('QTY') => array(
						'text' => $totalQty,
                		'field_model' => 'ProductStock.total_qty',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga Satuan') => array(
						'text' => $this->MkCommon->getFormatPrice($totalPrice, 0, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total Harga') => array(
						'text' => $this->MkCommon->getFormatPrice($grandtotal, 0, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'Product',
		);
	}

	function _callDataStock_cards ( $params, $limit = 30, $offset = 0 ) {
		$this->controller->loadModel('ProductHistory');

        $params_named = Common::hashEmptyField($params, 'named', array());
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
            'contain' => array(
                'Product',
            ),
            'order'=> array(
                'ProductHistory.product_id' => 'ASC',
                'ProductHistory.branch_id' => 'ASC',
                'ProductHistory.transaction_date' => 'ASC',
                'ProductHistory.id' => 'ASC',
            ),
            'group'=> array(
                'ProductHistory.product_id',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );

        $options = $this->controller->ProductHistory->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductHistory', $options );

        $this->controller->paginate = $this->controller->ProductHistory->getData('paginate', $options, array(
            'branch' => false,
        ));
		$data = $this->controller->paginate('ProductHistory');
		$result = array();
		$values = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductHistory.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'ProductHistory.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
			$tmpResult = array();

			foreach ($data as $key => $val) {
                $product_id = Common::hashEmptyField($val, 'ProductHistory.product_id');
                $tmp = $options;
                $tmp = Common::_callUnset($tmp, array(
                    'limit',
                    'group',
                ));

                $tmp['conditions']['ProductHistory.product_id'] = $product_id;
                $values = $this->controller->ProductHistory->getData('all', $tmp, array(
                    'branch' => false,
                ));

                foreach ($values as $key => $value) {
                    $product_history_id = Common::hashEmptyField($value, 'ProductHistory.id');
	                $product_id = Common::hashEmptyField($value, 'ProductHistory.product_id');
	                $transaction_type = Common::hashEmptyField($value, 'ProductHistory.transaction_type');
	                $transaction_id = Common::hashEmptyField($value, 'ProductHistory.transaction_id');
	                $branch_id = Common::hashEmptyField($value, 'ProductHistory.branch_id');

	                $value = $this->controller->ProductHistory->Product->getMergeList($value, array(
	                    'contain' => array(
	                        'ProductUnit',
	                    ),
	                ));
	                $value = $this->controller->ProductHistory->getMergeList($value, array(
	                    'contain' => array(
	                        'Branch',
	                    ),
	                ));

	                switch ($transaction_type) {
	                    case 'product_receipt':
	                        $modelName = 'ProductReceipt';
	                        break;
	                    case 'product_expenditure':
	                        $modelName = 'ProductExpenditure';
	                        break;
	                    case 'product_expenditure_void':
	                        $modelName = 'ProductExpenditure';
	                        break;
	                    case 'product_adjustment_min':
	                        $modelName = 'ProductAdjustment';
	                        break;
	                    case 'product_adjustment_min_void':
	                        $modelName = 'ProductAdjustment';
	                        break;
	                    case 'product_adjustment_plus':
	                        $modelName = 'ProductAdjustment';
	                        break;
	                    case 'product_adjustment_plus_void':
	                        $modelName = 'ProductAdjustment';
	                        break;
	                }

                    if( !empty($modelName) ) {
		                $value = $this->controller->ProductHistory->getMergeList($value, array(
		                    'contain' => array(
		                        'DocumentDetail' => array(
		                            'uses' => $modelName.'Detail',
		                            'contain' => array(
		                                'Document' => array(
		                                    'uses' => $modelName,
		                                    'elements' => array(
		                                        'branch' => false,
		                                        'status' => false,
		                                    ),
		                                ),
		                            ),
		                        ),
		                    ),
		                ));
		            }

	                if( $transaction_type == 'product_receipt' ) {
	                    $product_receipt_id = Common::hashEmptyField($value, 'DocumentDetail.Document.id');

	                    $value['DocumentDetail']['SerialNumber'] = $this->controller->ProductHistory->ProductReceiptDetail->ProductReceipt->ProductReceiptDetailSerialNumber->getData('list', array(
	                        'fields' => array(
	                            'ProductReceiptDetailSerialNumber.serial_number',
	                            'ProductReceiptDetailSerialNumber.serial_number',
	                        ),
	                        'conditions' => array(
	                            'ProductReceiptDetailSerialNumber.product_receipt_id' => $product_receipt_id,
	                            'ProductReceiptDetailSerialNumber.product_id' => $product_id,
	                        ),
	                    ), array(
	                        'status' => 'confirm',
	                    ));
	                } else if( $transaction_type == 'product_expenditure' ) {
	                    $product_expenditure_detail_id = Common::hashEmptyField($value, 'DocumentDetail.id');

	                    $value['DocumentDetail']['SerialNumber'] = $this->controller->ProductHistory->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->getData('list', array(
	                        'fields' => array(
	                            'ProductExpenditureDetailSerialNumber.serial_number',
	                            'ProductExpenditureDetailSerialNumber.serial_number',
	                        ),
	                        'conditions' => array(
	                            'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id' => $product_expenditure_detail_id,
	                            'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
	                        ),
	                    ));
	                } else if( in_array($transaction_type, array('product_adjustment_min', 'product_adjustment_plus')) ) {
	                    $product_adjustment_detail_id = Common::hashEmptyField($value, 'DocumentDetail.id');

	                    $value['DocumentDetail']['SerialNumber'] = $this->controller->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->getData('list', array(
	                        'fields' => array(
	                            'ProductAdjustmentDetailSerialNumber.serial_number',
	                            'ProductAdjustmentDetailSerialNumber.serial_number',
	                        ),
	                        'conditions' => array(
	                            'ProductAdjustmentDetailSerialNumber.product_adjustment_detail_id' => $product_adjustment_detail_id,
	                            'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
	                        ),
	                    ));
	                } else if( empty($transaction_type) ) {
                        $value['DocumentDetail']['SerialNumber'] = $this->controller->ProductHistory->ProductStock->find('list', array(
                            'fields' => array(
                                'ProductStock.serial_number',
                                'ProductStock.serial_number',
                            ),
                            'contain' => array(
                                'ProductHistory',
                            ),
                            'conditions' => array(
                                'ProductStock.product_history_id' => $product_history_id,
                                'ProductHistory.status' => 1,
                                'ProductHistory.transaction_type = \'\' ',
                                'ProductHistory.product_id' => $product_id,
                                'ProductHistory.branch_id' => $branch_id,
                            ),
                        ));
                    }

	                if( in_array($transaction_type, array('product_adjustment_min', 'product_adjustment_plus', 'product_adjustment_min_void', 'product_adjustment_plus_void')) ) {
	                    $tmpResult[$product_id][$branch_id]['Branch'] = Common::hashEmptyField($value, 'Branch');
	                    $tmpResult[$product_id][$branch_id]['Product'] = Common::hashEmptyField($value, 'Product');
	                    $tmpResult[$product_id][$branch_id]['ProductHistory'][] = $value;
	                } else {
	                    $document_type = Common::hashEmptyField($value, 'DocumentDetail.Document.document_type');
	                    $document_id = Common::hashEmptyField($value, 'DocumentDetail.Document.document_id');

	                    switch ($document_type) {
	                        case 'po':
	                            $transactionName = 'PurchaseOrder';
	                            break;
	                        
	                        default:
	                            $transactionName = 'Spk';
	                            break;
	                    }

                    	if( !empty($modelName) ) {
		                    $modelNameDetail = $modelName.'Detail';
		                    $value = $this->controller->ProductHistory->$modelNameDetail->$modelName->$transactionName->getMerge($value, $document_id, $transactionName.'.id', 'all', 'Transaction');
		                    
		                    $truck_id = Common::hashEmptyField($value, 'Transaction.truck_id');

		                    if( !empty($truck_id) ) {
		                        $value = $this->controller->ProductHistory->$modelNameDetail->$modelName->$transactionName->Truck->getMerge($value, $truck_id);
		                    }
		                }

	                    $tmpResult[$product_id][$branch_id]['Branch'] = Common::hashEmptyField($value, 'Branch');
	                    $tmpResult[$product_id][$branch_id]['Product'] = Common::hashEmptyField($value, 'Product');
	                    $tmpResult[$product_id][$branch_id]['ProductHistory'][] = $value;
	                }

	                $document_type = Common::hashEmptyField($value, 'DocumentDetail.Document.document_type');
	                $document_id = Common::hashEmptyField($value, 'DocumentDetail.Document.document_id');

	                switch ($document_type) {
	                    case 'po':
	                        $transactionName = 'PurchaseOrder';
	                        break;
	                    
	                    default:
	                        $transactionName = 'Spk';
	                        break;
	                }
	            }
			}
		}

        if(!empty($tmpResult)){
        	$idx = 0;
            $this->controller->ProductHistory->virtualFields['total_begining_balance'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_receipt\' OR ProductHistory.transaction_type = \'product_adjustment_plus\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.transaction_type = \'product_expenditure\' OR ProductHistory.transaction_type = \'product_adjustment_min\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';
            $this->controller->ProductHistory->virtualFields['total_qty_in'] = 'SUM(CASE WHEN ProductHistory.type = \'in\' THEN ProductHistory.qty ELSE 0 END)';
            $this->controller->ProductHistory->virtualFields['total_qty_out'] = 'SUM(CASE WHEN ProductHistory.type = \'out\' THEN ProductHistory.qty ELSE 0 END)';
            $this->controller->ProductHistory->Product->ProductStock->virtualFields['label'] = 'CONCAT(ProductStock.id, \'|\', ProductStock.price)';

            foreach ($tmpResult as $key => &$product) {
                if(!empty($product)){
                    foreach ($product as $key => &$branch) {
                        $product_id = Common::hashEmptyField($branch, 'Product.id');
                        $branch_id = Common::hashEmptyField($branch, 'Branch.id');

                        $branch_name = Common::hashEmptyField($branch, 'Branch.full_name');
                        $product_name = Common::hashEmptyField($branch, 'Product.full_name');

                        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
                        $options = Common::_callUnset($options, array(
                            'group',
                            'limit',
                            'conditions' => array(
                                'ProductHistory.product_id',
                                'ProductHistory.branch_id',
                                'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') >=',
                                'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <=',
                            ),
                        ));
                        $options['conditions']['DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <'] = $dateFrom;
                        // $options['conditions']['ProductHistory.product_type'] = 'default';
                        $options['conditions']['ProductHistory.product_id'] = $product_id;
                        $options['conditions']['ProductHistory.branch_id'] = $branch_id;
                        $options['order'] = array(
                            'ProductHistory.transaction_date' => 'DESC',
                            'ProductHistory.created' => 'DESC',
                        );

                        $lastHistory = $this->controller->ProductHistory->getData('first', $options, array(
                            'branch' => false,
                        ));

                        $tmpOption = $options;
                        $tmpOption['group'] = array(
                            'ProductHistory.price',
                        );
                        $tmpOption['fields'] = array(
                            'ProductHistory.price',
                            'ProductHistory.total_qty_in',
                            'ProductHistory.total_qty_out',
                        );
                        $lastHistoryByPrice = $this->controller->ProductHistory->getData('all', $tmpOption, array(
                            'branch' => false,
                        ));

                        $receiptSN = $this->controller->ProductHistory->ProductReceiptDetail->ProductReceipt->ProductReceiptDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductReceiptDetailSerialNumber.serial_number',
                                'ProductReceiptDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductReceipt',
                            ),
                            'conditions' => array(
                                'ProductReceiptDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductReceipt.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductReceipt.branch_id' => $branch_id,
                                'ProductReceipt.status' => 1,
                                'ProductReceipt.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                                // 'ProductReceipt.document_type <>' => 'spk',
                            ),
                        ), array(
                            'status' => 'confirm',
                        ));

                        $this->controller->ProductHistory->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->bindModel(array(
                            'hasOne' => array(
                                'ProductExpenditure' => array(
                                    'className' => 'ProductExpenditure',
                                    'foreignKey' => false,
                                    'conditions' => array(
                                        'ProductExpenditure.id = ProductExpenditureDetail.product_expenditure_id',
                                    ),
                                ),
                            )
                        ), false);
                        $expenditureSN = $this->controller->ProductHistory->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductExpenditureDetailSerialNumber.serial_number',
                                'ProductExpenditureDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductExpenditureDetail',
                                'ProductExpenditure',
                            ),
                            'conditions' => array(
                                'ProductExpenditureDetail.status' => 1,
                                'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductExpenditure.branch_id' => $branch_id,
                                'ProductExpenditure.status' => 1,
                                'ProductExpenditure.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                            ),
                        ));
                        $last_serial_number = array_diff($receiptSN, $expenditureSN);

                        $this->controller->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->bindModel(array(
                            'hasOne' => array(
                                'ProductAdjustment' => array(
                                    'className' => 'ProductAdjustment',
                                    'foreignKey' => false,
                                    'conditions' => array(
                                        'ProductAdjustment.id = ProductAdjustmentDetail.product_adjustment_id',
                                    ),
                                ),
                            )
                        ), false);
                        $adjustmentPlus = $this->controller->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductAdjustmentDetail',
                                'ProductAdjustment',
                            ),
                            'conditions' => array(
                                'ProductAdjustmentDetail.status' => 1,
                                'ProductAdjustmentDetail.type' => 'plus',
                                'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductAdjustment.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductAdjustment.branch_id' => $branch_id,
                                'ProductAdjustment.status' => 1,
                                'ProductAdjustment.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                            ),
                        ));
                        $last_serial_number = array_merge($last_serial_number, $adjustmentPlus);

                        $adjustmentMin = $this->controller->ProductHistory->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->getData('list', array(
                            'fields' => array(
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                                'ProductAdjustmentDetailSerialNumber.serial_number',
                            ),
                            'contain' => array(
                                'ProductAdjustmentDetail',
                                'ProductAdjustment',
                            ),
                            'conditions' => array(
                                'ProductAdjustmentDetail.status' => 1,
                                'ProductAdjustmentDetail.type' => 'min',
                                'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
                                'DATE_FORMAT(ProductAdjustment.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                'ProductAdjustment.branch_id' => $branch_id,
                                'ProductAdjustment.status' => 1,
                                'ProductAdjustment.transaction_status NOT' => array( 'unposting', 'revised', 'void' ),
                            ),
                        ));
                        $last_serial_number = array_diff($last_serial_number, $adjustmentMin);

                        $total_qty_in = Common::hashEmptyField($lastHistory, 'ProductHistory.total_qty_in', 0);
                        $total_qty_out = Common::hashEmptyField($lastHistory, 'ProductHistory.total_qty_out', 0);
                        $total_qty = $total_qty_in - $total_qty_out;

                        $stock = $this->controller->ProductHistory->Product->ProductStock->getData('list', array(
                            'fields' => array(
                                'ProductStock.label',
                                'ProductStock.serial_number',
                            ),
                            'conditions' => array(
                                'ProductStock.product_id' => $product_id,
                                'ProductStock.serial_number' => $last_serial_number,
                                'ProductStock.branch_id' => $branch_id,
                                'DATE_FORMAT(ProductStock.transaction_date, \'%Y-%m-%d\') <' => $dateFrom,
                                // 'ProductStock.type' => 'default',
                            ),
                        ), array(
                            'status' => false,
                            'branch' => false,
                        ));

                        $lastHistory = $this->controller->ProductHistory->Product->getMergeList($lastHistory, array(
                            'contain' => array(
                                'ProductUnit',
                            ),
                        ));
                        $lastHistory['ProductHistory']['ending'] = $total_qty;
                        $lastHistory['ProductHistory']['last_serial_number'] = $stock;
                        $lastHistory['ProductHistory']['by_price'] = $lastHistoryByPrice;
        				$ending_stock = array();

				        if( !empty($lastHistory['ProductHistory']['id']) ) {
				            $unit = Common::hashEmptyField($lastHistory, 'ProductUnit.name');
				            $start_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.ending', 0);
				            $total_begining_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.total_begining_balance');
            				$last_serial_number = Common::hashEmptyField($lastHistory, 'ProductHistory.last_serial_number');
            				
            				$by_price = Common::hashEmptyField($lastHistory, 'ProductHistory.by_price');

				            if( !empty($start_balance) ) {
				                $total_begining_price = $total_begining_balance / $start_balance;
				            } else {
				                $total_begining_price = 0;
				            }
    
				            if( !empty($by_price) ) {
				                foreach ($by_price as $key => $val_price) {
				                    $total_qty_in = Common::hashEmptyField($val_price, 'ProductHistory.total_qty_in');
				                    $total_qty_out = Common::hashEmptyField($val_price, 'ProductHistory.total_qty_out');
				                    $price = Common::hashEmptyField($val_price, 'ProductHistory.price');

				                    $ending_stock[$price]['qty'] = $total_qty_in - $total_qty_out;
				                    $ending_stock[$price]['price'] = $price;
				                }
				            }

				            if( !empty($last_serial_number) ) {
				                foreach ($last_serial_number as $label_sn => $sn) {
				                    $prices = explode('|', $label_sn);

				                    if( !empty($prices[1]) ) {
				                        if( !empty($ending_stock[$prices[1]]['serial_numbers']) ) {
				                            $ending_stock[$prices[1]]['serial_numbers'][] = $sn;
				                        } else {
				                            $ending_stock[$prices[1]]['serial_numbers'] = array(
				                                $sn,
				                            );
				                        }
				                    }
				                }
				            }
				        } else {
				            $unit = '';
				            $start_balance = 0;
				            $total_begining_balance = 0;
				            $total_begining_price = 0;
				        }

				        $idxKey = 0;
						$result['multiple'][$product_id][$branch_id]['detail'][$idxKey] = $cntTmp = array(
							__('Tanggal') => array(
								'text' => __('OPENING BALANCE'),
		                		'excel' => array(
									'colspan' => 3,
		            			),
							),
							__('Satuan') => array(
								'text' => $unit,
		                		'excel' => array(
									'align' => 'center',
		            			),
							),
							__('Masuk') => array(
		                		'excel' => array(
									'colspan' => 6,
		            			),
							),
							__('Saldo') => array(
								'text' => !empty($start_balance)?$start_balance:0,
		                		'excel' => array(
		                			'align' => 'center',
		            			),
							),
							__('Harga Satuan Saldo') => array(
								'text' => $this->MkCommon->getFormatPrice($total_begining_price, 0, 2),
                				'label' => __('Harga Satuan'),
		                		'excel' => array(
		                			'align' => 'right',
		            			),
							),
							__('Total Saldo') => array(
								'text' => $this->MkCommon->getFormatPrice($total_begining_balance, 0, 2),
                				'label' => __('Total'),
		                		'excel' => array(
		                			'align' => 'right',
		            			),
							),
						);

                        $result['multiple'][$product_id][$branch_id]['header'] = array(
							__('Cabang') => array(
								'text' => $branch_name,
								'excel' => array(
									'colspan' => count($cntTmp),
								),
							),
							__('Product') => array(
								'text' => $product_name,
								'excel' => array(
									'colspan' => count($cntTmp),
								),
							),
						);

				        if(!empty($branch['ProductHistory'])){
				            foreach ($branch['ProductHistory'] as $key => $value) {
				                $qty_in = '';
				                $price_in = '';
				                $total_in = '';

				                $qty_out = '';
				                $price_out = '';
				                $total_out = '';
                
				                $ending = 0;
				                $grandtotal_ending = 0;

				                $url = null;
				                $price = null;
				                $id = Common::hashEmptyField($value, 'Product.id');
                				$is_serial_number = Common::hashEmptyField($value, 'Product.is_serial_number');
				                $unit = Common::hashEmptyField($value, 'ProductUnit.name');

				                $transaction_id = Common::hashEmptyField($value, 'ProductHistory.transaction_id');
				                $transaction_type = Common::hashEmptyField($value, 'ProductHistory.transaction_type');
				                // $ending = Common::hashEmptyField($value, 'ProductHistory.ending');
				                // $balance = Common::hashEmptyField($value, 'ProductHistory.balance');
				                $transaction_date = Common::hashEmptyField($value, 'ProductHistory.transaction_date', null, array(
				                    'date' => 'd/m/Y',
				                ));

               					$nopol = Common::hashEmptyField($value, 'Truck.nopol', '-');
				                $nodoc = Common::hashEmptyField($value, 'DocumentDetail.Document.nodoc');
                				$docid = Common::hashEmptyField($value, 'DocumentDetail.Document.id');
                				$serial_numbers = Common::hashEmptyField($value, 'DocumentDetail.SerialNumber');
				                $qty = Common::hashEmptyField($value, 'ProductHistory.qty');
				                // $total_balance_price = $total_begining_price*$balance;

				                if( in_array($transaction_type, array( 'product_receipt', 'product_expenditure_void', 'product_adjustment_plus', 'product_adjustment_min_void' )) ) {
				                    $qty_in = Common::hashEmptyField($value, 'ProductHistory.qty');
				                    $price = $price_in = Common::hashEmptyField($value, 'ProductHistory.price');
                    				$doc_type = Common::hashEmptyField($value, 'ProductHistory.product_type');
				                    $total_in = $qty_in * $price_in;

				                    $total_ending_price = $price*$qty;
				            
                    				// if( $doc_type != 'barang_bekas' ) {            
					                    if( !empty($ending_stock[$price]['qty']) ) {
					                        $ending_stock[$price]['qty'] = $ending_stock[$price]['qty'] + $qty;
					                    } else {
					                        $ending_stock[$price] = array(
					                            'qty' => $qty,
					                            'price' => $price,
					                        );
					                    }

					                    $ending_stock[$price]['serial_numbers'] = $serial_numbers;
					                // } else {
				                 //        $nodoc = __('%s (Barang Bekas)', $nodoc);
				                 //    }

				                    if( $transaction_type == 'product_expenditure_void' ) {
				                        $nodoc = __('%s (Void)', $nodoc);
				                    }
				                } else if( in_array($transaction_type, array('product_expenditure', 'product_adjustment_min', 'product_adjustment_plus_void')) ) {
				                    $qty_out_tmp = $qty_out = Common::hashEmptyField($value, 'ProductHistory.qty');
				                    $price = $price_out = Common::hashEmptyField($value, 'ProductHistory.price');
				                    $total_out = $qty_out * $price_out;
				                    $total_ending_price = $price*$qty;
				            
				                    if( !empty($ending_stock) ) {
				                        foreach ($ending_stock as $key => $stock) {
				                            $sn_stock = Common::hashEmptyField($stock, 'serial_numbers');

				                            if( !empty($serial_numbers) && !empty($sn_stock) && !empty($is_serial_number) ) {
				                                foreach ($serial_numbers as $sn) {
				                                    if( in_array($sn, $sn_stock) ) {
				                                        $ending_qty = Common::hashEmptyField($stock, 'qty', 0) - $qty_out_tmp;

				                                        if( empty($ending_qty) ) {
				                                            unset($ending_stock[$key]);
				                                            break;
				                                        } else if( $ending_qty < 0 ) {
				                                            unset($ending_stock[$key]);
				                                            $qty_out_tmp = abs($ending_qty);
				                                        } else {
				                                            $ending_stock[$key]['qty'] = $ending_qty;
				                                            break;
				                                        }
				                                    }
				                                }
				                            } else {
				                                $ending_qty = Common::hashEmptyField($stock, 'qty', 0) - $qty_out_tmp;

				                                if( empty($ending_qty) ) {
				                                    unset($ending_stock[$key]);
				                                    break;
				                                } else if( $ending_qty < 0 ) {
				                                    unset($ending_stock[$key]);
				                                    $qty_out_tmp = abs($ending_qty);
				                                } else {
				                                    $ending_stock[$key]['qty'] = $ending_qty;
				                                    break;
				                                }
				                            }
				                        }
				                    }
				                } else {
				                    $qty_in = Common::hashEmptyField($value, 'ProductHistory.qty');
                    				$doc_type = Common::hashEmptyField($value, 'ProductHistory.product_type');
				                    $price = $price_in = Common::hashEmptyField($value, 'ProductHistory.price');
				                    $total_in = $qty_in * $price_in;

				                    $total_ending_price = $price*$qty;
				            
                    				// if( $doc_type != 'barang_bekas' ) {            
					                    if( !empty($ending_stock[$price]['qty']) ) {
					                        $ending_stock[$price]['qty'] = $ending_stock[$price]['qty'] + $qty;
					                    } else {
					                        $ending_stock[$price] = array(
					                            'qty' => $qty,
					                            'price' => $price,
					                        );
					                    }
					                // } else {
				                 //        $nodoc = __('%s (Barang Bekas)', $nodoc);
				                 //    }
				                }

					            if( !empty($ending_stock) ) {
					                $firstArr = reset($ending_stock);
					                $lastArr = $ending_stock;
					                array_splice($lastArr, 0, 1);

					                $ending_qty = Common::hashEmptyField($firstArr, 'qty', 0);
					                $ending_price = Common::hashEmptyField($firstArr, 'price', 0);
					                $ending_total = $ending_qty*$ending_price;
					                
					                $ending += $ending_qty;
					                $grandtotal_ending += $ending_total;

					                $balance = $ending_qty;
					                $balance_price = $ending_price;
					                $balance_total = $ending_total;
					            } else {
					                $ending += $start_balance;
					                $grandtotal_ending += $total_begining_balance;

					                if( empty($start_balance) ) {
					                    $total_begining_price = 0;
					                }

					                $balance = $start_balance;
					                $balance_price = $total_begining_price;
					                $balance_total = $total_begining_balance;
					            }

								$result['multiple'][$product_id][$branch_id]['detail'][$idxKey+=1] = array(
									__('Tanggal') => array(
										'text' => $transaction_date,
									),
									__('No. Referensi') => array(
		                				'text' => $nodoc,
									),
									__('No. Pol') => array(
		                				'text' => $nopol,
									),
									__('Satuan') => array(
										'text' => $unit,
				                		'excel' => array(
				                			'align' => 'center',
				            			),
									),
									__('Masuk') => array(
										'text' => $qty_in,
				                		'excel' => array(
				                			'align' => 'center',
				            			),
									),
									__('Harga Satuan Masuk') => array(
		                				'text' => is_numeric($price_in)?$this->MkCommon->getFormatPrice($price_in, 0, 2):'',
		                				'label' => __('Harga Satuan'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Total Masuk') => array(
		                				'text' => is_numeric($total_in)?$this->MkCommon->getFormatPrice($total_in, 0, 2):'',
		                				'label' => __('Total'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Keluar') => array(
		                				'text' => $qty_out,
				                		'excel' => array(
				                			'align' => 'center',
				            			),
									),
									__('Harga Satuan Keluar') => array(
		                				'text' => is_numeric($price_out)?$this->MkCommon->getFormatPrice($price_out, 0, 2):'',
		                				'label' => __('Harga Satuan'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Total Keluar') => array(
										'text' => is_numeric($total_out)?$this->MkCommon->getFormatPrice($total_out, 0, 2):'',
		                				'label' => __('Total'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Saldo') => array(
										'text' => $balance,
				                		'excel' => array(
				                			'align' => 'center',
				            			),
									),
									__('Harga Satuan Saldo') => array(
										'text' => $this->MkCommon->getFormatPrice($balance_price, 0, 2),
		                				'label' => __('Harga Satuan'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Total Saldo') => array(
										'text' => $this->MkCommon->getFormatPrice($balance_total, 0, 2),
		                				'label' => __('Total'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
								);

								
						        if( !empty($lastArr) ) {
						            foreach ($lastArr as $key => $stock) {
						                $ending_qty = Common::hashEmptyField($stock, 'qty', 0);
						                $ending_price = Common::hashEmptyField($stock, 'price', 0);
						                $ending_total = $ending_qty*$ending_price;

						                $ending += $ending_qty;
						                $grandtotal_ending += $ending_total;
										
										$result['multiple'][$product_id][$branch_id]['detail'][$idxKey+=1] = array(
											__('Tanggal') => array(
				                				'field_model' => 'ProductHistory.transaction_date',
											),
											__('No. Referensi') => array(
				                				'field_model' => 'ProductHistory.transaction_id',
											),
											__('No. Pol') => array(
				                				'field_model' => 'Truck.nopol',
											),
											__('Satuan') => array(
				                				'field_model' => 'ProductHistory.transaction_id',
											),
											__('Masuk') => array(
				                				'field_model' => 'ProductUnit.name',
											),
											__('Harga Satuan Masuk') => array(
		                						'label' => __('Harga Satuan'),
				                				'field_model' => 'ProductHistory.price',
											),
											__('Total Masuk') => array(
		                						'label' => __('Total'),
				                				'field_model' => 'ProductHistory.total',
											),
											__('Keluar') => array(
				                				'field_model' => 'ProductHistory.qty',
											),
											__('Harga Satuan Keluar') => array(
		                						'label' => __('Harga Satuan'),
				                				'field_model' => 'ProductHistory.price',
											),
											__('Total Keluar') => array(
		                						'label' => __('Total'),
				                				'field_model' => 'ProductHistory.total',
											),
											__('Saldo') => array(
												'text' => $ending_qty,
						                		'excel' => array(
						                			'align' => 'center',
						            			),
											),
											__('Harga Satuan Saldo') => array(
		                						'label' => __('Harga Satuan'),
												'text' => $this->MkCommon->getFormatPrice($ending_price, 0, 2),
						                		'excel' => array(
						                			'align' => 'right',
						            			),
											),
											__('Total Saldo') => array(
		                						'label' => __('Total'),
												'text' => $this->MkCommon->getFormatPrice($ending_total, 0, 2),
						                		'excel' => array(
						                			'align' => 'right',
						            			),
											),
										);
						            }
						        }

					            if( !empty($ending) ) {
					                $grandtotal_ending_price = $grandtotal_ending / $ending;
					            } else {
					                $grandtotal_ending_price = 0;
					            }

								$result['multiple'][$product_id][$branch_id]['detail'][$idxKey+=1] = $cntTmp = array(
									__('Tanggal') => array(
		                				'field_model' => 'ProductHistory.transaction_date',
									),
									__('No. Referensi') => array(
		                				'field_model' => 'ProductHistory.transaction_id',
									),
									__('No. Pol') => array(
		                				'field_model' => 'Truck.nopol',
									),
									__('Satuan') => array(
		                				'field_model' => 'ProductHistory.transaction_id',
									),
									__('Masuk') => array(
		                				'field_model' => 'ProductUnit.name',
									),
									__('Harga Satuan Masuk') => array(
                						'label' => __('Harga Satuan'),
		                				'field_model' => 'ProductHistory.price',
									),
									__('Total Masuk') => array(
                						'label' => __('Total'),
		                				'field_model' => 'ProductHistory.total',
									),
									__('Keluar') => array(
		                				'field_model' => 'ProductHistory.qty',
									),
									__('Harga Satuan Keluar') => array(
                						'label' => __('Harga Satuan'),
		                				'field_model' => 'ProductHistory.price',
				                		'excel' => array(
				                			'bold' => true,
				            			),
									),
									__('Total Keluar') => array(
										'text' => __('Total'),
                						'label' => __('Total'),
		                				'field_model' => 'ProductHistory.total',
				                		'excel' => array(
				                			'bold' => true,
				                			'align' => 'right',
				            			),
									),
									__('Saldo') => array(
										'text' => $ending,
				                		'excel' => array(
				                			'align' => 'center',
				                			'bold' => true,
				            			),
									),
									__('Harga Satuan Saldo') => array(
                						'label' => __('Harga Satuan'),
										'text' => $this->MkCommon->getFormatPrice($grandtotal_ending_price, 0, 2),
				                		'excel' => array(
				                			'align' => 'right',
				                			'bold' => true,
				            			),
									),
									__('Total Saldo') => array(
                						'label' => __('Total'),
										'text' => $this->MkCommon->getFormatPrice($grandtotal_ending, 0, 2),
				                		'excel' => array(
				                			'align' => 'right',
				                			'bold' => true,
				            			),
									),
								);

								$result['multiple_column'] = $cntTmp;

				                $total_begining_price = $grandtotal_ending_price;
				            }
       					}
                    }
                }
        		
        		$idx++;
            }
        }

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'ProductHistory',
		);
	}

	function _callDataExpenditure_reports ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('ProductExpenditureDetail');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

        $this->controller->ProductExpenditureDetail->unBindModel(array(
            'hasMany' => array(
                'ProductExpenditureDetailSerialNumber'
            )
        ));
        $this->controller->ProductExpenditureDetail->bindModel(array(
            'hasOne' => array(
                'ProductExpenditureDetailSerialNumber' => array(
                    'className' => 'ProductExpenditureDetailSerialNumber',
                    'foreignKey' => 'product_expenditure_detail_id',
                ),
            )
        ), false);

        $this->controller->ProductExpenditureDetail->ProductExpenditureDetailSerialNumber->virtualFields['qty'] = 'CASE WHEN ProductExpenditureDetailSerialNumber.id IS NULL THEN SUM(ProductExpenditureDetail.qty) ELSE SUM(ProductExpenditureDetailSerialNumber.qty) END';

		$options = array(
			'contain' => array(
				'ProductExpenditureDetailSerialNumber',
			),
            'order'=> array(
                'ProductExpenditure.status' => 'DESC',
                'ProductExpenditure.created' => 'DESC',
                'ProductExpenditure.id' => 'DESC',
                'ProductExpenditureDetail.id' => 'ASC',
            ),
			'group' => array(
				'ProductExpenditureDetailSerialNumber.id',
				'ProductExpenditureDetail.id',
			),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->ProductExpenditureDetail->ProductExpenditure->_callRefineParams($params, $options);
		$options = $this->controller->ProductExpenditureDetail->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductExpenditure', $options );

		$this->controller->paginate	= $this->controller->ProductExpenditureDetail->getData('paginate', $options, array(
			'branch' => false,
			'header' => true,
		));
		$data = $this->controller->paginate('ProductExpenditureDetail');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductExpenditureDetail.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'ProductExpenditureDetail.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
            $types = Configure::read('__Site.Spk.type');
            $totalQty = 0;

			foreach ($data as $key => $value) {
                $product_expenditure_detail_id = Common::hashEmptyField($value, 'ProductExpenditureDetail.id');
                $branch_id = Common::hashEmptyField($value, 'ProductExpenditure.branch_id');
                $document_type = Common::hashEmptyField($value, 'ProductExpenditure.document_type');

                $value = $this->RjProduct->_callGetDocReceipt($value);
		        $value = $this->controller->ProductExpenditureDetail->getMergeList($value, array(
		            'contain' => array(
		            	'Product' => array(
		                	'ProductUnit',
	            		),
		            ),
		        ));
		        $value = $this->controller->ProductExpenditureDetail->ProductExpenditure->getMergeList($value, array(
		            'contain' => array(
		            	'Branch',
		                'Spk' => array(
		                    'Truck',
		                ),
		            ),
		        ));
        		
        		// switch ($document_type) {
        		// 	case 'internal':
        				if( empty($value['ProductExpenditureDetailSerialNumber']['id']) ) {
			        		$this->controller->ProductExpenditureDetail->ProductHistory->virtualFields['grandtotal'] = 'SUM(ProductHistory.qty*ProductHistory.price)';
					        $history = $this->controller->ProductExpenditureDetail->ProductHistory->getData('first', array(
					        	'conditions' => array(
					        		'ProductHistory.transaction_type' => 'product_expenditure',
					        		'ProductHistory.transaction_id' => $product_expenditure_detail_id,
					        		'ProductHistory.branch_id' => $branch_id,
				        		),
				        	), array(
				        		'branch' => false,
				        	));
					    } else {
					    	$qty_sn = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber.qty');
					    	$price_sn = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber.price');
					    	$history['ProductHistory']['grandtotal'] = $qty_sn * $price_sn;
					    }
        // 				break;
    				// default:
    				// 	$history = array();
        // 				break;
        // 		}

                $nodoc = Common::hashEmptyField($value, 'ProductExpenditure.nodoc');
                $transaction_date = Common::hashEmptyField($value, 'ProductExpenditure.transaction_date', null, array(
                	'date' => 'd M Y',
            	));
                $document_type = Common::hashEmptyField($value, 'ProductExpenditure.document_type');
                $note = Common::hashEmptyField($value, 'ProductExpenditure.note', '-', array(
                	'strict' => true,
            	));
                $customStatus = $this->MkCommon->_callTransactionStatus($value, 'ProductExpenditure', 'transaction_status', $view);
                
                $qty = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber.qty', 0, array(
                	'strict' => true,
            	));
                $serial_number = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber.serial_number');

                $spk_nodoc = Common::hashEmptyField($value, 'Spk.nodoc');
                $nopol = Common::hashEmptyField($value, 'Spk.Truck.nopol');
                $branch = Common::hashEmptyField($value, 'Branch.code');

                $code = Common::hashEmptyField($value, 'Product.code');
                $name = Common::hashEmptyField($value, 'Product.name');
                $unit = Common::hashEmptyField($value, 'Product.ProductUnit.name');
                $spk_grandtotal = Common::hashEmptyField($history, 'ProductHistory.grandtotal');
				$grandtotal += $spk_grandtotal;

                $type = Common::hashEmptyField($types, $document_type);
            	$totalQty += $qty;

				$result[$key] = array(
					__('No Dokumen') => array(
						'text' => $nodoc,
                		'field_model' => 'ProductExpenditure.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nodoc\',width:120',
		                'align' => 'left',
					),
					__('Cabang') => array(
						'text' => $branch,
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
					),
					__('Tgl Pengeluaran') => array(
						'text' => $transaction_date,
                		'field_model' => 'ProductExpenditure.transaction_date',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'transaction_date\',width:120',
		                'align' => 'left',
					),
					__('No. SPK') => array(
						'text' => $spk_nodoc,
                		'fix_column' => true,
                		'field_model' => 'Spk.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'spk\',width:120',
		                'align' => 'left',
					),
					__('Jenis') => array(
						'text' => $type,
                		'field_model' => 'ProductExpenditure.document_type',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'document_type\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('NoPol') => array(
						'text' => $nopol,
                		'field_model' => 'Truck.nopol',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Keterangan') => array(
						'text' => $note,
                		'field_model' => 'ProductExpenditure.note',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'note\',width:150',
					),
					__('Kode Barang') => array(
						'text' => $code,
                		'field_model' => 'Product.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'code\',width:100',
					),
					__('Nama Barang') => array(
						'text' => $name,
                		'field_model' => 'Product.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'name\',width:100',
					),
					__('Satuan') => array(
						'text' => $unit,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'unit\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Serial Number') => array(
						'text' => $serial_number,
                		'field_model' => 'ProductExpenditureDetailSerialNumber.serial_number',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'serial_number\',width:120',
					),
					__('QTY') => array(
						'text' => $qty,
                		'field_model' => 'ProductExpenditureDetailSerialNumber.qty',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga Barang') => array(
						'text' => !empty($spk_grandtotal)?Common::getFormatPrice($spk_grandtotal):'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'grandtotal\',width:120',
		                'align' => 'right',
		                'mainalign' => 'right',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Status') => array(
						'text' => $customStatus,
                		'field_model' => 'ProductExpenditure.transaction_status',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'status\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}

			// if( empty($nextPage) ) {
			if( !empty($view) ) {
				$result[$key+1] = array(
					__('No Dokumen') => array(
                		'field_model' => 'ProductExpenditure.nodoc',
					),
					__('Cabang') => array(
                		'field_model' => 'Branch.branch',
					),
					__('Tgl Pengeluaran') => array(
                		'field_model' => 'ProductExpenditure.transaction_date',
					),
					__('No. SPK') => array(
                		'field_model' => 'Spk.nodoc',
					),
					__('Jenis') => array(
                		'field_model' => 'ProductExpenditure.document_type',
					),
					__('NoPol') => array(
                		'field_model' => 'Truck.nopol',
					),
					__('Keterangan') => array(
                		'field_model' => 'ProductExpenditure.note',
					),
					__('Kode Barang') => array(
                		'field_model' => 'Product.code',
					),
					__('Nama Barang') => array(
                		'field_model' => 'Product.name',
					),
					__('Satuan') => array(
                		'field_model' => 'ProductUnit.name',
					),
					__('Serial Number') => array(
						'text' => __('Total'),
                		'field_model' => 'ProductExpenditureDetailSerialNumber.serial_number',
		                'style' => 'font-weight: bold;',
                		'excel' => array(
                			'bold' => true,
            			),
					),
					__('QTY') => array(
						'text' => $totalQty,
                		'field_model' => 'ProductExpenditureDetailSerialNumber.qty',
		                'style' => 'text-align: center;font-weight: bold;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
                			'bold' => true,
            			),
					),
					__('Total Harga') => array(
						'text' => !empty($grandtotal)?Common::getFormatPrice($grandtotal):'-',
		                'style' => 'text-align: center;font-weight: bold;',
		                'data-options' => 'field:\'grandtotal\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
                			'bold' => true,
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'ProductExpenditureDetail',
		);
	}

	function _callDataReceipt_reports ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('ProductReceiptDetailSerialNumber');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
			'contain' => array(
				'ProductReceipt',
			),
            'order'=> array(
                'ProductReceipt.status' => 'DESC',
                'ProductReceipt.created' => 'DESC',
                'ProductReceipt.id' => 'DESC',
                'ProductReceiptDetailSerialNumber.id' => 'ASC',
            ),
			'group' => array(
				'ProductReceiptDetailSerialNumber.id',
			),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->ProductReceiptDetailSerialNumber->ProductReceipt->_callRefineParams($params, $options);
		$options = $this->controller->ProductReceiptDetailSerialNumber->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductReceipt', $options );

		$this->controller->paginate	= $this->controller->ProductReceiptDetailSerialNumber->getData('paginate', $options, array(
			'status' => 'confirm',
		));
		$data = $this->controller->paginate('ProductReceiptDetailSerialNumber');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductReceiptDetailSerialNumber.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'ProductReceiptDetailSerialNumber.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
            $types = Configure::read('__Site.Spk.type');
            $totalQty = 0;

			foreach ($data as $key => $value) {
                $value = $this->RjProduct->_callGetDocReceipt($value);
		        $value = $this->controller->ProductReceiptDetailSerialNumber->getMergeList($value, array(
		            'contain' => array(
		            	'Product' => array(
		                	'ProductUnit',
	            		),
		            ),
		        ));
		        $value = $this->controller->ProductReceiptDetailSerialNumber->ProductReceipt->getMergeList($value, array(
		            'contain' => array(
		            	'Branch',
                		'Employe' => array(
                			'elements' => array(
                				'branch' => false,
            				),
            			),
		                'Vendor' => array(
		                    'elements' => array(
		                        'status' => 'all',
		                        'branch' => false,
		                    ),
		                ),
		                'Warehouse' => array(
		                    'uses' => 'Branch',
		                    'primaryKey' => 'id',
		                    'foreignKey' => 'to_branch_id',
		                    'type' => 'first',
		                ),
		            ),
		        ));

                $nodoc = Common::hashEmptyField($value, 'ProductReceipt.nodoc');
                $transaction_date = Common::hashEmptyField($value, 'ProductReceipt.transaction_date', null, array(
                	'date' => 'd M Y',
            	));
                $note = Common::hashEmptyField($value, 'ProductReceipt.note', '-', array(
                	'strict' => true,
            	));
                $document_type = Common::hashEmptyField($value, 'ProductReceipt.document_type');
                $customStatus = $this->MkCommon->_callTransactionStatus($value, 'ProductReceipt', 'transaction_status', $view);
                
                $qty = Common::hashEmptyField($value, 'ProductReceiptDetailSerialNumber.qty', 1, array(
                	'strict' => true,
            	));
                $serial_number = Common::hashEmptyField($value, 'ProductReceiptDetailSerialNumber.serial_number');

                $document_nodoc = Common::hashEmptyField($value, 'Document.nodoc');
                $branch = Common::hashEmptyField($value, 'Branch.code');
                $vendor = Common::hashEmptyField($value, 'Vendor.name');
                $employe = Common::hashEmptyField($value, 'Employe.full_name');
                $warehouse = Common::hashEmptyField($value, 'Warehouse.name');

                $code = Common::hashEmptyField($value, 'Product.code');
                $name = Common::hashEmptyField($value, 'Product.name');
                $unit = Common::hashEmptyField($value, 'Product.ProductUnit.name');

            	$totalQty += $qty;

				$result[$key] = array(
					__('No Dokumen') => array(
						'text' => $nodoc,
                		'field_model' => 'ProductReceipt.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nodoc\',width:120',
		                'align' => 'left',
					),
					__('Cabang') => array(
						'text' => $branch,
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
					),
					__('Tgl Penerimaan') => array(
						'text' => $transaction_date,
                		'field_model' => 'ProductReceipt.transaction_date',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'transaction_date\',width:120',
		                'align' => 'left',
					),
					__('No. Transaksi') => array(
						'text' => $document_nodoc,
                		'fix_column' => true,
                		// 'field_model' => 'Document.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'document_nodoc\',width:120',
		                'align' => 'left',
					),
					__('Jenis Transaksi') => array(
						'text' => ucwords($document_type),
                		'field_model' => 'ProductReceipt.document_type',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'document_type\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Supplier') => array(
						'text' => $vendor,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'supplier\',width:120',
		                'align' => 'left',
					),
					__('Diterima Oleh') => array(
						'text' => $employe,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'employe\',width:120',
		                'align' => 'left',
					),
					__('Gudang Masuk') => array(
						'text' => $warehouse,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'warehouse\',width:150',
					),
					__('Keterangan') => array(
						'text' => $note,
                		'field_model' => 'ProductReceipt.note',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'note\',width:150',
					),
					__('Kode Barang') => array(
						'text' => $code,
                		'field_model' => 'Product.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'code\',width:100',
					),
					__('Nama Barang') => array(
						'text' => $name,
                		'field_model' => 'Product.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'name\',width:100',
					),
					__('Satuan') => array(
						'text' => $unit,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'unit\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Serial Number') => array(
						'text' => $serial_number,
                		'field_model' => 'ProductReceiptDetailSerialNumber.serial_number',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'serial_number\',width:120',
					),
					__('QTY') => array(
						'text' => $qty,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Status') => array(
						'text' => $customStatus,
                		'field_model' => 'ProductReceipt.transaction_status',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'status\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}

			// if( empty($nextPage) || !empty($view) ) {
			if( !empty($view) ) {
				$result[$key+1] = array(
					__('No Dokumen') => array(
                		'field_model' => 'ProductReceipt.nodoc',
					),
					__('Cabang') => array(
                		'field_model' => 'Branch.branch',
					),
					__('Tgl Penerimaan') => array(
                		'field_model' => 'ProductReceipt.transaction_date',
					),
					__('No. Dokumen') => array(
                		'field_model' => 'Document.nodoc',
					),
					__('Jenis') => array(
                		'field_model' => 'ProductReceipt.document_type',
					),
					__('Supplier') => array(
                		'field_model' => 'Supplier.name',
					),
					__('Diterima Oleh') => array(
                		'field_model' => 'Employe.full_name',
					),
					__('Gudang Masuk') => array(
                		'field_model' => 'Warehouse.name',
					),
					__('Keterangan') => array(
                		'field_model' => 'ProductReceipt.note',
					),
					__('Kode Barang') => array(
                		'field_model' => 'Product.code',
					),
					__('Nama Barang') => array(
                		'field_model' => 'Product.name',
					),
					__('Satuan') => array(
                		'field_model' => 'ProductUnit.name',
					),
					__('Serial Number') => array(
                		'field_model' => 'ProductExpenditureDetailSerialNumber.serial_number',
					),
					__('QTY') => array(
						'text' => $totalQty,
                		'field_model' => 'ProductReceiptDetailSerialNumber.qty',
		                'style' => 'text-align: center;font-weight: bold;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
                			'bold' => true,
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'ProductReceiptDetailSerialNumber',
		);
	}

	function _callDataTire_reports ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Spk');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->Spk->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Spk', $options );

		$this->controller->paginate	= $this->controller->Spk->getData('paginate', $options, array(
			'branch' => false,
		));
		$data = $this->controller->paginate('Spk');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Spk.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'Spk.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
		        $value = $this->controller->Spk->getMergeList($value, array(
		            'contain' => array(
		            	'Branch',
		                'Employe',
		                'Truck',
	                    'SpkProduct' => array(
	                        'contain' => array(
	                            'SpkProductTire',
	                        ),
	                    ),
                    	'SpkMechanic',
		            ),
		        ));

                $nopol = Common::hashEmptyField($value, 'Truck.NoPol');
                $transaction_date = Common::hashEmptyField($value, 'Spk.transaction_date', null, array(
                	'date' => 'd M Y',
            	));
                $estimation_date = Common::hashEmptyField($value, 'Spk.estimation_date', null, array(
                	'date' => 'd M Y H:i',
            	));
            	$complete_date = Common::hashEmptyField($value, 'Spk.complete_date', null, array(
                	'date' => 'd M Y H:i',
            	));
                $note = Common::hashEmptyField($value, 'Spk.note', '-', array(
                	'strict' => true,
            	));
                $customStatus = Common::_callTransactionStatus($value, 'Spk', 'transaction_status');

				$result[$key] = array(
					__('No. SPK') => array(
						'text' => Common::hashEmptyField($value, 'Spk.nodoc'),
                		'field_model' => 'Spk.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nodoc\',width:120',
		                'align' => 'left',
					),
					__('Cabang') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code'),
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
					),
					__('No Pol') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code'),
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
					),
					__('Kepala Mekanik') => array(
						'text' => $transaction_date,
                		'field_model' => 'Spk.transaction_date',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'transaction_date\',width:120',
		                'align' => 'left',
					),
					__('Mekanik') => array(
						'text' => $spk_nodoc,
                		'fix_column' => true,
                		'field_model' => 'Spk.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'spk\',width:120',
		                'align' => 'left',
					),
					__('Estimasi Penyelesaian') => array(
						'text' => $type,
                		'field_model' => 'ProductExpenditure.document_type',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'document_type\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Tgl Selesai') => array(
						'text' => $nopol,
                		'field_model' => 'Truck.nopol',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('QTY') => array(
						'text' => $qty,
                		'field_model' => 'ProductExpenditureDetailSerialNumber.qty',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Ban Diganti') => array(
						'text' => $note,
                		'field_model' => 'ProductExpenditure.note',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'note\',width:150',
					),
					__('Keterangan') => array(
						'text' => $code,
                		'field_model' => 'Product.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'code\',width:100',
					),
					__('Status') => array(
						'text' => $customStatus,
                		'field_model' => 'ProductExpenditure.transaction_status',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'status\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}

			if( empty($nextPage) ) {
				$result[$key+1] = array(
					__('No Dokumen') => array(
                		'field_model' => 'ProductExpenditure.nodoc',
					),
					__('Cabang') => array(
                		'field_model' => 'Branch.branch',
					),
					__('Tgl Pengeluaran') => array(
                		'field_model' => 'ProductExpenditure.transaction_date',
					),
					__('No. SPK') => array(
                		'field_model' => 'Spk.nodoc',
					),
					__('Jenis') => array(
                		'field_model' => 'ProductExpenditure.document_type',
					),
					__('NoPol') => array(
                		'field_model' => 'Truck.nopol',
					),
					__('Keterangan') => array(
                		'field_model' => 'ProductExpenditure.note',
					),
					__('Kode Barang') => array(
                		'field_model' => 'Product.code',
					),
					__('Nama Barang') => array(
                		'field_model' => 'Product.name',
					),
					__('Satuan') => array(
                		'field_model' => 'ProductUnit.name',
					),
					__('Serial Number') => array(
						'text' => __('Total'),
                		'field_model' => 'ProductExpenditureDetailSerialNumber.serial_number',
		                'style' => 'font-weight: bold;',
                		'excel' => array(
                			'bold' => true,
            			),
					),
					__('QTY') => array(
						'text' => $totalQty,
                		'field_model' => 'ProductExpenditureDetailSerialNumber.qty',
		                'style' => 'text-align: center;font-weight: bold;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
                			'bold' => true,
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'ProductExpenditureDetail',
		);
	}

	function _callDataSpk_reports ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('SpkProduct');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
			'contain' => array(
				'Spk',
			),
            'order'=> array(
                'Spk.status' => 'DESC',
                'Spk.created' => 'DESC',
                'Spk.id' => 'DESC',
            ),
            'group'=> array(
                'SpkProduct.id',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->SpkProduct->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Spk', $options );

		$this->controller->paginate	= $this->controller->SpkProduct->getData('paginate', $options, array(
			'branch' => false,
		));
		$data = $this->controller->paginate('SpkProduct');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'SpkProduct.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'SpkProduct.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

        App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));

		if( !empty($data) ) {
			$grandtotal_price = 0;

			foreach ($data as $key => $value) {
		        $value = $this->controller->SpkProduct->getMergeList($value, array(
		            'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                                'ProductCategory',
                            ),
                        ),
		            ),
		        ));
		        $value = $this->controller->SpkProduct->Spk->getMergeList($value, array(
		            'contain' => array(
		            	'Laka',
		            	'Branch',
		                'Employe',
		                'Driver',
                        'Driver' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
		                'Truck' => array(
		                	'contain' => array(
		                		'TruckBrand',
		                		'TruckCategory',
	                		),
	                	),
                    	// 'SpkMechanic',
		            ),
		        ));
		        // debug($value);
		        // die();

                $id = Common::hashEmptyField($value, 'Spk.id');
                $branch_id = Common::hashEmptyField($value, 'Spk.branch_id');
                $nodoc = Common::hashEmptyField($value, 'Spk.nodoc');
                $document_type = Common::hashEmptyField($value, 'Spk.document_type');
                $document_type = ucwords($document_type);
                $transaction_date = Common::hashEmptyField($value, 'Spk.transaction_date', null, array(
                	'date' => 'd M Y',
            	));
                $estimation_date = Common::hashEmptyField($value, 'Spk.estimation_date', null, array(
                	'date' => 'd M Y H:i',
            	));
            	$complete_date = Common::hashEmptyField($value, 'Spk.complete_date', null, array(
                	'date' => 'd M Y H:i',
            	));
                $note = Common::hashEmptyField($value, 'Spk.note', '-', array(
                	'strict' => true,
            	));
                $customStatus = Common::_callTransactionStatus($value, 'Spk', 'transaction_status');

                $brand = Common::hashEmptyField($value, 'Truck.TruckBrand.name', '-');
                $brand = Common::hashEmptyField($value, 'TruckBrand.name', $brand);

                $category = Common::hashEmptyField($value, 'Truck.TruckCategory.name', '-');
                $category = Common::hashEmptyField($value, 'TruckCategory.name', $category);
                
                $spk_product_id = Common::hashEmptyField($value, 'SpkProduct.id');
                $product_id = Common::hashEmptyField($value, 'Product.id');
                $productExpenditure = $this->controller->SpkProduct->ProductExpenditureDetail->getExpenditureByProduct($id, $product_id, $spk_product_id, $branch_id);
                $qty_out = Common::hashEmptyField($productExpenditure, 'ProductExpenditureDetailSerialNumber.total_qty');

                if( !empty($qty_out) ) {
                	$price = Common::hashEmptyField($productExpenditure, 'ProductExpenditureDetailSerialNumber.total_price')/$qty_out;
                } else {
                	$price = 0;
                }
                
                $total_price = $qty_out * $price;

				$grandtotal_price += $total_price;

				$result[$key] = array(
					__('Cabang') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code'),
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
					),
					__('Tgl') => array(
						'text' => Common::hashEmptyField($value, 'Spk.transaction_date', null, array(
		                	'date' => 'd M Y',
		            	)),
                		'field_model' => 'Spk.transaction_date',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'transaction_date\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('No. SPK') => array(
						'text' => !empty($view)?$this->Html->link($nodoc, array(
							'controller' => 'spk',
							'action' => 'detail',
							$id,
							'admin' => false,
							'full_base' => true,
						), array(
							'target' => '_blank',
						)):$nodoc,
                		'field_model' => 'Spk.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nodoc\',width:120',
		                'align' => 'left',
                		'fix_column' => true,
					),
					__('Jenis SPK') => array(
						'text' => $document_type,
                		'field_model' => 'Spk.document_type',
		                'data-options' => 'field:\'spk_document_type\',width:100',
					),
					__('Estimasi') => array(
						'text' => $estimation_date,
                		'field_model' => 'Spk.estimation_date',
		                'data-options' => 'field:\'estimation_date\',width:100',
					),
					__('Tgl Selesai') => array(
						'text' => $complete_date,
                		'field_model' => 'Spk.complete_date',
		                'data-options' => 'field:\'complete_date\',width:100',
					),
					__('No. LAKA') => array(
						'text' => Common::hashEmptyField($value, 'Laka.nodoc', '-'),
                		'field_model' => 'Laka.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nolaka\',width:100',
					),
					__('No Pol') => array(
						'text' => Common::hashEmptyField($value, 'Truck.nopol', '-'),
                		'field_model' => 'Truck.nopol',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nopol\',width:100',
					),
					__('Supir') => array(
						'text' => Common::hashEmptyField($value, 'Driver.driver_name'),
                		'field_model' => 'Driver.driver_name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'driver\',width:120',
		                'align' => 'left',
					),
					__('Merek') => array(
						'text' => $brand,
                		'field_model' => 'TruckBrand.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'brand\',width:100',
		                'align' => 'left',
					),
					__('Jenis') => array(
						'text' => $category,
                		'field_model' => 'TruckCategory.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'category\',width:100',
		                'align' => 'left',
					),
					__('Kapasitas') => array(
						'text' => Common::hashEmptyField($value, 'Truck.capacity', '-'),
                		'field_model' => 'Truck.capacity',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'capacity\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Kode Barang') => array(
						'text' => Common::hashEmptyField($value, 'Product.code'),
                		'field_model' => 'Product.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'code\',width:100',
		                'align' => 'left',
					),
					__('Nama Barang') => array(
						'text' => Common::hashEmptyField($value, 'Product.name'),
                		'field_model' => 'Product.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'product\',width:150',
		                'align' => 'left',
					),
					__('Ket. Produk') => array(
						'text' => Common::hashEmptyField($value, 'SpkProduct.note', '-'),
                		'field_model' => 'SpkProduct.note',
		                'data-options' => 'field:\'note_item\',width:120',
					),
					__('QTY') => array(
						'text' => Common::hashEmptyField($value, 'SpkProduct.qty'),
                		'field_model' => 'SpkProduct.qty',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('QTY Keluar') => array(
						'text' => !empty($qty_out)?$qty_out:'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty_out\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Satuan') => array(
						'text' => Common::hashEmptyField($value, 'Product.ProductUnit.name', '-'),
                		'field_model' => 'ProductUnit.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'unit\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga') => array(
						'text' => !empty($price)?Common::getFormatPrice($price):'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'price\',width:120',
		                'align' => 'right',
		                'mainalign' => 'right',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total Harga') => array(
						'text' => !empty($total_price)?Common::getFormatPrice($total_price):'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'total_price\',width:120',
		                'align' => 'right',
		                'mainalign' => 'right',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Keterangan') => array(
						'text' => Common::hashEmptyField($value, 'Spk.note', '-'),
                		'field_model' => 'Spk.note',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'note\',width:100',
					),
					__('Status') => array(
						'text' => $customStatus,
                		'field_model' => 'Spk.transaction_status',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'status\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}

			if( empty($nextPage) ) {
				$result[$key+1] = array(
					__('Cabang') => array(
                		'field_model' => 'Branch.code',
					),
					__('Tgl') => array(
                		'field_model' => 'Spk.transaction_date',
					),
					__('No. SPK') => array(
                		'field_model' => 'Spk.nodoc',
					),
					__('Jenis SPK') => array(
                		'field_model' => 'Spk.document_type',
					),
					__('Estimasi') => array(
                		'field_model' => 'Spk.estimation_date',
					),
					__('Tgl Selesai') => array(
                		'field_model' => 'Spk.complete_date',
					),
					__('No. Laka') => array(
                		'field_model' => 'Truck.nopol',
					),
					__('No Pol') => array(
                		'field_model' => 'Truck.nopol',
					),
					__('Supir') => array(
                		'field_model' => 'Driver.driver_name',
					),
					__('Merek') => array(
                		'field_model' => 'TruckBrand.name',
					),
					__('Jenis') => array(
                		'field_model' => 'TruckCategory.name',
					),
					__('Kapasitas') => array(
                		'field_model' => 'Truck.capacity',
					),
					__('Kode Barang') => array(
                		'field_model' => 'Product.code',
					),
					__('Nama Barang') => array(
                		'field_model' => 'Product.name',
					),
					__('QTY') => array(
                		'field_model' => 'SpkProduct.qty',
					),
					__('QTY Keluar') => array(
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Satuan') => array(
                		'field_model' => 'ProductUnit.name',
					),
					__('Harga') => array(
						'text' => __('Total'),
					),
					__('Total Harga') => array(
						'text' => !empty($grandtotal_price)?Common::getFormatPrice($grandtotal_price):'-',
					),
					__('Keterangan') => array(
                		'field_model' => 'Spk.note',
					),
					__('Status') => array(
                		'field_model' => 'Spk.transaction_status',
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'SpkProduct',
		);
	}

	function _callDataMaintenance_cost_report ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Truck');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->Truck->_callRefineParams($params, $options);
        $branch_id = $this->MkCommon->getConditionGroupBranch( $params, 'Truck', false, 'value' );
    	$year = Common::hashEmptyField($params, 'named.year', date('Y'));

		$this->controller->paginate	= $this->controller->Truck->getData('paginate', $options, true, array(
			'branch' => false,
		));
		$data = $this->controller->paginate('Truck');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Truck.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'Truck.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
			$grandtotalArr = array();

	        App::import('Helper', 'Html');
	        $this->Html = new HtmlHelper(new View(null));

			foreach ($data as $key => $value) {
		        $value = $this->controller->Truck->getMergeList($value, array(
		            'contain' => array(
		                'TruckCategory',
		                'TruckCustomer' => array(
		                	'type' => 'first',
		                	'conditions' => array(
		                		'TruckCustomer.primary' => 1,
	                		),
	                		'contain' => array(
                        		'CustomerNoType',
                			),
	                	),
	                	'TruckBrand',
	                	'TruckCategory',
		            ),
		        ));

                $truck_id = Common::hashEmptyField($value, 'Truck.id');
                $brand = Common::hashEmptyField($value, 'TruckBrand.name', '-');
                $category = Common::hashEmptyField($value, 'TruckCategory.name', '-');
                $nopol = Common::hashEmptyField($value, 'Truck.nopol');
                
				$result[$key] = array(
					__('No Pol') => array(
						'text' => $nopol,
                		'field_model' => 'Truck.nopol',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nopol\',width:100',
					),
					__('Kapasitas') => array(
						'text' => Common::hashEmptyField($value, 'Truck.capacity', '-'),
                		'field_model' => 'Truck.capacity',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'capacity\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Merek') => array(
						'text' => $brand,
                		'field_model' => 'TruckBrand.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'brand\',width:100',
		                'align' => 'left',
					),
					__('Jenis') => array(
						'text' => $category,
                		'field_model' => 'TruckCategory.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'category\',width:100',
		                'align' => 'left',
					),
					__('Tahun') => array(
						'text' => Common::hashEmptyField($value, 'Truck.tahun', '-'),
                		'field_model' => 'Truck.tahun',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'tahun\',width:100',
		                'align' => 'left',
                		'fix_column' => true,
					),
				);
				
				$total = 0;

                for ($i=1; $i <= 12; $i++) {
                	$monthName = date('F', mktime(0, 0, 0, $i, 1));
                	$monthYear = __('%s-%s', $year, date('m', mktime(0, 0, 0, $i, 1)));
                	
                	$jumlah = $this->controller->Truck->Spk->ProductExpenditure->_callMaintenanceCostByTruckMonthly($truck_id, $branch_id, $monthYear);
					$total += $jumlah;
                	$grandtotalArr[$i] = $jumlah + Common::hashEmptyField($grandtotalArr, $i, 0);

                	if( !empty($jumlah) ) {
	                	if( !empty($view) ) {
	                    	$date = __('%s - %s', Common::formatDate($monthYear, '01/m/Y'), Common::formatDate($monthYear, 't/m/Y'));

	                    	$monthLabel = $this->Html->link(Common::getFormatPrice($jumlah), array(
								'controller' => 'products',
								'action' => 'expenditure_reports',
								'nopol' => $nopol,
								'date' => Common::_callUrlEncode($date, true),
							), array(
								'target' => '_blank',
							));
	                    } else {
	                    	$monthLabel = Common::getFormatPrice($jumlah);
	                    }
	                } else {
	                	$monthLabel = '-';
	                }

					$result[$key] = array_merge($result[$key], array(
						$monthName => array(
							'text' => $monthLabel,
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'month_'.$i.'\',width:100',
			                'align' => 'right',
						),
					));
                }

                $result[$key] = array_merge($result[$key], array(
					__('Total') => array(
						'text' => !empty($total)?Common::getFormatPrice($total):'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'month_total\',width:100',
		                'align' => 'right',
					),
				));
			}

			// if( empty($nextPage) || !empty($view) ) {
			if( !empty($view) ) {
				$result[$key+1] = array(
					__('No Pol') => array(
		                'style' => 'text-align: center;',
					),
					__('Kapasitas') => array(
		                'style' => 'text-align: center;',
					),
					__('Merek') => array(
		                'style' => 'text-align: center;',
					),
					__('Jenis') => array(
		                'style' => 'text-align: center;',
					),
					__('Tahun') => array(
						'text' => __('Total'),
		                'style' => 'font-weight: bold;',
                		'excel' => array(
                			'bold' => true,
            			),
					),
				);
				
				$grandtotal_sum = 0;

                for ($i=1; $i <= 12; $i++) {
                	$monthName = date('F', mktime(0, 0, 0, $i, 1));
                	$grandtotal = Common::hashEmptyField($grandtotalArr, $i, 0);

					$result[$key+1] = array_merge($result[$key+1], array(
						$monthName => array(
							'text' => !empty($grandtotal)?Common::getFormatPrice($grandtotal):'-',
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'month_'.$i.'\',width:100',
			                'align' => 'right',
						),
					));
					
					$grandtotal_sum += $grandtotal;
                }

                $result[$key+1] = array_merge($result[$key+1], array(
					__('Total') => array(
						'text' => !empty($grandtotal_sum)?Common::getFormatPrice($grandtotal_sum):'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'month_total\',width:100',
		                'align' => 'right',
					),
				));
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'Truck',
		);
	}

	function _callDataAdjustment_report ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('ProductAdjustmentDetail');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
            'order'=> array(
                'ProductAdjustment.status' => 'DESC',
                'ProductAdjustment.created' => 'DESC',
                'ProductAdjustment.id' => 'DESC',
                'ProductAdjustmentDetail.id' => 'ASC',
            ),
            'group'=> array(
                'ProductAdjustmentDetail.id',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->ProductAdjustmentDetail->ProductAdjustment->_callRefineParams($params, $options);
		$options = $this->controller->ProductAdjustmentDetail->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductAdjustment', $options );

		$this->controller->paginate	= $this->controller->ProductAdjustmentDetail->getData('paginate', $options, array(
			'branch' => false,
			'header' => true,
		));
		$data = $this->controller->paginate('ProductAdjustmentDetail');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductAdjustmentDetail.id');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
				$value = Common::_callUnset($value, array(
					'ProductAdjustmentDetailSerialNumber',
				));

		        $value = $this->controller->ProductAdjustmentDetail->getMergeList($value, array(
		            'contain' => array(
		            	'Product' => array(
		                	'ProductUnit',
	            		),
                        'ProductAdjustmentDetailSerialNumber' => array(
                        	'type' => 'all',
                    	),
		            ),
		        ));
		        $value = $this->controller->ProductAdjustmentDetail->ProductAdjustment->getMergeList($value, array(
		            'contain' => array(
		            	'Branch',
		            ),
		        ));

                $nodoc = Common::hashEmptyField($value, 'ProductAdjustment.nodoc');
                $transaction_date = Common::hashEmptyField($value, 'ProductAdjustment.transaction_date', null, array(
                	'date' => 'd M Y',
            	));
                $note = Common::hashEmptyField($value, 'ProductAdjustmentDetail.note', '-', array(
                	'strict' => true,
            	));
                $customStatus = $this->MkCommon->_callTransactionStatus($value, 'ProductAdjustment', 'transaction_status', $view);
            	$price = Common::hashEmptyField($value, 'ProductAdjustmentDetail.price', 0);

                $branch = Common::hashEmptyField($value, 'Branch.code');
                $code = Common::hashEmptyField($value, 'Product.code');
                $name = Common::hashEmptyField($value, 'Product.name');
                $is_serial_number = Common::hashEmptyField($value, 'Product.is_serial_number');
                $unit = Common::hashEmptyField($value, 'Product.ProductUnit.name');
                $serialNumbers = Common::hashEmptyField($value, 'ProductAdjustmentDetailSerialNumber');
				
				if( !empty($is_serial_number) && !empty($serialNumbers) ) {
                    $serial_numbers = Set::extract('/ProductAdjustmentDetailSerialNumber/serial_number', $serialNumbers);
                    $serial_number = implode(', ', $serial_numbers);
	            } else {
	                $serial_number = __('Automatic');
	            }

				$result[$key] = array(
					__('No Dokumen') => array(
						'text' => $nodoc,
                		'field_model' => 'ProductAdjustment.nodoc',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nodoc\',width:120',
		                'align' => 'left',
					),
					__('Cabang') => array(
						'text' => $branch,
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
					),
					__('Tgl Penyesuaian') => array(
						'text' => $transaction_date,
                		'field_model' => 'ProductAdjustment.transaction_date',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'transaction_date\',width:120',
		                'align' => 'left',
					),
					__('Kode Barang') => array(
						'text' => $code,
                		'fix_column' => true,
                		'field_model' => 'Product.code',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'code\',width:100',
		                'align' => 'left',
					),
					__('Nama Barang') => array(
						'text' => $name,
                		'field_model' => 'Product.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'name\',width:120',
		                'align' => 'left',
					),
					__('Satuan') => array(
						'text' => $unit,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'unit\',width:100',
		                'align' => 'left',
					),
					__('Keterangan') => array(
						'text' => $note,
                		'field_model' => 'ProductAdjustmentDetail.note',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'note\',width:150',
					),
					__('Last Stok') => array(
						'text' => Common::hashEmptyField($value, 'ProductAdjustmentDetail.total_qty', '-'),
                		'field_model' => 'ProductAdjustmentDetail.total_qty',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'total_qty\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Penyesuaian') => array(
						'text' => Common::hashEmptyField($value, 'ProductAdjustmentDetail.qty', '-'),
                		'field_model' => 'ProductAdjustmentDetail.qty',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Selisih') => array(
						'text' => Common::hashEmptyField($value, 'ProductAdjustmentDetail.qty_difference', '-'),
                		'field_model' => 'ProductAdjustmentDetail.qty_difference',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'qty_difference\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga') => array(
						'text' => !empty($price)?Common::getFormatPrice($price):'-',
                		'field_model' => 'ProductAdjustmentDetail.price',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'price\',width:120',
		                'align' => 'right',
		                'mainalign' => 'right',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('No Seri') => array(
						'text' => $serial_number,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'serial_number\',width:80',
					),
					__('Status') => array(
						'text' => $customStatus,
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'status\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
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
			'model' => 'ProductAdjustmentDetail',
		);
	}

	function _callDataMin_stock_report ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Product');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
        // $branch_id = $this->MkCommon->getConditionGroupBranch( $params, 'ProductMinStock', array(), 'value' );

		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params['named']['status_stock'] = Common::hashEmptyField($params, 'named.status_stock', 'stock_minimum_empty');
        // $params['named']['branch_id'] = !empty($branch_id)?$branch_id:Configure::read('__Site.config_branch_id');
		$params['named']['branch'] = false;
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
			'contain' => array(
				'ProductMinStock',
			),
            'order'=> array(
                'ProductMinStock.min_stock' => 'DESC',
                'ViewStock.product_stock_cnt' => 'ASC',
                'Product.created' => 'DESC',
                'Product.id' => 'DESC',
            ),
            'group'=> array(
                'Product.id',
                'ProductMinStock.branch_id',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->Product->_callRefineParams($params, $options);

		$this->controller->paginate	= $this->controller->Product->getData('paginate', $options);
		$data = $this->controller->paginate('Product');
		// debug($this->controller->paginate);die();
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Product.id');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
                $id = Common::hashEmptyField($value, 'Product.id');
                $branch_id = Common::hashEmptyField($value, 'ProductMinStock.branch_id');
                
		        $value = $this->controller->Product->getMergeList($value, array(
		            'contain' => array(
	                	'ProductUnit',
	                	'ProductCategory',
		            ),
		        ));
		        $value = $this->controller->Product->ProductMinStock->getMergeList($value, array(
		            'contain' => array(
	                	'Branch',
		            ),
		        ));
                $value['ViewStock']['product_stock_cnt'] = $this->controller->Product->ProductStock->_callStock($id, $branch_id);

		        $type = Common::hashEmptyField($value, 'Product.type');
                $customType = str_replace('_', ' ', $type);
                $customType = ucwords($customType);

                $stock = Common::hashEmptyField($value, 'ViewStock.product_stock_cnt', 0);
                $min_stock = Common::hashEmptyField($value, 'ProductMinStock.min_stock', 0);
                $minus = $stock - $min_stock;

				$result[$key] = array(
					__('Cabang') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code', '-'),
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: left;',
		                'data-options' => 'field:\'branch\',width:100',
		                'align' => 'left',
					),
					__('Kode') => array(
						'text' => Common::hashEmptyField($value, 'Product.code'),
                		'field_model' => 'Product.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'code\',width:100',
		                'align' => 'left',
					),
					__('Nama') => array(
						'text' => Common::hashEmptyField($value, 'Product.name', '-'),
                		'field_model' => 'Product.name',
		                'style' => 'text-align: left;',
		                'data-options' => 'field:\'name\',width:120',
					),
					__('Tipe') => array(
						'text' => $customType,
                		'field_model' => 'Product.type',
		                'style' => 'text-align: left;',
		                'data-options' => 'field:\'type\',width:100',
		                'align' => 'left',
					),
					__('Satuan') => array(
						'text' => Common::hashEmptyField($value, 'ProductUnit.name', '-'),
                		'field_model' => 'ProductUnit.name',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'unit\',width:100',
		                'align' => 'left',
					),
					__('Grup') => array(
						'text' => Common::hashEmptyField($value, 'ProductCategory.name', '-'),
                		'field_model' => 'ProductCategory.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'category\',width:100',
		                'align' => 'left',
					),
					__('Stok') => array(
						'text' => !empty($stock)?$stock:'-',
                		'field_model' => 'ViewStock.product_stock_cnt',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'product_stock_cnt\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Min. Stok') => array(
						'text' => !empty($min_stock)?$min_stock:'-',
                		'field_model' => 'ProductMinStock.min_stock',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'min_stock\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Kekurangan') => array(
						'text' => !empty($minus)?abs($minus):'-',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'minus\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
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
			'model' => 'Product',
		);
	}

	function _callDataCategory_report ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('ProductCategory');
		$this->controller->loadModel('ProductExpenditureDetail');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
			'group' => array(
				'ProductCategory.id',
			),
            'order'=> array(
                'ProductCategory.id' => 'ASC',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
        
		$options = $this->controller->ProductCategory->_callRefineParams($params, $options);
    	$year = Common::hashEmptyField($params, 'named.year', date('Y'));
        $branch_id = $this->MkCommon->getConditionGroupBranch( $params, 'ProductCategory', false, 'value' );

		$this->controller->paginate	= $this->controller->ProductCategory->getData('paginate', $options, array(
			'branch' => false,
			'header' => true,
		));
		$data = $this->controller->paginate('ProductCategory');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductCategory.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'ProductCategory.nextPage');

        $grandtotal = 0;
        $grandtotal_qty = 0;

		if( !empty($data) ) {
			$grandtotalArr = array();
			$grandtotalQtyArr = array();

	        App::import('Helper', 'Html');
	        $this->Html = new HtmlHelper(new View(null));

			foreach ($data as $key => $value) {
				$id = Common::hashEmptyField($value, 'ProductCategory.id');
		        $value = $this->controller->ProductCategory->getMergeList($value, array(
		            'contain' => array(
	                	'ParentProductCategory' => array(
		                    'uses' => 'ProductCategory',
		                    'primaryKey' => 'id',
		                    'foreignKey' => 'parent_id',
		                ),
		            ),
		        ));

				$result[$key] = array(
					__('Grup Barang') => array(
						'text' => Common::hashEmptyField($value, 'ProductCategory.name'),
                		'field_model' => 'ProductCategory.name',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'product_category_name\',width:100',
					),
					__('Parent') => array(
						'text' => Common::hashEmptyField($value, 'ParentProductCategory.name', '-'),
                		'field_model' => 'ParentProductCategory.name',
		                'align' => 'center',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'parent_product_category_name\',width:100',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
                		'fix_column' => true,
					),
				);
				
				$total = 0;
				$total_qty = 0;

                for ($i=1; $i <= 12; $i++) {
                	$monthName = date('F', mktime(0, 0, 0, $i, 1));
                	$monthYear = __('%s-%s', $year, date('m', mktime(0, 0, 0, $i, 1)));
                	
                	$history = $this->controller->ProductExpenditureDetail->getExpenditureByProductCategoryId($id, $branch_id, $monthYear);

                	$jumlah = Common::hashEmptyField($history, 'ProductHistory.grandtotal', 0);
                	$qty = Common::hashEmptyField($history, 'ProductHistory.total_qty', 0);

					$total += $jumlah;
					$total_qty += $qty;

                	$grandtotalArr[$i] = $jumlah + Common::hashEmptyField($grandtotalArr, $i, 0);
                	$grandtotalQtyArr[$i] = $qty + Common::hashEmptyField($grandtotalQtyArr, $i, 0);

                	if( !empty($jumlah) ) {
	                	if( !empty($view) ) {
	                    	$date = __('%s - %s', Common::formatDate($monthYear, '01/m/Y'), Common::formatDate($monthYear, 't/m/Y'));

	                    	$monthLabel = $this->Html->link(Common::getFormatPrice($jumlah), array(
								'controller' => 'products',
								'action' => 'expenditure_reports',
								'product_category_id' => $id,
								'date' => Common::_callUrlEncode($date, true),
								'status' => 'posting',
							), array(
								'target' => '_blank',
							));
	                    } else {
	                    	$monthLabel = Common::getFormatPrice($jumlah);
	                    }
	                } else {
	                	$monthLabel = '-';
	                }

	                if( !empty($qty) ) {
	                	if( !empty($view) ) {
	                    	$date = __('%s - %s', Common::formatDate($monthYear, '01/m/Y'), Common::formatDate($monthYear, 't/m/Y'));

	                    	$monthQtyLabel = $this->Html->link(Common::getFormatPrice($qty), array(
								'controller' => 'products',
								'action' => 'expenditure_reports',
								'product_category_id' => $id,
								'date' => Common::_callUrlEncode($date, true),
								'status' => 'posting',
							), array(
								'target' => '_blank',
							));
	                    } else {
	                    	$monthQtyLabel = $qty;
	                    }
	                } else {
	                	$monthQtyLabel = '-';
	                }

					$result[$key] = array_merge($result[$key], array(
						$monthName => array(
							'text' => $monthLabel,
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'month_'.$i.'\',width:100',
			                'align' => 'center',
	                		'excel' => array(
	                			'headercolspan' => 2,
	            			),
			                'child' => array(
			                	__('Total') => array(
									'name' => __('Total'),
									'text' => $monthLabel,
					                'style' => 'text-align: center;',
					                'data-options' => 'field:\'month_total_'.$i.'\',width:100',
					                'align' => 'right',
		                		),
			                	__('QTY') => array(
									'name' => __('QTY'),
									'text' => $monthQtyLabel,
					                'style' => 'text-align: center;',
					                'data-options' => 'field:\'month_qty_'.$i.'\',width:100',
					                'align' => 'center',
		                		),
		                	),
						),
					));
                }

                $result[$key] = array_merge($result[$key], array(
					__('Total') => array(
						'text' => __('Total'),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'month_total\',width:100',
		                'align' => 'center',
                		'excel' => array(
                			'headercolspan' => 2,
            			),
		                'child' => array(
		                	__('Total') => array(
								'name' => __('Total'),
								'text' => !empty($total)?Common::getFormatPrice($total):'-',
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_total_'.$i.'\',width:100',
				                'align' => 'right',
	                		),
		                	__('QTY') => array(
								'name' => __('QTY'),
								'text' => !empty($total_qty)?$total_qty:'-',
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_qty_'.$i.'\',width:100',
				                'align' => 'center',
	                		),
	                	),
					),
				));
			}

			if( !empty($view) ) {
				$result[$key+1] = array(
					__('Grup Barang') => array(
		                'style' => 'text-align: center;',
					),
					__('Parent') => array(
						'text' => __('Total'),
		                'style' => 'font-weight: bold;',
                		'excel' => array(
                			'bold' => true,
            			),
					),
				);
				
				$grandtotal_sum = 0;
				$grandtotal_qty_sum = 0;

                for ($i=1; $i <= 12; $i++) {
                	$monthName = date('F', mktime(0, 0, 0, $i, 1));
                	$grandtotal = Common::hashEmptyField($grandtotalArr, $i, 0);
                	$grandtotal_qty = Common::hashEmptyField($grandtotalQtyArr, $i, 0);

					$result[$key+1] = array_merge($result[$key+1], array(
						$monthName => array(
							'text' => $monthName,
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'month_'.$i.'\',width:100',
			                'align' => 'center',
			                'child' => array(
			                	__('Total') => array(
									'name' => __('Total'),
									'text' => !empty($grandtotal)?Common::getFormatPrice($grandtotal):'-',
					                'style' => 'text-align: center;',
					                'data-options' => 'field:\'month_total_'.$i.'\',width:100',
					                'align' => 'right',
		                		),
			                	__('QTY') => array(
									'name' => __('QTY'),
									'text' => !empty($grandtotal_qty)?$grandtotal_qty:'-',
					                'style' => 'text-align: center;',
					                'data-options' => 'field:\'month_qty_'.$i.'\',width:100',
					                'align' => 'center',
		                		),
		                	),
						),
					));
					
					$grandtotal_sum += $grandtotal;
					$grandtotal_qty_sum += $grandtotal_qty;
                }

                $result[$key+1] = array_merge($result[$key+1], array(
					__('Total') => array(
						'text' => __('Total'),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'month_total\',width:100',
		                'align' => 'center',
		                'child' => array(
		                	__('Total') => array(
								'name' => __('Total'),
								'text' => !empty($grandtotal_sum)?Common::getFormatPrice($grandtotal_sum):'-',
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_total_'.$i.'\',width:100',
				                'align' => 'right',
	                		),
		                	__('QTY') => array(
								'name' => __('QTY'),
								'text' => !empty($grandtotal_qty_sum)?$grandtotal_qty_sum:'-',
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_qty_'.$i.'\',width:100',
				                'align' => 'center',
	                		),
	                	),
					),
				));
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'ProductCategory',
		);
	}

	function _callDataIndicator_maintenance ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Truck');
		$this->controller->loadModel('ProductCategoryTarget');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
			'group' => array(
				'Truck.id',
			),
        	'offset' => $offset,
        	'limit' => $limit,
        );
        
		$options = $this->controller->Truck->_callRefineParams($params, $options);
    	$year = Common::hashEmptyField($params, 'named.year', date('Y'));
        $branch_id = $this->MkCommon->getConditionGroupBranch( $params, 'Truck', false, 'value' );

		$this->controller->paginate	= $this->controller->Truck->getData('paginate', $options, true, array(
            'branch' => false,
		));
		$data = $this->controller->paginate('Truck');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Truck.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'Truck.nextPage');

        $grandtotal = 0;
        $grandtotal_qty = 0;

        $targets = $this->controller->ProductCategoryTarget->getData('all');
        $targets = $this->controller->ProductCategoryTarget->getMergeList($targets, array(
            'contain' => array(
                'ProductCategory',
            ),
        ));

		if( !empty($data) ) {
			$grandtotalArr = array();
			$grandtotalQtyArr = array();

	        App::import('Helper', 'Html');
	        $this->Html = new HtmlHelper(new View(null));
		    
		    $this->controller->Truck->Ttuj->virtualFields['total_lead_time'] = 'SUM(Ttuj.arrive_lead_time+Ttuj.back_lead_time)';

			foreach ($data as $key => $value) {
				$id = Common::hashEmptyField($value, 'Truck.id');
				$nopol = Common::hashEmptyField($value, 'Truck.nopol');

				$result[$key] = array(
					__('Truk') => array(
						'text' => !empty($view)?$this->Html->link($nopol, array(
							'controller' => 'revenues',
							'action' => 'detail_ritase',
							$id,
							'admin' => false,
							'full_base' => true,
						), array(
							'target' => '_blank',
						)):$nopol,
                		'field_model' => 'Truck.nopol',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'nopol\',width:120',
					),
				);
				
				if( !empty($targets) ) {
					foreach ($targets as $idx => $target) {
	                	$group_id = Common::hashEmptyField($target, 'ProductCategory.id');
	                	$group = Common::hashEmptyField($target, 'ProductCategory.name');
	                	$target = Common::hashEmptyField($target, 'ProductCategoryTarget.target', 0);

	                	$spk = $this->controller->Truck->Spk->SpkProduct->getData('first', array(
	                		'conditions' => array(
	                			'Spk.truck_id' => $id,
	                			'Product.product_category_id' => $group_id,
	                			'Spk.transaction_status' => 'finish',
	                			'Spk.status' => 1,
                			),
                			'contain' => array(
                				'Spk',
                				'Product',
            				),
            				'order' => array(
            					'Spk.complete_date' => 'DESC',
        					),
                		));
	                	$complete_date = Common::hashEmptyField($spk, 'Spk.complete_date');
	                	$progress = 0;
	                	$optionsTtuj = array(
	                		'conditions' => array(
	                			'Ttuj.truck_id' => $id,
                			),
                		);

	                	if( !empty($complete_date) ) {
            				$complete_date = Common::formatDate($complete_date, 'Y-m-d');
	                		$optionsTtuj['conditions']['Ttuj.ttuj_date >='] = $complete_date;
	                	}

                		$ttuj = $this->controller->Truck->Ttuj->getData('first', $optionsTtuj, true, array(
				            'status' => 'commit',
				            'branch' => false,
				        ));
	                	$total_lead_time = Common::hashEmptyField($ttuj, 'Ttuj.total_lead_time', 0);
                		
                		$progress = $total_lead_time / $target;
                		$progress = $progress * 100;
            			$progress_color = 'green';

            			$total_lead_time = Common::getFormatPrice($total_lead_time);
            			$target = Common::getFormatPrice($target);

                		if( $progress > 60 && $progress < 80 ) {
                			$progress_color = 'yellow';
                		} else if( $progress > 80 ) {
                			$progress_color = 'red';
                		}

						$result[$key] = array_merge($result[$key], array(
							!empty($view)?$group:__('%s (KM)', $group) => array(
								// 'text' => $target,
								'text' => !empty($view)?'<div class="progress xs" style="margin: 0;">
                                    <div class="progress-bar progress-bar-'.$progress_color.'" style="width: '.$progress.'%;"></div>
                                </div>':false,
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\''.$group.'\',width:150',
				                'align' => 'center',
		                		'excel' => array(
		                			'align' => 'center',
	                				'headercolspan' => 2,
		            			),
				                'child' => empty($view)?array(
				                	__('Target') => array(
										'name' => __('Target'),
										'text' => $target,
						                'style' => 'text-align: center;',
						                'data-options' => 'field:\'month_total_'.$idx.'\',width:100',
						                'align' => 'center',
			                		),
				                	__('Pemakaian') => array(
										'name' => __('Pemakaian'),
										'text' => $total_lead_time,
						                'style' => 'text-align: center;',
						                'data-options' => 'field:\'month_qty_'.$idx.'\',width:100',
						                'align' => 'center',
			                		),
								):false,
							),
						));
					}
	            }
			}

			// if( !empty($view) ) {
			// 	$result[$key+1] = array(
			// 		__('Grup Barang') => array(
		 //                'style' => 'text-align: center;',
			// 		),
			// 		__('Parent') => array(
			// 			'text' => __('Total'),
		 //                'style' => 'font-weight: bold;',
   //              		'excel' => array(
   //              			'bold' => true,
   //          			),
			// 		),
			// 	);
				
			// 	$grandtotal_sum = 0;
			// 	$grandtotal_qty_sum = 0;

   //              for ($i=1; $i <= 12; $i++) {
   //              	$monthName = date('F', mktime(0, 0, 0, $i, 1));
   //              	$grandtotal = Common::hashEmptyField($grandtotalArr, $i, 0);
   //              	$grandtotal_qty = Common::hashEmptyField($grandtotalQtyArr, $i, 0);

			// 		$result[$key+1] = array_merge($result[$key+1], array(
			// 			$monthName => array(
			// 				'text' => $monthName,
			//                 'style' => 'text-align: center;',
			//                 'data-options' => 'field:\'month_'.$i.'\',width:100',
			//                 'align' => 'center',
			//                 'child' => array(
			//                 	__('Total') => array(
			// 						'name' => __('Total'),
			// 						'text' => !empty($grandtotal)?Common::getFormatPrice($grandtotal):'-',
			// 		                'style' => 'text-align: center;',
			// 		                'data-options' => 'field:\'month_total_'.$i.'\',width:100',
			// 		                'align' => 'right',
		 //                		),
			//                 	__('QTY') => array(
			// 						'name' => __('QTY'),
			// 						'text' => !empty($grandtotal_qty)?$grandtotal_qty:'-',
			// 		                'style' => 'text-align: center;',
			// 		                'data-options' => 'field:\'month_qty_'.$i.'\',width:100',
			// 		                'align' => 'center',
		 //                		),
		 //                	),
			// 			),
			// 		));
					
			// 		$grandtotal_sum += $grandtotal;
			// 		$grandtotal_qty_sum += $grandtotal_qty;
   //              }

   //              $result[$key+1] = array_merge($result[$key+1], array(
			// 		__('Total') => array(
			// 			'text' => __('Total'),
		 //                'style' => 'text-align: center;',
		 //                'data-options' => 'field:\'month_total\',width:100',
		 //                'align' => 'center',
		 //                'child' => array(
		 //                	__('Total') => array(
			// 					'name' => __('Total'),
			// 					'text' => !empty($grandtotal_sum)?Common::getFormatPrice($grandtotal_sum):'-',
			// 	                'style' => 'text-align: center;',
			// 	                'data-options' => 'field:\'month_total_'.$i.'\',width:100',
			// 	                'align' => 'right',
	  //               		),
		 //                	__('QTY') => array(
			// 					'name' => __('QTY'),
			// 					'text' => !empty($grandtotal_qty_sum)?$grandtotal_qty_sum:'-',
			// 	                'style' => 'text-align: center;',
			// 	                'data-options' => 'field:\'month_qty_'.$i.'\',width:100',
			// 	                'align' => 'center',
	  //               		),
	  //               	),
			// 		),
			// 	));
			// }
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'Truck',
		);
	}

	function _callDataInsurances_report ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Insurance');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->Insurance->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Insurance', $options );

		$this->controller->paginate	= $this->controller->Insurance->getData('paginate', $options, array(
			'branch' => false,
		));
		$data = $this->controller->paginate('Insurance');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Insurance.id');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
                $id = $this->MkCommon->filterEmptyField($value, 'Insurance', 'id');
                $value = $this->controller->Insurance->getMergeList($value, array(
                    'contain' => array(
                        'Branch',
                    ),
                ));
                $value = $this->controller->Insurance->InsurancePayment->getPayment($value, $id);

                $nodoc = Common::hashEmptyField($value, 'Insurance.nodoc');
                $name = Common::hashEmptyField($value, 'Insurance.name');
                $to_name = Common::hashEmptyField($value, 'Insurance.to_name');
                $start_date = Common::hashEmptyField($value, 'Insurance.start_date');
                $end_date = Common::hashEmptyField($value, 'Insurance.end_date');
                $status = Common::hashEmptyField($value, 'Insurance.status');
                $transaction_status = Common::hashEmptyField($value, 'Insurance.transaction_status');
                $total = Common::hashEmptyField($value, 'Insurance.grandtotal', 0);
                $branch_id = Common::hashEmptyField($value, 'Insurance.branch_id');
                $branch = Common::hashEmptyField($value, 'Branch.code');

                if( empty($status) ) {
                    $transaction_status = 'void';
                    $value = Hash::insert($value, 'Insurance.transaction_status', $transaction_status);
                }

                $date = Common::getCombineDate($start_date, $end_date);
                $status = Common::_callTransactionStatus($value, 'Insurance');

                $total_payment = Common::hashEmptyField($value, 'InsurancePayment.grandtotal');
                $sisa = $total - $total_payment;

				$result[$key] = array(
					__('Cabang') => array(
						'text' => $branch,
					),
					__('No. Polis') => array(
						'text' => $nodoc,
					),
					__('Nama Asuransi') => array(
						'text' => $name,
					),
					__('Tgl Insurance') => array(
						'text' => $date,
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Nama Tertanggung') => array(
						'text' => $to_name,
					),
					__('Status') => array(
						'text' => $status,
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Total') => array(
						'text' => !empty($total)?Common::getFormatPrice($total, 2):0,
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total Pembayaran') => array(
						'text' => !empty($total_payment)?Common::getFormatPrice($total_payment, 2):0,
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Sisa') => array(
						'text' => !empty($sisa)?Common::getFormatPrice($sisa, 2):0,
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			}

			$last = $this->controller->Insurance->getData('first', array_merge($options, array(
				'offset' => $offset+$limit,
				'limit' => $limit,
			)), array(
				'branch' => false,
			));

			if( empty($last) ) {
            	$options = Common::_callUnset($options, array(
					'group',
					'limit',
					'offset',
				));

        		$this->controller->Insurance->virtualFields['grandtotal'] = 'SUM(grandtotal)';
				$insurance = $this->controller->Insurance->getData('first', $options, array(
					'branch' => false,
				));
                $payment = $this->controller->Insurance->InsurancePayment->getPayment(array());

				$grandtotal = Common::hashEmptyField($insurance, 'Insurance.grandtotal');
                $grandtotalPayment = Common::hashEmptyField($payment, 'InsurancePayment.grandtotal');
                $grandtotalSisa = $grandtotal - $grandtotalPayment;

				$key++;

				$result[$key] = array(
					__('Cabang') => array(
                		'field_model' => 'Branch.code',
					),
					__('No. Polis') => array(
                		'field_model' => 'Insurance.nodoc',
					),
					__('Nama Asuransi') => array(
                		'field_model' => 'Insurance.name',
					),
					__('Tgl Asuransi') => array(
                		'field_model' => 'Insurance.start_date',
					),
					__('Nama Tertanggung') => array(
                		'field_model' => 'Insurance.to_name',
					),
					__('status') => array(
                		'text' => __('Total'),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total') => array(
                		'text' => !empty($grandtotal)?Common::getFormatPrice($grandtotal, 2):0,
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total Pembayaran') => array(
                		'text' => !empty($grandtotalPayment)?Common::getFormatPrice($grandtotalPayment, 2):0,
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Sisa') => array(
                		'text' => !empty($grandtotalSisa)?Common::getFormatPrice($grandtotalSisa, 2):0,
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'Insurance',
		);
	}

	// function _callDataProfit_loss ( $params, $limit = 30, $offset = 0, $view = false ) {
	// 	$this->controller->loadModel('Truck');

 //        $params_named = Common::hashEmptyField($params, 'named', array(), array(
 //        	'strict' => true,
 //    	));
	// 	$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
	// 	$params = $this->MkCommon->_callRefineParams($params);
 //        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

 //        $date_from = Common::hashEmptyField($params, 'named.DateFrom');
 //        $date_to = Common::hashEmptyField($params, 'named.DateTo');

	// 	$options = array(
 //            'conditions' => array(
 //                'Truck.branch_id' => $allow_branch_id,
 //            ),
 //        	'offset' => $offset,
 //        	'limit' => $limit,
 //        );
	// 	$options = $this->controller->Truck->_callRefineParams($params, $options);

	// 	$this->controller->paginate	= $this->controller->Truck->getData('paginate', $options, true, array(
 //            'branch' => false,
 //        ));
	// 	$data = $this->controller->paginate('Truck');
	// 	$result = array();

	// 	$last_data = end($data);
	// 	$last_id = Common::hashEmptyField($last_data, 'Truck.id');

	// 	if( !empty($data) ) {
 //        	$this->controller->Truck->Revenue->virtualFields['total'] = 'SUM(Revenue.total)';
 //        	$this->controller->Truck->Ttuj->virtualFields['total'] = 'SUM(Ttuj.uang_jalan_1+Ttuj.uang_jalan_2+Ttuj.uang_jalan_extra+Ttuj.commission+Ttuj.uang_kuli_muat+Ttuj.uang_kuli_bongkar+Ttuj.asdp+Ttuj.uang_kawal+Ttuj.uang_keamanan+Ttuj.commission_extra)';

	// 		foreach ($data as $key => $value) {
	// 			$id = Common::hashEmptyField($value, 'Truck.id');
	// 	        $value = $this->controller->Truck->getMergeList($value, array(
	// 	            'contain' => array(
	// 	                'TruckCustomer' => array(
	// 	                    'type' => 'first',
	// 	                    'conditions' => array(
	// 	                        'TruckCustomer.primary' => 1,
	// 	                    ),
	// 	                    'contain' => array(
	// 	                        'CustomerNoType',
	// 	                    ),
	// 	                ),
	// 	                'TruckCategory',
	// 	                'TruckBrand',
	// 	                'Company',
	// 	                'Revenue' => array(
	// 	                	'type' => 'first',
	// 	                	'conditions' => array(
	// 	                		'Revenue.date_revenue >=' => $date_from,
	// 	                		'Revenue.date_revenue <=' => $date_to,
	//                 		),
	// 	                	'elements' => array(
	// 	                		'branch' => false,
	// 	                		'status' => 'commit',
	//                 		),
	//                 	),
	// 	                'Ttuj' => array(
	// 	                	'type' => 'first',
	// 	                    'conditions' => array(
	// 	                        'Ttuj.is_draft' => 0,
	// 	                		'Ttuj.ttuj_date >=' => $date_from,
	// 	                		'Ttuj.ttuj_date <=' => $date_to,
	// 	                    ),
	// 	                	'elements' => array(
	// 	                		'branch' => false,
	//                 		),
	//                 	),
	// 	            ),
	// 	        ));
	// 	        $value = $this->controller->Truck->CashBankDetail->getTotalPerTruck($value, array(
	// 	        	'conditions' => array(
 //                		'CashBank.tgl_cash_bank >=' => $date_from,
 //                		'CashBank.tgl_cash_bank <=' => $date_to,
 //                		'CashBankDetail.truck_id' => $id,
	//         		),
	//         	));
	// 	        $revenue_total = Common::hashEmptyField($value, 'Revenue.total', 0);
	// 	        $ttuj_total = Common::hashEmptyField($value, 'Ttuj.total', 0);
	// 	        $out_total = Common::hashEmptyField($value, 'CashBankDetail.total', 0);
	// 	        $expense = $ttuj_total + $out_total;
	// 	        $er = 0;

	// 	        if( !empty($revenue_total) ) {
	// 	        	$er = $expense/$revenue_total;
	// 	        }

	// 			$result[$key] = array(
	// 				__('No. ID') => array(
	// 					'text' => Common::hashEmptyField($value, 'Truck.id'),
 //                		'field_model' => 'Truck.id',
	// 	                'data-options' => 'field:\'id\',width:100',
	// 				),
	// 				__('Nopol') => array(
	// 					'text' => Common::hashEmptyField($value, 'Truck.nopol'),
 //                		'field_model' => 'Truck.nopol',
	// 	                'data-options' => 'field:\'nopol\',width:100',
	// 				),
	// 				__('Merek') => array(
	// 					'text' => Common::hashEmptyField($value, 'TruckBrand.name'),
 //                		'field_model' => 'TruckBrand.name',
	// 	                'data-options' => 'field:\'brand\',width:100',
 //                		'fix_column' => true,
	// 				),
	// 				__('Jenis') => array(
	// 					'text' => Common::hashEmptyField($value, 'TruckCategory.name'),
 //                		'field_model' => 'TruckCategory.name',
	// 	                'data-options' => 'field:\'category\',width:100',
	// 				),
	// 				__('Tahun') => array(
	// 					'text' => Common::hashEmptyField($value, 'Truck.tahun'),
 //                		'field_model' => 'Truck.tahun',
	// 	                'data-options' => 'field:\'tahun\',width:80',
	// 	                'align' => 'center',
 //                		'excel' => array(
 //                			'align' => 'center',
 //            			),
	// 				),
	// 				__('Alokasi') => array(
	// 					'text' => Common::hashEmptyField($value, 'TruckCustomer.CustomerNoType.code'),
 //                		'field_model' => 'CustomerNoType.code',
	// 	                'data-options' => 'field:\'company\',width:120',
	// 				),
	// 				__('Kapasitas') => array(
	// 					'text' => Common::hashEmptyField($value, 'Truck.capacity', '-'),
 //                		'field_model' => 'Truck.capacity',
	// 	                'style' => 'text-align: center;',
	// 	                'data-options' => 'field:\'capacity\',width:80',
	// 	                'align' => 'center',
 //                		'excel' => array(
 //                			'align' => 'center',
 //            			),
	// 				),
	// 				__('Revenue') => array(
	// 					'text' => Common::getFormatPrice($revenue_total, 2),
	// 	                'style' => 'text-align: right;',
	// 	                'data-options' => 'field:\'revenue\',width:120',
	// 	                'align' => 'center',
 //                		'excel' => array(
 //                			'align' => 'right',
 //            			),
	// 				),
	// 				__('Expense') => array(
	// 					'text' => Common::getFormatPrice($ttuj_total+$out_total, 2),
	// 	                'style' => 'text-align: right;',
	// 	                'data-options' => 'field:\'expense\',width:120',
	// 	                'align' => 'center',
 //                		'excel' => array(
 //                			'align' => 'right',
 //            			),
	// 				),
	// 				__('E/R (%)') => array(
	// 					'text' => Common::getFormatPrice($er, 2),
	// 	                'style' => 'text-align: right;',
	// 	                'data-options' => 'field:\'er\',width:120',
	// 	                'align' => 'center',
 //                		'excel' => array(
 //                			'align' => 'center',
 //            			),
	// 				),
	// 				__('Gross Profit') => array(
	// 					'text' => Common::getFormatPrice($revenue_total-$expense, 2),
	// 	                'style' => 'text-align: right;',
	// 	                'data-options' => 'field:\'gross_profit\',width:120',
	// 	                'align' => 'center',
 //                		'excel' => array(
 //                			'align' => 'right',
 //            			),
	// 				),
	// 			);
	// 		}

	// 		$last = $this->controller->Truck->getData('first', array_merge($options, array(
	// 			'offset' => $offset+$limit,
	// 			'limit' => $limit,
	// 		)), array(
	// 			'branch' => false,
	// 		));

	// 		if( empty($last) ) {
 //            	$options = Common::_callUnset($options, array(
	// 				'group',
	// 				'limit',
	// 				'offset',
	// 			));

	// 			$revenue = $this->controller->Truck->Revenue->getData('first', array(
 //                	'conditions' => array(
 //                		'Truck.status' => 1,
 //                		'Revenue.date_revenue >=' => $date_from,
 //                		'Revenue.date_revenue <=' => $date_to,
 //            		),
 //            		'contain' => array(
 //            			'Truck',
 //        			),
	// 			), array(
 //            		'branch' => false,
 //            		'status' => 'commit',
	// 			));
 //                $ttuj = $this->controller->Truck->Ttuj->getData('first', array(
 //                	'conditions' => array(
 //                		'Truck.status' => 1,
 //                        'Ttuj.is_draft' => 0,
 //                		'Ttuj.ttuj_date >=' => $date_from,
 //                		'Ttuj.ttuj_date <=' => $date_to,
 //            		),
 //            		'contain' => array(
 //            			'Truck',
 //        			),
	// 			), array(
 //            		'branch' => false,
	// 			));
	// 	        $cash_out = $this->controller->Truck->CashBankDetail->getTotalPerTruck(array(), array(
	// 	        	'conditions' => array(
 //                		'Truck.status' => 1,
 //                		'CashBank.tgl_cash_bank >=' => $date_from,
 //                		'CashBank.tgl_cash_bank <=' => $date_to,
	//         		),
 //            		'contain' => array(
 //            			'Truck',
 //            			'CashBank',
 //        			),
	//         	));

	// 			$revenue_total = Common::hashEmptyField($revenue, 'Revenue.total', 0);
 //                $ttuj_total = Common::hashEmptyField($ttuj, 'Ttuj.total', 0);
	// 	        $out_total = Common::hashEmptyField($cash_out, 'CashBankDetail.total', 0);
	// 	        $expense = $ttuj_total + $out_total;
	// 	        $er = 0;

	// 	        if( !empty($revenue_total) ) {
	// 	        	$er = $expense/$revenue_total;
	// 	        }

	// 			$key++;

	// 			$result[$key] = array(
	// 				__('No. ID') => array(
 //                		'field_model' => 'Truck.id',
	// 				),
	// 				__('Nopol') => array(
 //                		'field_model' => 'Truck.nopol',
	// 				),
	// 				__('Merek') => array(
 //                		'field_model' => 'TruckBrand.name',
	// 				),
	// 				__('Jenis') => array(
 //                		'field_model' => 'TruckCategory.name',
	// 				),
	// 				__('Tahun') => array(
 //                		'field_model' => 'Truck.tahun',
	// 				),
	// 				__('Alokasi') => array(
 //                		'field_model' => 'CustomerNoType.code',
	// 				),
	// 				__('Kapasitas') => array(
	// 					'text' => __('Total'),
	// 				),
	// 				__('Revenue') => array(
	// 					'text' => Common::getFormatPrice($revenue_total, 2),
 //                		'excel' => array(
 //                			'align' => 'right',
 //            			),
	// 				),
	// 				__('Expense') => array(
	// 					'text' => Common::getFormatPrice($ttuj_total+$out_total, 2),
 //                		'excel' => array(
 //                			'align' => 'right',
 //            			),
	// 				),
	// 				__('E/R (%)') => array(
	// 					'text' => Common::getFormatPrice($er, 2),
 //                		'excel' => array(
 //                			'align' => 'center',
 //            			),
	// 				),
	// 				__('Gross Profit') => array(
	// 					'text' => Common::getFormatPrice($revenue_total-$expense, 2),
 //                		'excel' => array(
 //                			'align' => 'right',
 //            			),
	// 				),
	// 			);
	// 		}
	// 	}

	// 	return array(
	// 		'data' => $result,
	// 		'last_id' => $last_id,
	// 		'model' => 'Truck',
	// 	);
	// }


	function _callProfitLossRecursive ( $options ) {
		$data = Common::hashEmptyField($options, 'data');
		$tmp = Common::hashEmptyField($options, 'tmp');
		$params = Common::hashEmptyField($options, 'params');
		$summaryBalances = Common::hashEmptyField($options, 'summaryBalances');
		$view = Common::hashEmptyField($options, 'view');
		$result = Common::hashEmptyField($options, 'result', array());

		$data = Common::hashEmptyField($data, 'data');
		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$dateTo = Common::hashEmptyField($params, 'named.dateTo');
		$MonthTo = Common::hashEmptyField($params, 'named.MonthTo', $dateTo);
		$flag = true;
		$monthHeaderArr = array();
		$MonthFromTmp = $MonthFrom;

		if( !empty($view) ) {
			$width = false;
		} else {
			$width = 15;
		}

		while ($flag) {
			$month_name = Common::formatDate($MonthFromTmp, 'F Y');
			$month = Common::formatDate($MonthFromTmp, 'Ym');

			$monthHeaderArr[$month_name] = array(
				'text' => '',
                'data-options' => 'field:\'month_'.$month.'\',width:150',
                'align' => 'right',
                'mainalign' => 'center',
        		'excel' => array(
        			'align' => 'center',
    			),
			);

			$nextMonth = strtotime('+1 MONTH', strtotime($MonthFromTmp));
			$MonthFromTmp = date('Y-m', $nextMonth);

			if( $MonthFromTmp > $MonthTo ) {
				$flag = false;
			}
		}

		if( !empty($data) ) {
			foreach ($data as $id => $value) {
				$name = Common::hashEmptyField($value, 'name');
				$coa_parent_id = Common::hashEmptyField($value, 'parent_id');
				$coa_level = Common::hashEmptyField($value, 'level');
				$padding_left = $coa_level * 10;

				if( !empty($view) ) {
					$label = $this->Html->tag('strong', $name, array(
						'style' => __('padding-left:%spx;', $padding_left),
					));
				} else {
					$label = $name;
				}

				$monthHeaderArr[__('Total')] = array(
					'text' => '',
	                'data-options' => 'field:\'total_'.$id.'\',width:150',
	                'align' => 'right',
	                'mainalign' => 'center',
	        		'excel' => array(
	        			'align' => 'center',
	    			),
				);

				$result[] = array_merge(array(
					__('COA') => array(
						'text' => $label,
                		'field_model' => 'Coa.coa_name',
		                'style' => 'text-align: left;font-weight:bold;',
		                'data-options' => 'field:\'coa_name\',width:250',
                		'excel' => array(
                			'bold' => true,
            			),
                		'fix_column' => true,
					),
				), $monthHeaderArr);

				if( !empty($tmp[$id]) ) {
					foreach ($tmp[$id] as $key => $coa) {
						$coa_id = Common::hashEmptyField($coa, 'Coa.id');
						$coa_type = Common::hashEmptyField($coa, 'Coa.type');
						$parent_id = Common::hashEmptyField($coa, 'Coa.parent_id');
						$coa_name = Common::hashEmptyField($coa, 'Coa.coa_name');
						$parent_name = Common::hashEmptyField($coa, 'CoaParent.coa_name');
						$parent_level = Common::hashEmptyField($coa, 'CoaParent.level');
						$parent_parent_id = Common::hashEmptyField($coa, 'CoaParent.parent_id');
						$level = Common::hashEmptyField($coa, 'Coa.level');
						$padding_left = $level * 10;

						$MonthFromTmp = $MonthFrom;
						$flag = true;
						$monthArr = array();
						$grandtotal = 0;

						while ($flag) {
							$month_name = Common::formatDate($MonthFromTmp, 'F Y');
							$month = Common::formatDate($MonthFromTmp, 'Y_m');
							$balance = Common::hashEmptyField($summaryBalances, __('%s-%s', $coa_id, $MonthFromTmp), 0);

							$monthArr[$month_name] = array(
								'text' => Common::getFormatPrice($balance, 2, '-'),
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_'.$month.'\',width:150',
		                		'mainalign' => 'center',
				                'align' => 'right',
				        		'excel' => array(
		                			'align' => 'right',
				    			),
							);

							if( !empty($this->total_profit_loss['grandtotal'][$coa_type][$MonthFromTmp]) ) {
								$this->total_profit_loss['grandtotal'][$coa_type][$MonthFromTmp] += $balance;
							} else {
								$this->total_profit_loss['grandtotal'][$coa_type][$MonthFromTmp] = $balance;
							}

							if( !empty($this->total_profit_loss['Parent'][$parent_id][$MonthFromTmp]) ) {
								$this->total_profit_loss['Parent'][$parent_id][$MonthFromTmp] += $balance;
							} else {
								$this->total_profit_loss['Parent'][$parent_id][$MonthFromTmp] = $balance;
							}

							$nextMonth = strtotime('+1 MONTH', strtotime($MonthFromTmp));
							$MonthFromTmp = date('Y-m', $nextMonth);

							if( $MonthFromTmp > $MonthTo ) {
								$flag = false;
							}
							
							$grandtotal += $balance;
						}

						$monthArr['Total'] = array(
							'text' => Common::getFormatPrice($grandtotal, 2, '-'),
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'total_'.$coa_id.'\',width:150',
	                		'mainalign' => 'center',
			                'align' => 'right',
			        		'excel' => array(
	                			'align' => 'right',
			    			),
						);

						if( !empty($view) ) {
							$label = $this->Html->tag('div', $coa_name, array(
								'style' => __('padding-left:%spx;', $padding_left),
							));
						} else {
							$label = $coa_name;
						}

						$result[] = array_merge(array(
							__('COA') => array(
								'text' => $label,
		                		'field_model' => 'Coa.coa_name',
				                'style' => 'text-align: left;',
				                'data-options' => 'field:\'coa_name\',width:250',
                				'fix_column' => true,
							),
						), $monthArr);
					}

					if( !empty($this->total_profit_loss['Parent'][$parent_id]) ) {
						$monthArr = array();
						$tmp_grandtotal = 0;

						foreach ($this->total_profit_loss['Parent'][$parent_id] as $grandtotal_month => $grandtotal) {
							$month_name = Common::formatDate($grandtotal_month, 'F Y');
							$month = Common::formatDate($grandtotal_month, 'Y_m');

							if( !empty($this->total_profit_loss['Parent'][$parent_parent_id][$grandtotal_month]) ) {
								$this->total_profit_loss['Parent'][$parent_parent_id][$grandtotal_month] += $grandtotal;
							} else {
								$this->total_profit_loss['Parent'][$parent_parent_id][$grandtotal_month] = $grandtotal;
							}

							$monthArr[$month_name] = array(
								'text' => Common::getFormatPrice($grandtotal, 2, '-'),
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_'.$month.'\',width:150',
		                		'mainalign' => 'center',
				                'align' => 'right',
				        		'excel' => array(
				        			'bold' => true,
		                			'align' => 'right',
				    			),
							);
							
							$tmp_grandtotal += $grandtotal;
						}

						$padding_left = $parent_level * 10;

						if( !empty($view) ) {
							$label = $this->Html->tag('strong', __('Total %s', $parent_name), array(
								'style' => __('padding-left:%spx;', $padding_left),
							));
						} else {
							$label = __('Total %s', $parent_name);
						}

						$monthArr['Total'] = array(
							'text' => Common::getFormatPrice($tmp_grandtotal, 2, '-'),
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'total_'.$parent_id.'\',width:150',
	                		'mainalign' => 'center',
			                'align' => 'right',
			        		'excel' => array(
	                			'align' => 'right',
			    			),
						);

						$result[] = array_merge(array(
							__('COA') => array(
								'text' => $label,
		                		'field_model' => 'Coa.coa_name',
		                		'style' => 'text-align: left;font-weight:bold;',
				                'data-options' => 'field:\'coa_name\',width:250',
				        		'excel' => array(
				        			'bold' => true,
				    			),
							),
						), $monthArr);
					}
				} else {
					$value = Common::_callUnset($value, array(
						'name',
						'parent_id',
						'level',
					));
					$val['data'] = $value;

					$result = $this->_callProfitLossRecursive(array(
			        	'data' => $val,
			        	'tmp' => $tmp,
			        	'params' => $params,
			        	'summaryBalances' => $summaryBalances,
			        	'result' => $result,
			        	'view' => $view,
			    	));

					if( !empty($this->total_profit_loss['Parent'][$id]) ) {
						$monthArr = array();
						$tmp_grandtotal = 0;

						foreach ($this->total_profit_loss['Parent'][$id] as $grandtotal_month => $grandtotal) {
							$month_name = Common::formatDate($grandtotal_month, 'F Y');
							$month = Common::formatDate($grandtotal_month, 'Y_m');

							if( !empty($this->total_profit_loss['Parent'][$coa_parent_id][$grandtotal_month]) ) {
								$this->total_profit_loss['Parent'][$coa_parent_id][$grandtotal_month] += $grandtotal;
							} else {
								$this->total_profit_loss['Parent'][$coa_parent_id][$grandtotal_month] = $grandtotal;
							}

							$monthArr[$month_name] = array(
								'text' => Common::getFormatPrice($grandtotal, 2, '-'),
				                'style' => 'text-align: right;font-weight:bold;',
				                'data-options' => 'field:\'month_'.$month.'\',width:150',
		                		'mainalign' => 'center',
				                'align' => 'right',
				        		'excel' => array(
		                			'align' => 'right',
				        			'bold' => true,
				    			),
							);
							
							$tmp_grandtotal += $grandtotal;
						}

						$padding_left = $coa_level * 10;

						if( !empty($view) ) {
							$label = $this->Html->tag('strong', __('Total %s', $name), array(
								'style' => __('padding-left:%spx;', $padding_left),
							));
						} else {
							$label = __('Total %s', $name);
						}

						$monthArr['Total'] = array(
							'text' => Common::getFormatPrice($tmp_grandtotal, 2, '-'),
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'total_'.$id.'\',width:150',
	                		'mainalign' => 'center',
			                'align' => 'right',
			        		'excel' => array(
	                			'align' => 'right',
			    			),
						);

						$result[] = array_merge(array(
							__('COA') => array(
								'text' => $label,
		                		'field_model' => 'Coa.coa_name',
		                		'style' => 'text-align: left;font-weight:bold;',
				                'data-options' => 'field:\'coa_name\',width:250',
				        		'excel' => array(
				        			'bold' => true,
				    			),
							),
						), $monthArr);
					}
				}
			}
		}

		return $result;
	}

	function _callDataProfit_loss ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Coa');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$dateTo = Common::hashEmptyField($params, 'named.dateTo');
		$MonthTo = Common::hashEmptyField($params, 'named.MonthTo', $dateTo);
		$cost_center_id = Common::hashEmptyField($params, 'named.cost_center_id');
		$this->total_profit_loss = array();

		App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));

		$options = array(
			'conditions' => array(
				'Coa.level' => 4,
                'Coa.coa_profit_loss >=' => 3,
			),
			'order' => array(
				'Coa.parent_id',
                'Coa.with_parent_code' => 'ASC',
                'Coa.code' => 'ASC',
                'Coa.id' => 'ASC',
			),
        );
		$options = $this->controller->Coa->_callRefineParams($params, $options);
		$data_tmp	= $this->controller->Coa->getData('all', $options);

		$result = array();
		$data = array();
		$tmp = array();
		$coa_ids = array();

		if( !empty($data_tmp) ) {
			foreach ($data_tmp as $key => &$value) {
				$id = Common::hashEmptyField($value, 'Coa.id');
				$parent_id = Common::hashEmptyField($value, 'Coa.parent_id');

				$value = $this->controller->Coa->getMerge($value, $parent_id, 'CoaParent');

				$tmp[$parent_id][] = $value;
				$coa_ids[$id] = $id;
			}
		}

        $parents = $this->controller->Coa->getData('threaded', array(
            'conditions' => array(
                'Coa.coa_profit_loss >=' => 3,
                'Coa.level <>' => 4,
                'Coa.status' => 1,
            ),
            'order' => array(
                'Coa.order_sort' => 'ASC',
                'Coa.order' => 'ASC',
                'Coa.code IS NULL' => 'ASC',
                'Coa.code' => 'ASC',
            )
        ));
        $data = $this->controller->Coa->_callGenerateParent($parents, $tmp);

        $this->controller->User->Journal->virtualFields['balancing'] = 'CASE WHEN Coa.type = \'debit\' THEN SUM(Journal.debit) - SUM(Journal.credit) ELSE SUM(Journal.credit) - SUM(Journal.debit) END';
        $this->controller->User->Journal->virtualFields['date_month'] = 'DATE_FORMAT(Journal.date, \'%Y-%m\')';
        $this->controller->User->Journal->virtualFields['index'] = 'CONCAT(Journal.coa_id, \'-\', DATE_FORMAT(Journal.date, \'%Y-%m\'))';
        $conditionsJournal = array(
            'Journal.coa_id' => $coa_ids,
            'DATE_FORMAT(Journal.date, \'%Y-%m\') >=' => $MonthFrom,
            'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $MonthTo,
        );

        if( !empty($cost_center_id) ) {
        	$conditionsJournal['Journal.cogs_id'] = $cost_center_id;
        }

        $summaryBalances = $this->controller->User->Journal->getData('list', array(
        	'fields' => array(
        		'Journal.index',
        		'Journal.balancing',
    		),
            'conditions' => $conditionsJournal,
            'group' => array(
                'Journal.coa_id',
                'DATE_FORMAT(Journal.date, \'%Y-%m\')',
            ),
        ));

        $result = $this->_callProfitLossRecursive(array(
        	'data' => $data,
        	'tmp' => $tmp,
        	'params' => $params,
        	'summaryBalances' => $summaryBalances,
        	'view' => $view,
    	));

    	if( !empty($result) ) {
			$flag = true;
			$MonthFromTmp = $MonthFrom;
			$grandtotal = 0;

			while ($flag) {
				$month_name = Common::formatDate($MonthFromTmp, 'F Y');
				$month = Common::formatDate($MonthFromTmp, 'Ym');

				if( !empty($this->total_profit_loss['grandtotal']) ) {
					$balance_credit = !empty($this->total_profit_loss['grandtotal']['credit'][$MonthFromTmp])?$this->total_profit_loss['grandtotal']['credit'][$MonthFromTmp]:0;
					$balance_debit = !empty($this->total_profit_loss['grandtotal']['debit'][$MonthFromTmp])?$this->total_profit_loss['grandtotal']['debit'][$MonthFromTmp]:0;

					$balance = $balance_credit - $balance_debit;
				} else {
					$balance = 0;
				}

				$monthHeaderArr[$month_name] = array(
					'text' => Common::getFormatPrice($balance, 2, '-'),
	                'data-options' => 'field:\'month_'.$month.'\',width:150',
	                'align' => 'right',
	                'mainalign' => 'center',
	        		'excel' => array(
	        			'align' => 'right',
	        			'bold' => true,
	    			),
				);

				$nextMonth = strtotime('+1 MONTH', strtotime($MonthFromTmp));
				$MonthFromTmp = date('Y-m', $nextMonth);

				if( $MonthFromTmp > $MonthTo ) {
					$flag = false;
				}
				
				$grandtotal += $balance;
			}

			if( !empty($view) ) {
				$label = $this->Html->tag('strong', __('Laba Rugi'), array(
					'style' => 'padding-left:10px;',
				));
			} else {
				$label = __('Laba Rugi');
			}

			$monthHeaderArr['Total'] = array(
				'text' => Common::getFormatPrice($grandtotal, 2, '-'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'total_labalrugi\',width:150',
        		'mainalign' => 'center',
                'align' => 'right',
        		'excel' => array(
        			'align' => 'right',
    			),
			);

	    	$result[] = array_merge(array(
				__('COA') => array(
					'text' => $label,
	        		'field_model' => 'Coa.coa_name',
	                'style' => 'text-align: left;font-weight:bold;',
	                'data-options' => 'field:\'coa_name\',width:250',
	        		'excel' => array(
	        			'bold' => true,
	    			),
				),
			), $monthHeaderArr);
	    }

		return array(
			'data' => $result,
			'model' => 'Coa',
		);
	}

	function _callBalanceSheetRecursive ( $options ) {
		$data = Common::hashEmptyField($options, 'data');
		$tmp = Common::hashEmptyField($options, 'tmp');
		$params = Common::hashEmptyField($options, 'params');
		$summaryBalances = Common::hashEmptyField($options, 'summaryBalances');
		$lastSummaryProfitLoss = Common::hashEmptyField($options, 'lastSummaryProfitLoss');
		$view = Common::hashEmptyField($options, 'view');
		$result = Common::hashEmptyField($options, 'result', array());

		$data = Common::hashEmptyField($data, 'data');

		if( !empty($view) ) {
			$width = false;
		} else {
			$width = 15;
		}

		if( !empty($data) ) {
			foreach ($data as $id => &$value) {
				$name = Common::hashEmptyField($value, 'name');
				$coa_parent_id = Common::hashEmptyField($value, 'parent_id');
				$coa_level = Common::hashEmptyField($value, 'level');
				$padding_left = $coa_level * 10;

				if( !empty($view) ) {
					$label = $this->Html->tag('strong', $name, array(
						'style' => __('padding-left:%spx;', $padding_left),
					));
				} else {
					$label = $name;
				}

				if( !empty($tmp[$id]) ) {
					foreach ($tmp[$id] as $key => $coa) {
						$coa_id = Common::hashEmptyField($coa, 'Coa.id');
						$coa_type = Common::hashEmptyField($coa, 'Coa.type');
						$parent_id = Common::hashEmptyField($coa, 'Coa.parent_id');
						$coa_name = Common::hashEmptyField($coa, 'Coa.coa_name');
						$parent_name = Common::hashEmptyField($coa, 'CoaParent.coa_name');
						$parent_level = Common::hashEmptyField($coa, 'CoaParent.level');
						$parent_parent_id = Common::hashEmptyField($coa, 'CoaParent.parent_id');
						$level = Common::hashEmptyField($coa, 'Coa.level');
						$saldo_awal = Common::hashEmptyField($coa, 'Coa.balance');
						$is_profit_loss = Common::hashEmptyField($coa, 'Coa.is_profit_loss');
						$padding_left = $level * 10;

						if( !empty($is_profit_loss) ) {
							$balance = Common::hashEmptyField($lastSummaryProfitLoss, 'Journal.profit_loss');
						} else {
							$balance = Common::hashEmptyField($summaryBalances, $coa_id);
							$balance = $saldo_awal + $balance;
						}

						$value['children'][] = array(
							'name' => $coa_name,
			                'parent_id' => $parent_id,
			                'level' => $level,
			                'balance' => $balance,
						);

						if( !empty($value['balance']) ) {
							$value['balance'] += $balance;
						} else {
							$value['balance'] = $balance;
						}
					}
				} else {
					$callSet = array(
						'name',
						'parent_id',
						'level',
						'balance',
					);
					$val['data'] = Common::_callUnset($value, $callSet);
					$dataSet = Common::_callSet($value, $callSet);

					$result = $this->_callBalanceSheetRecursive(array(
			        	'data' => $val,
			        	'tmp' => $tmp,
			        	'params' => $params,
			        	'summaryBalances' => $summaryBalances,
			        	'lastSummaryProfitLoss' => $lastSummaryProfitLoss,
			        	'result' => $result,
			        	'view' => $view,
			    	));
					$value = $dataSet + $result;

					if( !empty($result) ) {
						foreach ($result as $key => $val) {
							$balance = Common::hashEmptyField($val, 'balance');

							if( !empty($value['balance']) ) {
								$value['balance'] += $balance;
							} else {
								$value['balance'] = $balance;
							}
						}
					}
				}
			}
		}

		return $data;
	}

	function _callDataBalance_sheets ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Coa');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$MonthTo = $MonthFrom;

		App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));

		$options = array(
			'conditions' => array(
				'Coa.level' => 4,
                'Coa.coa_balance_sheets <' => 3,
			),
			'order' => array(
				'Coa.parent_id',
                'Coa.with_parent_code' => 'ASC',
                'Coa.code' => 'ASC',
                'Coa.id' => 'ASC',
			),
        );
		$options = $this->controller->Coa->_callRefineParams($params, $options);
		$data_tmp	= $this->controller->Coa->getData('all', $options);

		$result = array();
		$data = array();
		$tmp = array();
		$coa_ids = array();

		if( !empty($data_tmp) ) {
			foreach ($data_tmp as $key => &$value) {
				$id = Common::hashEmptyField($value, 'Coa.id');
				$parent_id = Common::hashEmptyField($value, 'Coa.parent_id');

				$value = $this->controller->Coa->getMerge($value, $parent_id, 'CoaParent');

				$tmp[$parent_id][] = $value;
				$coa_ids[$id] = $id;
			}
		}

        $parents = $this->controller->Coa->getData('threaded', array(
            'conditions' => array(
                'Coa.coa_balance_sheets <' => 3,
                'Coa.level <>' => 4,
                'Coa.status' => 1,
            ),
            'order' => array(
                'Coa.order_sort' => 'ASC',
                'Coa.order' => 'ASC',
                'Coa.code IS NULL' => 'ASC',
                'Coa.code' => 'ASC',
            )
        ));
        $data = $this->controller->Coa->_callGenerateParentByType($parents, $tmp);

        $this->controller->User->Journal->virtualFields['balancing'] = 'CASE WHEN Coa.type = \'debit\' THEN IFNULL(SUM(Journal.debit) - SUM(Journal.credit), 0) ELSE IFNULL(SUM(Journal.credit) - SUM(Journal.debit), 0) END';
        $this->controller->User->Journal->virtualFields['date_month'] = 'DATE_FORMAT(Journal.date, \'%Y-%m\')';
        $this->controller->User->Journal->virtualFields['index'] = 'Journal.coa_id';
        $summaryBalances = $this->controller->User->Journal->getData('list', array(
        	'fields' => array(
        		'Journal.index',
        		'Journal.balancing',
    		),
            'conditions' => array(
                'Journal.coa_id' => $coa_ids,
                'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $MonthFrom,
            ),
            'group' => array(
                'Journal.coa_id',
            ),
        ));

		$coa_profit_loss = $this->controller->Coa->getData('list', array(
        	'fields' => array(
        		'Coa.id',
    		),
			'conditions' => array(
				'Coa.level' => 4,
                'Coa.coa_profit_loss >=' => 3,
			),
        ));
        
        $this->controller->User->Journal->virtualFields['profit_loss'] = 'IFNULL(SUM(Journal.credit) - SUM(Journal.debit), 0)';
        $lastSummaryProfitLoss = $this->controller->User->Journal->getData('first', array(
        	'fields' => array(
        		'Journal.index',
        		'Journal.profit_loss',
    		),
    		'contain' => false,
            'conditions' => array(
                'Journal.coa_id' => $coa_profit_loss,
                'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $MonthFrom,
            ),
        ));

        if( !empty($data) ) {
        	foreach ($data as $type => $value) {
        		$coas = $this->_callBalanceSheetRecursive(array(
		        	'data' => array(
		        		'data' => $value,
	        		),
		        	'tmp' => $tmp,
		        	'params' => $params,
		        	'summaryBalances' => $summaryBalances,
		        	'lastSummaryProfitLoss' => $lastSummaryProfitLoss,
		        	'view' => $view,
		    	));
		        $result[$type] = $coas;

		        if( !empty($coas) ) {
		        	$grandtotal = 0;

		        	foreach ($coas as $key => $val) {
		        		$balance = Common::hashEmptyField($val, 'balance');
	
			        	$grandtotal += $balance;
		        	}
		        	
		        	$result[$type]['balance'] = $grandtotal;
		        }
        	}
        }

		return array(
			'data' => $result,
			'model' => 'Coa',
		);
	}

	function _callCogsDisplay ( $value, $default_options ) {
		$id = Common::hashEmptyField($value, 'Cogs.id');
		$parent_id = Common::hashEmptyField($value, 'Cogs.parent_id');
		$children = Common::hashEmptyField($value, 'children');
		$result = array();

		if( empty($children) ) {
			$options = $default_options;
			$options['conditions']['Journal.cogs_id'] = $id;

            $optionsRev = $options;
            $optionsRev['conditions']['Journal.type'] = array( 'in','revenue','general_ledger','invoice_payment','asset_selling' );
            $summaryRev = $this->controller->User->Journal->find('first', $optionsRev);

            $optionsExp = $options;
            $optionsExp['conditions']['Journal.type'] = array( 'out','document_payment','insurance_payment','lku_payment','ksu_payment','laka_payment','leasing_payment','po_payment','uang_Jalan_commission_payment','biaya_ttuj_payment' );
            $summaryExp = $this->controller->User->Journal->find('first', $optionsExp);

            $optionsMaintain = $options;
            $optionsMaintain['conditions']['Journal.type'] = array( 'spk_payment' );
            $summaryMaintain = $this->controller->User->Journal->find('first', $optionsMaintain);

            $revenue = Common::hashEmptyField($summaryRev, 'Journal.total_debit', 0);
            $expense = Common::hashEmptyField($summaryExp, 'Journal.total_credit', 0);
            $maintenance = Common::hashEmptyField($summaryMaintain, 'Journal.total_credit', 0);
            $out = $expense + $maintenance;
            $er = 0;
            $gross_profit = $revenue - $out;

            if( !empty($revenue) ) {
                $er = $out / $revenue;
            }

			$revenue = Common::getFormatPrice($revenue);
			$expense = Common::getFormatPrice($expense);
			$maintenance = Common::getFormatPrice($maintenance);
			$gross_profit = Common::getFormatPrice($gross_profit);

			if( !empty($parent_id) ) {
            	$excel = array();
			} else {
	            $excel = array(
	    			'bold' => true,
				);
			}
        } else {
        	$revenue = '';
            $expense = '';
            $maintenance = '';
            $out = '';
            $er = '';
            $gross_profit = '';
            $excel = array(
    			'bold' => true,
			);
        }
		
		$result = array(
			__('Cost Center') => array(
				'text' => Common::hashEmptyField($value, 'Cogs.name'),
        		'excel' => $excel,
			),
			__('Revenue') => array(
				'text' => $revenue,
			),
			__('Expense') => array(
				'text' => $expense,
			),
			__('Maintenance') => array(
				'text' => $maintenance,
			),
			__('Gross Profit') => array(
				'text' => $gross_profit,
			),
			__('E/R (%)') => array(
				'text' => $er,
			),
		);

		if( !empty($children) ) {
	    	foreach ($children as $key => $child) {
	    		$result = array_merge($result, $this->_callCogsDisplay($child, $default_options));
	    	}
	    }

		return $result;
	}

	// function _callDataProfit_loss_per_point ( $params, $limit = 30, $offset = 0, $view = false ) {
 //        $params_named = Common::hashEmptyField($params, 'named', array(), array(
 //        	'strict' => true,
 //    	));
	// 	$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
	// 	$params = $this->MkCommon->_callRefineParams($params);

 //        $date_from = Common::hashEmptyField($params, 'named.dateFrom');
 //        $date_to = Common::hashEmptyField($params, 'named.dateTo');
	// 	$result = array();

 //        if( empty($offset) ) {
	//         $options = $this->controller->User->Journal->getData('paginate', array(
	//             'conditions' => array(
	//                 'DATE_FORMAT(Journal.date, \'%Y-%m-%d\') >=' => $date_from,
	//                 'DATE_FORMAT(Journal.date, \'%Y-%m-%d\') <=' => $date_to,
	//             ),
	//             'contain' => false,
	//             'group' => array(
	//                 'Journal.cogs_id',
	//             ),
	//         ));
	// 		$options = $this->controller->User->Journal->_callRefineParams($params, $options);

	// 		$data = $this->controller->User->Cogs->getData('threaded', array(
	//             'conditions' => array(
	//                 'Cogs.status' => 1,
	//             ),
	//             'order' => array(
	//                 'Cogs.order_sort' => 'ASC',
	//                 'Cogs.order' => 'ASC',
	//                 'Cogs.code' => 'ASC',
	//             )
	//         ));

	// 		if( !empty($data) ) {
	// 			foreach ($data as $key => $value) {
	// 				$result = array_merge($result, $this->_callCogsDisplay($value, $options));
	// 			}
	// 		}
	// 	}

	// 	return array(
	// 		'data' => $result,
	// 		'model' => 'Cogs',
	// 	);
	// }

	function _callProfitLossPerPointRecursive ( $options ) {
		$data = Common::hashEmptyField($options, 'data');
		$summaryRev = Common::hashEmptyField($options, 'summaryRev');
		$summaryExp = Common::hashEmptyField($options, 'summaryExp');
		$summaryMaintain = Common::hashEmptyField($options, 'summaryMaintain');
		$summaryOther = Common::hashEmptyField($options, 'summaryOther');

		if( !empty($data) ) {			
			foreach ($data as &$value) {
				$id = Common::hashEmptyField($value, 'Cogs.id');
				$parent_name = Common::hashEmptyField($value, 'Cogs.name');
				$parent_id = Common::hashEmptyField($value, 'Cogs.parent_id');
				$children = Common::hashEmptyField($value, 'children');

				if( !empty($children) ) {
					$total_balance_rev = 0;
					$total_balance_exp = 0;
					$total_balance_maintain = 0;
					$total_balance_other = 0;

					foreach ($children as $key => &$cogs) {
						$cogs_id = Common::hashEmptyField($cogs, 'Cogs.id');
						$child = Common::hashEmptyField($cogs, 'children');
						$balance_rev = 0;
						$balance_exp = 0;
						$balance_maintain = 0;
						$balance_other = 0;

						if( !empty($child) ) {
							$child = $this->_callProfitLossPerPointRecursive(array(
					        	'data' => $child,
					        	'summaryRev' => $summaryRev,
					        	'summaryExp' => $summaryExp,
					        	'summaryMaintain' => $summaryMaintain,
					        	'summaryOther' => $summaryOther,
					    	));

					    	foreach ($child as $key => $val) {
								$balance_rev += Common::hashEmptyField($val, 'Cogs.balance_rev');
								$balance_exp += Common::hashEmptyField($val, 'Cogs.balance_exp');
								$balance_maintain += Common::hashEmptyField($val, 'Cogs.balance_maintain');
								$balance_other += Common::hashEmptyField($val, 'Cogs.balance_other');
					    	}

							$cogs['children'] = $child;
							$cogs['Cogs']['balance_rev'] = $balance_rev;
							$cogs['Cogs']['balance_exp'] = $balance_exp;
							$cogs['Cogs']['balance_maintain'] = $balance_maintain;
							$cogs['Cogs']['balance_other'] = $balance_other;
						} else {
							if( !empty($summaryRev[$cogs_id]) ) {
								$balance_rev = $summaryRev[$cogs_id];
							}
							if( !empty($summaryExp[$cogs_id]) ) {
								$balance_exp = $summaryExp[$cogs_id];
							}
							if( !empty($summaryMaintain[$cogs_id]) ) {
								$balance_maintain = $summaryMaintain[$cogs_id];
							}
							if( !empty($summaryOther[$cogs_id]) ) {
								$balance_other = $summaryOther[$cogs_id];
							}

							$cogs['Cogs']['balance_rev'] = $balance_rev;
							$cogs['Cogs']['balance_exp'] = $balance_exp;
							$cogs['Cogs']['balance_maintain'] = $balance_maintain;
							$cogs['Cogs']['balance_other'] = $balance_other;
						}

						$total_balance_rev += $balance_rev;
						$total_balance_exp += $balance_exp;
						$total_balance_maintain += $balance_maintain;
						$total_balance_other += $balance_other;
					}

					$value['children'] = $children;
					$value['Cogs']['balance_rev'] = $total_balance_rev;
					$value['Cogs']['balance_exp'] = $total_balance_exp;
					$value['Cogs']['balance_maintain'] = $total_balance_maintain;
					$value['Cogs']['balance_other'] = $total_balance_other;
				} else {
					$balance_rev = 0;
					$balance_exp = 0;
					$balance_maintain = 0;
					$balance_other = 0;

					if( !empty($summaryRev[$id]) ) {
						$balance_rev = $summaryRev[$id];
					}
					if( !empty($summaryExp[$id]) ) {
						$balance_exp = $summaryExp[$id];
					}
					if( !empty($summaryMaintain[$id]) ) {
						$balance_maintain = $summaryMaintain[$id];
					}
					if( !empty($summaryOther[$id]) ) {
						$balance_other = $summaryOther[$id];
					}

					$value['Cogs']['balance_rev'] = $balance_rev;
					$value['Cogs']['balance_exp'] = $balance_exp;
					$value['Cogs']['balance_maintain'] = $balance_maintain;
					$value['Cogs']['balance_other'] = $balance_other;
				}
			}
		}

		return $data;
	}

	function _callDataProfit_loss_per_point ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Cogs');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$dateTo = Common::hashEmptyField($params, 'named.dateTo');
		$MonthTo = Common::hashEmptyField($params, 'named.MonthTo', $dateTo);

		$data = $this->controller->User->Cogs->getData('threaded', array(
            'conditions' => array(
                'Cogs.status' => 1,
            ),
            'order' => array(
                'Cogs.order_sort' => 'ASC',
                'Cogs.order' => 'ASC',
                'Cogs.code' => 'ASC',
            )
        ));

        $this->controller->User->Journal->virtualFields['total_debit'] = 'SUM(Journal.debit)';
        $this->controller->User->Journal->virtualFields['total_credit'] = 'SUM(Journal.credit)';
        $options = $this->controller->User->Journal->getData('paginate', array(
            'conditions' => array(
                'DATE_FORMAT(Journal.date, \'%Y-%m\') >=' => $MonthFrom,
                'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $MonthTo,
            ),
            'contain' => array(
            	'Coa',
        	),
            'group' => array(
                'Journal.cogs_id',
            ),
        ));

        $optionsRev = $options;
        $optionsRev['conditions']['Coa.transaction_category'] = 'revenue';
        $optionsRev['fields'] = array(
        	'Journal.cogs_id',
        	'Journal.total_credit',
    	);
        $summaryRev = $this->controller->User->Journal->find('list', $optionsRev);

        $optionsExp = $options;
        $optionsExp['conditions']['Coa.transaction_category'] = 'expense';
        $optionsExp['fields'] = array(
        	'Journal.cogs_id',
        	'Journal.total_debit',
    	);
        $summaryExp = $this->controller->User->Journal->find('list', $optionsExp);

        $optionsMaintain = $options;
        $optionsMaintain['conditions']['Coa.transaction_category'] = 'maintenance';
        $optionsMaintain['fields'] = array(
        	'Journal.cogs_id',
        	'Journal.total_debit',
    	);
        $summaryMaintain = $this->controller->User->Journal->find('list', $optionsMaintain);

        $optionsOther = $options;
        $optionsOther['conditions']['Coa.transaction_category'] = 'other';
        $optionsOther['fields'] = array(
        	'Journal.cogs_id',
        	'Journal.total_debit',
    	);
        $summaryOther = $this->controller->User->Journal->find('list', $optionsOther);

		$result = $this->_callProfitLossPerPointRecursive(array(
        	'data' => $data,
        	'summaryRev' => $summaryRev,
        	'summaryExp' => $summaryExp,
        	'summaryMaintain' => $summaryMaintain,
        	'summaryOther' => $summaryOther,
    	));

    	if( !empty($result) ) {
			$total_balance_rev = 0;
			$total_balance_exp = 0;
			$total_balance_maintain = 0;
			$total_balance_other = 0;

    		foreach ($result as $key => $value) {
				$total_balance_rev += Common::hashEmptyField($value, 'Cogs.balance_rev');
				$total_balance_exp += Common::hashEmptyField($value, 'Cogs.balance_exp');
				$total_balance_maintain += Common::hashEmptyField($value, 'Cogs.balance_maintain');
				$total_balance_other += Common::hashEmptyField($value, 'Cogs.balance_other');
    		}

    		$result = array(
    			'data' => $result,
    			'total_balance_rev' => $total_balance_rev,
    			'total_balance_exp' => $total_balance_exp,
    			'total_balance_maintain' => $total_balance_maintain,
    			'total_balance_other' => $total_balance_other,
			);
    	}

		return array(
			'data' => $result,
			'model' => 'Cogs',
		);
	}

	function _callBudgetRecursive ( $options ) {
		$data = Common::hashEmptyField($options, 'data');
		$tmp = Common::hashEmptyField($options, 'tmp');
		$params = Common::hashEmptyField($options, 'params');
		$summaryBalances = Common::hashEmptyField($options, 'summaryBalances');
		$summaryBudgets = Common::hashEmptyField($options, 'summaryBudgets');
		$view = Common::hashEmptyField($options, 'view');
		$result = Common::hashEmptyField($options, 'result', array());

		$data = Common::hashEmptyField($data, 'data');
		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$dateTo = Common::hashEmptyField($params, 'named.dateTo');
		$MonthTo = Common::hashEmptyField($params, 'named.MonthTo', $dateTo);
		$flag = true;
		$monthHeaderArr = array();
		$MonthFromTmp = $MonthFrom;

		if( !empty($view) ) {
			$width = false;
		} else {
			$width = 15;
		}

		while ($flag) {
			$month_name = Common::formatDate($MonthFromTmp, 'F Y');
			$month = Common::formatDate($MonthFromTmp, 'Ym');

			$monthHeaderArr[$month_name] = array(
				'text' => $month_name,
                'data-options' => 'field:\'month_'.$month.'\',width:100',
                'align' => 'center',
        		'excel' => array(
					'headercolspan' => 4,
        			'align' => 'center',
    			),
                'child' => array(
                	__('Budget') => array(
						'name' => __('Budget'),
						'text' => '',
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'month_budget_'.$month.'\',width:120',
		                'align' => 'right',
		                'mainalign' => 'center',
						'width' => $width,
                		'excel' => array(
                			'align' => 'center',
            			),
            		),
                	__('Saldo') => array(
						'name' => __('Saldo'),
						'text' => '',
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'month_saldo_'.$month.'\',width:120,styler:targetBudget',
		                'align' => 'right',
		                'mainalign' => 'center',
        				'rel' => $month,
						'width' => $width,
                		'excel' => array(
                			'align' => 'center',
            			),
            		),
                	__('Selisih') => array(
						'name' => __('Selisih'),
						'text' => '',
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'month_selisih_'.$month.'\',width:120,styler:targetSelisih',
		                'align' => 'right',
		                'mainalign' => 'center',
        				'rel' => $month,
						'width' => $width,
                		'excel' => array(
                			'align' => 'center',
            			),
            		),
                	__('%') => array(
						'name' => __('%'),
						'text' => '',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'month_percent_'.$month.'\',width:80,styler:targetPercent',
		                'align' => 'center',
		                'mainalign' => 'center',
        				'rel' => $month,
						'width' => $width,
                		'excel' => array(
                			'align' => 'center',
            			),
            		),
            	),
			);

			$nextMonth = strtotime('+1 MONTH', strtotime($MonthFromTmp));
			$MonthFromTmp = date('Y-m', $nextMonth);

			if( $MonthFromTmp > $MonthTo ) {
				$flag = false;
			}
		}

		$labelTotal = __('Total');
		$monthHeaderArr[$labelTotal] = array(
			'text' => $labelTotal,
            'data-options' => 'field:\'month_total\',width:100',
            'align' => 'center',
    		'excel' => array(
				'headercolspan' => 4,
    			'align' => 'center',
			),
            'child' => array(
            	__('Budget') => array(
					'name' => __('Budget'),
					'text' => '',
	                'style' => 'text-align: right;',
	                'data-options' => 'field:\'month_budget_total\',width:120',
	                'align' => 'right',
	                'mainalign' => 'center',
					'width' => $width,
            		'excel' => array(
            			'align' => 'center',
        			),
        		),
            	__('Saldo') => array(
					'name' => __('Saldo'),
					'text' => '',
	                'style' => 'text-align: right;',
	                'data-options' => 'field:\'month_saldo_total\',width:120,styler:targetBudget',
	                'align' => 'right',
	                'mainalign' => 'center',
    				'rel' => $month,
					'width' => $width,
            		'excel' => array(
            			'align' => 'center',
        			),
        		),
            	__('Selisih') => array(
					'name' => __('Selisih'),
					'text' => '',
	                'style' => 'text-align: right;',
	                'data-options' => 'field:\'month_selisih_total\',width:120,styler:targetSelisih',
	                'align' => 'right',
	                'mainalign' => 'center',
    				'rel' => $month,
					'width' => $width,
            		'excel' => array(
            			'align' => 'center',
        			),
        		),
            	__('%') => array(
					'name' => __('%'),
					'text' => '',
	                'style' => 'text-align: center;',
	                'data-options' => 'field:\'month_percent_'.$month.'\',width:80,styler:targetPercent',
	                'align' => 'center',
	                'mainalign' => 'center',
    				'rel' => $month,
					'width' => $width,
            		'excel' => array(
            			'align' => 'center',
        			),
        		),
        	),
		);

		if( !empty($data) ) {
			foreach ($data as $id => $value) {
				$name = Common::hashEmptyField($value, 'name');

				if( !empty($view) ) {
					$label = $this->Html->tag('strong', $name);
				} else {
					$label = $name;
				}

				$result[] = array_merge(array(
					__('COA') => array(
						'text' => $label,
                		'field_model' => 'Coa.coa_name',
		                'style' => 'text-align: left;font-weight:bold;',
		                'data-options' => 'field:\'coa_name\',width:250',
                		'excel' => array(
                			'bold' => true,
							'headerrowspan' => 2,
            			),
                		'fix_column' => true,
					),
				), $monthHeaderArr);

				if( !empty($tmp[$id]) ) {
					foreach ($tmp[$id] as $key => $coa) {
						$coa_id = Common::hashEmptyField($coa, 'Coa.id');
						$coa_name = Common::hashEmptyField($coa, 'Coa.coa_name');

						$MonthFromTmp = $MonthFrom;
						$flag = true;
						$monthArr = array();
						$grandtotal = array();

						while ($flag) {
							$month_name = Common::formatDate($MonthFromTmp, 'F Y');
							$month = Common::formatDate($MonthFromTmp, 'Y_m');
							$balance = Common::hashEmptyField($summaryBalances, __('%s-%s', $coa_id, $MonthFromTmp), 0);
							$budget = Common::hashEmptyField($summaryBudgets, __('%s-%s', $coa_id, $MonthFromTmp), 0);
							$selisih = $budget-$balance;
							$tmpBudget = intval($budget);

							if( !empty($tmpBudget) ) {
								$selisih_percent = ($selisih/$budget)*100;
							} else {
								$selisih_percent = 0;
							}

							$monthArr[$month_name] = array(
								'text' => $month_name,
				                'style' => 'text-align: center;',
				                'data-options' => 'field:\'month_'.$month.'\',width:100',
				                'align' => 'center',
				        		'excel' => array(
				        			'headercolspan' => 4,
        							'align' => 'center',
				    			),
				                'child' => array(
				                	__('Budget') => array(
										'name' => __('Budget'),
										'text' => Common::getFormatPrice($budget, 2, '-'),
						                'style' => 'text-align: right;',
						                'data-options' => 'field:\'month_budget_'.$month.'\',width:120',
						                'align' => 'right',
										'width' => $width,
				                		'excel' => array(
				                			'align' => 'right',
				            			),
				            		),
				                	__('Saldo') => array(
										'name' => __('Saldo'),
										'text' => Common::getFormatPrice($balance, 2, '-'),
						                'style' => 'text-align: right;',
						                'data-options' => 'field:\'month_saldo_'.$month.'\',width:120',
						                'align' => 'right',
										'width' => $width,
				                		'excel' => array(
				                			'align' => 'right',
				            			),
				            		),
				                	__('Selisih') => array(
										'name' => __('Selisih'),
										'text' => Common::getFormatPrice($selisih, 2, '-'),
						                'style' => 'text-align: right;',
						                'data-options' => 'field:\'month_selisih_'.$month.'\',width:120',
						                'align' => 'right',
										'width' => $width,
				                		'excel' => array(
				                			'align' => 'right',
				            			),
				            		),
				                	__('%') => array(
										'name' => __('%'),
										'text' => __('%s%%', round($selisih_percent, 2)),
						                'style' => 'text-align: center;',
						                'data-options' => 'field:\'month_percent_'.$month.'\',width:80',
						                'align' => 'center',
										'width' => $width,
				                		'excel' => array(
				                			'align' => 'center',
				            			),
				            		),
				            	),
							);

							if( !empty($this->total_budgets['total']['budget'][$month_name]) ) {
								$this->total_budgets['total']['budget'][$month_name] += $budget;
							} else {
								$this->total_budgets['total']['budget'][$month_name] = $budget;
							}
							if( !empty($this->total_budgets['total']['balance'][$month_name]) ) {
								$this->total_budgets['total']['balance'][$month_name] += $balance;
							} else {
								$this->total_budgets['total']['balance'][$month_name] = $balance;
							}

							if( !empty($grandtotal['budget']) ) {
								$grandtotal['budget'] += $budget;
							} else {
								$grandtotal['budget'] = $budget;
							}
							if( !empty($grandtotal['balance']) ) {
								$grandtotal['balance'] += $balance;
							} else {
								$grandtotal['balance'] = $balance;
							}

							$nextMonth = strtotime('+1 MONTH', strtotime($MonthFromTmp));
							$MonthFromTmp = date('Y-m', $nextMonth);

							if( $MonthFromTmp > $MonthTo ) {
								$flag = false;
							}
						}

						$total_budget = Common::hashEmptyField($grandtotal, 'budget');
						$total_balance = Common::hashEmptyField($grandtotal, 'balance');
						$total_selisih = $total_budget-$total_balance;
						$tmpTotalSelisih = intval($total_budget);

						if( !empty($tmpTotalSelisih) ) {
							$total_selisih_percent = ($total_selisih/$total_budget)*100;
						} else {
							$total_selisih_percent = 0;
						}

						$monthArr[$labelTotal] = array(
							'text' => $labelTotal,
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'month_total\',width:100',
			                'align' => 'center',
			        		'excel' => array(
			        			'headercolspan' => 4,
    							'align' => 'center',
			    			),
			                'child' => array(
			                	__('Budget') => array(
									'name' => __('Budget'),
									'text' => Common::getFormatPrice($total_budget, 2, '-'),
					                'style' => 'text-align: right;',
					                'data-options' => 'field:\'month_budget_total\',width:120',
					                'align' => 'right',
									'width' => $width,
			                		'excel' => array(
			                			'align' => 'right',
			            			),
			            		),
			                	__('Saldo') => array(
									'name' => __('Saldo'),
									'text' => Common::getFormatPrice($total_balance, 2, '-'),
					                'style' => 'text-align: right;',
					                'data-options' => 'field:\'month_saldo_total\',width:120',
					                'align' => 'right',
									'width' => $width,
			                		'excel' => array(
			                			'align' => 'right',
			            			),
			            		),
			                	__('Selisih') => array(
									'name' => __('Selisih'),
									'text' => Common::getFormatPrice($total_selisih, 2, '-'),
					                'style' => 'text-align: right;',
					                'data-options' => 'field:\'month_selisih_total\',width:120',
					                'align' => 'right',
									'width' => $width,
			                		'excel' => array(
			                			'align' => 'right',
			            			),
			            		),
			                	__('%') => array(
									'name' => __('%'),
									'text' => __('%s%%', round($total_selisih_percent, 2)),
					                'style' => 'text-align: center;',
					                'data-options' => 'field:\'month_percent_total\',width:80',
					                'align' => 'center',
									'width' => $width,
			                		'excel' => array(
			                			'align' => 'center',
			            			),
			            		),
			            	),
						);

						$result[] = array_merge(array(
							__('COA') => array(
								'text' => $coa_name,
		                		'field_model' => 'Coa.coa_name',
				                'style' => 'text-align: left;',
				                'data-options' => 'field:\'coa_name\',width:250',
                				'fix_column' => true,
							),
						), $monthArr);
					}
				} else {
					$value = Common::_callUnset($value, array(
						'parent_id',
						'level',
						'name',
					));
					$val['data'] = $value;

					$result = $this->_callBudgetRecursive(array(
			        	'data' => $val,
			        	'tmp' => $tmp,
			        	'params' => $params,
			        	'summaryBalances' => $summaryBalances,
			        	'summaryBudgets' => $summaryBudgets,
			        	'result' => $result,
			    	));
				}
			}
		}

		return $result;
	}

	function _callDataBudget_report ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Coa');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$dateTo = Common::hashEmptyField($params, 'named.dateTo');
		$MonthTo = Common::hashEmptyField($params, 'named.MonthTo', $dateTo);
		$this->total_budgets = array();

		App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));

		$options = array(
			'conditions' => array(
				'Coa.level' => 4,
			),
			'order' => array(
				'Coa.parent_id',
                'Coa.with_parent_code' => 'ASC',
                'Coa.code' => 'ASC',
                'Coa.id' => 'ASC',
			),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->Coa->_callRefineParams($params, $options);
		$this->controller->paginate	= $this->controller->Coa->getData('paginate', $options);
		$data_tmp = $this->controller->paginate('Coa');

		$result = array();
		$data = array();
		$tmp = array();
		$coa_ids = array();

		if( !empty($data_tmp) ) {
			foreach ($data_tmp as $key => $value) {
				$id = Common::hashEmptyField($value, 'Coa.id');
				$parent_id = Common::hashEmptyField($value, 'Coa.parent_id');

				$tmp[$parent_id][] = $value;
				$coa_ids[$id] = $id;
			}
		}

        $parents = $this->controller->Coa->getData('threaded', array(
            'conditions' => array(
                'Coa.level <>' => 4,
                'Coa.status' => 1,
            ),
            'order' => array(
                'Coa.order_sort' => 'ASC',
                'Coa.order' => 'ASC',
                'Coa.code IS NULL' => 'ASC',
                'Coa.code' => 'ASC',
            )
        ));
        $data = $this->controller->Coa->_callGenerateParent($parents, $tmp);

        $this->controller->User->Journal->virtualFields['balancing'] = 'CASE WHEN Coa.type = \'debit\' THEN SUM(Journal.debit) - SUM(Journal.credit) ELSE SUM(Journal.credit) - SUM(Journal.debit) END';
        $this->controller->User->Journal->virtualFields['date_month'] = 'DATE_FORMAT(Journal.date, \'%Y-%m\')';
        $this->controller->User->Journal->virtualFields['index'] = 'CONCAT(Journal.coa_id, \'-\', DATE_FORMAT(Journal.date, \'%Y-%m\'))';
        $summaryBalances = $this->controller->User->Journal->getData('list', array(
        	'fields' => array(
        		'Journal.index',
        		'Journal.balancing',
    		),
            'conditions' => array(
                'Journal.coa_id' => $coa_ids,
                'DATE_FORMAT(Journal.date, \'%Y-%m\') >=' => $MonthFrom,
                'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $MonthTo,
            ),
            'group' => array(
                'Journal.coa_id',
                'DATE_FORMAT(Journal.date, \'%Y-%m\')',
            ),
        ));

        $this->controller->Coa->Budget->BudgetDetail->virtualFields['index'] = 'CONCAT(Budget.coa_id, \'-\', CONCAT(Budget.year, \'-\', LPAD(BudgetDetail.month, 2, \'0\')))';
        $summaryBudgets = $this->controller->Coa->Budget->BudgetDetail->getData('list', array(
        	'fields' => array(
        		'BudgetDetail.index',
        		'BudgetDetail.budget',
    		),
            'conditions' => array(
                'Budget.coa_id' => $coa_ids,
                'CONCAT(Budget.year, \'-\', LPAD(BudgetDetail.month, 2, \'0\')) >=' => $MonthFrom,
                'CONCAT(Budget.year, \'-\', LPAD(BudgetDetail.month, 2, \'0\')) <=' => $MonthTo,
            	'Budget.status' => 1,
            	'BudgetDetail.status' => 1,
            ),
            'contain' => array(
            	'Budget',
        	),
            'group' => array(
                'Budget.coa_id',
                'CONCAT(Budget.year, \'-\', LPAD(BudgetDetail.month, 2, \'0\'))',
            ),
        ));

        $result = $this->_callBudgetRecursive(array(
        	'data' => $data,
        	'tmp' => $tmp,
        	'params' => $params,
        	'summaryBalances' => $summaryBalances,
        	'summaryBudgets' => $summaryBudgets,
        	'view' => $view,
    	));

    	$MonthFromTmp = $MonthFrom;
		$flag = true;
		$monthArr = array();
		$grandtotal = array();

		if( !empty($view) ) {
			$width = false;
		} else {
			$width = 15;
		}

		while ($flag) {
			$month_name = Common::formatDate($MonthFromTmp, 'F Y');
			$month = Common::formatDate($MonthFromTmp, 'Y_m');
			$balance = Common::hashEmptyField($this->total_budgets, __('total.balance.%s', $month_name), 0);
			$budget = Common::hashEmptyField($this->total_budgets, __('total.budget.%s', $month_name), 0);
			$selisih = $budget-$balance;
			$tmpBudget = intval($budget);

			if( !empty($tmpBudget) ) {
				$selisih_percent = ($selisih/$budget)*100;
			} else {
				$selisih_percent = 0;
			}

			$monthArr[$month_name] = array(
				'text' => $month_name,
                'style' => 'text-align: center;',
                'data-options' => 'field:\'month_'.$month.'\',width:100',
                'align' => 'center',
        		'excel' => array(
        			'headercolspan' => 4,
					'align' => 'center',
    			),
                'child' => array(
                	__('Budget') => array(
						'name' => __('Budget'),
						'text' => Common::getFormatPrice($budget, 2, '-'),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'month_budget_'.$month.'\',width:120',
		                'align' => 'right',
						'width' => $width,
                		'excel' => array(
                			'align' => 'right',
            			),
            		),
                	__('Saldo') => array(
						'name' => __('Saldo'),
						'text' => Common::getFormatPrice($balance, 2, '-'),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'month_saldo_'.$month.'\',width:120',
		                'align' => 'right',
						'width' => $width,
                		'excel' => array(
                			'align' => 'right',
            			),
            		),
                	__('Selisih') => array(
						'name' => __('Selisih'),
						'text' => Common::getFormatPrice($selisih, 2, '-'),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'month_selisih_'.$month.'\',width:120',
		                'align' => 'right',
						'width' => $width,
                		'excel' => array(
                			'align' => 'right',
            			),
            		),
                	__('%') => array(
						'name' => __('%'),
						'text' => __('%s%%', round($selisih_percent, 2)),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'month_percent_'.$month.'\',width:80',
		                'align' => 'center',
						'width' => $width,
                		'excel' => array(
                			'align' => 'center',
            			),
            		),
            	),
			);

			if( !empty($grandtotal['budget']) ) {
				$grandtotal['budget'] += $budget;
			} else {
				$grandtotal['budget'] = $budget;
			}
			if( !empty($grandtotal['balance']) ) {
				$grandtotal['balance'] += $balance;
			} else {
				$grandtotal['balance'] = $balance;
			}

			$nextMonth = strtotime('+1 MONTH', strtotime($MonthFromTmp));
			$MonthFromTmp = date('Y-m', $nextMonth);

			if( $MonthFromTmp > $MonthTo ) {
				$flag = false;
			}
		}

		$total_budget = Common::hashEmptyField($grandtotal, 'budget');
		$total_balance = Common::hashEmptyField($grandtotal, 'balance');
		$total_selisih = $total_budget-$total_balance;
		$tmpTotalBudget = $total_budget;

		if( !empty($tmpTotalBudget) ) {
			$total_selisih_percent = ($total_selisih/$total_budget)*100;
		} else {
			$total_selisih_percent = 0;
		}

		$labelTotal = __('Total');
		$monthArr[$labelTotal] = array(
			'text' => $labelTotal,
            'style' => 'text-align: center;',
            'data-options' => 'field:\'month_total\',width:100',
            'align' => 'center',
    		'excel' => array(
    			'headercolspan' => 4,
				'align' => 'center',
			),
            'child' => array(
            	__('Budget') => array(
					'name' => __('Budget'),
					'text' => Common::getFormatPrice($total_budget, 2, '-'),
	                'style' => 'text-align: right;',
	                'data-options' => 'field:\'month_budget_total\',width:120',
	                'align' => 'right',
					'width' => $width,
            		'excel' => array(
            			'align' => 'right',
        			),
        		),
            	__('Saldo') => array(
					'name' => __('Saldo'),
					'text' => Common::getFormatPrice($total_balance, 2, '-'),
	                'style' => 'text-align: right;',
	                'data-options' => 'field:\'month_saldo_total\',width:120',
	                'align' => 'right',
					'width' => $width,
            		'excel' => array(
            			'align' => 'right',
        			),
        		),
            	__('Selisih') => array(
					'name' => __('Selisih'),
					'text' => Common::getFormatPrice($total_selisih, 2, '-'),
	                'style' => 'text-align: right;',
	                'data-options' => 'field:\'month_selisih_total\',width:120',
	                'align' => 'right',
					'width' => $width,
            		'excel' => array(
            			'align' => 'right',
        			),
        		),
            	__('%') => array(
					'name' => __('%'),
					'text' => __('%s%%', round($total_selisih_percent, 2)),
	                'style' => 'text-align: center;',
	                'data-options' => 'field:\'month_percent_total\',width:80',
	                'align' => 'center',
					'width' => $width,
            		'excel' => array(
            			'align' => 'center',
        			),
        		),
        	),
		);

		$result[] = array_merge(array(
			__('COA') => array(
				'text' => __('Total'),
        		'field_model' => 'Coa.coa_name',
                'style' => 'text-align: left;',
                'data-options' => 'field:\'coa_name\',width:250',
				'fix_column' => true,
			),
		), $monthArr);

		return array(
			'data' => $result,
			'model' => 'Coa',
		);
	}

	function _callDataProfit_loss_per_truck ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Truck');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);
        $allow_branch_id = Configure::read('__Site.config_allow_branch_id');

		$dateFrom = Common::hashEmptyField($params, 'named.dateFrom');
		$MonthFrom = Common::hashEmptyField($params, 'named.MonthFrom', $dateFrom);
		$dateTo = Common::hashEmptyField($params, 'named.dateTo');
		$MonthTo = Common::hashEmptyField($params, 'named.MonthTo', $dateTo);

		$options = array(
            'conditions' => array(
                'Truck.branch_id' => $allow_branch_id,
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->Truck->_callRefineParams($params, $options);

		$this->controller->paginate	= $this->controller->Truck->getData('paginate', $options, true, array(
            'branch' => false,
        ));
		$data = $this->controller->paginate('Truck');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'Truck.id');

		if( !empty($data) ) {

	        $this->controller->User->Journal->virtualFields['total_debit'] = 'SUM(Journal.debit)';
	        $this->controller->User->Journal->virtualFields['total_credit'] = 'SUM(Journal.credit)';
        	// $this->controller->Truck->Revenue->virtualFields['total'] = 'SUM(Revenue.total)';
        	// $this->controller->Truck->Ttuj->virtualFields['total'] = 'SUM(Ttuj.uang_jalan_1+Ttuj.uang_jalan_2+Ttuj.uang_jalan_extra+Ttuj.commission+Ttuj.uang_kuli_muat+Ttuj.uang_kuli_bongkar+Ttuj.asdp+Ttuj.uang_kawal+Ttuj.uang_keamanan+Ttuj.commission_extra)';


	        $optionsCost = $this->controller->User->Journal->getData('paginate', array(
	            'conditions' => array(
	                'DATE_FORMAT(Journal.date, \'%Y-%m\') >=' => $MonthFrom,
	                'DATE_FORMAT(Journal.date, \'%Y-%m\') <=' => $MonthTo,
	            ),
	            'contain' => array(
	            	'Coa',
	        	),
	        ));
	        $revenue_total = 0;
	        $expense_total = 0;
	        $maintenance_total = 0;
	        $other_total = 0;

			foreach ($data as $key => $value) {
				$id = Common::hashEmptyField($value, 'Truck.id');
		        $value = $this->controller->Truck->getMergeList($value, array(
		            'contain' => array(
		                'TruckCustomer' => array(
		                    'type' => 'first',
		                    'conditions' => array(
		                        'TruckCustomer.primary' => 1,
		                    ),
		                    'contain' => array(
		                        'CustomerNoType',
		                    ),
		                ),
		                'TruckCategory',
		                'TruckBrand',
		                'Company',
		                // 'Revenue' => array(
		                // 	'type' => 'first',
		                // 	'conditions' => array(
		                // 		'Revenue.date_revenue >=' => $MonthFrom,
		                // 		'Revenue.date_revenue <=' => $MonthTo,
	                	// 	),
		                // 	'elements' => array(
		                // 		'branch' => false,
		                // 		'status' => 'commit',
	                	// 	),
	                	// ),
		                // 'Ttuj' => array(
		                // 	'type' => 'first',
		                //     'conditions' => array(
		                //         'Ttuj.is_draft' => 0,
		                // 		'Ttuj.ttuj_date >=' => $MonthFrom,
		                // 		'Ttuj.ttuj_date <=' => $MonthTo,
		                //     ),
		                // 	'elements' => array(
		                // 		'branch' => false,
	                	// 	),
	                	// ),
		            ),
		        ));
		        // $value = $this->controller->Truck->CashBankDetail->getTotalPerTruck($value, array(
		        // 	'conditions' => array(
          //       		'CashBank.tgl_cash_bank >=' => $MonthFrom,
          //       		'CashBank.tgl_cash_bank <=' => $MonthTo,
          //       		'CashBankDetail.truck_id' => $id,
	        	// 	),
	        	// ));
		        // $revenue_total = Common::hashEmptyField($value, 'Revenue.total', 0);
		        // $ttuj_total = Common::hashEmptyField($value, 'Ttuj.total', 0);
		        // $out_total = Common::hashEmptyField($value, 'CashBankDetail.total', 0);
		        // $expense = $ttuj_total + $out_total;
		        // $er = 0;

		        // if( !empty($revenue_total) ) {
		        // 	$er = $expense/$revenue_total;
		        // }
		        $optionsCostTmp = $optionsCost;
		        $optionsCostTmp['conditions']['Journal.truck_id'] = $id;

		        $optionsRev = $optionsCostTmp;
		        $optionsRev['conditions']['Coa.transaction_category'] = 'revenue';
		        $optionsRev['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_credit',
		    	);
		        $summaryRev = $this->controller->User->Journal->find('first', $optionsRev);

		        $optionsExp = $optionsCostTmp;
		        $optionsExp['conditions']['Coa.transaction_category'] = 'expense';
		        $optionsExp['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_debit',
		    	);
		        $summaryExp = $this->controller->User->Journal->find('first', $optionsExp);

		        $optionsMaintain = $optionsCostTmp;
		        $optionsMaintain['conditions']['Coa.transaction_category'] = 'maintenance';
		        $optionsMaintain['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_debit',
		    	);
		        $summaryMaintain = $this->controller->User->Journal->find('first', $optionsMaintain);

		        $optionsOther = $optionsCostTmp;
		        $optionsOther['conditions']['Coa.transaction_category'] = 'other';
		        $optionsOther['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_debit',
		    	);
		        $summaryOther = $this->controller->User->Journal->find('first', $optionsOther);

		        $revenue = Common::hashEmptyField($summaryRev, 'Journal.total_credit', 0);
	            $expense = Common::hashEmptyField($summaryExp, 'Journal.total_debit', 0);
	            $maintenance = Common::hashEmptyField($summaryMaintain, 'Journal.total_debit', 0);
	            $other = Common::hashEmptyField($summaryMaintain, 'Journal.total_debit', 0);
	            $out = $expense + $maintenance + $other;
	            $er = 0;
	            $gross_profit = $revenue - $out;

	            if( !empty($revenue) ) {
	                $er = $out / $revenue;
	            }

		        $revenue_total += $revenue;
		        $expense_total += $expense;
		        $maintenance_total += $maintenance;
		        $other_total += $other;

				$result[$key] = array(
					__('No. ID') => array(
						'text' => Common::hashEmptyField($value, 'Truck.id'),
                		'field_model' => 'Truck.id',
		                'data-options' => 'field:\'id\',width:100',
					),
					__('Nopol') => array(
						'text' => Common::hashEmptyField($value, 'Truck.nopol'),
                		'field_model' => 'Truck.nopol',
		                'data-options' => 'field:\'nopol\',width:100',
					),
					__('Merek') => array(
						'text' => Common::hashEmptyField($value, 'TruckBrand.name'),
                		'field_model' => 'TruckBrand.name',
		                'data-options' => 'field:\'brand\',width:100',
                		'fix_column' => true,
					),
					__('Jenis') => array(
						'text' => Common::hashEmptyField($value, 'TruckCategory.name'),
                		'field_model' => 'TruckCategory.name',
		                'data-options' => 'field:\'category\',width:100',
					),
					__('Tahun') => array(
						'text' => Common::hashEmptyField($value, 'Truck.tahun'),
                		'field_model' => 'Truck.tahun',
		                'data-options' => 'field:\'tahun\',width:80',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Alokasi') => array(
						'text' => Common::hashEmptyField($value, 'TruckCustomer.CustomerNoType.code'),
                		'field_model' => 'CustomerNoType.code',
		                'data-options' => 'field:\'company\',width:120',
					),
					__('Kapasitas') => array(
						'text' => Common::hashEmptyField($value, 'Truck.capacity', '-'),
                		'field_model' => 'Truck.capacity',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'capacity\',width:80',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Revenue') => array(
						'text' => Common::getFormatPrice($revenue, 2),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'revenue\',width:120',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Biaya Jalan') => array(
						'text' => Common::getFormatPrice($expense, 2),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'expense\',width:120',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Maintenance') => array(
						'text' => Common::getFormatPrice($maintenance, 2),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'maintenance\',width:120',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Biaya Lain-lain') => array(
						'text' => Common::getFormatPrice($other, 2),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'maintenance\',width:120',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('E/R (%)') => array(
						'text' => __('%s%%', round($er, 2)),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'er\',width:120',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Gross Profit') => array(
						'text' => Common::getFormatPrice($gross_profit, 2),
		                'style' => 'text-align: right;',
		                'data-options' => 'field:\'gross_profit\',width:120',
		                'align' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			}

			$last = $this->controller->Truck->getData('first', array_merge($options, array(
				'offset' => $offset+$limit,
				'limit' => $limit,
			)), array(
				'branch' => false,
			));

			if( empty($last) && empty($view) ) {
		        $optionsCostTmp = $optionsCost;

		        $optionsRev = $optionsCostTmp;
		        $optionsRev['conditions']['Coa.transaction_category'] = 'revenue';
		        $optionsRev['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_credit',
		    	);
		        $summaryRev = $this->controller->User->Journal->find('first', $optionsRev);

		        $optionsExp = $optionsCostTmp;
		        $optionsExp['conditions']['Coa.transaction_category'] = 'expense';
		        $optionsExp['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_debit',
		    	);
		        $summaryExp = $this->controller->User->Journal->find('first', $optionsExp);

		        $optionsMaintain = $optionsCostTmp;
		        $optionsMaintain['conditions']['Coa.transaction_category'] = 'maintenance';
		        $optionsMaintain['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_debit',
		    	);
		        $summaryMaintain = $this->controller->User->Journal->find('first', $optionsMaintain);

		        $optionsOther = $optionsCostTmp;
		        $optionsOther['conditions']['Coa.transaction_category'] = 'other';
		        $optionsOther['fields'] = array(
		        	'Journal.cogs_id',
		        	'Journal.total_debit',
		    	);
		        $summaryOther = $this->controller->User->Journal->find('first', $optionsOther);

		        $revenue = Common::hashEmptyField($summaryRev, 'Journal.total_credit', 0);
	            $expense = Common::hashEmptyField($summaryExp, 'Journal.total_debit', 0);
	            $maintenance = Common::hashEmptyField($summaryMaintain, 'Journal.total_debit', 0);
	            $other = Common::hashEmptyField($summaryMaintain, 'Journal.total_debit', 0);
	            $out = $expense + $maintenance + $other;
	            $er = 0;
	            $gross_profit = $revenue - $out;

	            if( !empty($revenue) ) {
	                $er = $out / $revenue;
	            }

				$key++;

				$result[$key] = array(
					__('No. ID') => array(
                		'field_model' => 'Truck.id',
					),
					__('Nopol') => array(
                		'field_model' => 'Truck.nopol',
					),
					__('Merek') => array(
                		'field_model' => 'TruckBrand.name',
					),
					__('Jenis') => array(
                		'field_model' => 'TruckCategory.name',
					),
					__('Tahun') => array(
                		'field_model' => 'Truck.tahun',
					),
					__('Alokasi') => array(
                		'field_model' => 'CustomerNoType.code',
					),
					__('Kapasitas') => array(
						'text' => __('Total'),
					),
					__('Revenue') => array(
						'text' => Common::getFormatPrice($revenue, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Biaya Jalan') => array(
						'text' => Common::getFormatPrice($expense, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Maintenance') => array(
						'text' => Common::getFormatPrice($maintenance, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Biaya Lain-lain') => array(
						'text' => Common::getFormatPrice($other, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('E/R (%)') => array(
						'text' => __('%s%%', round($er, 2)),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Gross Profit') => array(
						'text' => Common::getFormatPrice($gross_profit, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			} else {
				$key++;

	            $out = $expense_total + $maintenance_total + $other_total;
	            $er = 0;
	            $gross_profit = $revenue_total - $out;

	            if( !empty($revenue_total) ) {
	                $er = $out / $revenue_total;
	            }

				$result[$key] = array(
					__('No. ID') => array(
                		'field_model' => 'Truck.id',
					),
					__('Nopol') => array(
                		'field_model' => 'Truck.nopol',
					),
					__('Merek') => array(
                		'field_model' => 'TruckBrand.name',
					),
					__('Jenis') => array(
                		'field_model' => 'TruckCategory.name',
					),
					__('Tahun') => array(
                		'field_model' => 'Truck.tahun',
					),
					__('Alokasi') => array(
                		'field_model' => 'CustomerNoType.code',
					),
					__('Kapasitas') => array(
						'text' => __('Total'),
					),
					__('Revenue') => array(
						'text' => Common::getFormatPrice($revenue_total, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Biaya Jalan') => array(
						'text' => Common::getFormatPrice($expense_total, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Maintenance') => array(
						'text' => Common::getFormatPrice($maintenance_total, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Biaya Lain-lain') => array(
						'text' => Common::getFormatPrice($other_total, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('E/R (%)') => array(
						'text' => __('%s%%', round($er, 2)),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Gross Profit') => array(
						'text' => Common::getFormatPrice($gross_profit, 2),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
				);
			}
		}

		return array(
			'data' => $result,
			'last_id' => $last_id,
			'model' => 'Truck',
		);
	}

	function _callDataUang_jalan ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('UangJalan');
		$this->controller->loadModel('GroupClassification');
		$this->controller->loadModel('City');
		$this->controller->loadModel('GroupMotor');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->UangJalan->_callRefineParams($params, $options);

		$this->controller->paginate	= $this->controller->UangJalan->getData('paginate', $options);
		$data = $this->controller->paginate('UangJalan');
		$result = array();

		if( !empty($data) ) {
	        $groupClassifications = $this->controller->GroupClassification->getData('list', array(
	            'fields' => array(
	                'GroupClassification.id', 'GroupClassification.name',
	            ),
	        ));
	        $values = $data;

			// foreach ($values as &$val) {
   //              $id = Common::hashEmptyField($val, 'UangJalan.id');
   //              $val = $this->controller->UangJalan->gerMergeBiaya( $val, $id, true );
			// }

        	$this->controller->UangJalan->ViewUangJalanTipeMotor->virtualFields['cnt'] = 'MAX(ViewUangJalanTipeMotor.cnt)';
        	$this->controller->UangJalan->ViewCommissionGroupMotor->virtualFields['cnt'] = 'MAX(ViewCommissionGroupMotor.cnt)';
        	$this->controller->UangJalan->ViewAsdpGroupMotor->virtualFields['cnt'] = 'MAX(ViewAsdpGroupMotor.cnt)';
        	$this->controller->UangJalan->ViewUangKawalGroupMotor->virtualFields['cnt'] = 'MAX(ViewUangKawalGroupMotor.cnt)';
        	$this->controller->UangJalan->ViewUangKeamananGroupMotor->virtualFields['cnt'] = 'MAX(ViewUangKeamananGroupMotor.cnt)';

			$uangJalanTipeMotor = $this->controller->UangJalan->ViewUangJalanTipeMotor->find('first');
			$commissionGroupMotor = $this->controller->UangJalan->ViewCommissionGroupMotor->find('first');
			$asdpGroupMotor = $this->controller->UangJalan->ViewAsdpGroupMotor->find('first');
			$uangKawalGroupMotor = $this->controller->UangJalan->ViewUangKawalGroupMotor->find('first');
			$uangKeamananGroupMotor = $this->controller->UangJalan->ViewUangKeamananGroupMotor->find('first');

            $UangJalanTipeMotorCnt = Common::hashEmptyField($uangJalanTipeMotor, 'ViewUangJalanTipeMotor.cnt');
            $CommissionGroupMotorCnt = Common::hashEmptyField($commissionGroupMotor, 'ViewCommissionGroupMotor.cnt');
            $AsdpGroupMotorCnt = Common::hashEmptyField($asdpGroupMotor, 'ViewAsdpGroupMotor.cnt');
            $UangKawalGroupMotorCnt = Common::hashEmptyField($uangKawalGroupMotor, 'ViewUangKawalGroupMotor.cnt');
            $UangKeamananGroupMotorCnt = Common::hashEmptyField($uangKeamananGroupMotor, 'ViewUangKeamananGroupMotor.cnt');

			foreach ($data as $key => $value) {
                $branch_id = Common::hashEmptyField($value, 'UangJalan.branch_id');
                $from_city_id = Common::hashEmptyField($value, 'UangJalan.from_city_id');
                $to_city_id = Common::hashEmptyField($value, 'UangJalan.to_city_id');

                $value = $this->controller->City->getMerge($value, $from_city_id, 'FromCity');
                $value = $this->controller->City->getMerge($value, $to_city_id, 'ToCity');
                $value = $this->controller->UangJalan->Branch->getMerge($value, $branch_id);

                $value = $this->controller->UangJalan->getMergeList($value, array(
					'contain' => array(
						'UangJalanTipeMotor' => array(
							'contain' => array(
								'GroupMotor',
							),
						),
						'CommissionGroupMotor' => array(
							'contain' => array(
								'GroupMotor',
							),
						),
						'AsdpGroupMotor' => array(
							'contain' => array(
								'GroupMotor',
							),
						),
						'UangKawalGroupMotor' => array(
							'contain' => array(
								'GroupMotor',
							),
						),
						'UangKeamananGroupMotor' => array(
							'contain' => array(
								'GroupMotor',
							),
						),
					),
				));

                $group_classification_1_id = Common::hashEmptyField($value, 'UangJalan.group_classification_1_id', 0);
                $group_classification_2_id = Common::hashEmptyField($value, 'UangJalan.group_classification_2_id', 0);
                $group_classification_3_id = Common::hashEmptyField($value, 'UangJalan.group_classification_3_id', 0);
                $group_classification_4_id = Common::hashEmptyField($value, 'UangJalan.group_classification_4_id', 0);

                $classifications1 = Common::hashEmptyField($groupClassifications, $group_classification_1_id, '');
                $classifications2 = Common::hashEmptyField($groupClassifications, $group_classification_2_id, '');
                $classifications3 = Common::hashEmptyField($groupClassifications, $group_classification_3_id, '');
                $classifications4 = Common::hashEmptyField($groupClassifications, $group_classification_4_id, '');

				$result[$key] = array(
					__('ID/No. Ref') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.id'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Kode Cabang') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Nama') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.title'),
					),
					__('Dari') => array(
						'text' => Common::hashEmptyField($value, 'FromCity.name'),
					),
					__('Tujuan') => array(
						'text' => Common::hashEmptyField($value, 'ToCity.name'),
					),
					__('Kapasitas') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.capacity'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Jarak Tempuh') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.distance'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Lead Time Sampai Tujuan') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.arrive_lead_time'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Lead Time Ke Pool') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.back_lead_time'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Klasifikasi 1') => array(
						'text' => $classifications1,
					),
					__('Klasifikasi 2') => array(
						'text' => $classifications2,
					),
					__('Klasifikasi 3') => array(
						'text' => $classifications3,
					),
					__('Klasifikasi 4') => array(
						'text' => $classifications4,
					),
					__('Uang Jalan Pertama') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_jalan_1'),
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Uang Jalan Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_jalan_per_unit'),
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);

		        for ($i=1; $i <= $UangJalanTipeMotorCnt; $i++) {
	            	$index = $i-1;
		            $result[$key] = array_merge($result[$key], array(
						__('Group Motor Uang Jalan %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('UangJalanTipeMotor.%s.GroupMotor.name', $index)),
						),
						__('Biaya Uang Jalan Per Group %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('UangJalanTipeMotor.%s.UangJalanTipeMotor.uang_jalan_1', $index)),
	                		'excel' => array(
	                			'align' => 'right',
	            			),
						),
		            ));
		        }

	            $result[$key] = array_merge($result[$key], array(
					__('Uang Jalan Kedua') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_jalan_2'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Uang Jalan Extra') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_jalan_extra'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Uang Jalan Extra Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_jalan_extra_per_unit'),
	            		'excel' => array(
	            			'align' => 'center',
	        			),
					),
					__('Min Kapasitas Ujalan Extra') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.min_capacity'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Komisi') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.commission'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Komisi Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.commission_per_unit'),
	            		'excel' => array(
	            			'align' => 'center',
	        			),
					),
	            ));

		        for ($i=1; $i <= $CommissionGroupMotorCnt; $i++) {
	            	$index = $i-1;
		            $result[$key] = array_merge($result[$key], array(
						__('Group Motor Komisi %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('CommissionGroupMotor.%s.GroupMotor.name', $index)),
						),
						__('Biaya Komisi Per Group %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('CommissionGroupMotor.%s.CommissionGroupMotor.commission', $index)),
		            		'excel' => array(
		            			'align' => 'right',
		        			),
						),
		            ));
		        }

		        $result[$key] = array_merge($result[$key], array(
					__('Komisi Extra') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.commission_extra'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Min Kapasitas Komisi Extra') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.commission_min_qty'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Komisi Extra Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.commission_extra_per_unit'),
	            		'excel' => array(
	            			'align' => 'center',
	        			),
					),
					__('Uang Penyebrangan') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.asdp'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Uang Penyebrangan Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.asdp_per_unit'),
	            		'excel' => array(
	            			'align' => 'center',
	        			),
					),
		        ));

		        for ($i=1; $i <= $AsdpGroupMotorCnt; $i++) {
	            	$index = $i-1;
		            $result[$key] = array_merge($result[$key], array(
						__('Group Motor Uang Penyebrangan %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('AsdpGroupMotor.%s.GroupMotor.name', $index)),
						),
						__('Biaya Uang Penyebrangan Per Group %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('AsdpGroupMotor.%s.AsdpGroupMotor.asdp', $index)),
		            		'excel' => array(
		            			'align' => 'right',
		        			),
						),
		            ));
		        }

		        $result[$key] = array_merge($result[$key], array(
					__('Uang Kawal') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_kawal'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Uang Kawal Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_kawal_per_unit'),
	            		'excel' => array(
	            			'align' => 'center',
	        			),
					),
		        ));

		        for ($i=1; $i <= $UangKawalGroupMotorCnt; $i++) {
	            	$index = $i-1;
		            $result[$key] = array_merge($result[$key], array(
						__('Group Motor Uang Kawal %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('UangKawalGroupMotor.%s.GroupMotor.name', $index)),
						),
						__('Biaya Uang Kawal Per Group %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('UangKawalGroupMotor.%s.UangKawalGroupMotor.uang_kawal', $index)),
		            		'excel' => array(
		            			'align' => 'right',
		        			),
						),
		            ));
		        }

		        $result[$key] = array_merge($result[$key], array(
					__('Uang Keamanan') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_keamanan'),
	            		'excel' => array(
	            			'align' => 'right',
	        			),
					),
					__('Uang Keamanan Per Unit ?') => array(
						'text' => Common::hashEmptyField($value, 'UangJalan.uang_keamanan_per_unit'),
	            		'excel' => array(
	            			'align' => 'center',
	        			),
					),
		        ));

		        for ($i=1; $i <= $UangKeamananGroupMotorCnt; $i++) {
	            	$index = $i-1;
		            $result[$key] = array_merge($result[$key], array(
						__('Group Motor Uang Keamanan %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('UangKeamananGroupMotor.%s.GroupMotor.name', $index)),
						),
						__('Biaya Uang Keamanan Per Group %s', $i) => array(
							'text' => Common::hashEmptyField($value, __('UangKeamananGroupMotor.%s.UangKeamananGroupMotor.uang_keamanan', $index)),
		            		'excel' => array(
		            			'align' => 'right',
		        			),
						),
		            ));
		        }
		    }
		}

		return array(
			'data' => $result,
			'model' => 'UangJalan',
		);
	}

	function _callDataRevenue_period ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('ViewRevenueQty');
		$this->controller->loadModel('Ttuj');
		$this->controller->loadModel('City');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
			'contain' => array(
				'ViewTtujQty',
			),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->ViewRevenueQty->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ViewRevenueQty', $options );
        $options = $this->controller->ViewRevenueQty->getData('paginate', $options, true, array(
            'branch' => false,
        ));

		$this->controller->paginate	= $options;
		$data = $this->controller->paginate('ViewRevenueQty');
		$result = array();

        App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));

		if( !empty($data) ) {
			$total_qty_unit = 0;
			$total_qty = 0;
			$grandtotal = 0;

			foreach ($data as $key => $value) {
                $id = Common::hashEmptyField($value, 'ViewRevenueQty.id');
                $ttuj_id = Common::hashEmptyField($value, 'ViewRevenueQty.ttuj_id');
                $branch_id = Common::hashEmptyField($value, 'ViewRevenueQty.branch_id');
                
                $customer_id = Common::hashEmptyField($value, 'ViewTtujQty.customer_id');
                $customer_id = Common::hashEmptyField($value, 'ViewRevenueQty.customer_id', $customer_id);
                $truck_id = Common::hashEmptyField($value, 'ViewTtujQty.truck_id');
                $truck_id = Common::hashEmptyField($value, 'ViewRevenueQty.truck_id', $truck_id);

                $from_city_id = Common::hashEmptyField($value, 'ViewTtujQty.from_city_id');
                $to_city_id = Common::hashEmptyField($value, 'ViewTtujQty.to_city_id');
                $from_city_id = Common::hashEmptyField($value, 'ViewRevenueQty.from_city_id', $from_city_id);
                $to_city_id = Common::hashEmptyField($value, 'ViewRevenueQty.to_city_id', $to_city_id);

                $value = $this->controller->ViewRevenueQty->Branch->getMerge($value, $branch_id);
                $value = $this->controller->ViewRevenueQty->Truck->getMerge($value, $truck_id);

                $invoice_id = $this->controller->Ttuj->Revenue->RevenueDetail->getData('list', array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $id,
                        'RevenueDetail.status' => 1,
                    ),
                    'fields' => array(
                        'RevenueDetail.invoice_id', 'RevenueDetail.invoice_id',
                    ),
                    'group' => array(
                        'RevenueDetail.revenue_id',
                        'RevenueDetail.invoice_id',
                    ),
                ), array(
                    'branch' => false,
                ));

                $value = $this->controller->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->controller->Ttuj->Revenue->RevenueDetail->Invoice->getMerge($value, $invoice_id, 'all');
                
                $value = $this->controller->City->getMerge($value, $from_city_id, 'FromCity');
                $value = $this->controller->City->getMerge($value, $to_city_id, 'ToCity');
                
                $nopol = Common::hashEmptyField($value, 'Truck.nopol');
                $nopol = Common::hashEmptyField($value, 'ViewTtujQty.nopol', $nopol);
                $nopol = Common::hashEmptyField($value, 'ViewRevenueQty.nopol', $nopol);
                $total = Common::hashEmptyField($value, 'ViewRevenueQty.total');

                $qty = Common::hashEmptyField($value, 'ViewTtujQty.qty');
                $qty_unit = Common::hashEmptyField($value, 'ViewRevenueQty.qty_unit');

                $no_invoices = Set::extract('/Invoice/Invoice/no_invoice', $value);
                $no_invoice = !empty($no_invoices)?implode(', ', $no_invoices):'-';
				
				$total_qty_unit += $qty_unit;
				$total_qty += $qty;
				$grandtotal += $total;

				$result[$key] = array(
					__('Tgl') => array(
						'text' => Common::hashEmptyField($value, 'ViewRevenueQty.date_revenue', null, array(
		                	'date' => 'd M Y',
		            	)),
                		'field_model' => 'ViewRevenueQty.date_revenue',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'date_revenue\',width:120',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Cabang') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code'),
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Customer') => array(
						'text' => Common::hashEmptyField($value, 'Customer.code'),
                		'field_model' => 'CustomerNoType.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'customer\',width:150',
		                'align' => 'left',
                		// 'fix_column' => true,
					),
					__('No TTUJ') => array(
						'text' => Common::hashEmptyField($value, 'ViewTtujQty.no_ttuj'),
                		'field_model' => 'ViewTtujQty.no_ttuj',
		                'data-options' => 'field:\'no_ttuj\',width:100',
		                'align' => 'left',
					),
					__('Nopol') => array(
						'text' => $nopol,
                		'field_model' => 'ViewRevenueQty.nopol',
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Dari') => array(
						'text' => Common::hashEmptyField($value, 'FromCity.name'),
		                'data-options' => 'field:\'from_city\',width:80',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Tujuan') => array(
						'text' => Common::hashEmptyField($value, 'ToCity.name'),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'to_city\',width:80',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Qty TTUJ') => array(
						'text' => !empty($qty)?$qty:'-',
		                'data-options' => 'field:\'total_qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Jumlah Unit') => array(
						'text' => !empty($qty_unit)?$qty_unit:'-',
		                'data-options' => 'field:\'qty_unit\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Total') => array(
						'text' => Common::getFormatPrice($total),
		                'data-options' => 'field:\'total\',width:100',
		                'align' => 'right',
		                'mainalign' => 'center',
					),
					__('No Invoice') => array(
						'text' => $no_invoice,
		                'data-options' => 'field:\'no_invoice\',width:100',
		                'align' => 'left',
					),
					__('Status') => array(
						'text' => Common::_callStatusRevenue($value, 'ViewRevenueQty'),
                		'field_model' => 'Revenue.transaction_status',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'data-options' => 'field:\'transaction_status\',width:100',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}

			$key++;

			if( !empty($view) ) {
				$result[$key] = array(
					__('Tgl') => array(
                		'field_model' => 'ViewRevenueQty.date_revenue',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'date_revenue\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Cabang') => array(
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Customer') => array(
                		'field_model' => 'CustomerNoType.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'customer\',width:120',
		                'align' => 'left',
                		'fix_column' => true,
					),
					__('No TTUJ') => array(
                		'field_model' => 'ViewTtujQty.no_ttuj',
		                'data-options' => 'field:\'no_ttuj\',width:100',
		                'align' => 'left',
					),
					__('Nopol') => array(
                		'field_model' => 'ViewRevenueQty.nopol',
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Dari') => array(
		                'data-options' => 'field:\'from_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Tujuan') => array(
						'text' => $this->Html->tag('strong', __('Total')),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'to_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Qty TTUJ') => array(
						'text' => $this->Html->tag('strong', $total_qty),
		                'data-options' => 'field:\'total_qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Jumlah Unit') => array(
						'text' => $this->Html->tag('strong', $total_qty_unit),
		                'data-options' => 'field:\'qty_unit\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Total') => array(
						'text' => $this->Html->tag('strong', Common::getFormatPrice($grandtotal)),
		                'data-options' => 'field:\'total\',width:100',
		                'align' => 'right',
		                'mainalign' => 'center',
					),
					__('No Invoice') => array(
		                'data-options' => 'field:\'no_invoice\',width:100',
		                'align' => 'left',
					),
					__('Status') => array(
                		'field_model' => 'ViewRevenueQty.transaction_status',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'data-options' => 'field:\'transaction_status\',width:100',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			} else {
				$last = $this->controller->ViewRevenueQty->getData('first', array_merge($options, array(
					'offset' => $offset+$limit,
					'limit' => $limit,
				)));

				if( empty($last) ) {
	            	$options = Common::_callUnset($options, array(
						'group',
						'limit',
						'offset',
					));

	        		$this->controller->ViewRevenueQty->virtualFields['total_qty'] = 'SUM(IFNULL(ViewTtujQty.qty, 0))';
	        		$this->controller->ViewRevenueQty->virtualFields['total_qty_unit'] = 'SUM(IFNULL(ViewRevenueQty.qty_unit, 0))';
	        		$this->controller->ViewRevenueQty->virtualFields['grandtotal'] = 'SUM(IFNULL(ViewRevenueQty.total, 0))';

					$value = $this->controller->ViewRevenueQty->getData('first', $options);
					unset($this->controller->ViewRevenueQty->virtualFields);

	                $total_qty = Common::hashEmptyField($value, 'ViewRevenueQty.total_qty');
	                $total_qty_unit = Common::hashEmptyField($value, 'ViewRevenueQty.total_qty_unit');
	                $grandtotal = Common::hashEmptyField($value, 'ViewRevenueQty.grandtotal');

					$result[$key] = array(
					__('Tgl') => array(
                		'field_model' => 'ViewRevenueQty.date_revenue',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'date_revenue\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Cabang') => array(
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Customer') => array(
                		'field_model' => 'CustomerNoType.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'customer\',width:120',
		                'align' => 'left',
                		'fix_column' => true,
					),
					__('No TTUJ') => array(
                		'field_model' => 'Ttuj.no_ttuj',
		                'data-options' => 'field:\'no_ttuj\',width:100',
		                'align' => 'left',
					),
					__('Nopol') => array(
                		'field_model' => 'ViewRevenueQty.nopol',
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Dari') => array(
		                'data-options' => 'field:\'from_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Tujuan') => array(
						'text' => __('Total'),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'to_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Qty TTUJ') => array(
						'text' => $total_qty,
		                'data-options' => 'field:\'total_qty\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Jumlah Unit') => array(
						'text' => $total_qty_unit,
		                'data-options' => 'field:\'qty_unit\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Total') => array(
						'text' => Common::getFormatPrice($grandtotal),
		                'data-options' => 'field:\'total\',width:100',
		                'align' => 'right',
		                'mainalign' => 'center',
					),
					__('No Invoice') => array(
		                'data-options' => 'field:\'no_invoice\',width:100',
		                'align' => 'left',
					),
					__('Status') => array(
                		'field_model' => 'ViewRevenueQty.transaction_status',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'data-options' => 'field:\'transaction_status\',width:100',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					);
				}
			}
		}

		return array(
			'data' => $result,
			'model' => 'ViewRevenueQty',
		);
	}

	function _callDataRevenue_detail ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('RevenueDetail');
		$this->controller->loadModel('City');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));
		$params = $this->MkCommon->_callRefineParams($params);

		$options = array(
            'conditions' => array(
                'RevenueDetail.status' => 1,
            ),
            'contain' => array(
                'Revenue',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->RevenueDetail->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'Revenue', $options );
        $options = $this->controller->RevenueDetail->Revenue->getData('paginate', $options, true, array(
            'branch' => false,
        ));

		$this->controller->paginate	= $options;
		$data = $this->controller->paginate('RevenueDetail');
		$result = array();

        App::import('Helper', 'Html');
        $this->Html = new HtmlHelper(new View(null));

		if( !empty($data) ) {
            $totalUnit = 0;
            $totalInvoice = 0;

			foreach ($data as $key => $value) {
                $id = Common::hashEmptyField($value, 'Revenue.id');
                $ttuj_id = Common::hashEmptyField($value, 'Revenue.ttuj_id');
                $invoice_id = Common::hashEmptyField($value, 'RevenueDetail.invoice_id');
                $branch_id = Common::hashEmptyField($value, 'Revenue.branch_id');

                $is_charge = Common::hashEmptyField($value, 'RevenueDetail.is_charge');
                $total_price_unit = Common::hashEmptyField($value, 'RevenueDetail.total_price_unit');
                $price_unit = Common::hashEmptyField($value, 'RevenueDetail.price_unit');
                $unit = Common::hashEmptyField($value, 'RevenueDetail.qty_unit');
                
                $value = $this->controller->RevenueDetail->Revenue->Ttuj->getMerge($value, $ttuj_id);
                $value = $this->controller->RevenueDetail->Revenue->Branch->getMerge($value, $branch_id);

                $customer_id = Common::hashEmptyField($value, 'Ttuj.customer_id');
                $customer_id = Common::hashEmptyField($value, 'Revenue.customer_id', $customer_id);

                $from_city_id = Common::hashEmptyField($value, 'Ttuj.from_city_id');
                $from_city_id = Common::hashEmptyField($value, 'Revenue.from_city_id', $from_city_id);
                
                $to_city_id = Common::hashEmptyField($value, 'Ttuj.to_city_id');
                $city_id = Common::hashEmptyField($value, 'RevenueDetail.city_id', $to_city_id);

                $value = $this->controller->RevenueDetail->Revenue->Ttuj->Customer->getMerge($value, $customer_id);
                $value = $this->controller->RevenueDetail->Invoice->getMerge($value, $invoice_id);
                $value = $this->controller->City->getMerge($value, $city_id, 'ToCity');
                $value = $this->controller->City->getMerge($value, $from_city_id, 'FromCity');

                $truck_id = Common::hashEmptyField($value, 'Ttuj.truck_id');
                $truck_id = Common::hashEmptyField($value, 'Revenue.truck_id', $truck_id);
                $value = $this->controller->RevenueDetail->Revenue->Truck->getMerge($value, $truck_id);
                
                $nopol = Common::hashEmptyField($value, 'Truck.nopol');
                $nopol = Common::hashEmptyField($value, 'Ttuj.nopol', $nopol);
                $nopol = Common::hashEmptyField($value, 'Revenue.nopol', $nopol);

                // $no_invoices = Set::extract('/Invoice/Invoice/no_invoice', $value);
                // $no_invoice = !empty($no_invoices)?implode(', ', $no_invoices):'-';
                $no_invoice = Common::hashEmptyField($value, 'Invoice.no_invoice');

                if( !empty($is_charge) ) {
                    $totalPriceFormat = !empty($total_price_unit)?Common::getFormatPrice($total_price_unit):'-';
                    $customPrice = !empty($price_unit)?Common::getFormatPrice($price_unit, false):'-';
                } else {
                    $total_price_unit = 0;
                    $customPrice = '';
                }

                $totalUnit += $unit;
                $totalInvoice += $total_price_unit;

				$result[$key] = array(
					__('Tgl') => array(
						'text' => Common::hashEmptyField($value, 'Revenue.date_revenue', null, array(
		                	'date' => 'd M Y',
		            	)),
                		'field_model' => 'Revenue.date_revenue',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'date_revenue\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Cabang') => array(
						'text' => Common::hashEmptyField($value, 'Branch.code'),
                		'field_model' => 'Branch.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Customer') => array(
						'text' => Common::hashEmptyField($value, 'Customer.code'),
                		'field_model' => 'CustomerNoType.code',
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'customer\',width:120',
		                'align' => 'left',
                		// 'fix_column' => true,
					),
					__('No TTUJ') => array(
						'text' => Common::hashEmptyField($value, 'Ttuj.no_ttuj'),
                		'field_model' => 'Ttuj.no_ttuj',
		                'data-options' => 'field:\'no_ttuj\',width:100',
		                'align' => 'left',
					),
					__('Nopol') => array(
						'text' => $nopol,
                		'field_model' => 'Revenue.nopol',
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Dari') => array(
						'text' => Common::hashEmptyField($value, 'FromCity.name'),
		                'data-options' => 'field:\'from_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Tujuan') => array(
						'text' => Common::hashEmptyField($value, 'ToCity.name'),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'to_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Jumlah Unit') => array(
						'text' => !empty($unit)?$unit:'-',
		                'data-options' => 'field:\'qty_unit\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga Unit') => array(
						'text' => $customPrice,
		                'data-options' => 'field:\'price\',width:120',
		                'align' => 'right',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total') => array(
						'text' => $totalPriceFormat,
		                'data-options' => 'field:\'total\',width:120',
		                'align' => 'right',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('No Invoice') => array(
						'text' => $no_invoice,
		                'data-options' => 'field:\'no_invoice\',width:120',
		                'align' => 'left',
					),
					__('Status') => array(
						'text' => Common::_callStatusRevenue($value),
                		'field_model' => 'Revenue.transaction_status',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'data-options' => 'field:\'transaction_status\',width:100',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			}

			$key++;

			if( !empty($view) ) {
				$result[$key] = array(
					__('Tgl') => array(
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'date_revenue\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Cabang') => array(
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'branch\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Customer') => array(
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'customer\',width:120',
		                'align' => 'left',
					),
					__('No TTUJ') => array(
		                'data-options' => 'field:\'no_ttuj\',width:100',
		                'align' => 'left',
					),
					__('Nopol') => array(
		                'data-options' => 'field:\'nopol\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Dari') => array(
		                'data-options' => 'field:\'from_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Tujuan') => array(
						'text' => $this->Html->tag('strong', __('Total')),
		                'style' => 'text-align: center;',
		                'data-options' => 'field:\'to_city\',width:100',
		                'align' => 'left',
		                'mainalign' => 'center',
					),
					__('Jumlah Unit') => array(
						'text' => $this->Html->tag('strong', $totalUnit),
		                'data-options' => 'field:\'qty_unit\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Harga Unit') => array(
		                'data-options' => 'field:\'price\',width:120',
		                'align' => 'right',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('Total') => array(
						'text' => $this->Html->tag('strong', Common::getFormatPrice($totalInvoice)),
		                'data-options' => 'field:\'total\',width:120',
		                'align' => 'right',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'right',
            			),
					),
					__('No Invoice') => array(
		                'data-options' => 'field:\'no_invoice\',width:120',
		                'align' => 'left',
					),
					__('Status') => array(
		                'align' => 'center',
		                'mainalign' => 'center',
		                'data-options' => 'field:\'transaction_status\',width:100',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);
			} else {
				$last = $this->controller->RevenueDetail->getData('first', array_merge($options, array(
					'offset' => $offset+$limit,
					'limit' => $limit,
				)));

				if( empty($last) ) {
	            	$options = Common::_callUnset($options, array(
						'group',
						'limit',
						'offset',
					));

	        		$this->controller->RevenueDetail->virtualFields['total_qty_unit'] = 'SUM(IFNULL(RevenueDetail.qty_unit, 0))';
	        		$this->controller->RevenueDetail->virtualFields['grandtotal_price_unit'] = 'SUM(IFNULL(RevenueDetail.total_price_unit, 0))';

					$value = $this->controller->RevenueDetail->getData('first', $options);

					$total_unit = Common::hashEmptyField($value, 'RevenueDetail.total_qty_unit');
	                $grandtotal = Common::hashEmptyField($value, 'RevenueDetail.grandtotal_price_unit');

					$result[$key] = array(
						__('Tgl') => array(
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'date_revenue\',width:100',
			                'align' => 'center',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'center',
	            			),
						),
						__('Cabang') => array(
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'branch\',width:100',
			                'align' => 'center',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'center',
	            			),
						),
						__('Customer') => array(
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'customer\',width:120',
			                'align' => 'left',
						),
						__('No TTUJ') => array(
			                'data-options' => 'field:\'no_ttuj\',width:100',
			                'align' => 'left',
						),
						__('Nopol') => array(
			                'data-options' => 'field:\'nopol\',width:100',
			                'align' => 'center',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'center',
	            			),
						),
						__('Dari') => array(
			                'data-options' => 'field:\'from_city\',width:100',
			                'align' => 'left',
			                'mainalign' => 'center',
						),
						__('Tujuan') => array(
							'text' => __('Total'),
			                'style' => 'text-align: center;',
			                'data-options' => 'field:\'to_city\',width:100',
			                'align' => 'left',
			                'mainalign' => 'center',
						),
						__('Jumlah Unit') => array(
							'text' => Common::getFormatPrice($total_unit),
			                'data-options' => 'field:\'qty_unit\',width:80',
			                'align' => 'center',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'center',
	            			),
						),
						__('Harga Unit') => array(
			                'data-options' => 'field:\'price\',width:120',
			                'align' => 'right',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'right',
	            			),
						),
						__('Total') => array(
							'text' => Common::getFormatPrice($grandtotal),
			                'data-options' => 'field:\'total\',width:120',
			                'align' => 'right',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'right',
	            			),
						),
						__('No Invoice') => array(
			                'data-options' => 'field:\'no_invoice\',width:120',
			                'align' => 'left',
						),
						__('Status') => array(
			                'align' => 'center',
			                'mainalign' => 'center',
			                'data-options' => 'field:\'transaction_status\',width:100',
	                		'excel' => array(
	                			'align' => 'center',
	            			),
						),
					);
				}
			}
		}

		return array(
			'data' => $result,
			'model' => 'RevenueDetail',
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
			$document_status = Common::hashEmptyField($dataSave, 'Report.document_status');

			$dataQueue = Common::hashEmptyField($value, 'dataQueue');
			$filename_path = Common::hashEmptyField($value, 'file.filename_path');

			$start_date = Common::hashEmptyField($report, 'Report.start_date');
			$end_date = Common::hashEmptyField($report, 'Report.end_date');
			$periods = Common::getCombineDate($start_date, $end_date);
			$titles = array(
				'title' => $title,
				'periods' => $periods,
			);

			$this->exportExcel($titles, $data, $filename_path, $document_status);

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

	function exportExcel( $titles, $data, $path = false, $document_status = null ) {
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
		$this->processReportTableData( $titles, $data, $theader, $document_status );
		// $this->PhpExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->PhpExcel->_xls->getActiveSheet();
		$this->PhpExcel->save($path);
	}

	function processReportTableData( $titles, $data, $theader = true, $document_status = null ) {
		$table = array();
		// $idx = 64; // Acii A
		// $dimensi = 64; // Acii A
		$dimensi = 0; // Acii A
		$column = null;

		if( !empty($data['multiple_column']) ) {
			$column = $data['multiple_column'];
		} else if( !empty($data[0]) ) {
			$column = $data[0];
		}

		if( !empty($column) ) {
			$num = 0;

			foreach ($column as $label => $value) {
				$text = Common::hashEmptyField($value, 'text', '', array(
					'isset' => true,
				));
				$label = Common::hashEmptyField($value, 'label', $label);
				$childs = Common::hashEmptyField($value, 'child');
				$rowspan = Common::hashEmptyField($value, 'excel.headerrowspan');
				$colspan = Common::hashEmptyField($value, 'excel.headercolspan');
				// $width = Common::hashEmptyField($value, 'width');

				$dataArr = Common::_callUnset($value, array(
					'text',
					'horizontal',
				));

				$table[$num] = array_merge($dataArr, array(
					'label' => $label,
					'rowspan' => $rowspan,
					'colspan' => $colspan,
					// 'width' => $width,
				));

				// if( $idx >= 90 ) {
				// 	$dimensi++;
				// } else {
				// 	$idx++;
				// }

				if( !empty($childs) ) {
					$childTmp = array();

					foreach ($childs as $key => $child) {
						$label = Common::hashEmptyField($child, 'name', '');
						$width = Common::hashEmptyField($child, 'width');
						$rowspan = Common::hashEmptyField($child, 'excel.headerrowspan');
						$colspan = Common::hashEmptyField($child, 'excel.headercolspan');

						$childTmp[] = array_merge($dataArr, array(
							'label' => $label,
							'width' => $width,
							'rowspan' => $rowspan,
							'colspan' => $colspan,
						));

						// if( $idx >= 90 ) {
							$dimensi++;
						// } else {
						// 	$idx++;
						// }
					}

					$table[$num]['child'] = $childTmp;
				} else {
					// if( $idx >= 90 ) {
						$dimensi++;
					// } else {
					// 	$idx++;
					// }
				}
				
				$num++;
			}
		}

		// $cell_end = chr($idx);

		// if( $dimensi > 64 ) {
		// 	$dimensi_chr = chr($dimensi);
		// 	$cell_end = __('A%s', $dimensi_chr);
		// }
		$cell_end = Common::getNameFromNumber($dimensi);

		if( !empty($theader) ) {
			$title = Common::hashEmptyField($titles, 'title');
			$periods = Common::hashEmptyField($titles, 'periods');
			$this->PhpExcel->setReportHeader($title, $periods, 'A1', 'A2', sprintf('A1:%s1', $cell_end), sprintf('A2:%s2', $cell_end));
			
			$bold = true;
		} else {
			$table = array();
			$bold = false;
		}

		if( !empty($data['multiple_column']) ) {
			if( !empty($data['multiple']) ) {
				$tmpResult = $data['multiple'];

				if(!empty($tmpResult)){
		            foreach ($tmpResult as $key => $product) {
		                if(!empty($product)){
		                    foreach ($product as $key => $branch) {
								$headers = Common::hashEmptyField($branch, 'header');
								$details = Common::hashEmptyField($branch, 'detail');
								
								if( !empty($details) ) {
									$this->PhpExcel->addTableRow(array(
								    	array(
											'text' => '',
										)
							    	));
									
									foreach ($headers as $label => $val) {
										$text = Common::hashEmptyField($val, 'text', '', array(
											'isset' => true,
										));
										$excel = Common::hashEmptyField($val, 'excel');

									    $this->PhpExcel->addTableRow(array(
									    	array(
												'text' => __('%s: %s', $label, $text),
												'options' => $excel,
											)
								    	));
									}
									
									$this->PhpExcel->addTableRow(array(
								    	array(
											'text' => '',
										)
							    	));

									// $idx += 3;

									// if( $idx >= 90 ) {
									// 	$dimensi++;
									// }
									
									// $cell_end = chr($idx);

									if( $dimensi > 64 ) {
										$dimensi_chr = chr($dimensi);
										$cell_end = __('A%s', $dimensi_chr);
									}

									$this->PhpExcel->addTableHeader($table, array(
										'name' => 'Calibri',
										'bold' => $bold,
										'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
									));

									foreach ($details as $label => $values) {
										$dataTable = array();

										if( !empty($values) ) {
											foreach ($values as $key => $value) {
												$text = Common::hashEmptyField($value, 'text', '', array(
													'isset' => true,
												));
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
									
									$this->PhpExcel->addTableRow(array(
								    	array(
											'text' => '',
										)
							    	));
								}
	                    	}
	                    }
					}
				}
			}
		} else {
			if( !empty($data) ) {
				// add heading with different font and bold text
				$this->PhpExcel->addTableHeader($table, array(
					'name' => 'Calibri',
					'bold' => $bold,
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'fill_color' => 'd70601',
					'text_color' => 'FFFFFF',
				), $cell_end);

				foreach ($data as $label => $values) {
					$dataTable = array();

					if( !empty($values) ) {
						foreach ($values as $key => $value) {
							$text = Common::hashEmptyField($value, 'text', '', array(
								'isset' => true,
							));
							$excel = Common::hashEmptyField($value, 'excel');
							$childs = Common::hashEmptyField($value, 'child');

							if( !empty($childs) ) {
								foreach ($childs as $key => $child) {
									$text = Common::hashEmptyField($child, 'text', '', array(
										'isset' => true,
									));
									$excel = Common::hashEmptyField($child, 'excel');

									if( !empty($excel) ) {
										$dataTable[] = array(
											'text' => $text,
											'options' => $excel,
										);
									} else {
										$dataTable[] = $text;
									}
								}
							} else {
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
					}

					if( !empty($dataTable) ) {
					    $this->PhpExcel->addTableRow($dataTable);
					}
				}
			}
		}

    	if( $document_status == 'completed' ) {
	        $full_name = Configure::read('__Site.config_user_data.Employe.full_name');
			$this->PhpExcel->addTableRow(array(
		    	array(
					'text' => '',
				)
	    	));
			$this->PhpExcel->addTableRow(array(
		    	array(
					'text' => __('Printed on : %s, by : %s', date('d F Y'), $full_name),
					'options' => array(
						'colspan' => 5,
					),
				)
	    	));
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