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
				if( !empty($refine['ProductBrand']['name']) ) {
					$refine_conditions['ProductBrand']['name'] = $refine['ProductBrand']['name'];
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
		if(isset($refine['ProductBrand']) && !empty($refine['ProductBrand'])) {
			foreach($refine['ProductBrand'] as $param => $value) {
				if($value) {
					$parameters[trim($param)] = rawurlencode($value);
				}
			}
		}

		return $parameters;
	}

    function _callStockSerialNumber ( $session_id, $product_id, $data ) {
        $serial_numbers = $this->controller->Product->ProductReceiptDetailSerialNumber->getMergeAll(array(), 'all', $product_id, $session_id, 'ProductReceiptDetailSerialNumber.session_id');
        $result = array();
        
        if( !empty($serial_numbers['ProductReceiptDetailSerialNumber']) ) {
            foreach ($serial_numbers['ProductReceiptDetailSerialNumber'] as $key => $value) {
                $serial_number = $this->MkCommon->filterEmptyField($value, 'ProductReceiptDetailSerialNumber', 'serial_number');

                $result[$key] = $data;
                $result[$key]['qty'] = 1;
                $result[$key]['serial_number'] = $serial_number;
            }
        }

        return $result;
    }

    function _callOutStock ( $product_id, $qty, $serial_number = false ) {
        $conditions = array(
            'conditions' => array(
                'ProductStock.product_id' => $product_id,
            ),
        );

        if( !empty($serial_number) ) {
            $conditions['conditions']['ProductStock.serial_number'] = $serial_number;
        }

        $stock = $this->controller->Product->ProductStock->getData('first', $conditions, array(
            'status' => 'FIFO',
        ));

        $id = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'id');
        $product_history_id = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'product_history_id');
        $qty_total = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'qty_total');
        $qty_use = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'qty_use');
        $price = $this->MkCommon->filterEmptyField($stock, 'ProductStock', 'price');
        $qty_total -= $qty;

        if( $qty_total < 0 ) {
            $status = false;
        } else {
            $status = true;
        }

        $result = array(
            'id' => $id,
            'product_history_id' => $product_history_id,
            'product_id' => $product_id,
            'qty_use' => $qty_use + $qty,
            'status' => $status,
        );

        if( !empty($serial_number) ) {
            return $result;   
        } else {
            return array(
                'price' => $price,
                'ProductStock' => $result,
            );   
        }
    }

    function _callOutStockSerialNumber ( $serial_numbers ) {
        $result = array();

        if( !empty($serial_numbers) ) {
            foreach ($serial_numbers as $key => $value) {
                $product_id = $this->MkCommon->filterEmptyField($value, 'product_id');
                $serial_number = $this->MkCommon->filterEmptyField($value, 'serial_number');

                $result[] = $this->_callOutStock( $product_id, 1, $serial_number);
            }
        }

        return $result;
    }

    function _callStock ( $transaction_type, $data, $detail, $type = 'in', $model = 'ProductReceipt' ) {
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

            if( $type == 'out' ) {
                $ending -= $qty;
                $serial_numbers = $this->MkCommon->filterEmptyField($detail, $modelDetail, 'ProductExpenditureDetailSerialNumber');
            } else if( $type == 'in' ) {
                if( !in_array($document_type, array( 'spk' )) ) {
                    $ending += $qty;
                }

                $serial_number = $this->MkCommon->filterEmptyField($detail, $modelDetail, 'serial_number');
            }

            $stock = array(
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
            $detail['ProductHistory'] = $stock;
            $stock['type'] = in_array($document_type, array( 'spk' ))?'barang_bekas':'default';

            if( $ending < 0 ) {
                $detail['ProductExpenditureDetail']['out_stock'] = true;
            }

            switch ($type) {
                case 'in':
                    if( !empty($serial_number) ) {
                        $detail['ProductHistory']['ProductStock'] = $this->_callStockSerialNumber( $session_id, $product_id, $stock );
                    } else {
                        $detail['ProductHistory']['ProductStock'][] = array_merge($stock, array(
                            'serial_number' => sprintf('%s-%s', $this->MkCommon->getNoRef($product_id), date('ymdHis')),
                        ));
                    }
                    break;
                
                default:
                    if( !empty($serial_numbers) ) {
                        $detail['ProductHistory']['ProductStock'] = $this->_callOutStockSerialNumber( $serial_numbers );
                    } else {
                        $result = $this->_callOutStock($product_id, $qty);
                        $detail['ProductHistory']['price'] = $this->MkCommon->filterEmptyField($result, 'price', false, $price);
                        $detail['ProductHistory']['ProductStock'][] = $this->MkCommon->filterEmptyField($result, 'ProductStock');
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

            switch ($document_type) {
                case 'po':
                    $value = $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->getMerge(array(), $document_number, 'active', 'PurchaseOrder.nodoc');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
                    break;

                case 'spk':
                    $value = $this->controller->Product->SpkProduct->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
                    break;

                case 'wht':
                    $value = $this->controller->Product->ProductExpenditureDetail->ProductExpenditure->getMerge(array(), $document_number, 'ProductExpenditure.nodoc');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditure', 'id');
                    break;

                case 'production':
                    $value = $this->controller->Product->SpkProduction->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'active');
                    $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
                    break;
                
                default:
                    $document_id = '';
                    break;
            }

            $data['ProductReceipt']['id'] = $id;
            $data['ProductReceipt']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductReceipt']['document_id'] = $document_id;
            $data['ProductReceipt']['branch_id'] = Configure::read('__Site.config_branch_id');

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
                            break;
                        case 'production':
                            $documentDetail = $this->controller->Product->SpkProduction->getMergeData(array(), $document_id, $product_id);
                            $qty_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $document_type, $product_id);
                            $total_receipt = $qty_receipt + $qty;
                            
                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduction', 'id');
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'SpkProduction', 'qty');
                            $detailPrice = 0;

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

                    if( $qty > $detailQty ) {
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

                    $dataDetail[$key] = $this->_callStock('product_receipt', $data, $dataDetail[$key], 'in');

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

    function _callPurchaseOrders( $params, $vendor_id = false ) {
    	$this->controller->loadModel('PurchaseOrder');
        $options =  $this->controller->PurchaseOrder->_callRefineParams($params, array(
            'conditions' => array(
                'PurchaseOrder.vendor_id' => $vendor_id,
            ),
            'limit' => 10,
        ));
        $this->controller->paginate = $this->controller->PurchaseOrder->getData('paginate', $options, array(
            'status' => 'unreceipt_draft',
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

    function _callBeforeSaveSerialNumber ( $data, $id = false, $session_id = false ) {
        $dataSave = array();

        if( !empty($data) ) {
            if( !empty($data) ) {
                foreach ($data as $key => $serial_number) {
                    $dataSave[]['ProductReceiptDetailSerialNumber'] = array(
                        'product_id' => $id,
                        'session_id' => $session_id,
                        'serial_number' => $serial_number,
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
                $dataRequest['ProductReceiptDetailSerialNumber']['serial_number'][$key] = $serial_number;
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

        $value = $this->controller->Product->ProductReceiptDetail->ProductReceipt->getMergeList($value, array(
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
            $serial_numbers = Set::extract('/ProductExpenditureDetail/ProductExpenditureDetail/ProductExpenditureDetailSerialNumber', $data);
        }

        if( !empty($serial_numbers) ) {
            $data['ProductExpenditureDetailSerialNumber'] = array();
            
            foreach ($serial_numbers as $key => $value) {
                $product_id = $this->MkCommon->filterEmptyField($value, 'ProductExpenditureDetailSerialNumber', 'product_id');
                $serial_number = $this->MkCommon->filterEmptyField($value, 'ProductExpenditureDetailSerialNumber', 'serial_number');

                if( !empty($serial_number) ) {
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

    function _callExpenditureSN ( $data, $product, $details ) {
        $detail_serial_numbers = $this->MkCommon->filterEmptyField($data, 'ProductExpenditureDetailSerialNumber', 'serial_numbers');

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
                    $details['ProductExpenditureDetailSerialNumber'][] = array(
                        'product_id' => $product_id,
                        'serial_number' => $serial_number,
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

            $value = $this->controller->Product->ProductExpenditureDetail->ProductExpenditure->Spk->getMerge(array(), $document_number, 'Spk.nodoc', 'pending-out');
            $document_id = $this->MkCommon->filterEmptyField($value, 'Spk', 'id');
            $document_type = $this->MkCommon->filterEmptyField($value, 'Spk', 'document_type');

            $data['ProductExpenditure']['id'] = $id;
            $data['ProductExpenditure']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductExpenditure']['document_id'] = $document_id;
            $data['ProductExpenditure']['document_type'] = $document_type;
            $data['ProductExpenditure']['branch_id'] = Configure::read('__Site.config_branch_id');

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
                            'document_status' => $status,
                        );
                        $dataDetail[$key] = $this->_callStock('product_expenditure', $data, $dataDetail[$key], 'out', 'ProductExpenditure');
                    }

                    $total += $qty;
                }

                $data['ProductExpenditure']['total'] = $total;
                $data['ProductExpenditureDetail'] = $dataDetail;
            }
        }
        // debug($data);die();

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
                $qty = $this->MkCommon->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');
                $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($transaction_id, $document_id, $document_type, $product_id);
                $qty -= $in_qty;

                if( !empty($qty) ) {
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

                        $nodelName = 'SpkProduction';
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

                $in_qty = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($transaction_id, $document_id, $document_type, $product_id);
                // $qty -= $in_qty;

                if( !empty($qty) ) {
                    $value['SpkProduct']['qty'] = $qty;
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

    function _callSpkInternals( $params, $vendor_id = false ) {
        $this->controller->loadModel('Spk');
        $options =  $this->controller->Spk->_callRefineParams($params, array(
            'conditions' => array(
                'Spk.vendor_id' => $vendor_id,
            ),
            'limit' => 10,
        ));
        $this->controller->paginate = $this->controller->Spk->getData('paginate', $options, array(
            'status' => 'unreceipt_draft',
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
        $options =  $this->controller->ProductExpenditure->_callRefineParams($params, array(
            'conditions' => array(
                'Spk.vendor_id' => $vendor_id,
            ),
            'contain' => array(
                'Spk',
            ),
            'limit' => 10,
        ));
        $this->controller->paginate = $this->controller->ProductExpenditure->getData('paginate', $options, array(
            'status' => 'untransfer_draft',
            'branch' => false,
        ));
        $values = $this->controller->paginate('ProductExpenditure');

        return $values;
    }

    function _callProductions( $params, $vendor_id = false ) {
        $this->controller->loadModel('Spk');
        $options = array(
            'limit' => 10,
        );

        if( !empty($vendor_id) ) {
            $options['conditions']['vendor_id'] = $vendor_id;
        }

        $options =  $this->controller->Spk->_callRefineParams($params, $options);
        $this->controller->paginate = $this->controller->Spk->getData('paginate', $options, array(
            'status' => 'unreceipt_draft',
            'type' => 'production',
        ));
        $values = $this->controller->paginate('Spk');

        return $values;
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
}
?>