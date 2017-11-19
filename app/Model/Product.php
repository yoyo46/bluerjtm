<?php
class Product extends AppModel {
	var $name = 'Product';

    var $belongsTo = array(
        'ProductUnit' => array(
            'className' => 'ProductUnit',
            'foreignKey' => 'product_unit_id',
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
        'SupplierQuotationDetail' => array(
            'className' => 'SupplierQuotationDetail',
            'foreignKey' => 'product_id',
        ),
        'ProductReceiptDetail' => array(
            'className' => 'ProductReceiptDetail',
            'foreignKey' => 'product_id',
        ),
        'ProductReturDetail' => array(
            'className' => 'ProductReturDetail',
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
        'ProductAdjustmentDetailSerialNumber' => array(
            'className' => 'ProductAdjustmentDetailSerialNumber',
            'foreignKey' => 'product_id',
        ),
        'ProductAdjustmentDetail' => array(
            'className' => 'ProductAdjustmentDetail',
            'foreignKey' => 'product_id',
        ),
        'ProductMinStock' => array(
            'className' => 'ProductMinStock',
            'foreignKey' => 'product_id',
        ),
        'ViewStock' => array(
            'className' => 'ViewStock',
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
            'contain' => array(),
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

        $default_options = $this->merge_options($default_options, $options);

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
            $product_min_stock_id = Common::hashEmptyField($value, 'ProductMinStock.id');
            $productMinStock = Common::hashEmptyField($data, 'ProductMinStock');
            $branch_id = Configure::read('__Site.config_branch_id');

            if( !empty($productMinStock) ) {
                $min_stock = Common::hashEmptyField($data, 'ProductMinStock.min_stock');
                unset($data['ProductMinStock']);

                $data['ProductMinStock'][] = array(
                    'id' => $product_min_stock_id,
                    'min_stock' => $min_stock,
                    'branch_id' => $branch_id,
                );
            }

            if( empty($id) ) {
                $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);
            } else {
                $data['Product']['id'] = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            $code = Common::hashEmptyField($data, 'Product.code');
            $name = Common::hashEmptyField($data, 'Product.name');

            if( !empty($code) && !empty($name) ) {
                $defaul_msg = sprintf(__('%s (%s) %s'), $defaul_msg, $code, $name);
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                // $this->ProductMinStock->deleteAll(array(
                //     'ProductMinStock.product_id' => $id,
                //     'ProductMinStock.branch_id' => $branch_id,
                // ));
                $this->saveAll($data);

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
        $unit = !empty($data['named']['unit'])?$data['named']['unit']:false;
        $status_stock = !empty($data['named']['status_stock'])?$data['named']['status_stock']:false;
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $direction = !empty($data['named']['direction'])?$data['named']['direction']:false;
        $branch = isset($data['named']['branch'])?$data['named']['branch']:Configure::read('__Site.config_branch_id');
        $productMinStock = strpos($sort, 'ProductMinStock.');
        $viewStock = strpos($sort, 'ViewStock.');
        
        if( is_numeric($productMinStock) || !empty($status_stock) ) {
            if( !empty($branch) ) {
                $conditions['ProductMinStock.branch_id'] = $branch;
            } else {
                $conditions = array();
            }

            $this->unBindModel(array(
                'hasMany' => array(
                    'ProductMinStock'
                )
            ));
            $this->bindModel(array(
                'hasOne' => array(
                    'ProductMinStock' => array(
                        'className' => 'ProductMinStock',
                        'foreignKey' => 'product_id',
                        'conditions' => $conditions,
                    )
                )
            ), false);
        }
        
        if( !empty($code) ) {
            $default_options['conditions']['Product.code LIKE'] = '%'.$code.'%';
        }
        if( !empty($name) ) {
            $default_options['conditions']['Product.name LIKE'] = '%'.$name.'%';
        }
        if( !empty($group) ) {
            $default_options['conditions']['Product.product_category_id'] = $group;
        }
        if( !empty($unit) ) {
            $default_options['conditions']['Product.product_unit_id'] = $unit;
        }
        if( !empty($status_stock) ) {
            $this->unBindModel(array(
                'hasMany' => array(
                    'ViewStock'
                )
            ));
            $this->bindModel(array(
                'hasOne' => array(
                    'ViewStock' => array(
                        'className' => 'ViewStock',
                        'foreignKey' => 'product_id',
                        'conditions' => array(
                            'OR' => array(
                                'ProductMinStock.branch_id' => NULL,
                                'ViewStock.branch_id = ProductMinStock.branch_id',
                            ),
                        ),
                    )
                )
            ), false);

            switch ($status_stock) {
                case 'stock_available':
                    $default_options['conditions']['ViewStock.product_stock_cnt >'] = 0;
                    $default_options['conditions'][] = 'ViewStock.product_stock_cnt > IFNULL(ProductMinStock.min_stock, 0)';
                    break;
                case 'stock_empty':
                    $default_options['conditions'][]['OR'] = array(
                        array(
                            'ViewStock.product_stock_cnt' => 0,
                        ),
                        array(
                            'ViewStock.product_stock_cnt' => NULL,
                        ),
                    );
                    $default_options['group'] = array(
                        'Product.id',
                        'ViewStock.branch_id',
                    );
                    break;
                case 'stock_minimum':
                    $default_options['conditions']['ViewStock.product_stock_cnt >'] = 0;
                    $default_options['conditions'][] = 'ViewStock.product_stock_cnt <= IFNULL(ProductMinStock.min_stock, 0)';
                    break;
                case 'stock_minimum_empty':
                    $default_options['conditions'][]['OR'] = array(
                        array(
                            'ViewStock.product_stock_cnt >' => 0,
                            'ViewStock.product_stock_cnt <= IFNULL(ProductMinStock.min_stock, 0)',
                        ),
                        array(
                            'ViewStock.product_stock_cnt' => null,
                        ),
                    );
                    break;
            }

            $default_options['contain'][] = 'ProductMinStock';
            $default_options['contain'][] = 'ViewStock';
        }

        if( !empty($sort) ) {
            if( is_numeric($productMinStock) ) {
                $default_options['contain'][] = 'ProductMinStock';
            }
            if( is_numeric($viewStock) ) {
                $default_options['contain'][] = 'ViewStock';
                $default_options['group'] = array(
                    'Product.id',
                    'ViewStock.branch_id',
                );
            }

            $default_options['order'] = array(
                $sort => $direction,
            );
        }
        
        return $default_options;
    }
}
?>