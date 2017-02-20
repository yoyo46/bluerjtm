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
		$params = $this->controller->params->params;

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
        $this->controller->Product->ProductStock->virtualFields['avg_price'] = 'SUM(ProductStock.price) / SUM(ProductStock.qty - ProductStock.qty_use)';

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
                $price = Common::hashEmptyField($value, 'ProductStock.avg_price', 0);
                $total = $qty * $price;

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
                		'field_model' => 'ProductStock.avg_price',
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
                		'field_model' => 'ProductStock.avg_price',
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
                'ProductHistory.created' => 'ASC',
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

                        $this->controller->ProductHistory->virtualFields['total_begining_balance'] = 'SUM(CASE WHEN ProductHistory.transaction_type = \'product_receipt\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.transaction_type = \'product_expenditure\' THEN ProductHistory.price*ProductHistory.qty ELSE 0 END)';

                        $lastHistory = $this->controller->ProductHistory->getData('first', $options, array(
                            'branch' => false,
                        ));
                        $lastHistory['ProductHistory']['ending'] = Common::hashEmptyField($productHistory, 'ProductHistory.ending');
                        $lastHistory = $this->controller->ProductHistory->Product->getMergeList($lastHistory, array(
                            'contain' => array(
                                'ProductUnit',
                            ),
                        ));

				        if( !empty($lastHistory) ) {
				            $unit = Common::hashEmptyField($lastHistory, 'ProductUnit.name');
				            $start_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.ending', 0);
				            $total_begining_balance = Common::hashEmptyField($lastHistory, 'ProductHistory.total_begining_balance');

				            if( !empty($start_balance) ) {
				                $total_begining_price = $total_begining_balance / $start_balance;
				            } else {
				                $total_begining_price = 0;
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

				                $url = null;
				                $price = null;
				                $id = Common::hashEmptyField($value, 'Product.id');
				                $unit = Common::hashEmptyField($value, 'ProductUnit.name');

				                $transaction_id = Common::hashEmptyField($value, 'ProductHistory.transaction_id');
				                $transaction_type = Common::hashEmptyField($value, 'ProductHistory.transaction_type');
				                $ending = Common::hashEmptyField($value, 'ProductHistory.ending');
				                $balance = Common::hashEmptyField($value, 'ProductHistory.balance');
				                $transaction_date = Common::hashEmptyField($value, 'ProductHistory.transaction_date', null, array(
				                    'date' => 'd/m/Y',
				                ));

               					$nopol = Common::hashEmptyField($value, 'Truck.nopol', '-');
				                $nodoc = Common::hashEmptyField($value, 'DocumentDetail.Document.nodoc');
				                $qty = Common::hashEmptyField($value, 'ProductHistory.qty');
				                $total_balance_price = $total_begining_price*$balance;

				                switch ($transaction_type) {
				                    case 'product_receipt':
				                        $qty_in = Common::hashEmptyField($value, 'ProductHistory.qty');
				                        $price = $price_in = Common::hashEmptyField($value, 'ProductHistory.price');
				                        $total_in = $qty_in * $price_in;
				                        $total_ending_price = $price*$qty;
				                        $grandtotal_ending = $total_balance_price + $total_ending_price;
				                        break;
				                    case 'product_expenditure':
				                        $qty_out = Common::hashEmptyField($value, 'ProductHistory.qty');
				                        $price = $price_out = Common::hashEmptyField($value, 'ProductHistory.price');
				                        $total_out = $qty_out * $price_out;
				                        $total_ending_price = $price*$qty;
				                        $grandtotal_ending = $total_balance_price - $total_ending_price;
				                        break;
				                }

				                if( !empty($ending) ) {
				                    $grandtotal_ending_price = $grandtotal_ending / $ending;
				                } else {
				                    $grandtotal_ending_price = 0;
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
										'text' => $this->MkCommon->getFormatPrice($total_begining_price, 0, 2),
		                				'label' => __('Harga Satuan'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Total Saldo') => array(
										'text' => $this->MkCommon->getFormatPrice($total_balance_price, 0, 2),
		                				'label' => __('Total'),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
								);

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
										'text' => $qty,
				                		'excel' => array(
				                			'align' => 'center',
				            			),
									),
									__('Harga Satuan Saldo') => array(
                						'label' => __('Harga Satuan'),
										'text' => $this->MkCommon->getFormatPrice($price, 0, 2),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
									__('Total Saldo') => array(
                						'label' => __('Total'),
										'text' => $this->MkCommon->getFormatPrice($total_ending_price, 0, 2),
				                		'excel' => array(
				                			'align' => 'right',
				            			),
									),
								);

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