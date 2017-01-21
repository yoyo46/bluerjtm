<?php
class Product extends AppModel {
	var $name = 'Product';

    var $belongsTo = array(
        'ProductUnit' => array(
            'className' => 'ProductUnit',
            'foreignKey' => 'product_unit_id',
        ),
        'SupplierQuotationDetail' => array(
            'className' => 'SupplierQuotationDetail',
            'foreignKey' => 'product_id',
        ),
        'TruckCategory' => array(
            'className' => 'TruckCategory',
            'foreignKey' => 'truck_category_id',
        ),
        'ProductCategory' => array(
            'className' => 'ProductCategory',
            'foreignKey' => 'product_category_id',
        ),
    );

    var $hasMany = array(
        'ProductReceiptDetail' => array(
            'className' => 'ProductReceiptDetail',
            'foreignKey' => 'product_id',
        ),
        'PurchaseOrderDetail' => array(
            'className' => 'PurchaseOrderDetail',
            'foreignKey' => 'product_id',
        ),
        'ProductReceiptDetailSerialNumber' => array(
            'className' => 'ProductReceiptDetailSerialNumber',
            'foreignKey' => 'product_id',
        ),
        'ProductStock' => array(
            'className' => 'ProductStock',
            'foreignKey' => 'product_id',
        ),
        'ProductHistory' => array(
            'className' => 'ProductHistory',
            'foreignKey' => 'product_id',
        ),
        'ProductExpenditureDetail' => array(
            'className' => 'ProductExpenditureDetail',
            'foreignKey' => 'product_id',
        ),
        'SpkProduct' => array(
            'className' => 'SpkProduct',
            'foreignKey' => 'product_id',
        ),
        'ProductExpenditureDetailSerialNumber' => array(
            'className' => 'ProductExpenditureDetailSerialNumber',
            'foreignKey' => 'product_id',
        ),
        'SpkProduction' => array(
            'className' => 'SpkProduction',
            'foreignKey' => 'product_id',
        ),
    );

	var $validate = array(
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode barang harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Kode barang sudah terdaftar, mohon masukkan kode lain.'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama barang harap diisi'
            ),
        ),
        'product_unit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Satuan barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Satuan barang harap dipilih'
            ),
        ),
        'product_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Grup barang harap dipilih'
            ),
        ),
	);

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['full_name'] = 'CONCAT(Product.code, " - ", Product.name)';
    }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Product.name' => 'ASC'
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['Product.status'] = 1;
                break;
            case 'sq':
                $default_options['conditions']['Product.status'] = 1;
                $default_options['conditions']['Product.is_supplier_quotation'] = 1;
                break;
            case 'no-sq':
                $default_options['conditions']['Product.status'] = 1;
                $default_options['conditions']['Product.is_supplier_quotation'] = 0;
                break;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = array_merge($default_options['order'], $options['order']);
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id = false, $modelName  = 'SupplierQuotationDetail', $quotation_id = false ){
        if( !empty($data[0]) ) {
            foreach ($data as $key => $value) {
                $id = !empty($value[$modelName]['product_id'])?$value[$modelName]['product_id']:false;

                $value = $this->getData('first', array(
                    'conditions' => array(
                        'Product.id' => $id,
                    ),
                ));

                if( !empty($value) ) {
                    $product_unit_id = !empty($value['Product']['product_unit_id'])?$value['Product']['product_unit_id']:false;
                    $product_category_id = !empty($value['Product']['product_category_id'])?$value['Product']['product_category_id']:false;
                    $value = $this->ProductUnit->getMerge($value, $product_unit_id);

                    $data[$key] = array_merge($data[$key], $value);

                    switch ($modelName) {
                        case 'SupplierQuotationDetail':
                            $data[$key]['Product']['rate'] = $this->SupplierQuotationDetail->SupplierQuotation->_callRatePrice($id, $quotation_id);
                            break;
                        case 'PurchaseOrderDetail':
                            $data[$key]['PurchaseOrderDetail']['code'] = $this->filterEmptyField($value, 'Product', 'code');
                            $data[$key]['PurchaseOrderDetail']['name'] = $this->filterEmptyField($value, 'Product', 'name');
                            $data[$key]['PurchaseOrderDetail']['unit'] = $this->filterEmptyField($value, 'ProductUnit', 'name');
                            break;
                    }
                }
            }
        } else if( empty($data['Product']) && !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'Product.id' => $id
                ),
            ), array(
                'status' => 'all',
            ));

            if(!empty($value)){
                $product_unit_id = !empty($value['Product']['product_unit_id'])?$value['Product']['product_unit_id']:false;
                $value = $this->ProductUnit->getMerge($value, $product_unit_id);

                $data = array_merge($data, $value);
            }
        }

        return $data;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('barang');

        if ( !empty($data) ) {
            if( empty($id) ) {
                $this->create();
                $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);
            } else {
                $this->id = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            $this->set($data);
            $flagValidates = $this->validates();
            $code = !empty($data['Product']['code'])?$data['Product']['code']:false;
            $name = !empty($data['Product']['name'])?$data['Product']['name']:false;

            if( !empty($code) && !empty($name) ) {
                $defaul_msg = sprintf(__('%s (%s) %s'), $defaul_msg, $code, $name);
            }

            if( $flagValidates ) {
                if( $this->save($data) ) {
                    $id = $this->id;
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                        ),
                    );
                } else {
                    $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = !empty($data['named']['keyword'])?$data['named']['keyword']:false;
        $code = !empty($data['named']['code'])?$data['named']['code']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $group = !empty($data['named']['group'])?$data['named']['group']:false;

        // if( !empty($keyword) ) {
        //     $default_options['conditions']['OR'] = array(
        //         'Product.code LIKE' => '%'.$keyword.'%',
        //         'Product.name LIKE' => '%'.$keyword.'%',
        //     );
        // }
        
        if( !empty($code) ) {
            $default_options['conditions']['Product.code LIKE'] = '%'.$code.'%';
        }
        if( !empty($name) ) {
            $default_options['conditions']['Product.name LIKE'] = '%'.$name.'%';
        }
        if( !empty($group) ) {
            $default_options['conditions']['Product.product_category_id'] = $group;
        }
        
        return $default_options;
    }
}
?>