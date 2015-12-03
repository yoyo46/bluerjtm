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
                $values = array_filter($dataDetailPrice);
                unset($data['SupplierQuotationDetail']);

                foreach ($values as $key => $price) {
                    $product_id = $this->MkCommon->filterEmptyField($dataDetail, 'product_id', $key);
                    $disc = $this->MkCommon->filterEmptyField($dataDetail, 'disc', $key);
                    $ppn = $this->MkCommon->filterEmptyField($dataDetail, 'ppn', $key);

                    $ppn = $this->MkCommon->_callPriceConverter($ppn);
                    $disc = $this->MkCommon->_callPriceConverter($disc);
                    $price = $this->MkCommon->_callPriceConverter($price);

                    $dataSQDetail['SupplierQuotationDetail'] = array(
                        'product_id' => $product_id,
                        'price' => $price,
                        'ppn' => $ppn,
                        'disc' => $disc,
                    );
                    $dataSQDetail = $this->controller->PurchaseOrder->PurchaseOrderDetail->Product->getMerge($dataSQDetail, $product_id);
                    $dataSave[] = $dataSQDetail;
                }
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
}
?>