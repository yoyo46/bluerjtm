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
            $nodoc = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'nodoc');
            $vendor_id = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'vendor_id');
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'transaction_date');
            // $available_date = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'available_date');
            $dataDetail = $this->MkCommon->filterEmptyField($data, 'SupplierQuotationDetail');
            $dataDetailPrice = $this->MkCommon->filterEmptyField($dataDetail, 'price');

            if( empty($nodoc) && !empty($vendor_id) ) {
                $vendor = $this->controller->SupplierQuotation->Vendor->getMerge(array(), $vendor_id);

                if( !empty($vendor) ) {
                    $nodoc_tmp = Common::_callGeneratePatternCode($vendor, 'Vendor');
                    $data['SupplierQuotation']['nodoc'] = $nodoc_tmp;
                    $data['SupplierQuotation']['nodoc_tmp'] = $nodoc_tmp;

                    $last_number = Common::hashEmptyField($vendor, 'Vendor.last_number');

                    $data['Vendor']['id'] = $vendor_id;
                    $data['Vendor']['last_number'] = $last_number+1;
                }
            }

            // $dateArr = $this->MkCommon->_callSplitDate($available_date);
            $transaction_date = $this->MkCommon->getDate($transaction_date);

            $data['SupplierQuotation']['user_id'] = Configure::read('__Site.config_user_id');
            $data['SupplierQuotation']['transaction_date'] = $transaction_date;

            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'SupplierQuotation' => array(
                        'available_from',
                        'available_to',
                    ),
                )
            ));

            // if( !empty($dateArr) ) {
            //     $availableFrom = $this->MkCommon->filterEmptyField($dateArr, 'DateFrom');
            //     $availableTo = $this->MkCommon->filterEmptyField($dateArr, 'DateTo');

            //     $data['SupplierQuotation']['available_from'] = $availableFrom;
            //     $data['SupplierQuotation']['available_to'] = $availableTo;

            //     unset($data['SupplierQuotation']['available_date']);
            // }

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
                    $price = $this->MkCommon->_callPriceConverter($price) * 1;
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
            $nodoc_tmp = Common::hashEmptyField($data, 'SupplierQuotation.nodoc_tmp');
            // $available_from = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'available_from');
            // $available_to = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'available_to');

            // $data['SupplierQuotation']['available_date'] = $this->MkCommon->_callUnSplitDate($available_from, $available_to);
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'SupplierQuotation' => array(
                        'available_from',
                        'available_to',
                    ),
                )
            ), true);

            if( !empty($nodoc_tmp) ) {
                unset($data['SupplierQuotation']['nodoc']);
            }
        }
        
        $transaction_date = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'transaction_date', date('Y-m-d'));
        $data['SupplierQuotation']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);
        
        $vendors = $this->controller->SupplierQuotation->Vendor->getData('list');
        
        $this->MkCommon->_layout_file('select');
        $this->controller->set('active_menu', 'Penawaran Supplier');
        $this->controller->set(compact(
            'vendors'
        ));

        return $data;
    }

    function calculate ( $value, $ppn_include = false, $modelName = 'PurchaseOrderDetail' ) {
        $price = $this->MkCommon->filterEmptyField($value, $modelName, 'price');
        $qty = $this->MkCommon->filterEmptyField($value, $modelName, 'qty');
        $disc = $this->MkCommon->filterEmptyField($value, $modelName, 'disc');
        $ppn = $this->MkCommon->filterEmptyField($value, $modelName, 'ppn');

        // $total = ( $price * $qty ) - $disc;
        $total = $price - $disc;

        if( empty($ppn_include) ) {
            $total += $ppn;
        }
        
        $total = $total * $qty;

        return $total;
    }

    function _callBeforeSavePO ( $data, $id = false ) {
        if( !empty($data) ) {
            $dataSave = array();
            $ppn_include = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'ppn_include');
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'transaction_date');
            $no_sq = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'no_sq');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'transaction_status');

            $supplierQuotation = $this->controller->PurchaseOrder->SupplierQuotation->getMerge(array(), $no_sq, 'SupplierQuotation.nodoc');

            $dataDetail = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderDetail');
            $dataDetailProduct = $this->MkCommon->filterEmptyField($dataDetail, 'product_id');

            $transaction_date = $this->MkCommon->getDate($transaction_date);
            $supplier_quotation_id = $this->MkCommon->filterEmptyField($supplierQuotation, 'SupplierQuotation', 'id');
           
            $nodoc = Common::hashEmptyField($data, 'PurchaseOrder.nodoc');
            $vendor_id = Common::hashEmptyField($data, 'PurchaseOrder.vendor_id');

            if( empty($nodoc) && !empty($vendor_id) ) {
                $vendor = $this->controller->SupplierQuotation->Vendor->getMerge(array(), $vendor_id);

                if( !empty($vendor) ) {
                    $nodoc_tmp = Common::_callGeneratePatternCode($vendor, 'Vendor', '_po');
                    $data['PurchaseOrder']['nodoc'] = $nodoc_tmp;
                    $data['PurchaseOrder']['nodoc_tmp'] = $nodoc_tmp;

                    $last_number = Common::hashEmptyField($vendor, 'Vendor.last_number_po');

                    $data['Vendor']['id'] = $vendor_id;
                    $data['Vendor']['last_number_po'] = $last_number+1;
                }
            }

            $data['PurchaseOrder']['id'] = $id;
            $data['PurchaseOrder']['user_id'] = Configure::read('__Site.config_user_id');
            $data['PurchaseOrder']['transaction_date'] = $transaction_date;
            $data['PurchaseOrder']['supplier_quotation_id'] = $supplier_quotation_id;

            // if( !empty($supplier_quotation_id) ) {
            //     $data['SupplierQuotation'] = array(
            //         'id' => $supplier_quotation_id,
            //         'transaction_status' => 'po',
            //     );
            // }

            if( !empty($dataDetailProduct) ) {
                $grandtotal = 0;
                $values = array_filter($dataDetailProduct);
                unset($data['PurchaseOrderDetail']);

                foreach ($values as $key => $product_id) {
                    $dataPODetail = array();
                    $supplier_quotation_detail_id = $this->MkCommon->filterEmptyField($dataDetail, 'supplier_quotation_detail_id', $key);

                    $qty = $this->MkCommon->filterEmptyField($dataDetail, 'qty', $key);
                    $note = $this->MkCommon->filterEmptyField($dataDetail, 'note', $key);

                    $product = $this->controller->PurchaseOrder->PurchaseOrderDetail->Product->getMerge(array(), $product_id);
                    $is_supplier_quotation = $this->MkCommon->filterEmptyField($product, 'Product', 'is_supplier_quotation');

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
                    } else if( empty($is_supplier_quotation) ) {
                        $price = $this->MkCommon->filterEmptyField($dataDetail, 'price', $key);
                        $disc = $this->MkCommon->filterEmptyField($dataDetail, 'disc', $key);
                        $ppn = $this->MkCommon->filterEmptyField($dataDetail, 'ppn', $key);
                    } else {
                        $price = null;
                        $disc = null;
                        $ppn = null;
                    }

                    $ppn = $this->MkCommon->_callPriceConverter($ppn);
                    $disc = $this->MkCommon->_callPriceConverter($disc);
                    $price = $this->MkCommon->_callPriceConverter($price) * 1;

                    $code = $this->MkCommon->filterEmptyField($product, 'Product', 'code');
                    $name = $this->MkCommon->filterEmptyField($product, 'Product', 'name');
                    $is_supplier_quotation = $this->MkCommon->filterEmptyField($product, 'Product', 'is_supplier_quotation');
                    $unit = $this->MkCommon->filterEmptyField($product, 'ProductUnit', 'name');

                    $dataPODetail['PurchaseOrderDetail'] = array(
                        'product_id' => $product_id,
                        'code' => $code,
                        'name' => $name,
                        'unit' => $unit,
                        'is_supplier_quotation' => $is_supplier_quotation,
                        'supplier_quotation_detail_id' => $supplier_quotation_detail_id,
                        'note' => $note,
                        'price' => $price,
                        'ppn' => $ppn,
                        'disc' => $disc,
                        'qty' => $qty,
                    );

                    $total = $this->calculate($dataPODetail, $ppn_include);
                    $dataPODetail['PurchaseOrderDetail']['total'] = $total;

                    $dataSave[] = $dataPODetail;
                    $grandtotal += $total;
                }

                $data['PurchaseOrder']['grandtotal'] = $grandtotal;
            } else {
                $data['PurchaseOrder']['invalid_detail_po'] = true;
            }

            if( !empty($dataSave) ) {
                $data['PurchaseOrderDetail'] = $dataSave;
            }
        }

        return $data;
    }

    function _callBeforeRenderPO ( $data ) {
        if( !empty($data) ) {
            $nodoc_tmp = Common::hashEmptyField($data, 'PurchaseOrder.nodoc_tmp');

            if( !empty($nodoc_tmp) ) {
                unset($data['PurchaseOrder']['nodoc']);
            }
        }

        $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'transaction_date', date('Y-m-d'));
        $supplier_quotation_id = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'supplier_quotation_id');
        $data = $this->controller->PurchaseOrder->SupplierQuotation->getMerge($data, $supplier_quotation_id);

        $data['PurchaseOrder']['transaction_date'] = $this->MkCommon->getDate($transaction_date, true);

        if( empty($data['PurchaseOrder']['no_sq']) ) {
            $data['PurchaseOrder']['no_sq'] = $this->MkCommon->filterEmptyField($data, 'SupplierQuotation', 'nodoc');
        }

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
            $this->MkCommon->_callAllowClosing($data, 'PurchaseOrderPayment', 'transaction_date');

            $values = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPaymentDetail', 'document_id');
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment', 'transaction_date');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment', 'transaction_status');
            $document_type = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment', 'document_type');

            $dataSave['PurchaseOrderPayment'] = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment');
            $dataSave['PurchaseOrderPayment']['id'] = $id;
            $dataSave['PurchaseOrderPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $dataSave['PurchaseOrderPayment']['user_id'] = Configure::read('__Site.config_user_id');

            if( !empty($values) ) {
                $grandtotal = 0;

                foreach ($values as $key => $document_id) {
                    $idArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPaymentDetail', 'id');
                    $priceArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPaymentDetail', 'price');
                    
                    switch ($document_type) {
                        case 'spk':
                            $totalRemainArr = $this->MkCommon->filterEmptyField($data, 'Spk', 'total_remain');

                            $spk = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->Spk->getMerge(array(), $document_id);
                            $nodoc = $this->MkCommon->filterEmptyField($spk, 'Spk', 'nodoc');
                            $transaction_date = $this->MkCommon->filterEmptyField($spk, 'Spk', 'transaction_date');
                            $note = $this->MkCommon->filterEmptyField($spk, 'Spk', 'note');
                            $is_asset = $this->MkCommon->filterEmptyField($spk, 'Spk', 'is_asset');
                            
                            $total_po = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->Spk->SpkProduct->_callGrandtotal($document_id);
                            $draft_paid = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->_callPaidSpk($document_id, $id);
                            $modelNameDocument = 'Spk';
                            break;
                        
                        default:
                            $totalRemainArr = $this->MkCommon->filterEmptyField($data, 'PurchaseOrder', 'total_remain');

                            $purchaseOrder = $this->controller->PurchaseOrder->getMerge(array(), $document_id);
                            $nodoc = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'nodoc');
                            $transaction_date = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'transaction_date');
                            $note = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'note');
                            $is_asset = $this->MkCommon->filterEmptyField($purchaseOrder, 'PurchaseOrder', 'is_asset');
                            
                            $total_po = $this->controller->PurchaseOrder->PurchaseOrderDetail->_callGrandtotal($document_id);
                            $draft_paid = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->_callPaidPO($document_id, $id);
                            $modelNameDocument = 'PurchaseOrder';
                            break;
                    }

                    $idDetail = !empty($idArr[$key])?$idArr[$key]:false;
                    $price = !empty($priceArr[$key])?$this->MkCommon->_callPriceConverter($priceArr[$key])*1:false;
                    $grandtotal += $price;
                    
                    $total_remain = $total_po - $draft_paid;

                    $dataSave['PurchaseOrderPaymentDetail'][$key] = array(
                        'PurchaseOrderPaymentDetail' => array(
                            // 'id' => $idDetail,
                            'document_id' => $document_id,
                            'price' => $price,
                        ),
                    );

                    switch ($document_type) {
                        case 'spk':
                            $dataSave['PurchaseOrderPaymentDetail'][$key]['Spk'] = array(
                                'id' => $document_id,
                                'total_spk' => $total_po,
                                'total_remain' => $total_remain,
                                'total_paid' => $draft_paid,
                                'nodoc' => $nodoc,
                                'transaction_date' => $transaction_date,
                                'note' => $note,
                            );
                            break;
                        
                        default:
                            $dataSave['PurchaseOrderPaymentDetail'][$key]['PurchaseOrder'] = array(
                                'id' => $document_id,
                                'total_po' => $total_po,
                                'total_remain' => $total_remain,
                                'total_paid' => $draft_paid,
                                'nodoc' => $nodoc,
                                'transaction_date' => $transaction_date,
                                'note' => $note,
                            );
                            break;
                    }

                    $draft_paid += $price;

                    if( $draft_paid >= $total_po ) {
                        $draft_status = 'paid';
                    } else {
                        $draft_status = 'half_paid';
                    }

                    $dataSave['PurchaseOrderPaymentDetail'][$key][$modelNameDocument]['draft_payment_status'] = $draft_status;

                    if( $transaction_status == 'posting' ) {
                        switch ($document_type) {
                            case 'spk':
                                $paid = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->_callPaidSpk($document_id, $id, 'paid-posting');
                                break;
                            
                            default:
                                $paid = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->_callPaidPO($document_id, $id, 'paid-posting');
                                break;
                        }

                        $paid += $price;

                        if( $paid >= $total_po ) {
                            $status = 'paid';
                        } else {
                            $status = 'half_paid';
                        }
                        
                        $dataSave['PurchaseOrderPaymentDetail'][$key][$modelNameDocument]['payment_status'] = $status;
                    }
                }

                $dataSave['PurchaseOrderPayment']['grandtotal'] = $grandtotal;
            }
        }

        return $dataSave;
    }

    function _callBeforeRenderPayment ( $data, $document_id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'PurchaseOrderPayment' => array(
                        'transaction_date',
                    ),
                ),
            ), true);
            $data_empty = false;
        } else {
            $data['PurchaseOrderPayment']['transaction_date'] = date('d/m/Y');
            $data_empty = true;
        }

        $document_type = $this->MkCommon->filterEmptyField($data, 'PurchaseOrderPayment', 'document_type');

        switch ($document_type) {
            case 'spk':
                $vendors = $this->controller->PurchaseOrder->PurchaseOrderPaymentDetail->Spk->_callVendors('unpaid', $document_id);
                break;
            
            default:
                $vendors = $this->controller->PurchaseOrder->_callVendors('unpaid', $document_id);
                break;
        }

        $coas = $this->controller->GroupBranch->Branch->BranchCoa->getCoas();
        $cogs_result = $this->MkCommon->_callCogsOptGroup('PurchaseOrderPayment');
        $cogs_id = Common::hashEmptyField($cogs_result, 'cogs_id');
        
        if( !empty($data_empty) ) {
            $data['PurchaseOrderPayment']['cogs_id'] = $cogs_id;
        }

        $this->MkCommon->_layout_file(array(
            'select',
        ));

        $this->controller->set(compact(
            'vendors', 'coas'
        ));

        return $data;
    }

    function _callBeforeViewReport( $params ) {
        $vendors = $this->controller->PurchaseOrder->_callVendors('unpaid');

        $dateFrom = $this->MkCommon->filterEmptyField($params, 'named', 'DateFrom', false, array(
            'date' => 'd M Y',
        ));
        $dateTo = $this->MkCommon->filterEmptyField($params, 'named', 'DateTo', false, array(
            'date' => 'd M Y',
        ));

        $period_text = __('Periode %s - %s', $dateFrom, $dateTo);

        $title = __('Laporan PO');
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'period_text', 'vendors'
        ));
    }
}
?>