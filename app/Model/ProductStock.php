<?php
class ProductStock extends AppModel {
	var $name = 'ProductStock';

    var $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'ProductReceiptDetail' => array(
            'className' => 'ProductReceiptDetail',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductStock.transaction_type' => 'product_receipts',
            ),
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductStock.id' => 'ASC'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductStock.status'] = 1;
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
        $this->virtualFields['qty_cnt'] = 'SUM(ProductStock.qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductStock.product_id' => $product_id,
            ),
        ));
        $total_stock = $this->filterEmptyField($value, 'ProductStock', 'qty_cnt');

        return $total_stock;
    }

    public function afterSave($created, $options = array()){
        $product_id = $this->filterEmptyField($this->data, 'ProductStock', 'product_id');

        $qty = $this->_callStock($product_id);

        $this->Product->id = $product_id;
        $this->Product->set('product_stock_cnt', $qty);
        $this->Product->save();
    }
}
?>