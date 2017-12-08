<?php
class SpkProduct extends AppModel {
	var $name = 'SpkProduct';

    var $belongsTo = array(
        'Spk' => array(
            'foreignKey' => 'spk_id',
        ),
        'Product' => array(
            'foreignKey' => 'product_id',
        ),
    );
    var $hasMany = array(
        'ProductExpenditureDetail' => array(
            'className' => 'ProductExpenditureDetail',
            'foreignKey' => 'product_id',
        ),
        'SpkProductTire' => array(
            'className' => 'SpkProductTire',
            'foreignKey' => 'spk_product_id',
        ),
    );

	var $validate = array(
        'product_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Barang harap dipilih'
            ),
        ),
        'qty' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Qty harap diisi'
            ),
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Qty harap diisi'
            ),
        ),
        // 'price_service' => array(
        //     'eksternalValidate' => array(
        //         'rule' => array('eksternalValidate', 'price_service'),
        //         'message' => 'Harga jasa harap diisi'
        //     ),
        // ),
        'price' => array(
            'checkPrice' => array(
                'rule' => array('eksternalValidate', 'price'),
                'message' => 'Harga barang harap diisi'
            ),
        ),
        'empty_tire' => array(
            'checkEmptyTire' => array(
                'rule' => array('checkEmptyTire'),
                'message' => 'Posisi Ban diganti harap dipilih'
            ),
        ),
	);

    function checkEmptyTire () {
        $data = $this->data;
        
        $empty_tire = Common::hashEmptyField($data, 'SpkProduct.empty_tire');

        if( !empty($empty_tire) ) {
            return false;
        } else {
            return true;
        }
    }

    function eksternalValidate ( $data, $field = false ) {
        $dataSpk = $this->Spk->data;
        $data = $this->data;
        $price_service = Common::hashEmptyField($data, 'SpkProduct.'.$field);

        if( Common::_callDisplayToggle('eksternal', $dataSpk, true) && empty($price_service) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkProduct.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'pending-out':
                $default_options['conditions']['Spk.status'] = 1;
                $default_options['conditions']['Spk.document_type'] = array( 'internal' );
                $default_options['conditions']['Spk.transaction_status'] = array( 'open' );
                $default_options['contain'][] = 'Spk';
                break;
            case 'unexit':
                $default_options['conditions']['SpkProduct.status'] = 1;
                $default_options['conditions']['SpkProduct.draft_document_status <>'] = 'full';
                break;
            case 'unreceipt':
                $default_options['conditions']['SpkProduct.status'] = 1;
                $default_options['conditions']['SpkProduct.receipt_status <>'] = 'full';
                break;
            case 'active':
                $default_options['conditions']['SpkProduct.status'] = 1;
                break;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['contain'])){
            $default_options['contain'] = $options['contain'];
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'SpkProduct.spk_id' => $id
            ),
        ));

        if(!empty($values)){
            $data['SpkProduct'] = $values;
        }

        return $data;
    }

    function getMergeProduct( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'SpkProduct.spk_id' => $id,
                'SpkProduct.product_id' => $product_id,
            ),
        ));

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = $this->filterEmptyField($data, 'named', 'nodoc');
        $dateFrom = $this->filterEmptyField($data, 'named', 'DateFrom');
        $dateTo = $this->filterEmptyField($data, 'named', 'DateTo');
        $code = $this->filterEmptyField($data, 'named', 'code');
        $name = $this->filterEmptyField($data, 'named', 'name');
        $group = $this->filterEmptyField($data, 'named', 'group');
        $nopol = $this->filterEmptyField($data, 'named', 'nopol');
        $status = $this->filterEmptyField($data, 'named', 'status');
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $sortTruck = strpos($sort, 'Truck.');
        $sortDriver = strpos($sort, 'Driver.');
        $sortTruckBrand = strpos($sort, 'TruckBrand.');
        $sortTruckCategory = strpos($sort, 'TruckCategory.');
        $sortProduct = strpos($sort, 'Product.');
        $sortProductUnit = strpos($sort, 'ProductUnit.');
        $bindArr = array();

        if( is_numeric($sortTruck) || is_numeric($sortTruckBrand) || is_numeric($sortTruckCategory) || !empty($nopol) ) {
            $bindArr['hasOne']['Truck'] = array(
                'foreignKey' => FALSE, 
                'conditions' => array(
                    'Spk.truck_id = Truck.id', 
                ), 
            );
        }
        if( is_numeric($sortDriver) ) {
            $bindArr['hasOne']['Driver'] = array(
                'foreignKey' => FALSE, 
                'conditions' => array(
                    'Spk.driver_id = Driver.id', 
                ), 
            );
        }
        if( is_numeric($sortTruckBrand) ) {
            $bindArr['hasOne']['TruckBrand'] = array(
                'foreignKey' => FALSE, 
                'conditions' => array(
                    'Truck.truck_brand_id = TruckBrand.id', 
                ), 
            );
        }
        if( is_numeric($sortTruckCategory) ) {
            $bindArr['hasOne']['TruckCategory'] = array(
                'foreignKey' => FALSE, 
                'conditions' => array(
                    'Truck.truck_category_id = TruckCategory.id', 
                ), 
            );
        }
        if( is_numeric($sortProductUnit) ) {
            $bindArr['hasOne']['ProductUnit'] = array(
                'foreignKey' => FALSE, 
                'conditions' => array(
                    'Product.product_unit_id = ProductUnit.id', 
                ), 
            );
        }

        if( !empty($bindArr) ) {
            $this->bindModel($bindArr, false);
        }

        if( !empty($nodoc) ) {
            $default_options['conditions']['Spk.nodoc LIKE'] = '%'.$nodoc.'%';
            $default_options['contain'][] = 'Spk';
        }
        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Spk.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Spk.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
            
            $default_options['contain'][] = 'Spk';
        }
        if( !empty($code) ) {
            $default_options['conditions']['Product.code LIKE'] = '%'.$code.'%';
            $default_options['contain'][] = 'Product';
        }
        if( !empty($name) ) {
            $default_options['conditions']['Product.name LIKE'] = '%'.$name.'%';
            $default_options['contain'][] = 'Product';
        }
        if( !empty($group) ) {
            $default_options['conditions']['Product.product_category_id'] = $group;
            $default_options['contain'][] = 'Product';
        }

        $default_options = $this->defaultOptionParams($data, $default_options, array(
            'nodoc' => array(
                'field' => 'Spk.nodoc',
                'type' => 'like',
                'contain' => array(
                    'Spk',
                ),
            ),
            'document_type' => array(
                'field' => 'Spk.document_type',
                'type' => 'like',
                'contain' => array(
                    'Spk',
                ),
            ),
            'product_code' => array(
                'field' => 'Product.code',
                'type' => 'like',
                'contain' => array(
                    'Product',
                ),
            ),
            'nopol' => array(
                'field' => 'Truck.nopol',
                'type' => 'like',
                'contain' => array(
                    'Spk',
                    'Truck',
                ),
            ),
            'status' => array(
                'field' => 'Spk.transaction_status',
            ),
        ));

        if( !empty($sort) ) {
            if( is_numeric($sortTruck) ) {
                $default_options['contain'][] = 'Spk';
                $default_options['contain'][] = 'Truck';
            }
            if( is_numeric($sortDriver) ) {
                $default_options['contain'][] = 'Spk';
                $default_options['contain'][] = 'Driver';
            }
            if( is_numeric($sortTruckBrand) ) {
                $default_options['contain'][] = 'Spk';
                $default_options['contain'][] = 'Truck';
                $default_options['contain'][] = 'TruckBrand';
            }
            if( is_numeric($sortTruckCategory) ) {
                $default_options['contain'][] = 'Spk';
                $default_options['contain'][] = 'Truck';
                $default_options['contain'][] = 'TruckCategory';
            }
            if( is_numeric($sortProduct) ) {
                $default_options['contain'][] = 'Product';
            }
            if( is_numeric($sortProductUnit) ) {
                $default_options['contain'][] = 'Product';
                $default_options['contain'][] = 'ProductUnit';
            }
        }
        
        return $default_options;
    }

    function getMergeData( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'SpkProduct.spk_id' => $id,
                'SpkProduct.product_id' => $product_id,
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function _callGrandtotal( $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'SpkProduct.spk_id' => $id,
            ),
        ));
        $grandtotal = 0;

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->getMergeList($value, array(
                    'contain' => array(
                        'Spk',
                    ),
                ));

                $ppn_include = Common::hashEmptyField($value, 'Spk.ppn_include');
                $product_id = Common::hashEmptyField($value, 'SpkProduct.product_id');
                $qty = Common::hashEmptyField($value, 'SpkProduct.qty');
                $price_service = Common::hashEmptyField($value, 'SpkProduct.price');

                $total = $qty*$price_service;
                $grandtotal += $total;
            }
        }

        return $grandtotal;
    }
}
?>