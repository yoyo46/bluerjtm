<?php
App::uses('Sanitize', 'Utility');
class RjPurchaseComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callBeforeSaveQuotation ( $data ) {
        if( !empty($data) ) {
            $dataSave = array();
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'transaction_date');
            $available_date = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'available_date');
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'SupplierQuotationDetail');
            $dataDetailPrice = $this->MkCommon->filterEmptyField($dataDetail, 'price');

            $dateArr = $this->MkCommon->_callSplitDate($available_date);
            $transaction_date = $this->MkCommon->getDate($transaction_date);

            $data['SupplierQuotation']['user_id'] = Configure::read('__Site.config_user_id');
            $data['SupplierQuotation']['transaction_date'] = $transaction_date;

            if( !empty($dateArr) ) {
                $availableFrom = $this->MkCommon->filterEmptyField($dateArr, 'DateFrom');
                $availableTo = $this->MkCommon->filterEmptyField($dateArr, 'DateTo');

                $data['SupplierQuotation']['available_from'] = $availableFrom;
                $data['SupplierQuotation']['available_to'] = $availableTo;

                unset($data['SupplierQuotation']['available_date']);
            }

            if( !empty($dataDetailPrice) ) {
                $grandtotal = 0;
                $values = array_filter($dataDetailPrice);
                unset($data['SupplierQuotationDetail']);

                foreach ($values as $key => $price) {
                    $product_id = $this->MkCommon->filterEmptyField($dataDetail, 'product_id', $key);
                    $disc = $this->MkCommon->filterEmptyField($dataDetail, 'disc', $key);
                    $ppn = $this->MkCommon->filterEmptyField($dataDetail, 'ppn', $key);

                    $ppn = $this->MkCommon->_callPriceConverter($ppn);
                    $disc = $this->MkCommon->_callPriceConverter($disc);
                    $price = $this->MkCommon->_callPriceConverter($price);
                    $total = $price - $disc + $ppn;

                    $dataSQDetail['SupplierQuotationDetail'] = array(
                        'product_id' => $product_id,
                        'price' => $price,
                        'ppn' => $ppn,
                        'disc' => $disc,
                    );
                    $dataSQDetail = $this->controller->PurchaseOrder->PurchaseOrderDetail->Product->getMerge($dataSQDetail, $product_id);
                    $dataSave[] = $dataSQDetail;
                    $grandtotal += $total;
                }

                $data['SupplierQuotation']['grandtotal'] = $grandtotal;
            }

            if( !empty($dataSave) ) {
                $data['SupplierQuotationDetail'] = $dataSave;
            }
        }

        return $data;
    }

    function _callBeforeRenderQuotation ( $data ) {
        if( !empty($data) ) {
            $available_from = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'available_from');
            $available_to = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'available_to');

            $data['SupplierQuotation']['available_date'] = $this->MkCommon->_callUnSplitDate($available_from, $available_to);
        }
        
        $transaction_date = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'transaction_date', date('Y-m-d'));
        $data['SupplierQuotation']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);

        return $data;
    }

    function _callBeforeSavePO ( $data ) {
        if( !empty($data) ) {
            $dataSave = array();
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'transaction_date');
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderDetail');
            $dataDetailProduct = $this->MkCommon->filterEmptyField($dataDetail, 'product_id');

            $transaction_date = $this->MkCommon->getDate($transaction_date);

            $data['PurchaseOrder']['user_id'] = Configure::read('__Site.config_user_id');
            $data['PurchaseOrder']['transaction_date'] = $transaction_date;

            if( !empty($dataDetailProduct) ) {
                $values = array_filter($dataDetailProduct);
                unset($data['PurchaseOrderDetail']);

                foreach ($values as $key => $product_id) {
                    $dataPODetail = array();
                    $supplier_quotation_detail_id = $this->MkCommon->filterEmptyField($dataDetail, 'supplier_quotation_detail_id', $key);
                    $qty = $this->MkCommon->filterEmptyField($dataDetail, 'qty', $key);

                    if( !empty($supplier_quotation_detail_id) ) {
                        $sqDetail = $this->controller->User->SupplierQuotation->SupplierQuotationDetail->getData('first', array(
                            'conditions' => array(
                                'SupplierQuotationDetail.id' => $supplier_quotation_detail_id
                            ),
                        ), array(
                            'status' => 'all',
                        ));

                        $price = $this->MkCommon->filterEmptyField($sqDetail, 'SupplierQuotationDetail', 'price');
                        $disc = $this->MkCommon->filterEmptyField($sqDetail, 'SupplierQuotationDetail', 'disc');
                        $ppn = $this->MkCommon->filterEmptyField($sqDetail, 'SupplierQuotationDetail', 'ppn');
                    } else {
                        $price = $this->MkCommon->filterEmptyField($dataDetail, 'price', $key);
                        $disc = $this->MkCommon->filterEmptyField($dataDetail, 'disc', $key);
                        $ppn = $this->MkCommon->filterEmptyField($dataDetail, 'ppn', $key);
                    }

                    $ppn = $this->MkCommon->_callPriceConverter($ppn);
                    $disc = $this->MkCommon->_callPriceConverter($disc);
                    $price = $this->MkCommon->_callPriceConverter($price);

                    $dataPODetail['PurchaseOrderDetail'] = array(
                        'product_id' => $product_id,
                        'supplier_quotation_detail_id' => $supplier_quotation_detail_id,
                        'price' => $price,
                        'ppn' => $ppn,
                        'disc' => $disc,
                        'qty' => $qty,
                    );
                    $dataPODetail = $this->controller->PurchaseOrder->PurchaseOrderDetail->Product->getMerge($dataPODetail, $product_id);
                    $dataSave[] = $dataPODetail;
                }
            }

            if( !empty($dataSave) ) {
                $data['PurchaseOrderDetail'] = $dataSave;
            }
        }

        return $data;
    }

    function _callBeforeRenderPO ( $data ) {
        $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'transaction_date', date('Y-m-d'));
        $data['PurchaseOrder']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);

        return $data;
    }

    function _callBeforeSavePayment ( $data, $id = false ) {
        $dataSave = array();

        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'PurchaseOrderPayment' => array(
                        'transaction_date',
                    ),
                ),
            ));

            $values = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPaymentDetail', 'purchase_order_id');
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment', 'transaction_date');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment', 'transaction_status');

            $dataSave['PurchaseOrderPayment'] = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment');
            $dataSave['PurchaseOrderPayment']['id'] = $id;
            $dataSave['PurchaseOrderPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $dataSave['PurchaseOrderPayment']['user_id'] = Configure::read('__Site.config_user_id');

            if( !empty($values) ) {
                $grandtotal = 0;

                foreach ($values as $key => $purchase_order_id) {
                    $idArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPaymentDetail', 'id');
                    $priceArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPaymentDetail', 'price');
                    $totalRemainArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'total_remain');
                    
                    $purchaseOrder = $this->controller->PurchaseOrder->getMerge(array(), $purchase_order_id, 'unpaid');
                    $nodoc = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'nodoc');
                    $transaction_date = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'transaction_date');
                    $note = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'note');
                    $is_asset = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'is_asset');
                    $total_po = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'grandtotal');

                    $idDetail = !empty($idArr[$key])?$idArr[$key]:false;
                    $price = !empty($priceArr[$key])?$this->MkCommon->_callPriceConverter($priceArr[$key]):false;
                    $grandtotal += $price;
                    
                    $paid = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->_callPaidPO($purchase_order_id, $id);
                    $total_remain = $total_po - $paid;

                    $dataSave['PurchaseOrderPaymentDetail'][$key] = array(
                        'PurchaseOrderPaymentDetail' => array(
                            // 'id' => $idDetail,
                            'purchase_order_id' => $purchase_order_id,
                            'price' => $price,
                        ),
                        'PurchaseOrder' => array(
                            'id' => $purchase_order_id,
                            'total_remain' => $total_remain,
                            'nodoc' => $nodoc,
                            'transaction_date' => $transaction_date,
                            'note' => $note,
                        ),
                    );

                    if( $transaction_status == 'posting' ) {
                        $paid += $price;

                        if( $paid >= $total_po ) {
                            $transaction_status = 'paid';
                        } else {
                            $transaction_status = 'half_paid';
                        }

                        $dataSave['PurchaseOrderPaymentDetail'][$key]['PurchaseOrder']['transaction_status'] = $transaction_status;
                    }
                }

                $dataSave['PurchaseOrderPayment']['grandtotal'] = $grandtotal;
            }
        }

        return $dataSave;
    }

    function _callBeforeRenderPayment ( $data, $purchase_order_id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'PurchaseOrderPayment' => array(
                        'transaction_date',
                    ),
                ),
            ), true);
        } else {
            $data['PurchaseOrderPayment']['transaction_date'] = date('d/m/Y');
        }

        $vendors = $this->controller->PurchaseOrder->getData('list', array(
            'contain' => array(
                'Vendor',
            ),
            'fields' => array(
                'Vendor.id', 'Vendor.name',
            ),
            'group' => array(
                'PurchaseOrder.vendor_id',
            ),
        ), array(
            'status' => 'unpaid',
            'special_id' => $purchase_order_id
        ));
        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas();
        $this->MkCommon->_layout_file(array(
            'select',
        ));

        $this->controller->set(compact(
            'vendors', 'coas'
        ));

        return $data;
    }
}
?>