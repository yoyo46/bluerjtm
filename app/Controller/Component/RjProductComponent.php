<?php
App::uses('Sanitize', 'Utility');
class RjProductComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callRefineParams ( $data ) {
		$keyword = $this->MkCommon->filterEmptyField($data, 'named', 'keyword');

		if( !empty($keyword) ) {
			$this->controller->request->data['Search']['keyword'] = $keyword;
		}
	}
	
	function processRefine($refine = false, $default_conditions = array()) {
		if(!$refine) {
			return false;
		} else {
			$refine_conditions = array();

			if(!empty($refine)) {
				if( !empty($refine['ProductCategory']['name']) ) {
					$refine_conditions['ProductCategory']['name'] = $refine['ProductCategory']['name'];
				}
				if( !empty($refine['ProductCategory']['parent']) ) {
					$refine_conditions['ProductCategory']['parent'] = $refine['ProductCategory']['parent'];
				}
			}
				
			return $refine_conditions;
		}
	}

	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['ProductCategory']) && !empty($refine['ProductCategory'])) {
			foreach($refine['ProductCategory'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}

    function _callStockSerialNumber ( $session_id, $product_id, $data, $price = null, $modelSn = 'ProductReceiptDetailSerialNumber', $dataSave = array() ) {
        $import = Common::hashEmptyField($dataSave, 'ProductAdjustment.import');
        $result = array();

        if( !empty($import) ) {
            $serial_numbers = Common::hashEmptyField($dataSave, 'ProductAdjustmentDetailSerialNumber.serial_numbers.'.$product_id);

            if( !empty($serial_numbers) ) {
                foreach ($serial_numbers as $key => $serial_number) {
                    $result[$key] = $data;
                    $result[$key]['qty'] = 1;
                    $result[$key]['serial_number'] = strtoupper($serial_number);
                    $result[$key]['price'] = $price;
                }
            }
        } else {
            $serial_numbers = $this->controller->Product->$modelSn->getMergeAll(array(), 'all', $product_id, $session_id, $modelSn.'.session_id');

            if( !empty($serial_numbers[$modelSn]) ) {
                foreach ($serial_numbers[$modelSn] as $key => $value) {
                    $serial_number = $this->MkCommon->filterEmptyField($value, $modelSn, 'serial_number');

                    $result[$key] = $data;
                    $result[$key]['qty'] = 1;
                    $result[$key]['serial_number'] = strtoupper($serial_number);
                    $result[$key]['price'] = $price;
                }
            }
        }

        return $result;
    }

    function _callCheckStock ( $product_id, $qty, $serial_number = false, $stock_id = false ) {
        $conditions = array(
            'conditions' => array(
                'ProductStock.id NOT' => $stock_id,
                'ProductStock.product_id' => $product_id,
            ),
        );

        if( !empty($serial_number) ) {
            $conditions['conditions']['ProductStock.serial_number'] = $serial_number;
            $elements = 'in_stock';
        } else {
            $elements = 'FIFO';
        }

        $stock = $this->controller->Product->ProductStock->getData('first', $conditions, array(
            'status' => $elements,
        ));

        $id = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'id');
        $product_history_id = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'product_history_id');
        $stok_awal = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'qty');
        $qty_remain = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'qty_total');
        $qty_use = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'qty_use');
        $price = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'price');
        $qty_total = $qty_remain - $qty;
        $qty_use += $qty;

        if( $qty_total < 0 ) {
            $status = false;
            $qty_use = $stok_awal;
        } else {
            $status = true;
        }

        $result = array(
            'id' => $id,
            'product_history_id' => $product_history_id,
            'product_id' => $product_id,
            'qty_use' => $qty_use,
            'qty_remain' => $qty_remain,
            'status' => $status,
        );

        if( !empty($serial_number) ) {
            return $result;   
        } else {
            $serial_number = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'serial_number');
            $result['serial_number'] = strtoupper($serial_number);

            return array(
                'price' => $price,
                'ProductStock' => $result,
            );   
        }
    }

    function _callOutStock ( $product_id, $qty, $serial_number = false ) {
        $flag = true;
        $result = array();

        if( !empty($serial_number) ) {
            $result = $this->_callCheckStock($product_id, $qty, $serial_number);
        } else {
            $stocks = array();

            while ($flag) {
                $resultTmp = $this->_callCheckStock($product_id, $qty, false, $stocks);
                $stock_id = Common::hashEmptyField($resultTmp, 'ProductStock.id');

                if( !empty($stock_id) ) {
                    $stocks[] = $stock_id;
                    $status = Common::hashEmptyField($resultTmp, 'ProductStock.status');
                    $qty_remain = Common::hashEmptyField($resultTmp, 'ProductStock.qty_remain');

                    if( !empty($status) ) {
                        $flag = false;
                        $qty_out = $qty;
                    } else {
                        $qty -= $qty_remain;
                        $qty_out = $qty_remain;
                    }

                    $resultTmp['ProductStock']['qty_out'] = $qty_out;
                    $result[] = $resultTmp;
                } else {
                    $flag = false;
                }
            }
        }

        return $result;
    }

    function _callOutStockSerialNumber ( $detail, $stock_history, $serial_numbers, $modelDetail = 'ProductExpenditureDetail' ) {
        $result = array();

        if( !empty($serial_numbers) ) {
            foreach ($serial_numbers as $key => $value) {
                $product_id = $this->MkCommon->filterEmptyField($value, 'product_id');
                $serial_number = $this->MkCommon->filterEmptyField($value, 'serial_number');
                $price = $this->MkCommon->filterEmptyField($value, 'price');
                $qty_out = Common::hashEmptyField($value, 'qty', 1);

                $detail[$modelDetail]['Product']['ProductStock'][] = $this->_callOutStock( $product_id, $qty_out, $serial_number);

                if( !empty($detail['ProductHistory'][$price]['qty']) ) {
                    $detail['ProductHistory'][$price]['qty'] += $qty_out;
                } else {
                    $detail['ProductHistory'][$price] = $stock_history;
                    $detail['ProductHistory'][$price]['qty'] = $qty_out;
                    $detail['ProductHistory'][$price]['price'] = $price;
                }
            }
        }

        return $detail;
    }

    function _callStock ( $transaction_type, $data, $detail, $type = 'in', $model = 'ProductReceipt', $documentDetail = null, $modelDetail = 'ProductExpenditureDetail', $modelSn = 'ProductExpenditureDetailSerialNumber' ) {
        $transaction_status = $this->MkCommon->filterEmptyField($data, $model, 'transaction_status');
        $document_type = $this->MkCommon->filterEmptyField($data, $model, 'document_type');
        $transaction_date = $this->MkCommon->filterEmptyField($data, $model, 'transaction_date');
        $session_id = $this->MkCommon->filterEmptyField($data, $model, 'session_id');
        $to_branch_id = $this->MkCommon->filterEmptyField($data, $model, 'to_branch_id', Configure::read('__Site.config_branch_id'));
        $modelDetail = __('%sDetail', $model);

        if( $transaction_status == 'posting' ) {
            $product_id = $this->MkCommon->filterEmptyField($detail, $modelDetail, 'product_id');
            $qty = $this->MkCommon->filterEmptyField($detail, $modelDetail, 'qty');
            $price = $this->MkCommon->filterEmptyField($detail, $modelDetail, 'price');

            $history = $this->controller->Product->ProductHistory->getMerge(array(), $product_id);
            $balance = $this->MkCommon->filterEmptyField($history, 'ProductHistory', 'ending', 0);
            $ending = $balance;
            $stock_qty = $this->controller->Product->ProductHistory->_callStockTransaction($product_id, $transaction_date);

            if( $type == 'out' ) {
                $ending -= $qty;
                $serial_numbers = $this->MkCommon->filterEmptyField($detail, $modelDetail, $modelSn);
            } else if( $type == 'in' ) {
                if( !in_array($document_type, array( 'spk' )) ) {
                    $ending += $qty;
                }

                $serial_number = $this->MkCommon->filterEmptyField($detail, $modelDetail, 'serial_number');
            }

            $stock_history = array(
                'branch_id' => $to_branch_id,
                'transaction_date' => $transaction_date,
                'balance' => $balance,
                'ending' => $ending,
                'product_id' => $product_id,
                'transaction_type' => $transaction_type,
                'type' => $type,
                'qty' => $qty,
                'price' => $price,
            );
            $detail['ProductHistory'] = $stock_history;
            $stock = $stock_history;

            if( in_array($document_type, array( 'spk' )) ) {
                // $price_service = Common::hashEmptyField($documentDetail, 'SpkProduct.price_service', 0);
                $price_service = Common::hashEmptyField($documentDetail, 'SpkProduct.price', 0);
                
                $stock['price'] = $detail['ProductHistory']['price'] = $price_service;
                $stock['type'] = $detail['ProductHistory']['product_type'] = 'barang_bekas';
            } else {
                $stock['type'] = 'default';
            }

            if( $qty > $stock_qty && empty($serial_numbers) ) {
                $detail[$modelDetail]['out_stock'] = true;
            }

            switch ($type) {
                case 'in':
                    if( !empty($serial_number) ) {
                        switch ($document_type) {
                            case 'production':
                                $price = Common::hashEmptyField($documentDetail, 'SpkProduction.price');
                                $detail['ProductHistory']['price'] = $price;
                                $detail['ProductHistory']['ProductStock'] = $this->_callStockSerialNumber( $session_id, $product_id, $stock, $price );
                                break;
                            case 'wht':
                                $serial_numbers = $this->controller->Product->ProductReceiptDetailSerialNumber->getMergeAll(array(), 'all', $product_id, $session_id, 'ProductReceiptDetailSerialNumber.session_id');
                                $product_expenditure_detail_id = Set::extract('/ProductReceiptDetail/Product/'.$modelDetail.'/id', $detail);
                                $detail_serial_numbers = $this->controller->Product->ProductExpenditureDetailSerialNumber->getMergeAll(array(), 'all', $product_id, $product_expenditure_detail_id, 'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id');

                                $result = array();
                                $total_price = 0;
                                
                                if( !empty($detail_serial_numbers['ProductExpenditureDetailSerialNumber']) ) {
                                    foreach ($detail_serial_numbers['ProductExpenditureDetailSerialNumber'] as $key => $val) {
                                        $sn_id = Common::hashEmptyField($val, 'ProductExpenditureDetailSerialNumber.id');
                                        $price = Common::hashEmptyField($val, 'ProductExpenditureDetailSerialNumber.price');
                                        
                                        if( !empty($serial_numbers['ProductReceiptDetailSerialNumber'][$key]) ) {
                                            $snArr = $serial_numbers['ProductReceiptDetailSerialNumber'][$key];
                                            $serial_number = Common::hashEmptyField($snArr, 'ProductReceiptDetailSerialNumber.serial_number');
                                            $serial_number = strtoupper($serial_number);
                                            
                                            $total_price += $price;

                                            $result[$key] = $stock;
                                            $result[$key]['qty'] = 1;
                                            $result[$key]['serial_number'] = $serial_number;
                                            $result[$key]['price'] = $price;

                                            $detail['ProductReceiptDetail']['Product']['ProductExpenditureDetailSerialNumber'][] = array(
                                                'id' => $sn_id,
                                                'qty_use' => 1,
                                            );
                                        }
                                    }
                                
                                    $total_price = $total_price / count($detail_serial_numbers['ProductExpenditureDetailSerialNumber']);
                                    $detail['ProductHistory']['price'] = $total_price;
                                }

                                $detail['ProductHistory']['ProductStock'] = $result;
                                break;
                            default:
                                if( $transaction_type == 'product_adjustment_plus' ) {
                                    $detailHistory = Common::hashEmptyField($detail, 'ProductHistory');
                                    $detailHistory['ProductStock'] = $this->_callStockSerialNumber( $session_id, $product_id, $stock, $price, 'ProductAdjustmentDetailSerialNumber', $data );

                                    $detail['ProductHistory'] = array(
                                        $detailHistory,
                                    );
                                    
                                    $session_id = Common::hashEmptyField($data, 'ProductAdjustment.session_id');
                                    
                                    $this->controller->Product->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->virtualFields['qty'] = '1';
                                    $productAdjustmentSN = $this->controller->Product->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->find('all', array(
                                        'conditions' => array(
                                            'ProductAdjustmentDetailSerialNumber.product_id' => $product_id,
                                            'ProductAdjustmentDetailSerialNumber.session_id' => $session_id,
                                        ),
                                        'fields' => array(
                                            'ProductAdjustmentDetailSerialNumber.product_id',
                                            'ProductAdjustmentDetailSerialNumber.serial_number',
                                            'ProductAdjustmentDetailSerialNumber.qty',
                                        ),
                                    ));

                                    if( !empty($serial_number) ) {
                                        $detail[$modelDetail][$modelSn] = $productAdjustmentSN;
                                    }
                                } else {
                                    $detail['ProductHistory']['ProductStock'] = $this->_callStockSerialNumber( $session_id, $product_id, $stock, $price, $modelSn, $data );
                                }
                                break;
                        }
                    } else {
                        switch ($document_type) {
                            case 'wht':
                                $product_expenditure_detail_id = Set::extract('/ProductReceiptDetail/Product/ProductExpenditureDetail/id', $detail);
                                $serial_numbers = $this->controller->Product->ProductExpenditureDetailSerialNumber->getMergeAll(array(), 'all', $product_id, $product_expenditure_detail_id, 'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id');

                                if( !empty($serial_numbers['ProductExpenditureDetailSerialNumber']) ) {
                                    $total_price = 0;
                                    $totalQtyExpenditure = 0;
                                    $detail['ProductReceiptDetail']['serial_number'] = true;

                                    foreach ($serial_numbers['ProductExpenditureDetailSerialNumber'] as $key => $val) {
                                        $sn_id = Common::hashEmptyField($val, 'ProductExpenditureDetailSerialNumber.id');
                                        $serial_number = Common::hashEmptyField($val, 'ProductExpenditureDetailSerialNumber.serial_number');
                                        $qtyExpenditure = Common::hashEmptyField($val, 'ProductExpenditureDetailSerialNumber.qty');
                                        $price = Common::hashEmptyField($val, 'ProductExpenditureDetailSerialNumber.price');
                                        
                                        $total_price += $price;
                                        $totalQtyExpenditure += $qtyExpenditure;

                                        if( $totalQtyExpenditure > $qty ) {
                                            $qtyExpenditure = $qty;
                                        }

                                        $detail['ProductHistory']['ProductStock'][] = array_merge($stock, array(
                                            'serial_number' => $serial_number,
                                            'qty' => $qtyExpenditure,
                                            'price' => $price,
                                        ));
                                        $detail['ProductReceiptDetail']['Product']['ProductExpenditureDetailSerialNumber'][] = array(
                                            'id' => $sn_id,
                                            'qty_use' => $qtyExpenditure,
                                            'serial_number' => $serial_number,
                                        );

                                        if( $totalQtyExpenditure > $qty ) {
                                            break;
                                        }
                                    }
                                    
                                    $total_price = $total_price / count($serial_numbers['ProductExpenditureDetailSerialNumber']);
                                    $detail['ProductHistory']['price'] = $total_price;
                                }
                                break;
                            case 'production':
                                $price = Common::hashEmptyField($documentDetail, 'SpkProduction.price');
                                $detail['ProductHistory']['price'] = $price;
                                $detail['ProductHistory']['ProductStock'][] = array_merge($stock, array(
                                    'serial_number' => sprintf('%s-%s', Common::getNoRef($product_id), date('ymdHis')),
                                    'price' => $price,
                                ));
                                break;
                            
                            default:
                                if( $transaction_type == 'product_adjustment_plus' ) {
                                    $detailHistory = Common::hashEmptyField($detail, 'ProductHistory');
                                    $detailHistory['ProductStock'][] = array_merge($stock, array(
                                        'serial_number' => sprintf('%s-%s', Common::getNoRef($product_id), date('ymdHis')),
                                    ));

                                    $detail['ProductHistory'] = array(
                                        $detailHistory,
                                    );
                                } else {
                                    $detail['ProductHistory']['ProductStock'][] = array_merge($stock, array(
                                        'serial_number' => sprintf('%s-%s', Common::getNoRef($product_id), date('ymdHis')),
                                    ));
                                }
                                break;
                        }
                    }
                    break;
                
                default:
                    unset($detail['ProductHistory']);

                    if( !empty($serial_numbers) ) {
                        $detail = $this->_callOutStockSerialNumber( $detail, $stock_history, $serial_numbers, $modelDetail );
                    } else {
                        $result = $this->_callOutStock($product_id, $qty);

                        if( !empty($result) ) {
                            // $total_price = 0;

                            foreach ($result as $key => $val) {
                                $serial_number = Common::hashEmptyField($val, 'ProductStock.serial_number');
                                $qty_out = Common::hashEmptyField($val, 'ProductStock.qty_out');
                                $price = Common::hashEmptyField($val, 'price');
                                // $total_price += $price;

                                if( !empty($detail['ProductHistory'][$price]['qty']) ) {
                                    $detail['ProductHistory'][$price]['qty'] += $qty_out;
                                } else {
                                    $balance -= $qty_out;

                                    $detail['ProductHistory'][$price] = $stock_history;
                                    $detail['ProductHistory'][$price]['qty'] = $qty_out;
                                    $detail['ProductHistory'][$price]['price'] = $price;
                                }

                                $detail[$modelDetail]['Product']['ProductStock'][] = $this->MkCommon->filterEmptyField($val, 'ProductStock');
                                // $detail['ProductHistory'][$key]['ProductHistory']['qty'] = $qty_out;

                                if( !empty($serial_number) ) {
                                    $detail[$modelDetail][$modelSn][] = array(
                                        'serial_number' => $serial_number,
                                        'product_id' => $product_id,
                                        'qty' => $qty_out,
                                        'price' => $price,
                                    );
                                }

                                $detail[$modelDetail]['without_serial_number'] = true;
                            }

                            // $total_price = $total_price / count($result);
                            // $detail['ProductHistory']['price'] = $total_price;
                        }
                    }
                    break;
            }
        }

        return $detail;
    }

    function _callBeforeSaveReceipt ( $data, $id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'ProductReceipt' => array(
                        'transaction_date',
                    ),
                )
            ));
            $document_number = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_number');
            $document_type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'transaction_status');
            $session_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'session_id');
            $list_product_id = Common::hashEmptyField($data, 'ProductReceiptDetail.product_id');

            switch ($document_type) {
                case 'po':
                    $value = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->getMerge(array(), $document_number, 'PurchaseOrder.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
                    $reference_date = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'transaction_date');
                    break;

                case 'spk':
                    $value = $this->controller->Product->SpkProduct->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
                    $reference_date = $this->MkCommon->filterEmptyField($value, 'Spk', 'transaction_date');
                    break;

                case 'wht':
                    $value = $this->controller->Product->ProductExpenditureDetail->ProductExpenditure->getMerge(array(), $document_number, 'ProductExpenditure.nodoc');
                    $value = $this->controller->Product->ProductExpenditureDetail->ProductExpenditure->getMergeList($value, array(
                        'contain' => array(
                            'Spk' => array(
                                'elements' => array(
                                    'branch' => false,
                                ),
                            ),
                        ),
                    ));
                    $document_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'id');
                    $reference_date = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'transaction_date');
                    $data['ProductReceipt']['to_branch_id'] = Common::hashEmptyField($value, 'Spk.to_branch_id');
                    break;

                case 'production':
                    $value = $this->controller->Product->SpkProduction->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
                    $reference_date = $this->MkCommon->filterEmptyField($value, 'Spk', 'transaction_date');
                    break;
                
                default:
                    $document_id = '';
                    break;
            }

            $last_doc = $this->controller->Product->ProductReceiptDetail->getData('first', array(
                'conditions' => array(
                    'ProductReceipt.id <>' => $id,
                    'ProductReceiptDetail.product_id' => $list_product_id,
                    // 'ProductReceipt.document_id' => $document_id,
                    'ProductReceipt.document_type' => $document_type,
                    'ProductReceipt.status' => 1,
                ),
                'contain' => array(
                    'ProductReceipt',
                ),
                'order' => array(
                    'ProductReceipt.transaction_date' => 'ASC',
                    'ProductReceipt.id' => 'ASC',
                ),
            ));

            $transaction_date = Common::hashEmptyField($data, 'ProductReceipt.transaction_date');
            $last_transaction_date = Common::hashEmptyField($last_doc, 'ProductReceipt.transaction_date');
            $data['ProductReceipt']['last_transaction_date'] = $last_transaction_date;

            if( $transaction_date < $last_transaction_date ) {
                $data['ProductReceipt']['invalid_date'] = true;
            }

            $data['ProductReceipt']['id'] = $id;
            $data['ProductReceipt']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductReceipt']['document_id'] = $document_id;
            $data['ProductReceipt']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['ProductReceipt']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['ProductReceipt']['reference_date'] = $reference_date;

            $details = $this->MkCommon->filterEmptyField($data, 'ProductReceiptDetail', 'product_id');
            $receiptQty = $this->MkCommon->filterEmptyField($data, 'ProductReceiptDetail', 'qty');

            if( !empty($details) ) {
                $total = 0;
                $dataDetail = array();
                $values = array_filter($details);

                foreach ($values as $key => $product_id) {
                    $qty = $this->MkCommon->filterIssetField($receiptQty, $key);

                    $product = $this->controller->Product->getMerge(array(), $product_id);

                    $code = $this->MkCommon->filterEmptyField($product, 'Product', 'code');
                    $name = $this->MkCommon->filterEmptyField($product, 'Product', 'name');
                    $is_serial_number = $this->MkCommon->filterEmptyField($product, 'Product', 'is_serial_number');
                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');

                    if( !empty($is_serial_number) ) {
                        $serial_number = $this->controller->Product->ProductReceiptDetailSerialNumber->getCount($session_id, $product_id);
                    } else {
                        $serial_number = 0;
                    }

                    if( $qty != $serial_number ) {
                        $serial_number = 0;
                    }

                    $dataDetail[$key]['ProductReceiptDetail'] = array(
                        'product_id' => $product_id,
                        'is_serial_number' => $is_serial_number,
                        'code' => $code,
                        'name' => $name,
                        'unit' => $unit,
                        'qty' => $qty,
                        'serial_number' => $serial_number,
                    );

                    switch ($document_type) {
                        case 'spk':
                            $documentDetail = $this->controller->Product->SpkProduct->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $document_type, $product_id);
                            $total_receipt = $qty_receipt + $qty;
                            
                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'id');
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'qty');
                            $detailPrice = 0;

                            $model = 'SpkProduct';
                            break;
                        case 'wht':
                            $documentDetail = $this->controller->Product->ProductExpenditureDetail->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $document_type, $product_id);
                            $total_receipt = $qty_receipt + $qty;
                            
                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'ProductExpenditureDetail', 'id');
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'ProductExpenditureDetail', 'qty');
                            $detailPrice = 0;

                            $model = 'ProductExpenditureDetail';
                            unset($dataDetail[$key]['ProductReceiptDetail']['serial_number']);
                            break;
                        case 'production':
                            $documentDetail = $this->controller->Product->SpkProduction->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $document_type, $product_id);
                            $total_receipt = $qty_receipt + $qty;
                            
                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduction', 'id');
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduction', 'qty');
                            $detailPrice = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduction', 'price');

                            $model = 'SpkProduction';
                            break;
                        default:
                            $documentDetail = $this->controller->Product->PurchaseOrderDetail->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $document_type, $product_id);
                            $total_receipt = $qty_receipt + $qty;
                            
                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'id');
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'qty');
                            $detailPrice = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'price');
                            
                            $model = 'PurchaseOrderDetail';
                            break;
                    }

                    if( $total_receipt >= $detailQty ) {
                        $receipt_detail_status = 'full';
                    } else {
                        $receipt_detail_status = 'half';
                    }

                    if( $total_receipt > $detailQty ) {
                        $over_receipt = true;
                    } else {
                        $over_receipt = false;
                    }

                    $dataDetail[$key]['ProductReceiptDetail']['document_detail_id'] = $detailId;
                    $dataDetail[$key]['ProductReceiptDetail']['doc_qty'] = $detailQty;
                    $dataDetail[$key]['ProductReceiptDetail']['in_qty'] = $qty_receipt;
                    $dataDetail[$key]['ProductReceiptDetail']['over_receipt'] = $over_receipt;
                    $dataDetail[$key]['ProductReceiptDetail']['price'] = $detailPrice;
                    $dataDetail[$key]['ProductReceiptDetail']['Product'] = array(
                        'id' => $product_id,
                        'truck_category_id' => 1,
                        $model => array(
                            array(
                                'id' => $detailId,
                                'receipt_status' => $receipt_detail_status,
                            ),
                        ),
                    );

                    $dataDetail[$key] = $this->_callStock('product_receipt', $data, $dataDetail[$key], 'in', 'ProductReceipt', $documentDetail, 'ProductReceiptDetail', 'ProductReceiptDetailSerialNumber');

                    $total += $qty;
                }

                $data['ProductReceipt']['total'] = $total;
                $data['ProductReceiptDetail'] = $dataDetail;
            }
        }

        return $data;
    }

    function _callBeforeRenderReceipt ( $data, $value = false ) {
        $document_id = false;

        if( empty($data) ) {
            $data = $value;

            if( empty($data) ) {
                $data['ProductReceipt']['document_type'] = 'po';
            }

            $id = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'id');
            $type = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_id');
            $details = $this->MkCommon->filterEmptyField($value, 'ProductReceiptDetail');

            $data['ProductReceipt']['document_number'] = $this->MkCommon->filterEmptyField($data, 'Document', 'nodoc');
            $data['ProductReceipt']['to_branch_id'] = Configure::read('__Site.config_branch_id');

            if( empty($value) ) {
                $data['ProductReceipt']['session_id'] = String::uuid();
                $data['ProductReceipt']['transaction_date'] = date('Y-m-d');
            }

            if( !empty($details) ) {
                foreach ($details as $key => &$detail) {
                    $product = $this->MkCommon->filterEmptyField($detail, 'Product');
                    $product_id = $this->MkCommon->filterEmptyField($detail, 'ProductReceiptDetail', 'product_id');

                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');
                    $detail['Product']['unit'] = $this->MkCommon->filterEmptyField($detail, 'ProductUnit', 'name', $unit);

                    switch ($type) {
                        case 'spk':
                            $documentDetail = $this->controller->Product->SpkProduct->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $type, $product_id);
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'qty');
                            break;
                        case 'wht':
                            $documentDetail = $this->controller->Product->ProductExpenditureDetail->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $type, $product_id);
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'ProductExpenditureDetail', 'qty');
                            break;
                        case 'production':
                            $documentDetail = $this->controller->Product->SpkProduction->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $type, $product_id);
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduction', 'qty');
                            break;
                        default:
                            $documentDetail = $this->controller->Product->PurchaseOrderDetail->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $type, $product_id);
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'qty');
                            break;
                    }
                            
                    $detail['ProductReceiptDetail']['doc_qty'] = $detailQty;
                    $detail['ProductReceiptDetail']['in_qty'] = $qty_receipt;
                }

                $data['ProductReceiptDetail'] = $details;
            }
        } else {
            $type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_id');
        }

        $data = $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'ProductReceipt' => array(
                    'transaction_date',
                ),
            )
        ), true);
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type');

        switch ($document_type) {
            case 'spk':
                $vendors = $this->controller->Product->SpkProduct->Spk->_callVendors('unreceipt_draft', $document_id);
                break;
            case 'wht':
                $vendors = $this->controller->Product->ProductExpenditureDetail->ProductExpenditure->_callVendors('untransfer_draft', $document_id);
                break;
            case 'production':
                $vendors = $this->controller->Product->SpkProduction->Spk->_callVendors('unreceipt_draft', $document_id, 'production');
                break;
            default:
                $vendors = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->_callVendors('unreceipt_draft', $document_id);
                break;
        }

        $this->controller->request->data = $data;

        $employes = $this->controller->User->Employe->getData('list', array(
        	'fields' => array(
        		'Employe.id', 'Employe.full_name',
    		),
    		'contain' => false,
    	));
        $toBranches = $this->controller->GroupBranch->Branch->getData('list', array(
        	'fields' => array(
        		'Branch.id', 'Branch.code',
    		),
    		'contain' => false,
    	));

        $settings = $this->MkCommon->_callSettingGeneral('Product', 'spk_internal_policy', false);
        $spk_internal_policy = $this->MkCommon->filterEmptyField($settings, 'Product', 'spk_internal_policy');

        $this->MkCommon->_layout_file('select');
    	$this->controller->set(compact(
    		'employes', 'toBranches',
            'vendors', 'type', 'spk_internal_policy'
		));
    }

    function _callPurchaseOrders( $params, $vendor_id = false, $status = 'unreceipt_draft' ) {
    	$this->controller->loadModel('PurchaseOrder');
        $options =  $this->controller->PurchaseOrder->_callRefineParams($params, array(
            'conditions' => array(
                'PurchaseOrder.vendor_id' => $vendor_id,
            ),
            'limit' => 10,
        ));
        $this->controller->paginate = $this->controller->PurchaseOrder->getData('paginate', $options, array(
            'status' => $status,
        ));
        $values = $this->controller->paginate('PurchaseOrder');

        $this->controller->PurchaseOrder->PurchaseOrderDetail->virtualFields['total_qty'] = 'SUM(PurchaseOrderDetail.qty)';
        $values = $this->controller->PurchaseOrder->getMergeList($values, array(
            'contain' => array(
                'PurchaseOrderDetail' => array(
                    'type' => 'first',
                ),
            ),
        ));

        return $values;
    }

    // function _callPurchaseOrder( $data ) {
    //     $document_number = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_number');
    //     $vendor_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'vendor_id');

    //     $value =  $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->getData('first', array(
    //         'conditions' => array(
    //             'PurchaseOrder.vendor_id' => $vendor_id,
    //             'PurchaseOrder.nodoc' => $document_number,
    //         ),
    //     ), array(
    //         'status' => 'unreceipt',
    //     ));

    //     $purchase_order_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
    //     $value =  $this->controller->Product->PurchaseOrderDetail->getMerge($value, $purchase_order_id, 'ProductReceipt');

    //     return $value;
    // }

    function _callBeforeSaveSerialNumber ( $data, $id = false, $session_id = false, $modelName = 'ProductReceiptDetailSerialNumber' ) {
        $dataSave = array();

        if( !empty($data) ) {
            if( !empty($data) ) {
                foreach ($data as $key => $serial_number) {
                    $dataSave[][$modelName] = array(
                        'product_id' => $id,
                        'session_id' => $session_id,
                        'serial_number' => strtoupper($serial_number),
                    );
                }
            }
        }

        return $dataSave;
    }

    function _callBeforeViewSerialNumber ( $values, $session_id = false ) {
        if( !empty($values) ) {
            $dataRequest = array();

            foreach ($values as $key => $value) {
                $serial_number = $this->MkCommon->filterEmptyField($value, 'ProductReceiptDetailSerialNumber', 'serial_number');
                $dataRequest['ProductReceiptDetailSerialNumber']['serial_number'][$key] = strtoupper($serial_number);
            }

            $this->controller->request->data = $dataRequest;
        }

        $this->controller->request->data['ProductReceipt']['session_id'] = $session_id;
    }

    function _callBeforeRenderReceipts () {
        $vendors = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->Vendor->getData('list');

        $this->controller->set(compact(
            'vendors'
        ));
    }

    function _callGetDocReceipt ( $value ) {
        $this->Product = ClassRegistry::init('Product'); 
        $document_id = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_id');
        $document_type = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_type');

        switch ($document_type) {
            case 'spk':
                $modalName = 'Spk';
                break;
            case 'wht':
                $modalName = 'ProductExpenditure';
                break;
            case 'production':
                $modalName = 'Spk';
                break;
            default:
                $modalName = 'PurchaseOrder';
                break;
        }

        $value = $this->Product->ProductReceiptDetail->ProductReceipt->getMergeList($value, array(
            'contain' => array(
                'Document' => array(
                    'uses' => $modalName,
                    'primaryKey' => 'id',
                    'foreignKey' => 'document_id',
                    'type' => 'first',
                    'elements' => array(
                        'branch' => false,
                    ),
                ),
            ),
        ));

        return $value;
    }

    function _callBeforeRenderExpenditures () {
    }

    function _callBeforeRenderExpenditure ( $data, $value = false ) {
        $document_id = false;

        if( empty($data) ) {
            $data = $value;

            $id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'id');
            $type = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'document_id');
            $transaction_status = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'transaction_status');
            $details = $this->MkCommon->filterEmptyField($value, 'ProductExpenditureDetail');

            $data['ProductExpenditure']['document_number'] = $this->MkCommon->filterEmptyField($data, 'Spk', 'nodoc');
            $serial_numbers = Set::extract('/ProductExpenditureDetail/ProductExpenditureDetailSerialNumber/ProductExpenditureDetailSerialNumber', $data);

            if( empty($value) ) {
                $data['ProductExpenditure']['transaction_date'] = date('Y-m-d');
            }

            if( !empty($details) ) {
                foreach ($details as $key => $val) {
                    $product_expenditure_detail_id = $this->MkCommon->filterEmptyField($val, 'ProductExpenditureDetail', 'id');
                    $product_id = $this->MkCommon->filterEmptyField($val, 'ProductExpenditureDetail', 'product_id');
                    $is_serial_number = $this->MkCommon->filterEmptyField($val, 'Product', 'is_serial_number');
                    
                    $spk_detail = $this->controller->Product->SpkProduct->getMergeProduct(array(), $document_id, $product_id);
                    $spk_qty = $this->MkCommon->filterEmptyField($spk_detail, 'SpkProduct', 'qty');
                    
                    $out_qty = $this->controller->Product->ProductExpenditureDetail->getTotalExpenditure($id, $document_id, $product_id);

                    // if( !empty($is_serial_number) ) {
                    //     $product_serial_numbers = $this->controller->Product->ProductStock->_callSerialNumbers($product_id, $id);

                        // if( $transaction_status == 'posting' ) {
                        //     $product_serial_numbers = array_merge($product_serial_numbers, $this->controller->Product->ProductExpenditureDetailSerialNumber->getData('list', array(
                        //         'conditions' => array(
                        //             'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
                        //             'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id' => $product_expenditure_detail_id,
                        //         ),
                        //         'fields' => array(
                        //             'ProductExpenditureDetailSerialNumber.serial_number',
                        //             'ProductExpenditureDetailSerialNumber.serial_number',
                        //         ),
                        //         'group' => array(
                        //             'ProductExpenditureDetailSerialNumber.serial_number',
                        //         ),
                        //     )));
                        // }

                    //     $data['ProductExpenditureDetail'][$key]['ProductExpenditureDetail']['serial_numbers'] = $product_serial_numbers;
                    // }
                    
                    $data['ProductExpenditureDetail'][$key]['ProductExpenditureDetail']['spk_qty'] = $spk_qty;
                    $data['ProductExpenditureDetail'][$key]['ProductExpenditureDetail']['out_qty'] = $out_qty;
                }
            }
        } else {
            $type = $this->MkCommon->filterEmptyField($data, 'ProductExpenditure', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($data, 'ProductExpenditure', 'document_id');
            $details = $this->MkCommon->filterEmptyField($data, 'ProductExpenditureDetail');
            $serial_numbers = array();

            if( !empty($details) ) {
                foreach ($details as $key => $detail) {
                    $without_serial_number = $this->MkCommon->filterEmptyField($detail, 'ProductExpenditureDetail', 'without_serial_number');
                    $detail_serial_numbers = Common::hashEmptyField($detail, 'ProductExpenditureDetail.ProductExpenditureDetailSerialNumber', array());

                    if( empty($without_serial_number) ) {
                        $serial_numbers = array_merge($serial_numbers, $detail_serial_numbers);
                    }
                }
            }
        }

        if( !empty($serial_numbers) ) {
            $data['ProductExpenditureDetailSerialNumber'] = array();
            
            foreach ($serial_numbers as $key => $value) {
                if( !empty($value['ProductExpenditureDetailSerialNumber']) ) {
                    $product_id = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber.product_id');
                    $serial_number = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber.serial_number');
                } else {
                    $product_id = Common::hashEmptyField($value, 'product_id');
                    $serial_number = Common::hashEmptyField($value, 'serial_number');
                }

                if( !empty($serial_number) ) {
                    $serial_number = strtoupper($serial_number);
                    $data['ProductExpenditureDetailSerialNumber']['serial_numbers'][$product_id][$serial_number] = $serial_number;
                }
            }
        }

        $data = $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'ProductExpenditure' => array(
                    'transaction_date',
                ),
            )
        ), true);
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductExpenditure', 'document_type');

        $this->controller->request->data = $data;

        $employes = $this->controller->User->Employe->getData('list', array(
            'fields' => array(
                'Employe.id', 'Employe.full_name',
            ),
            'contain' => false,
        ));
        $toBranches = $this->controller->GroupBranch->Branch->getData('list', array(
            'fields' => array(
                'Branch.id', 'Branch.code',
            ),
            'contain' => false,
        ));

        $this->MkCommon->_layout_file('select');
        $this->controller->set(compact(
            'employes', 'toBranches',
            'vendors', 'type'
        ));
    }

    function _callExpenditureSN ( $data, $product, $details, $modelSn = 'ProductExpenditureDetailSerialNumber' ) {
        $detail_serial_numbers = $this->MkCommon->filterEmptyField($data, $modelSn, 'serial_numbers');

        $product_id = $this->MkCommon->filterEmptyField($product, 'Product', 'id');
        $is_serial_number = $this->MkCommon->filterEmptyField($product, 'Product', 'is_serial_number');
        $product_serial_numbers = $this->MkCommon->filterIssetField($detail_serial_numbers, $product_id);
        
        $qty = $this->MkCommon->filterEmptyField($details, 'qty');

        // if( !empty($is_serial_number) ) {
            if( !empty($product_serial_numbers) ) {
                $count_sn = count($product_serial_numbers);

                if( $qty != $count_sn ) {
                    $details['sn_match'] = true;
                }

                foreach ($product_serial_numbers as $idx => $serial_number) {
                    $stock = $this->controller->Product->ProductStock->getData('first', array(
                        'conditions' => array(
                            'ProductStock.product_id' => $product_id,
                            'ProductStock.serial_number' => $serial_number,
                        ),
                    ), array(
                        'status' => 'in_stock',
                    ));
                    $stock_sn = Common::hashEmptyField($stock, 'ProductStock.qty', 0);
                    $qty_out = 1;

                    if( !empty($details['sn_match']) ) {
                        if( $stock_sn >= $qty ) {
                            $qty_out = $qty;
                            unset($details['sn_match']);
                        } else {
                            $details['out_stock'] = true;
                        }
                    }

                    $details[$modelSn][] = array(
                        'product_id' => $product_id,
                        'serial_number' => strtoupper($serial_number),
                        'price' => Common::hashEmptyField($stock, 'ProductStock.price', 0),
                        'qty' => $qty_out,
                    );
                }
            } else if( !empty($is_serial_number) ) {
                $details['sn_empty'] = true;
            }
        // }

        return $details;
    }

    function _callBeforeSaveExpenditure ( $data, $id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'ProductExpenditure' => array(
                        'transaction_date',
                    ),
                )
            ));
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'ProductExpenditure', 'transaction_status');
            $document_number = $this->MkCommon->filterEmptyField($data, 'ProductExpenditure', 'document_number');

            $value = $this->controller->Product->ProductExpenditureDetail->ProductExpenditure->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'open');
            $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
            $document_type = $this->MkCommon->filterEmptyField($value, 'Spk', 'document_type');
            $transaction_date = $this->MkCommon->filterEmptyField($value, 'Spk', 'transaction_date');

            $data['ProductExpenditure']['id'] = $id;
            $data['ProductExpenditure']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductExpenditure']['document_id'] = $document_id;
            $data['ProductExpenditure']['document_type'] = $document_type;
            $data['ProductExpenditure']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['ProductExpenditure']['spk_date'] = $this->MkCommon->filterEmptyField($value, 'Spk', 'transaction_date');

            $details = $this->MkCommon->filterEmptyField($data, 'ProductExpenditureDetail', 'product_id');
            $qtys = $this->MkCommon->filterEmptyField($data, 'ProductExpenditureDetail', 'qty');

            if( !empty($details) ) {
                $total = 0;
                $dataDetail = array();
                $values = array_filter($details);

                foreach ($values as $key => $product_id) {
                    $qty = $this->MkCommon->filterIssetField($qtys, $key);

                    $product = $this->controller->Product->getMerge(array(), $product_id);
                    $spk_detail = $this->controller->Product->SpkProduct->getMergeProduct(array(), $document_id, $product_id);

                    $code = $this->MkCommon->filterEmptyField($product, 'Product', 'code');
                    $name = $this->MkCommon->filterEmptyField($product, 'Product', 'name');
                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');
                    $is_serial_number = $this->MkCommon->filterEmptyField($product, 'Product', 'is_serial_number');

                    $spk_product_id = $this->MkCommon->filterEmptyField($spk_detail, 'SpkProduct', 'id');
                    $spk_qty = $this->MkCommon->filterEmptyField($spk_detail, 'SpkProduct', 'qty');
                    $out_qty = $this->controller->Product->ProductExpenditureDetail->getTotalExpenditure($id, $document_id, $product_id);
                    $remain_qty = $spk_qty - $out_qty;

                    // if( !empty($is_serial_number) ) {
                    //     $serial_numbers = $this->controller->Product->ProductStock->_callSerialNumbers($product_id, $id);
                    // } else {
                    //     $serial_numbers = false;
                    // }

                    if( $qty >= $remain_qty ) {
                        $status = 'full';
                    } else {
                        $status = 'half';
                    }

                    $dataDetail[$key]['ProductExpenditureDetail'] = array(
                        'product_id' => $product_id,
                        'spk_product_id' => $spk_product_id,
                        'code' => $code,
                        'name' => $name,
                        'unit' => $unit,
                        'spk_qty' => $spk_qty,
                        'out_qty' => $out_qty,
                        'qty' => $qty,
                        'is_serial_number' => $is_serial_number,
                        // 'serial_numbers' => $serial_numbers,
                        'qty_over' => ($qty > $remain_qty)?true:false,
                    );
                    $dataDetail[$key]['ProductExpenditureDetail'] = $this->_callExpenditureSN($data, $product, $dataDetail[$key]['ProductExpenditureDetail']);

                    if( !empty($qty) ) {
                        $dataDetail[$key]['ProductExpenditureDetail']['Product'] = array(
                            'id' => $product_id,
                            'truck_category_id' => 1,
                        );
                        $dataDetail[$key]['ProductExpenditureDetail']['SpkProduct'] = array(
                            'id' => $spk_product_id,
                            'draft_document_status' => $status,
                        );

                        if( $transaction_status == 'posting' ) {
                            $dataDetail[$key]['ProductExpenditureDetail']['SpkProduct']['document_status'] = $status;
                        }

                        $dataDetail[$key] = $this->_callStock('product_expenditure', $data, $dataDetail[$key], 'out', 'ProductExpenditure');

                        if( empty($is_serial_number) && empty($dataDetail[$key]['ProductExpenditureDetail']['Product']['ProductStock']) ) {
                            // if( $transaction_status != 'posting' ) {
                                $stock_qty = $this->controller->Product->ProductHistory->_callStockTransaction($product_id, $transaction_date);

                                if($qty > $stock_qty ) {
                                    $dataDetail[$key]['ProductExpenditureDetail']['out_stock'] = true;
                                }
                            // } else {
                            //     $dataDetail[$key]['ProductExpenditureDetail']['sn_match'] = true;
                            // }
                        }
                    }

                    $total += $qty;
                }

                $data['ProductExpenditure']['total'] = $total;
                $data['ProductExpenditureDetail'] = $dataDetail;
            }
        }

        return $data;
    }

    function _callBeforeRenderSpkProducts ( $values, $transaction_id = false ) {
        if( !empty($values) ) {
            foreach ($values as $key => $value) {
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
                $document_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'spk_id');
                $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'product_id');
                $qty = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'qty');
                $out_qty = $this->controller->Product->ProductExpenditureDetail->getTotalExpenditure($transaction_id, $document_id, $product_id);
                // $qty -= $out_qty;

                if( !empty($qty) ) {
                    $is_serial_number = $this->MkCommon->filterEmptyField($value, 'Product', 'is_serial_number');

                    $value['SpkProduct']['qty'] = $qty;
                    $value['SpkProduct']['out_qty'] = $out_qty;

                    // if( !empty($is_serial_number) ) {
                    //     $serial_numbers = $this->controller->Product->ProductStock->_callSerialNumbers($product_id, $transaction_id);
                    //     $value['Product']['serial_numbers'] = $serial_numbers;
                    // }
                    
                    $values[$key] = $value;
                } else {
                    unset($values[$key]);
                }
            }
        }

        $this->controller->set('module_title', __('Barang'));
        $this->controller->set(compact(
            'values'
        ));
    }

    function _callBeforeRenderReceiptPODetails ( $values, $transaction_id = false ) {
        $data = $this->controller->request->data;
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type', 'po');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->controller->Product->PurchaseOrderDetail->getMergeList($value, array(
                    'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                                'ProductCategory',
                            ),
                        ),
                    ),
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'purchase_order_id');
                $product_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'product_id');
                $total_qty = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');
                $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($transaction_id, $document_id, $document_type, $product_id);
                $qty_retur = $this->controller->Product->ProductReturDetail->getTotalRetur(false, $document_id, 'po', $product_id);

                $total_qty -= $qty_retur;
                $qty = $total_qty - $in_qty;

                if( !empty($qty) ) {
                    $value['PurchaseOrderDetail']['total_qty'] = $total_qty;
                    $value['PurchaseOrderDetail']['qty'] = $qty;
                    $value['PurchaseOrderDetail']['in_qty'] = $in_qty;
                    $values[$key] = $value;
                } else {
                    unset($values[$key]);
                }
            }
        }

        $this->controller->set('module_title', __('Barang'));
        $this->controller->set(compact(
            'values'
        ));
    }

    function _callBeforeRenderReceiptSpkProducts ( $values, $transaction_id = false ) {
        $data = $this->controller->request->data;
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_type', 'spk');

        if( !empty($values) ) {
            $nodelProduct = 'SpkProduct';

            foreach ($values as $key => $value) {
                switch ($document_type) {
                    case 'wht':
                        $document_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditureDetail', 'product_expenditure_id');
                        $product_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditureDetail', 'product_id');
                        $qty = $this->MkCommon->filterEmptyField($value, 'ProductExpenditureDetail', 'qty');
                        
                        $nodelName = 'ProductExpenditureDetail';
                        break;
                    case 'production':
                        $document_id = $this->MkCommon->filterEmptyField($value, 'SpkProduction', 'spk_id');
                        $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduction', 'product_id');
                        $qty = $this->MkCommon->filterEmptyField($value, 'SpkProduction', 'qty');

                        $nodelProduct = $nodelName = 'SpkProduction';
                        break;
                    default:
                        $document_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'spk_id');
                        $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'product_id');
                        $qty = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'qty');

                        $nodelName = 'SpkProduct';
                        break;
                }

                $value = $this->controller->Product->$nodelName->getMergeList($value, array(
                    'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                                'ProductCategory',
                            ),
                        ),
                    ),
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'spk_id');
                $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'product_id');
                $total_qty = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'qty');
                $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($transaction_id, $document_id, $document_type, $product_id);
                $qty_retur = $this->controller->Product->ProductReturDetail->getTotalRetur(false, $document_id, 'spk', $product_id);

                $total_qty -= $qty_retur;
                $qty = $total_qty - $in_qty;

                if( !empty($qty) ) {
                    $value[$nodelProduct]['total_qty'] = $total_qty;
                    $value[$nodelProduct]['qty'] = $qty;
                    $value[$nodelProduct]['in_qty'] = $in_qty;
                    $values[$key] = $value;
                } else {
                    unset($values[$key]);
                }
            }
        }

        $this->controller->set('module_title', __('Barang'));
        $this->controller->set(compact(
            'values'
        ));
    }

    function _callSpkInternals( $params, $vendor_id = false, $type = null ) {
        $this->controller->loadModel('Spk');
        $options =  $this->controller->Spk->_callRefineParams($params, array(
            'conditions' => array(
                'Spk.vendor_id' => $vendor_id,
            ),
            'limit' => 10,
        ));
        $this->controller->paginate = $this->controller->Spk->getData('paginate', $options, array(
            'status' => 'unreceipt_draft',
            'type' => $type,
        ));
        $values = $this->controller->paginate('Spk');

        return $values;
    }

    // function _callSpkInternal( $data ) {
    //     $document_number = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_number');
    //     $vendor_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'vendor_id');

    //     $value =  $this->controller->Product->SpkProduct->Spk->getData('first', array(
    //         'conditions' => array(
    //             'Spk.vendor_id' => $vendor_id,
    //             'Spk.nodoc' => $document_number,
    //         ),
    //     ), array(
    //         'status' => 'unreceipt',
    //     ));

    //     $spk_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
    //     $value =  $this->controller->Product->SpkProduct->getMerge($value, $spk_id, 'ProductReceipt');

    //     return $value;
    // }

    function _callWHts( $params, $vendor_id = false ) {
        $this->controller->loadModel('ProductExpenditure');
        $options = array(
            'contain' => array(
                'Spk',
            ),
            'limit' => 10,
        );

        if( !empty($vendor_id) ) {
            $options['conditions']['Spk.vendor_id'] = $vendor_id;
        }

        $options =  $this->controller->ProductExpenditure->_callRefineParams($params, $options);
        $this->controller->paginate = $this->controller->ProductExpenditure->getData('paginate', $options, array(
            'status' => 'untransfer_draft',
            'branch' => false,
        ));
        $values = $this->controller->paginate('ProductExpenditure');

        return $values;
    }

    function _callProductions( $params, $vendor_id = false ) {
        $this->controller->loadModel('ProductExpenditure');
        $options = array(
            'contain' => array(
                'Spk',
            ),
            'limit' => 10,
        );

        if( !empty($vendor_id) ) {
            $options['conditions']['Spk.vendor_id'] = $vendor_id;
        }

        $options =  $this->controller->ProductExpenditure->_callRefineParams($params, $options);
        $this->controller->paginate = $this->controller->ProductExpenditure->getData('paginate', $options, array(
            'status' => 'unproduction_draft',
            'branch' => false,
        ));
        $values = $this->controller->paginate('ProductExpenditure');

        return $values;

        // $this->controller->loadModel('Spk');
        // $options = array(
        //     'limit' => 10,
        // );

        // if( !empty($vendor_id) ) {
        //     $options['conditions']['Spk.vendor_id'] = $vendor_id;
        // }

        // $options =  $this->controller->Spk->_callRefineParams($params, $options);
        // $this->controller->paginate = $this->controller->Spk->getData('paginate', $options, array(
        //     'status' => 'unreceipt_draft',
        //     'type' => 'production',
        // ));
        // $values = $this->controller->paginate('Spk');

        // return $values;
    }

    function _callBeforeViewCurrentStockReports( $params ) {
        $productUnits = $this->controller->Product->ProductUnit->getData('list');
        $productCategories = $this->controller->Product->ProductCategory->getData('list');

        $title = __('Laporan Current Stok Per %s', date('d F Y'));
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'productUnits', 'productCategories'
        ));
    }

    function _callBeforeViewStockCards( $params ) {
        $productCategories = $this->controller->Product->ProductCategory->getData('list');

        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Kartu Stok');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'productCategories', 'period_text'
        ));
    }

    function _callBeforeViewExpenditureReports( $params ) {
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Laporan Pengeluaran');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'period_text'
        ));
    }

    function _callBeforeViewReceiptReports( $params ) {
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Laporan Penerimaan');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $vendors = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->Vendor->getData('list');
        
        $this->controller->set('vendors', $vendors);
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'period_text'
        ));
    }

    function _callBeforeSaveRetur ( $data, $id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'ProductRetur' => array(
                        'transaction_date',
                    ),
                )
            ));
            $document_number = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_number');
            $document_type = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_type');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'transaction_status');

            switch ($document_type) {
                case 'po':
                    $value = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->getMerge(array(), $document_number, 'PurchaseOrder.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
                    $reference_date = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'transaction_date');
                    break;

                case 'spk':
                    $value = $this->controller->Product->SpkProduct->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
                    $reference_date = $this->MkCommon->filterEmptyField($value, 'Spk', 'transaction_date');
                    break;
                
                default:
                    $document_id = '';
                    break;
            }

            $last_doc = $this->controller->Product->ProductReturDetail->ProductRetur->getData('first', array(
                'conditions' => array(
                    'ProductRetur.id <>' => $id,
                    'ProductRetur.document_id' => $document_id,
                    'ProductRetur.document_type' => $document_type,
                ),
                'order' => array(
                    'ProductRetur.transaction_date' => 'DESC',
                    'ProductRetur.id' => 'DESC',
                ),
            ));

            $transaction_date = Common::hashEmptyField($data, 'ProductRetur.transaction_date');
            $last_transaction_date = Common::hashEmptyField($last_doc, 'ProductRetur.transaction_date');
            $data['ProductRetur']['last_transaction_date'] = $last_transaction_date;

            if( $transaction_date < $last_transaction_date ) {
                $data['ProductRetur']['invalid_date'] = true;
            }

            $data['ProductRetur']['id'] = $id;
            $data['ProductRetur']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductRetur']['document_id'] = $document_id;
            $data['ProductRetur']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['ProductRetur']['reference_date'] = $reference_date;

            $details = $this->MkCommon->filterEmptyField($data, 'ProductReturDetail', 'product_id');
            $returQty = $this->MkCommon->filterEmptyField($data, 'ProductReturDetail', 'qty');

            if( !empty($details) ) {
                $total = 0;
                $dataDetail = array();
                $values = array_filter($details);

                foreach ($values as $key => $product_id) {
                    $qty = $this->MkCommon->filterIssetField($returQty, $key);

                    $product = $this->controller->Product->getMerge(array(), $product_id);

                    $code = $this->MkCommon->filterEmptyField($product, 'Product', 'code');
                    $name = $this->MkCommon->filterEmptyField($product, 'Product', 'name');
                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');

                    $dataDetail[$key]['ProductReturDetail'] = array(
                        'product_id' => $product_id,
                        'code' => $code,
                        'name' => $name,
                        'unit' => $unit,
                        'qty' => $qty,
                    );

                    switch ($document_type) {
                        case 'spk':
                            $documentDetail = $this->controller->Product->SpkProduct->getMergeData(array(), $document_id, $product_id, $id);
                            $qty_retur = $this->controller->Product->ProductReturDetail->getTotalRetur($id, $document_id, $document_type, $product_id);
                            $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt(null, $document_id, $document_type, $product_id);
                            $spk_qty = Common::hashEmptyField($documentDetail, 'SpkProduct.qty');
                            
                            $total_retur = $qty_retur + $qty;
                            $spk_qty -= $total_retur;

                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'id');
                            $detailQty = $poQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'qty');
                            $detailPrice = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'price');
                            $detailQty = $detailQty - $in_qty;

                            if( $spk_qty == $in_qty ) {
                                $receipt_status = 'full';
                            } else {
                                $receipt_status = Common::hashEmptyField($documentDetail, 'SpkProduct.receipt_status');
                            }

                            $model = 'SpkProduct';
                            break;
                        default:
                            $documentDetail = $this->controller->Product->PurchaseOrderDetail->getMergeData(array(), $document_id, $product_id, $id);
                            $qty_retur = $this->controller->Product->ProductReturDetail->getTotalRetur($id, $document_id, $document_type, $product_id);
                            $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt(null, $document_id, $document_type, $product_id);
                            $po_qty = Common::hashEmptyField($documentDetail, 'PurchaseOrderDetail.qty');

                            $total_retur = $qty_retur + $qty;

                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'id');
                            $detailQty = $poQty = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'qty');
                            $detailPrice = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'price');
                            $detailQty = $detailQty - $in_qty;

                            if( $po_qty == $in_qty ) {
                                $receipt_status = 'full';
                            } else {
                                $receipt_status = Common::hashEmptyField($documentDetail, 'PurchaseOrderDetail.receipt_status');
                            }

                            $model = 'PurchaseOrderDetail';
                            break;
                    }


                    if( $total_retur >= $detailQty ) {
                        $retur_detail_status = 'full';
                    } else {
                        $retur_detail_status = 'half';
                    }

                    if( $qty_retur > $detailQty ) {
                        $over_retur = true;
                    } else {
                        $over_retur = false;
                    }

                    $dataDetail[$key]['ProductReturDetail']['document_detail_id'] = $detailId;
                    $dataDetail[$key]['ProductReturDetail']['doc_qty'] = $poQty;
                    $dataDetail[$key]['ProductReturDetail']['retur_qty'] = $qty_retur;
                    $dataDetail[$key]['ProductReturDetail']['in_qty'] = $in_qty;
                    $dataDetail[$key]['ProductReturDetail']['over_retur'] = $over_retur;
                    $dataDetail[$key]['ProductReturDetail']['price'] = $detailPrice;
                    $dataDetail[$key]['ProductReturDetail']['Product'] = array(
                        'id' => $product_id,
                        'truck_category_id' => 1,
                        $model => array(
                            array(
                                'id' => $detailId,
                                'retur_status' => $retur_detail_status,
                                'receipt_status' => $receipt_status,
                            ),
                        ),
                    );

                    $total += $qty;
                }

                $data['ProductRetur']['total'] = $total;
                $data['ProductReturDetail'] = $dataDetail;
            }
        }

        return $data;
    }

    function _callBeforeRenderRetur ( $data, $value = false ) {
        $document_id = false;

        if( empty($data) ) {
            $data = $value;

            if( empty($data) ) {
                $data['ProductRetur']['document_type'] = 'po';
            }

            $id = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'id');
            $type = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'document_id');
            $details = $this->MkCommon->filterEmptyField($value, 'ProductReturDetail');

            $data['ProductRetur']['document_number'] = $this->MkCommon->filterEmptyField($data, 'Document', 'nodoc');

            if( empty($value) ) {
                $data['ProductRetur']['transaction_date'] = date('Y-m-d');
            }

            if( !empty($details) ) {
                foreach ($details as $key => &$detail) {
                    $product = $this->MkCommon->filterEmptyField($detail, 'Product');
                    $product_id = $this->MkCommon->filterEmptyField($detail, 'ProductReturDetail', 'product_id');

                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');
                    $detail['Product']['unit'] = $this->MkCommon->filterEmptyField($detail, 'ProductUnit', 'name', $unit);

                    switch ($type) {
                        case 'spk':
                            $documentDetail = $this->controller->Product->SpkProduct->getMergeData(array(), $document_id, $product_id, $id);
                            $qty_retur = $this->controller->Product->ProductReturDetail->getTotalRetur($id, $document_id, $type, $product_id);
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduct', 'qty');
                            $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt(null, $document_id, $type, $product_id);
                            break;

                        default:
                            $documentDetail = $this->controller->Product->PurchaseOrderDetail->getMergeData(array(), $document_id, $product_id, $id);
                            $qty_retur = $this->controller->Product->ProductReturDetail->getTotalRetur($id, $document_id, $type, $product_id);
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'qty');
                            $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt(null, $document_id, $type, $product_id);
                            break;
                    }
                            
                    $detail['ProductReturDetail']['doc_qty'] = $detailQty;
                    $detail['ProductReturDetail']['retur_qty'] = $qty_retur;
                    $detail['ProductReturDetail']['in_qty'] = $in_qty;
                }

                $data['ProductReturDetail'] = $details;
            }
        } else {
            $type = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_id');
        }

        $data = $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'ProductRetur' => array(
                    'transaction_date',
                ),
            )
        ), true);
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_type');

        switch ($document_type) {
            case 'spk':
                $vendors = $this->controller->Product->SpkProduct->Spk->_callVendors('unretur_draft', $document_id, 'eksternal');
                break;
            default:
                $vendors = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->_callVendors('unretur_draft', $document_id);
                break;
        }

        $this->controller->request->data = $data;

        $employes = $this->controller->User->Employe->getData('list', array(
            'fields' => array(
                'Employe.id', 'Employe.full_name',
            ),
            'contain' => false,
        ));

        $this->MkCommon->_layout_file('select');
        $this->controller->set(compact(
            'employes', 'vendors', 'type'
        ));
    }

    function _callBeforeRenderReturPODetails ( $values, $transaction_id = false ) {
        $data = $this->controller->request->data;
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_type', 'po');

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->controller->Product->PurchaseOrderDetail->getMergeList($value, array(
                    'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                                'ProductCategory',
                            ),
                        ),
                    ),
                ));
                $document_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'purchase_order_id');
                $product_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'product_id');
                $total_qty = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');
                $retur_qty = $this->controller->Product->ProductReturDetail->getTotalRetur($transaction_id, $document_id, $document_type, $product_id);
                $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt(null, $document_id, $document_type, $product_id);

                $qty = $total_qty - $retur_qty;
                $qty = $qty - $in_qty;

                if( $qty < 0 ) {
                    $qty = 0;
                }

                if( !empty($qty) ) {
                    $value['PurchaseOrderDetail']['total_qty'] = $total_qty;
                    $value['PurchaseOrderDetail']['qty'] = $qty;
                    $value['PurchaseOrderDetail']['retur_qty'] = $retur_qty;
                    $value['PurchaseOrderDetail']['in_qty'] = $in_qty;
                    $values[$key] = $value;
                } else {
                    unset($values[$key]);
                }
            }
        }

        $this->controller->set('module_title', __('Barang'));
        $this->controller->set(compact(
            'values'
        ));
    }

    function _callBeforeRenderReturSpkProducts ( $values, $transaction_id = false ) {
        $data = $this->controller->request->data;
        $document_type = $this->MkCommon->filterEmptyField($data, 'ProductRetur', 'document_type', 'spk');

        if( !empty($values) ) {
            $nodelProduct = 'SpkProduct';

            foreach ($values as $key => $value) {
                $value = $this->controller->Product->SpkProduct->getMergeList($value, array(
                    'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                                'ProductCategory',
                            ),
                        ),
                    ),
                ));

                $document_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'spk_id');
                $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'product_id');
                $total_qty = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'qty');
                $retur_qty = $this->controller->Product->ProductReturDetail->getTotalRetur($transaction_id, $document_id, $document_type, $product_id);
                $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt(null, $document_id, $document_type, $product_id);

                $qty = $total_qty - $retur_qty;
                $qty = $qty - $in_qty;

                if( $qty < 0 ) {
                    $qty = 0;
                }

                if( !empty($qty) ) {
                    $value['SpkProduct']['total_qty'] = $total_qty;
                    $value['SpkProduct']['qty'] = $qty;
                    $value['SpkProduct']['retur_qty'] = $retur_qty;
                    $value['SpkProduct']['in_qty'] = $in_qty;
                    $values[$key] = $value;
                } else {
                    unset($values[$key]);
                }
            }
        }

        $this->controller->set('module_title', __('Barang'));
        $this->controller->set(compact(
            'values'
        ));
    }

    function _callGetDocRetur ( $value ) {
        $this->Product = ClassRegistry::init('Product'); 
        $document_id = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'document_id');
        $document_type = $this->MkCommon->filterEmptyField($value, 'ProductRetur', 'document_type');

        switch ($document_type) {
            case 'spk':
                $modalName = 'Spk';
                break;
            default:
                $modalName = 'PurchaseOrder';
                break;
        }

        $value = $this->Product->ProductReturDetail->ProductRetur->getMergeList($value, array(
            'contain' => array(
                'Document' => array(
                    'uses' => $modalName,
                    'primaryKey' => 'id',
                    'foreignKey' => 'document_id',
                    'type' => 'first',
                    'elements' => array(
                        'branch' => false,
                    ),
                ),
            ),
        ));

        return $value;
    }

    function _callBeforeSaveAdjustment ( $data, $id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'ProductAdjustment' => array(
                        'transaction_date',
                    ),
                )
            ));
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'ProductAdjustment', 'transaction_status');
            $transaction_date = Common::hashEmptyField($data, 'ProductAdjustment.transaction_date');
            $session_id = $this->MkCommon->filterEmptyField($data, 'ProductAdjustment', 'session_id');
            $import = $this->MkCommon->filterEmptyField($data, 'ProductAdjustment', 'import');

            $data['ProductAdjustment']['id'] = $id;
            $data['ProductAdjustment']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductAdjustment']['branch_id'] = Configure::read('__Site.config_branch_id');

            $details = $this->MkCommon->filterEmptyField($data, 'ProductAdjustmentDetail', 'product_id');
            $adjustQty = $this->MkCommon->filterEmptyField($data, 'ProductAdjustmentDetail', 'qty');
            $adjustPrice = $this->MkCommon->filterEmptyField($data, 'ProductAdjustmentDetail', 'price');
            $adjustNote = $this->MkCommon->filterEmptyField($data, 'ProductAdjustmentDetail', 'note');

            if( !empty($details) ) {
                $total = 0;
                $dataDetail = array();
                $values = array_filter($details);

                foreach ($values as $key => $product_id) {
                    $qty = $this->MkCommon->filterIssetField($adjustQty, $key);
                    $price = $this->MkCommon->filterIssetField($adjustPrice, $key);
                    $note = $this->MkCommon->filterIssetField($adjustNote, $key);
                    
                    $price = Common::_callPriceConverter($price);

                    $product = $this->controller->Product->getMerge(array(), $product_id);

                    $code = $this->MkCommon->filterEmptyField($product, 'Product', 'code');
                    $name = $this->MkCommon->filterEmptyField($product, 'Product', 'name');
                    $is_serial_number = $this->MkCommon->filterEmptyField($product, 'Product', 'is_serial_number');
                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');
                    
                    $product_stock_cnt = $this->controller->Product->ProductStock->_callStock($product_id);
                    $qty_difference = $qty - $product_stock_cnt;
                    $type = 'plus';

                    if( $qty_difference == 0 ) {
                        $flag_qty_difference = false;
                    } else {
                        $flag_qty_difference = true;

                        if( $qty_difference < 0 ) {
                            $price = false;
                            $type = 'min';
                        }
                    }

                    $dataDetail[$key]['ProductAdjustmentDetail'] = array(
                        'product_id' => $product_id,
                        'is_serial_number' => $is_serial_number,
                        'code' => $code,
                        'name' => $name,
                        'unit' => $unit,
                        'note' => $note,
                        'qty' => abs($qty_difference),
                        'price' => $price,
                        'flag_qty_difference' => $flag_qty_difference,
                        'qty_difference' => $qty_difference,
                        'total_qty' => $product_stock_cnt,
                        'type' => $type,
                    );

                    if( $qty_difference < 0 ) {
                        $dataDetail[$key]['ProductAdjustmentDetail'] = $this->_callExpenditureSN($data, $product, $dataDetail[$key]['ProductAdjustmentDetail'], 'ProductAdjustmentDetailSerialNumber');
                        $dataDetail[$key]['ProductAdjustmentDetail']['Product'] = array(
                            'id' => $product_id,
                            'status' => 1,
                        );

                        $dataDetail[$key] = $this->_callStock('product_adjustment_min', $data, $dataDetail[$key], 'out', 'ProductAdjustment', null, 'ProductAdjustmentDetail', 'ProductAdjustmentDetailSerialNumber');
                    } else {
                        if( !empty($is_serial_number) ) {
                            if( !empty($import) ) {
                                $serial_number = Common::hashEmptyField($data, 'ProductAdjustmentDetailSerialNumber.serial_numbers.'.$product_id);

                                if( is_array($serial_number) ) {
                                    $serial_number = count($serial_number);
                                } else {
                                    $serial_number = 0;
                                }
                            } else {
                                $serial_number = $this->controller->Product->ProductAdjustmentDetailSerialNumber->getCount($session_id, $product_id);
                            }
                        } else {
                            $serial_number = 0;
                        }

                        if( abs($qty_difference) != $serial_number ) {
                            $serial_number = 0;
                        }

                        $dataDetail[$key]['ProductAdjustmentDetail']['serial_number'] = $serial_number;
                        $dataDetail[$key] = $this->_callStock('product_adjustment_plus', $data, $dataDetail[$key], 'in', 'ProductAdjustment', null, 'ProductAdjustmentDetail', 'ProductAdjustmentDetailSerialNumber');
                    }
                    
                    $dataDetail[$key]['ProductAdjustmentDetail']['qty'] = $qty;
                    $total += $qty;
                }

                $data['ProductAdjustment']['total'] = $total;
                $data['ProductAdjustmentDetail'] = $dataDetail;
            }
        }

        return $data;
    }

    function _callBeforeRenderAdjustment ( $data, $value = null ) {
        if( empty($data) ) {
            if( !empty($value) ) {
                $value = $this->controller->Product->ProductAdjustmentDetail->ProductAdjustment->getMergeList($value, array(
                    'contain' => array(
                        'ProductAdjustmentDetail' => array(
                            'Product' => array(
                                'contain' => array(
                                    'ProductUnit',
                                ),
                            ),
                            'ProductAdjustmentDetailSerialNumber',
                        ),
                    ),
                ));

                $data = $value;
                $details = Common::hashEmptyField($data, 'ProductAdjustmentDetail');
                $serial_numbers = array();

                if( !empty($details) ) {
                    foreach ($details as $key => &$detail) {
                        $product_id = Common::hashEmptyField($detail, 'Product.id');
                        $code = Common::hashEmptyField($detail, 'Product.code');
                        $name = Common::hashEmptyField($detail, 'Product.name');
                        $unit = Common::hashEmptyField($detail, 'Product.ProductUnit.name');
                        $is_serial_number = Common::hashEmptyField($detail, 'Product.is_serial_number');

                        $sn = Set::extract('/ProductAdjustmentDetailSerialNumber/ProductAdjustmentDetailSerialNumber/serial_number', $detail);

                        $serial_numbers[$product_id] = $sn;

                        $detail['ProductAdjustmentDetail']['name'] = $name;
                        $detail['ProductAdjustmentDetail']['code'] = $code;
                        $detail['ProductAdjustmentDetail']['unit'] = $unit;
                        $detail['ProductAdjustmentDetail']['is_serial_number'] = $is_serial_number;
                    }

                    $data['ProductAdjustmentDetail'] = $details;
                }
            } else {
                $data['ProductAdjustment']['session_id'] = String::uuid();
            }
        } else {
            $serial_numbers = Common::hashEmptyField($data, 'ProductAdjustmentDetailSerialNumber.serial_numbers');
        }

        if( !empty($serial_numbers) ) {
            foreach ($serial_numbers as $key => &$value) {
                if( !empty($value) && is_array($value) ) {
                    $value = array_combine($value, $value);
                }
            }
            $data['ProductAdjustmentDetailSerialNumber']['serial_numbers'] = $serial_numbers;
        }

        $transaction_date = $this->MkCommon->filterEmptyField($data, 'ProductAdjustment', 'transaction_date', date('Y-m-d'));
        $data['ProductAdjustment']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);

        return $data;
    }

    function _callBeforeViewAdjustSerialNumber ( $values, $session_id = false ) {
        if( !empty($values) ) {
            $dataRequest = array();

            foreach ($values as $key => $value) {
                $serial_number = $this->MkCommon->filterEmptyField($value, 'ProductAdjustmentDetailSerialNumber', 'serial_number');
                $dataRequest['ProductAdjustmentDetailSerialNumber']['serial_number'][$key] = strtoupper($serial_number);
            }

            $this->controller->request->data = $dataRequest;
        }

        $this->controller->request->data['ProductAdjustment']['session_id'] = $session_id;
    }

    function _callBeforeViewAdjustmentReports( $params ) {
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Laporan Penyesuaian Qty');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'period_text'
        ));
    }

    function _callBeforeViewMinStockReport( $params ) {
        $title = __('Laporan Minimum Stok');
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
    }

    function _callBeforeViewProductCategoryReports( $params ) {
        $year = Common::hashEmptyField($params, 'named.year');
        $title = __('Laporan Grup Barang');

        if( !empty($year) ) {
            $title .= __(' - Tahun %s', $year);
        }

        $productCategories = $this->controller->Product->ProductCategory->getData('list');

        $this->MkCommon->_layout_file('select');
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set('productCategories', $productCategories);
    }
}
?>