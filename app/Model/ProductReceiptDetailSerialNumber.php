<?php
class ProductReceiptDetailSerialNumber extends AppModel {
	var $name = 'ProductReceiptDetailSerialNumber';

    var $belongsTo = array(
        'ProductReceipt' => array(
            'className' => 'ProductReceipt',
            'foreignKey' => 'product_receipt_id',
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
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
        'serial_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. seri harap diisi'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductReceiptDetailSerialNumber.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductReceiptDetailSerialNumber.status'] = 1;
                break;
            case 'confirm':
                $default_options['conditions']['ProductReceiptDetailSerialNumber.status'] = 1;
                $default_options['conditions']['ProductReceiptDetailSerialNumber.active'] = 1;
                break;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
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

    function doSave( $data, $id = false, $session_id = false ) {
        $result = false;
        $msg = __('menyimpan no. seri');

        if( !empty($data) ) {
            $flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                $this->deleteAll(array(
                    'ProductReceiptDetailSerialNumber.product_id' => $id,
                    'ProductReceiptDetailSerialNumber.session_id' => $session_id,
                ));
                
                if( !empty($flag) ) {
                    $msg = __('Berhasil %s', $msg);
                    $this->saveAll($data);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                        ),
                    );
                } else {
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal %s', $msg),
                    'status' => 'error',
                );
            }
        }

        return $result;
    }

    function getCount ( $session_id, $product_id ) {
        $result = $this->getData('count', array(
            'conditions' => array(
                'ProductReceiptDetailSerialNumber.session_id' => $session_id,
                'ProductReceiptDetailSerialNumber.product_id' => $product_id,
            ),
        ), array(
            'status' => 'active',
        ));
        return $result;
    }

    function getMergeAll ( $data, $type = 'all', $product_id, $relation_id, $fieldName = 'ProductReceiptDetailSerialNumber.product_receipt_id' ) {
        $values = $this->getData($type, array(
            'conditions' => array(
                'ProductReceiptDetailSerialNumber.product_id' => $product_id,
                $fieldName => $relation_id,
            ),
        ), array(
            'status' => 'active',
        ));

        if( !empty($values) ) {
            $data['ProductReceiptDetailSerialNumber'] = $values;
        }

        return $data;
    }
}
?>