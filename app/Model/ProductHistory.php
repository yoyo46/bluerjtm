<?php
class ProductHistory extends AppModel {
	var $name = 'ProductHistory';

    var $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'ProductReceiptDetail' => array(
            'className' => 'ProductReceiptDetail',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => 'product_receipts',
            ),
        ),
        'ProductExpenditureDetail' => array(
            'className' => 'ProductExpenditureDetail',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => array( 'product_expenditure', 'product_expenditure_void' ),
            ),
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'ProductAdjustmentDetail' => array(
            'className' => 'ProductAdjustmentDetail',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => array( 'product_adjustment_plus', 'product_adjustment_min', 'product_adjustment_plus_void', 'product_adjustment_min_void' ),
            ),
        ),
    );

    var $hasMany = array(
        'ProductStock' => array(
            'className' => 'ProductStock',
            'foreignKey' => 'product_history_id',
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductHistory.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductHistory.status'] = 1;
                $default_options['conditions']['ProductHistory.transaction_type NOT'] = array( 'product_expenditure_void', 'product_adjustment_min_void' );
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductHistory.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('stok barang');

        if ( !empty($data) ) {
            $this->create();
            $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);

            $this->set($data);
            $flagValidates = $this->validates();

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

    function _callStock ( $product_id ) {
        $this->virtualFields['qty_cnt'] = 'SUM(ProductHistory.qty)';
        $values = $this->getData('all', array(
            'conditions' => array(
                'ProductHistory.product_id' => $product_id,
                'ProductHistory.product_type' => 'default',
            ),
            'group' => array(
                'ProductHistory.type',
            ),
        ));
        $total_stock = 0;

        if( !empty($values) ) {
            $in = 0;
            $out = 0;

            foreach ($values as $key => $value) {
                $type = $this->filterEmptyField($value, 'ProductHistory', 'type');
                $qty = $this->filterEmptyField($value, 'ProductHistory', 'qty_cnt');

                if( $type == 'out' ) {
                    $out += $qty;
                } else {
                    $in += $qty;
                }
            }

            $total_stock = $in - $out;
        }

        return $total_stock;
    }

    public function afterSave($created, $options = array()){
        $product_id = $this->filterEmptyField($this->data, 'ProductHistory', 'product_id');

        $qty = $this->_callStock($product_id);
        $dataSave = array(
            'Product' => array(
                'id' => $product_id,
                'product_stock_cnt' => $qty,
            ),
        );

        $this->Product->saveAll($dataSave);
    }

    function getMerge( $data, $id, $fieldName = 'ProductHistory.product_id' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id,
                'ProductHistory.product_type' => 'default',
            ),
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $code = !empty($data['named']['code'])?$data['named']['code']:false;
        $product_code = !empty($data['named']['product_code'])?$data['named']['product_code']:false;
        $group = !empty($data['named']['group'])?$data['named']['group']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ProductHistory.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($code) ) {
            $default_options['conditions']['Product.code LIKE'] = '%'.$code.'%';
            $default_options['contain'][] = 'Product';
        }
        if( !empty($product_code) ) {
            $default_options['conditions']['Product.code'] = $product_code;
            $default_options['contain'][] = 'Product';
        }
        if( !empty($group) ) {
            $default_options['conditions']['Product.product_category_id'] = $group;
            $default_options['contain'][] = 'Product';
        }
        
        return $default_options;
    }

    function _callStockTransaction ( $product_id, $transaction_date = null ) {
        $this->virtualFields['qty_cnt'] = 'SUM(CASE WHEN ProductHistory.type = \'in\' THEN ProductHistory.qty ELSE 0 END) - SUM(CASE WHEN ProductHistory.type = \'out\' THEN ProductHistory.qty ELSE 0 END)';
        $conditions = array(
            'ProductHistory.product_id' => $product_id,
            'ProductHistory.product_type' => 'default',
        );

        if( !empty($transaction_date) ) {
            $conditions['ProductHistory.transaction_date <='] = $transaction_date;
        }

        $value = $this->getData('first', array(
            'conditions' => $conditions,
        ));
        $total_stock = $this->filterEmptyField($value, 'ProductHistory', 'qty_cnt', 0);

        return $total_stock;
    }
}
?>