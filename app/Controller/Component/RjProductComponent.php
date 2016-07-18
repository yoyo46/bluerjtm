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

    function _callStock ( $transaction_type, $data, $detail, $type = 'in' ) {
        $transaction_status = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'transaction_status');
        $session_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'session_id');
        $to_branch_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'to_branch_id');

        if( $transaction_status == 'posting' ) {
            $product_id = $this->MkCommon->filterEmptyField($detail, 'ProductReceiptDetail', 'product_id');
            $qty = $this->MkCommon->filterEmptyField($detail, 'ProductReceiptDetail', 'qty');
            $price = $this->MkCommon->filterEmptyField($detail, 'ProductReceiptDetail', 'price');
            $serial_number = $this->MkCommon->filterEmptyField($detail, 'ProductReceiptDetail', 'serial_number');
            
            $history = $this->controller->Product->ProductHistory->getMerge(array(), $product_id);
            $balance = $this->MkCommon->filterEmptyField($history, 'ProductHistory', 'ending', 0);
            $ending = $balance;

            if( $type == 'out' ) {
                $ending -= $qty;
            } else if( $type == 'in' ) {
                $ending += $qty;
            }

            $stock = array(
                'branch_id' => $to_branch_id,
                'balance' => $balance,
                'ending' => $ending,
                'product_id' => $product_id,
                'transaction_type' => $transaction_type,
                'type' => $type,
                'qty' => $qty,
                'price' => $price,
            );
            $detail['ProductHistory'] = $stock;

            if( !empty($serial_number) ) {
                $detail['ProductHistory']['ProductStock'] = $this->_callStockSerialNumber( $session_id, $product_id, $stock );
            } else {
                $detail['ProductHistory']['ProductStock'][] = array_merge($stock, array(
                    'serial_number' => sprintf('%s-%s', $this->MkCommon->getNoRef($product_id), date('ymdHis')),
                ));
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
                        case 'po':
                            $documentDetail = $this->controller->Product->PurchaseOrderDetail->getMergeData(array(), $document_id, $product_id);
                            $total_receipt = $this->controller->Product->ProductReceiptDetail->getTotalReceipt($id, $document_id, $product_id);
                            $total_receipt += $qty;
                            
                            $detailId = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'id');
                            $detailQty = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'qty');
                            $detailPrice = $this->MkCommon->filterEmptyField($documentDetail, 'PurchaseOrderDetail', 'total');

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
                            
                            $dataDetail[$key]['ProductReceiptDetail']['over_receipt'] = $over_receipt;
                            $dataDetail[$key]['ProductReceiptDetail']['price'] = $detailPrice;
                            $dataDetail[$key]['ProductReceiptDetail']['Product'] = array(
                                'id' => $product_id,
                                'truck_category_id' => 1,
                                'PurchaseOrderDetail' => array(
                                    array(
                                        'id' => $detailId,
                                        'receipt_status' => $receipt_detail_status,
                                    ),
                                ),
                            );
                            break;
                    }

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

            $type = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_type');
            $document_id = $this->MkCommon->filterEmptyField($value, 'ProductReceipt', 'document_id');

            $data['ProductReceipt']['document_number'] = $this->MkCommon->filterEmptyField($data, 'Document', 'nodoc');

            if( empty($value) ) {
                $data['ProductReceipt']['session_id'] = String::uuid();
                $data['ProductReceipt']['transaction_date'] = date('Y-m-d');
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
            case 'po':
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

        $this->MkCommon->_layout_file('select');
    	$this->controller->set(compact(
    		'employes', 'toBranches',
            'vendors', 'type'
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

        return $values;
    }

    function _callPurchaseOrder( $data ) {
        $document_number = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'document_number');
        $vendor_id = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'vendor_id');

        $value =  $this->controller->Product->PurchaseOrderDetail->PurchaseOrder->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.vendor_id' => $vendor_id,
                'PurchaseOrder.nodoc' => $document_number,
            ),
        ), array(
            'status' => 'unreceipt',
        ));

        $purchase_order_id = $this->MkCommon->filterEmptyField($value, 'PurchaseOrder', 'id');
        $value =  $this->controller->Product->PurchaseOrderDetail->getMerge($value, $purchase_order_id, 'ProductReceipt');

        return $value;
    }

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
            case 'po':
                $value = $this->controller->Product->ProductReceiptDetail->ProductReceipt->getMergeList($value, array(
                    'contain' => array(
                        'Document' => array(
                            'uses' => 'PurchaseOrder',
                            'primaryKey' => 'id',
                            'foreignKey' => 'document_id',
                            'type' => 'first',
                        ),
                    ),
                ));
                break;
        }

        return $value;
    }
}
?>