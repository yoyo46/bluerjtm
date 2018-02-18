<?php
App::uses('Sanitize', 'Utility');
class RjSpkComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callMechanicBeforeSave ( $data ) {
        $spkMechanic = $this->MkCommon->filterEmptyField($data, 'SpkMechanic', false, array());
        $spkMechanic = array_filter($spkMechanic);
        $mechanic = Common::_callDisplayToggle('mechanic', $data, true);

        if( !empty($mechanic) ) {
            if( !empty($spkMechanic['employe_id']) ) {
                $data = $this->MkCommon->_callUnset(array(
                    'SpkMechanic',
                ), $data);
                $spkMechanic['employe_id'] = array_unique($spkMechanic['employe_id']);
                $spkMechanic['employe_id'] = array_values($spkMechanic['employe_id']);

                foreach ($spkMechanic['employe_id'] as $key => $mechanic_id) {
                    $data['SpkMechanic'][]['SpkMechanic'] = array(
                        'employe_id' => $mechanic_id,
                    );
                }
            } else {
                $data['Spk']['mechanic'] = '';
            }
        } else if( !empty($data['SpkMechanic']) ) {
            unset($data['SpkMechanic']);
        }

        return $data;
    }

    function _callProductBeforeSave ( $data ) {
        $spkProduct = $this->MkCommon->filterEmptyField($data, 'SpkProduct', false, array());
        $spkProduct = array_filter($spkProduct);

        if( !empty($spkProduct['product_id']) ) {
            $data = $this->MkCommon->_callUnset(array(
                'SpkProduct',
            ), $data);

            $grandtotal = 0;

            foreach ($spkProduct['product_id'] as $key => $product_id) {
                $note = !empty($spkProduct['note'][$key])?$spkProduct['note'][$key]:false;
                $qty = !empty($spkProduct['qty'][$key])?$spkProduct['qty'][$key]:false;
                // $price_service = !empty($spkProduct['price_service'][$key])?$spkProduct['price_service'][$key]:false;
                $price = !empty($spkProduct['price'][$key])?Common::_callPriceConverter($spkProduct['price'][$key]):false;
                // $price_service_type = !empty($spkProduct['price_service_type'][$key])?$spkProduct['price_service_type'][$key]:false;
                $tire_position = !empty($spkProduct['tire_position'][$key])?$spkProduct['tire_position'][$key]:false;
                $document_status = !empty($spkProduct['document_status'][$key])?$spkProduct['document_status'][$key]:false;
                $draft_document_status = !empty($spkProduct['draft_document_status'][$key])?$spkProduct['draft_document_status'][$key]:false;
                $receipt_status = !empty($spkProduct['receipt_status'][$key])?$spkProduct['receipt_status'][$key]:false;
                $retur_status = !empty($spkProduct['retur_status'][$key])?$spkProduct['retur_status'][$key]:false;

                $product = $this->controller->Spk->SpkProduct->Product->getData('first', array(
                    'conditions' => array(
                        'Product.id' => $product_id,
                    ),
                ));
                $product = $this->controller->Spk->SpkProduct->Product->getMergeList($product, array(
                    'contain' => array(
                        'ProductCategory',
                    ),
                ));
                $category = Common::hashEmptyField($product, 'ProductCategory.name');
                $category = Common::toSlug($category);

                $dataProduct = array(
                    'product_id' => $product_id,
                    'note' => $note,
                    'qty' => $qty,
                    // 'price_service' => $price_service,
                    'price' => $price,
                    // 'price_service_type' => $price_service_type,
                    'document_status' => $document_status,
                    'draft_document_status' => $draft_document_status,
                    'receipt_status' => $receipt_status,
                    'retur_status' => $retur_status,
                );
                $dataProduct = $this->MkCommon->dataConverter($dataProduct, array(
                    'price' => array(
                        // 'price_service',
                        'price',
                    )
                ));

                if( $category == 'ban' ) {
                    if( !empty($tire_position) ) {
                        foreach ($tire_position as $key => $position) {
                            $dataProduct['SpkProductTire'][] = array(
                                'product_id' => $product_id,
                                'position' => $position,
                            );
                        }
                    } else {
                        $dataProduct['empty_tire'] = true;
                    }
                }

                $grandtotal += $price * $qty;
                $data['SpkProduct'][]['SpkProduct'] = $dataProduct;
            }
            
            $data['Spk']['grandtotal'] = $grandtotal;
        } else {
            $data['Spk']['product'] = '';
        }

        return $data;
    }

    function _callProductionBeforeSave ( $data ) {
        $document_type = $this->MkCommon->filterEmptyField($data, 'Spk', 'document_type');
        $spkProduction = $this->MkCommon->filterEmptyField($data, 'SpkProduction', false, array());
        $spkProduction = array_filter($spkProduction);

        if( $document_type == 'production' ) {
            if( !empty($spkProduction['product_id']) ) {
                $data = $this->MkCommon->_callUnset(array(
                    'SpkProduction',
                ), $data);
                $grandtotal = 0;

                foreach ($spkProduction['product_id'] as $key => $product_id) {
                    $qty = !empty($spkProduction['qty'][$key])?$spkProduction['qty'][$key]:false;
                    $price = !empty($spkProduction['price'][$key])?Common::_callPriceConverter($spkProduction['price'][$key]):false;
                    $document_status = !empty($spkProduction['document_status'][$key])?$spkProduction['document_status'][$key]:false;
                    $receipt_status = !empty($spkProduction['receipt_status'][$key])?$spkProduction['receipt_status'][$key]:false;

                    $dataProduct = array(
                        'product_id' => $product_id,
                        'qty' => $qty,
                        'price' => $price,
                        'document_status' => $document_status,
                        'receipt_status' => $receipt_status,
                    );

                    $grandtotal += $price * $qty;
                    $data['SpkProduction'][]['SpkProduction'] = $dataProduct;
                }
                
                $grandtotal_spk = Common::hashEmptyField($data, 'Spk.grandtotal');

                if( $grandtotal_spk != $grandtotal ) {
                    $data['Spk']['production_notbalance'] = '';
                }
            } else {
                $data['Spk']['production'] = '';
            }
        }

        return $data;
    }

    function _callBeforeSave ( $data ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'Spk' => array(
                        'transaction_date',
                        'start_date',
                        'estimation_date',
                        'complete_date',
                    ),
                )
            ));
            $nopol = $this->MkCommon->filterEmptyField($data, 'Spk', 'nopol');
            $nolaka = $this->MkCommon->filterEmptyField($data, 'Spk', 'nolaka');

            $current_truck = $this->controller->Spk->Truck->getMerge(array(), $nopol, 'Truck.nopol');
            $current_truck_id = Common::hashEmptyField($current_truck, 'Truck.id');

            $data['Spk']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['Spk']['truck_id'] = $this->MkCommon->filterEmptyField($current_truck, 'Truck', 'id');

            if( !empty($nolaka) ) {
                $laka = $this->controller->Spk->Laka->getData('first', array(
                    'conditions' => array(
                        'Laka.nodoc' => $nolaka,
                    ),
                ));
                $data['Spk']['laka_id'] = Common::hashEmptyField($laka, 'Laka.id');
            }

            $data = $this->_callMechanicBeforeSave($data);
            $data = $this->_callProductBeforeSave($data);
            $data = $this->_callProductionBeforeSave($data);

            $this->controller->set('current_truck', $current_truck);
            $this->controller->set('current_truck_id', $current_truck_id);
        }

        return $data;
    }

    function _callBeforeRender () {
        $employes = $this->controller->User->Employe->getData('list', array(
            'fields' => array(
                'Employe.id', 'Employe.full_name',
            ),
            'contain' => false,
        ), array(
            'role' => 'mekanik',
        ));
        $toBranches = $this->controller->GroupBranch->Branch->getData('list', array(
            'fields' => array(
                'Branch.id', 'Branch.code',
            ),
            'contain' => false,
        ));
        $vendors = $this->controller->Spk->Vendor->getData('list');

        $this->MkCommon->_layout_file('select');
        $this->controller->set(compact(
            'employes', 'toBranches',
            'vendors'
        ));
    }

    function _callProductBeforeRender ( $data ) {
        $spkProduct = $this->MkCommon->filterEmptyField($data, 'SpkProduct');

        if( !empty($spkProduct) ) {
            foreach ($spkProduct as $key => &$value) {
                $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduct', 'product_id');
                
                $value = $this->controller->Spk->SpkProduct->getMergeList($value, array(
                    'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                                'ProductCategory',
                            ),
                        ),
                    ),
                ));
                $value['Product']['product_stock_cnt'] = $this->controller->Spk->SpkProduct->Product->ProductStock->_callStock($product_id);
            }

            $data['SpkProduct'] = $spkProduct;
        }

        return $data;
    }

    function _callProductionBeforeRender ( $data ) {
        $document_type = $this->MkCommon->filterEmptyField($data, 'Spk', 'document_type');
        $spkProduction = $this->MkCommon->filterEmptyField($data, 'SpkProduction');

        if( !empty($spkProduction) && $document_type == 'production' ) {
            foreach ($spkProduction as $key => &$value) {
                $product_id = $this->MkCommon->filterEmptyField($value, 'SpkProduction', 'product_id');
                
                $value = $this->controller->Spk->SpkProduction->getMergeList($value, array(
                    'contain' => array(
                        'Product' => array(
                            'contain' => array(
                                'ProductUnit',
                            ),
                        ),
                    ),
                ));
            }

            $data['SpkProduction'] = $spkProduction;
        } else {
            $data['SpkProduction'] = array();
        }

        return $data;
    }

    function _callMechanicBeforeRender ( $data ) {
        $spkMechanic = $this->MkCommon->filterEmptyField($data, 'SpkMechanic', false, array());

        if( !empty($spkMechanic) ) {
            $spkMechanic = Set::extract('/SpkMechanic/employe_id', $spkMechanic);

            if( !empty($spkMechanic) ) {
                $spkMechanic = array_unique($spkMechanic);
                $spkMechanic = array_values($spkMechanic);
                $data['SpkMechanic']['employe_id'] = $spkMechanic;
            }
        }

        return $data;
    }

    function _callSpkBeforeRender ( $data, $value = false ) {
        $document_id = false;

        if( empty($data) ) {
            $data = $value;

            if( empty($value) ) {
                $data['Spk']['transaction_date'] = date('Y-m-d');
                $data['Spk']['start_date'] = date('Y-m-d');
            } else {
                $data['Spk']['start_time'] = Common::hashEmptyField($data, 'Spk.start_date', null, array(
                    'date' => 'H:i',
                ));
                $data['Spk']['estimation_time'] = Common::hashEmptyField($data, 'Spk.estimation_date', null, array(
                    'date' => 'H:i',
                ));
                $data['Spk']['complete_time'] = Common::hashEmptyField($data, 'Spk.complete_date', null, array(
                    'date' => 'H:i',
                ));
                $data['Spk']['nolaka'] = Common::hashEmptyField($data, 'Laka.nodoc');
            }

            $current_truck['Truck'] = Common::hashEmptyField($data, 'Truck.Truck');
            $current_truck_id = Common::hashEmptyField($data, 'Truck.id');

            $this->controller->set(array(
                'current_truck' => $current_truck,
                'current_truck_id' => $current_truck_id,
            ));
        }

        $data = $this->_callMechanicBeforeRender($data);
        $data = $this->_callProductBeforeRender($data);
        $data = $this->_callProductionBeforeRender($data);

        $data = $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'Spk' => array(
                    'start_date',
                    'estimation_date',
                    'transaction_date',
                    'complete_date',
                ),
            )
        ), true);
        $this->controller->request->data = $data;

        $employes = $this->controller->User->Employe->getData('list', array(
        	'fields' => array(
        		'Employe.id', 'Employe.full_name',
    		),
            'contain' => false,
    	), array(
    		'role' => 'mekanik',
    	));
        $toBranches = $this->controller->GroupBranch->Branch->getData('list', array(
        	'fields' => array(
        		'Branch.id', 'Branch.code',
    		),
    		'contain' => false,
    	));
        $vendors = $this->controller->Spk->Vendor->getData('list');
        $drivers = $this->controller->Spk->Driver->getData('list', array(
            'fields' => array(
                'Driver.id', 'Driver.driver_name'
            ),
        ), array(
            'branch' => false,
        ));

        $this->MkCommon->_layout_file('select');
    	$this->controller->set(compact(
    		'employes', 'toBranches',
            'vendors', 'value', 'drivers'
		));
    }

    function _callBeforeViewTireReports( $params ) {
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Laporan Pergantian Ban');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'period_text'
        ));
    }

    function _callBeforeViewSpkReports( $params ) {
        $dateFrom = Common::hashEmptyField($params, 'named.DateFrom');
        $dateTo = Common::hashEmptyField($params, 'named.DateTo');
        $title = __('Laporan SPK');

        if( !empty($dateFrom) && !empty($dateTo) ) {
            $period_text = __('Periode %s', $this->MkCommon->getCombineDate($dateFrom, $dateTo));
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
        $this->controller->set(compact(
            'period_text'
        ));
    }

    function _callBeforeViewMaintenanceCostReports( $params ) {
        $year = Common::hashEmptyField($params, 'named.year');
        $title = __('Laporan Perbaikan');

        if( !empty($year) ) {
            $title .= __(' - Tahun %s', $year);
        }
        
        $this->controller->set('sub_module_title', $title);
        $this->controller->set('active_menu', $title);
    }

    function _callBeforeSavePayment ( $data, $id = false ) {
        $dataSave = array();

        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'SpkPayment' => array(
                        'transaction_date',
                    ),
                ),
            ));
            $this->MkCommon->_callAllowClosing($data, 'SpkPayment', 'transaction_date');

            $values = $this->MkCommon->filterEmptyField($data, 'SpkPaymentDetail', 'spk_id');
            $transaction_date = $this->MkCommon->filterEmptyField($data, 'SpkPayment', 'transaction_date');
            $transaction_status = $this->MkCommon->filterEmptyField($data, 'SpkPayment', 'transaction_status');

            $dataSave['SpkPayment'] = $this->MkCommon->filterEmptyField($data, 'SpkPayment');
            $dataSave['SpkPayment']['id'] = $id;
            $dataSave['SpkPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $dataSave['SpkPayment']['user_id'] = Configure::read('__Site.config_user_id');

            if( !empty($values) ) {
                $grandtotal = 0;

                foreach ($values as $key => $spk_id) {
                    $idArr = $this->MkCommon->filterEmptyField($data, 'SpkPaymentDetail', 'id');
                    $priceArr = $this->MkCommon->filterEmptyField($data, 'SpkPaymentDetail', 'price');
                    $totalRemainArr = $this->MkCommon->filterEmptyField($data, 'Spk', 'total_remain');
                    
                    $spk = $this->controller->Spk->getMerge(array(), $spk_id);
                    $nodoc = $this->MkCommon->filterEmptyField($spk, 'Spk', 'nodoc');
                    $transaction_date = $this->MkCommon->filterEmptyField($spk, 'Spk', 'transaction_date');
                    $note = $this->MkCommon->filterEmptyField($spk, 'Spk', 'note');
                    $is_asset = $this->MkCommon->filterEmptyField($spk, 'Spk', 'is_asset');
                    
                    $total_spk = $this->controller->Spk->SpkProduct->_callGrandtotal($spk_id);
                    // $total_spk = $this->MkCommon->filterEmptyField($spk, 'Spk', 'grandtotal');

                    $idDetail = !empty($idArr[$key])?$idArr[$key]:false;
                    $price = !empty($priceArr[$key])?$this->MkCommon->_callPriceConverter($priceArr[$key])*1:false;
                    $grandtotal += $price;
                    
                    $draft_paid = $this->controller->Spk->SpkPaymentDetail->_callPaidSpk($spk_id, $id);
                    $total_remain = $total_spk - $draft_paid;

                    $dataSave['SpkPaymentDetail'][$key] = array(
                        'SpkPaymentDetail' => array(
                            // 'id' => $idDetail,
                            'spk_id' => $spk_id,
                            'price' => $price,
                        ),
                        'Spk' => array(
                            'id' => $spk_id,
                            'total_spk' => $total_spk,
                            'total_remain' => $total_remain,
                            'total_paid' => $draft_paid,
                            'nodoc' => $nodoc,
                            'transaction_date' => $transaction_date,
                            'note' => $note,
                        ),
                    );
                    $draft_paid += $price;

                    if( $draft_paid >= $total_spk ) {
                        $draft_status = 'paid';
                    } else {
                        $draft_status = 'half_paid';
                    }
                    
                    $dataSave['SpkPaymentDetail'][$key]['Spk']['draft_payment_status'] = $draft_status;

                    if( $transaction_status == 'posting' ) {
                        $paid = $this->controller->Spk->SpkPaymentDetail->_callPaidSpk($spk_id, $id, 'paid-posting');
                        $paid += $price;

                        if( $paid >= $total_spk ) {
                            $status = 'paid';
                        } else {
                            $status = 'half_paid';
                        }
                        
                        $dataSave['SpkPaymentDetail'][$key]['Spk']['payment_status'] = $status;
                    }
                }

                $dataSave['SpkPayment']['grandtotal'] = $grandtotal;
            }
        }

        return $dataSave;
    }

    function _callBeforeRenderPayment ( $data, $spk_id = false ) {
        if( !empty($data) ) {
            $data = $this->MkCommon->dataConverter($data, array(
                'date' => array(
                    'SpkPayment' => array(
                        'transaction_date',
                    ),
                ),
            ), true);
        } else {
            $data['SpkPayment']['transaction_date'] = date('d/m/Y');
        }

        $vendors = $this->controller->Spk->_callVendors('unpaid', $spk_id);
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