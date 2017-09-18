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

	function _callDataCurrent_stock_reports ( $params, $limit = 30, $offset = 0 ) {
		$this->controller->loadModel('Product');

        $params_named = Common::hashEmptyField($params, 'named', array());
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));

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
		$params = $this->controller->params->params;

        $params_named = Common::hashEmptyField($params, 'named', array());
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));

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

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductHistory.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'ProductHistory.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
			$tmpResult = array();

			foreach ($data as $key => $value) {
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
                }

                $value = $this->controller->ProductHistory->getMergeList($value, array(
                    'contain' => array(
                        'DocumentDetail' => array(
                            'uses' => $modelName.'Detail',
                            'contain' => array(
                                'Document' => array(
                                    'uses' => $modelName,
                                ),
                            ),
                        ),
                    ),
                ));

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

                $modelNameDetail = $modelName.'Detail';
                $value = $this->controller->ProductHistory->$modelNameDetail->$modelName->$transactionName->getMerge($value, $document_id, $transactionName.'.id', 'all', 'Transaction');
                
                $truck_id = Common::hashEmptyField($value, 'Transaction.truck_id');

                if( !empty($truck_id) ) {
                    $value = $this->controller->ProductHistory->$modelNameDetail->$modelName->$transactionName->Truck->getMerge($value, $truck_id);
                }

                $tmpResult[$product_id][$branch_id]['Branch'] = Common::hashEmptyField($value, 'Branch');
                $tmpResult[$product_id][$branch_id]['Product'] = Common::hashEmptyField($value, 'Product');
                $tmpResult[$product_id][$branch_id]['ProductHistory'][] = $value;
			}
		}

        if(!empty($tmpResult)){
        	$idx = 0;
            $this->controller->ProductHistory->virtualFields['total_begining_balance'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_receipt\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.transaction_type = \'product_expenditure\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';
            $this->controller->ProductHistory->virtualFields['total_qty_in'] = 'SUM(CASE WHEN ProductHistory.type = \'in\' THEN ProductHistory.qty ELSE 0 END)';
            $this->controller->ProductHistory->virtualFields['total_qty_out'] = 'SUM(CASE WHEN ProductHistory.type = \'out\' THEN ProductHistory.qty ELSE 0 END)';

            foreach ($tmpResult as $key => &$product) {
                if(!empty($product)){
                    foreach ($product as $key => &$branch) {
                        $product_id = Common::hashEmptyField($branch, 'Product.id');
                        $branch_id = Common::hashEmptyField($branch, 'Branch.id');

                        $branch_name = Common::hashEmptyField($branch, 'Branch.full_name');
                        $product_name = Common::hashEmptyField($branch, 'Product.full_name');

                        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
                        $options = Common::_callUnset($options, array(
                            'conditions' => array(
                                'ProductHistory.product_id',
                                'ProductHistory.branch_id',
                                'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') >=',
                                'DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <=',
                            ),
                        ));
                        $options['conditions']['DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <'] = $dateFrom;
                        $options['conditions']['ProductHistory.product_id'] = $product_id;
                        $options['conditions']['ProductHistory.branch_id'] = $branch_id;
                        $options['order'] = array(
                            'ProductHistory.transaction_date' => 'DESC',
                            'ProductHistory.created' => 'DESC',
                        );

                        $productHistory = $this->controller->ProductHistory->getData('first', $options, array(
                            'branch' => false,
                        ));

                        $lastHistory = $this->controller->ProductHistory->getData('first', $options, array(
                            'branch' => false,
                        ));

                        $total_qty_in = Common::hashEmptyField($lastHistory, 'ProductHistory.total_qty_in', 0);
                        $total_qty_out = Common::hashEmptyField($lastHistory, 'ProductHistory.total_qty_out', 0);
                        $total_qty = $total_qty_in - $total_qty_out;
                        
                        $lastHistory = $this->controller->ProductHistory->Product->getMergeList($lastHistory, array(
                            'contain' => array(
                                'ProductUnit',
                            ),
                        ));
                        $lastHistory['ProductHistory']['ending'] = $total_qty;
        				$ending_stock = array();

				        if( !empty($lastHistory['ProductHistory']['id']) ) {
				            $unit = Common::hashEmptyField($lastHistory, 'ProductUnit.name');
				            $start_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.ending', 0);
				            $total_begining_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.total_begining_balance');

				            if( !empty($start_balance) ) {
				                $total_begining_price = $total_begining_balance / $start_balance;
				            } else {
				                $total_begining_price = 0;
				            }
				    
				            $ending_stock[$total_begining_price]['qty'] = $start_balance;
				            $ending_stock[$total_begining_price]['price'] = $total_begining_price;
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
				                $qty = Common::hashEmptyField($value, 'ProductHistory.qty');
				                // $total_balance_price = $total_begining_price*$balance;

				                switch ($transaction_type) {
				                    case 'product_receipt':
				                        $qty_in = Common::hashEmptyField($value, 'ProductHistory.qty');
				                        $price = $price_in = Common::hashEmptyField($value, 'ProductHistory.price');
				                        $total_in = $qty_in * $price_in;
				                        $total_ending_price = $price*$qty;
				                        // $grandtotal_ending = $total_balance_price + $total_ending_price;
                
				                        if( !empty($ending_stock[$price]['qty']) ) {
				                            $ending_stock[$price]['qty'] = $ending_stock[$price]['qty'] + $qty;
				                        } else {
				                            $ending_stock[$price] = array(
				                                'qty' => $qty,
				                                'price' => $price,
				                            );
				                        }
				                        break;
				                    case 'product_expenditure':
				                        $qty_out_tmp = $qty_out = Common::hashEmptyField($value, 'ProductHistory.qty');
				                        $price = $price_out = Common::hashEmptyField($value, 'ProductHistory.price');
				                        $total_out = $qty_out * $price_out;
				                        $total_ending_price = $price*$qty;
				                        // $grandtotal_ending = $total_balance_price - $total_ending_price;
                
				                        if( !empty($ending_stock) ) {
				                            foreach ($ending_stock as $key => $stock) {
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
				                        break;
				                }

				                // if( !empty($ending) ) {
				                //     $grandtotal_ending_price = $grandtotal_ending / $ending;
				                // } else {
				                //     $grandtotal_ending_price = 0;
				                // }

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

	function _callDataReceipt_reports ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('ProductReceiptDetail');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));

		$options = array(
            'order'=> array(
                'ProductReceipt.status' => 'DESC',
                'ProductReceipt.created' => 'DESC',
                'ProductReceipt.id' => 'DESC',
                'ProductReceiptDetail.id' => 'ASC',
            ),
        	'offset' => $offset,
        	'limit' => $limit,
        );
		$options = $this->controller->ProductReceiptDetail->ProductReceipt->_callRefineParams($params, $options);
		$options = $this->controller->ProductReceiptDetail->_callRefineParams($params, $options);
        $options = $this->MkCommon->getConditionGroupBranch( $params, 'ProductReceipt', $options );

		$this->controller->paginate	= $this->controller->ProductReceiptDetail->getData('paginate', $options, array(
			'branch' => false,
			'header' => true,
		));
		$data = $this->controller->paginate('ProductReceiptDetail');
		$result = array();

		$last_data = end($data);
		$last_id = Common::hashEmptyField($last_data, 'ProductReceiptDetail.id');

		$paging = $this->controller->params->paging;
        $nextPage = Common::hashEmptyField($paging, 'ProductReceiptDetail.nextPage');

        $totalQty = 0;
        $totalPrice = 0;
        $grandtotal = 0;

		if( !empty($data) ) {
            $types = Configure::read('__Site.Spk.type');
            $totalQty = 0;

			foreach ($data as $key => $value) {
                $value = $this->RjProduct->_callGetDocReceipt($value);
		        $value = $this->controller->ProductReceiptDetail->getMergeList($value, array(
		            'contain' => array(
		            	'Product' => array(
		                	'ProductUnit',
	            		),
		            ),
		        ));
		        $value = $this->controller->ProductReceiptDetail->ProductReceipt->getMergeList($value, array(
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
                
                $qty = Common::hashEmptyField($value, 'ProductReceiptDetail.qty', 0, array(
                	'strict' => true,
            	));

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
					__('QTY') => array(
						'text' => $qty,
                		'field_model' => 'ProductReceiptDetail.qty',
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

			if( empty($nextPage) || !empty($view) ) {
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
					__('QTY') => array(
						'text' => $totalQty,
                		'field_model' => 'ProductReceiptDetail.qty',
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
			'model' => 'ProductReceiptDetail',
		);
	}

	function _callDataTire_reports ( $params, $limit = 30, $offset = 0, $view = false ) {
		$this->controller->loadModel('Spk');

        $params_named = Common::hashEmptyField($params, 'named', array(), array(
        	'strict' => true,
    	));
		$params['named'] = array_merge($params_named, $this->MkCommon->processFilter($params));

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

		$options = array(
			'contain' => array(
				'Spk',
			),
            'order'=> array(
                'Spk.status' => 'DESC',
                'Spk.created' => 'DESC',
                'Spk.id' => 'DESC',
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
                
				$result[$key] = array(
					__('No Pol') => array(
						'text' => Common::hashEmptyField($value, 'Truck.nopol'),
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
                	
                	$jumlah = $this->controller->Truck->Spk->_callMaintenanceCostByTruckMonthly($truck_id, $branch_id, $monthYear);
					$total += $jumlah;
                	$grandtotalArr[$i] = $jumlah + Common::hashEmptyField($grandtotalArr, $i, 0);

					$result[$key] = array_merge($result[$key], array(
						$monthName => array(
							'text' => !empty($jumlah)?Common::getFormatPrice($jumlah):'-',
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

			if( empty($nextPage) || !empty($view) ) {
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
                }
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

		$options = array(
            'order'=> array(
                'ProductAdjustment.status' => 'DESC',
                'ProductAdjustment.created' => 'DESC',
                'ProductAdjustment.id' => 'DESC',
                'ProductAdjustmentDetail.id' => 'ASC',
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
		        $value = $this->controller->ProductAdjustmentDetail->getMergeList($value, array(
		            'contain' => array(
		            	'Product' => array(
		                	'ProductUnit',
	            		),
                        'ProductAdjustmentDetailSerialNumber',
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
					__('Tgl Adjustment') => array(
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
					__('Adjustment') => array(
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
					__('Difference') => array(
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
		$idx = 64; // Acii A
		$dimensi = 64; // Acii A
		$column = null;

		if( !empty($data['multiple_column']) ) {
			$column = $data['multiple_column'];
		} else if( !empty($data[0]) ) {
			$column = $data[0];
		}

		if( !empty($column) ) {
			foreach ($column as $label => $value) {
				$text = Common::hashEmptyField($value, 'text');
				$label = Common::hashEmptyField($value, 'label', $label);
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
										$text = Common::hashEmptyField($val, 'text');
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