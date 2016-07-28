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
                $qty = $this->MkCommon->filterIssetField($spkProduct, 'qty', $key);
                $price_service = $this->MkCommon->filterIssetField($spkProduct, 'price_service', $key);
                $price = $this->MkCommon->filterIssetField($spkProduct, 'price', $key);

                $dataProduct = array(
                    'product_id' => $product_id,
                    'qty' => $qty,
                    'price_service' => $price_service,
                    'price' => $price,
                );
                $dataProduct = $this->MkCommon->dataConverter($dataProduct, array(
                    'price' => array(
                        'price_service',
                        'price',
                    )
                ));

                $data['SpkProduct'][]['SpkProduct'] = $dataProduct;
            }
        } else {
            $data['Spk']['product'] = '';
        }

        return $data;
    }

    function _callBeforeSave ( $data, $id = false ) {
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
            $spkProduct = $this->controller->Spk->SpkProduct->getMergeList($spkProduct, array(
                'contain' => array(
                    'Product' => array(
                        'contain' => array(
                            'ProductUnit',
                        ),
                    ),
                ),
            ));

            $data['SpkProduct'] = $spkProduct;
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
            }
        }

        $data = $this->_callMechanicBeforeRender($data);
        $data = $this->_callProductBeforeRender($data);

        $data = $this->MkCommon->dataConverter($data, array(
            'date' => array(
                'Spk' => array(
                    'start_date',
                    'estimation_date',
                    'complete_date',
                    'transaction_date',
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

        $this->MkCommon->_layout_file('select');
    	$this->controller->set(compact(
    		'employes', 'toBranches',
            'vendors'
		));
    }
}
?>