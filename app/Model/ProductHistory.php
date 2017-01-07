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
                'ProductHistory.transaction_type' => 'product_expenditure',
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
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductHistory.status'] = 1;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductHistory.branch_id'] = Configure::read('__Site.config_branch_id');
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

        $this->Product->id = $product_id;
        $this->Product->set('product_stock_cnt', $qty);
        $this->Product->save();
    }

    function getMerge( $data, $id, $fieldName = 'ProductHistory.product_id' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id
            ),
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }
}
?>