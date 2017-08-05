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

            foreach ($spkProduct['product_id'] as $key => $product_id) {
                $qty = !empty($spkProduct['qty'][$key])?$spkProduct['qty'][$key]:false;
                $price_service = !empty($spkProduct['price_service'][$key])?$spkProduct['price_service'][$key]:false;
                $price = !empty($spkProduct['price'][$key])?$spkProduct['price'][$key]:false;
                $price_service_type = !empty($spkProduct['price_service_type'][$key])?$spkProduct['price_service_type'][$key]:false;
                $tire_position = !empty($spkProduct['tire_position'][$key])?$spkProduct['tire_position'][$key]:false;

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
                    'qty' => $qty,
                    'price_service' => $price_service,
                    'price' => $price,
                    'price_service_type' => $price_service_type,
                );
                $dataProduct = $this->MkCommon->dataConverter($dataProduct, array(
                    'price' => array(
                        'price_service',
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

                $data['SpkProduct'][]['SpkProduct'] = $dataProduct;
            }
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

                foreach ($spkProduction['product_id'] as $key => $product_id) {
                    $qty = !empty($spkProduction['qty'][$key])?$spkProduction['qty'][$key]:false;
                    $price = !empty($spkProduction['price'][$key])?$spkProduction['price'][$key]:false;

                    $price = Common::_callPriceConverter($price);

                    $dataProduct = array(
                        'product_id' => $product_id,
                        'qty' => $qty,
                        'price' => $price,
                    );

                    $data['SpkProduction'][]['SpkProduction'] = $dataProduct;
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
            $truck = $this->controller->Spk->Truck->getMerge(array(), $nopol, 'Truck.nopol');

            $data['Spk']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['Spk']['truck_id'] = $this->MkCommon->filterEmptyField($truck, 'Truck', 'id');

            $data = $this->_callMechanicBeforeSave($data);
            $data = $this->_callProductBeforeSave($data);
            $data = $this->_callProductionBeforeSave($data);
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
        $spkProduction = $this->MkCommon->filterEmptyField($data, 'SpkProduction');

        if( !empty($spkProduction) ) {
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
            }
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
}
?>