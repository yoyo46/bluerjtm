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

    function _callBeforeSaveReceipt ( $data, $id = false ) {
        if( !empty($data) ) {
            $dataSave = array();
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'transaction_date');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'transaction_status');

            $dataDetail = $this->MkCommon->filterEmptyField($data, 'ProductReceiptDetail');
            $dataDetailProduct = $this->MkCommon->filterEmptyField($dataDetail, 'product_id');

            $transaction_date = $this->MkCommon->getDate($transaction_date);

            $data['ProductReceipt']['id'] = $id;
            $data['ProductReceipt']['user_id'] = Configure::read('__Site.config_user_id');
            $data['ProductReceipt']['transaction_date'] = $transaction_date;

            if( !empty($dataDetailProduct) ) {
                $grandtotal = 0;
                $values = array_filter($dataDetailProduct);
                unset($data['ProductReceiptDetail']);

                foreach ($values as $key => $product_id) {
                    $dataPODetail = array();
                    $qty = $this->MkCommon->filterEmptyField($dataDetail, 'qty', $key);

                    $product = $this->controller->Product->getMerge(array(), $product_id);
                    $code = $this->MkCommon->filterEmptyField($product, 'Product', 'code');
                    $name = $this->MkCommon->filterEmptyField($product, 'Product', 'name');
                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');

                    $dataPODetail['ProductReceiptDetail'] = array(
                        'product_id' => $product_id,
                        'code' => $code,
                        'name' => $name,
                        'unit' => $unit,
                        'qty' => $qty,
                    );

                    $dataSave[] = $dataPODetail;
                    $grandtotal += $qty;
                }

                $data['PurchaseOrder']['grandtotal'] = $qty;
            }

            if( !empty($dataSave) ) {
                $data['ProductReceiptDetail'] = $dataSave;
            }
        }

        return $data;
    }

    function _callBeforeRenderReceipt ( $data ) {
        $transaction_date = $this->MkCommon->filterEmptyField($data, 'ProductReceipt', 'transaction_date', date('Y-m-d'));
        $data['ProductReceipt']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);

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
    		'employes', 'toBranches'
		));

        return $data;
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
            'status' => 'unreceipt',
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
        $value =  $this->controller->Product->PurchaseOrderDetail->getMerge($value, $purchase_order_id);

        return $value;
    }
}
?>