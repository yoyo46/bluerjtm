<?php
class ProductReceiptDetail extends AppModel {
	var $name = 'ProductReceiptDetail';

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

    var $hasMany = array(
        'ProductStock' => array(
            'className' => 'ProductStock',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductStock.transaction_type' => 'product_receipts',
            ),
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
        ),
        'serial_number' => array(
            'notMatch' => array(
                'rule' => array('serial_number'),
                'message' => 'Mohon pilih no. seri'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductReceiptDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductReceiptDetail.status'] = 1;
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

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'ProductReceiptDetail.product_receipt_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            if( !empty($values) ) {
                foreach ($values as $key => $value) {
                    $product_id = $this->filterEmptyField($value, 'ProductReceiptDetail', 'product_id');
                    $product_receipt_id = $this->filterEmptyField($value, 'ProductReceiptDetail', 'product_receipt_id');
                    
                    $value = $this->Product->getMerge($value, $product_id);
                    $value = $this->ProductReceipt->ProductReceiptDetailSerialNumber->getMergeAll($value, 'count', $product_id, $product_receipt_id);
                    $values[$key] = $value;
                }
            }

            $data['ProductReceiptDetail'] = $values;
        }

        return $data;
    }

    function doSave( $datas, $product_receipt_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menyimpan penerimaan barang');

        if( !empty($product_receipt_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'ProductReceiptDetail.product_receipt_id' => $product_receipt_id,
            ));
        }

        if ( !empty($datas['ProductReceiptDetail']) ) {
            foreach ($datas['ProductReceiptDetail'] as $key => $data) {
                $this->create();

                if( !empty($product_receipt_id) ) {
                    $data['ProductReceiptDetail']['product_receipt_id'] = $product_receipt_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( !$flagSave ) {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }

            if( empty($result) ) {
                $msg = __('Berhasil menyimpan penerimaan barang');
                $result = true;
            }
        }

        return $result;
    }

    function getTotalReceipt( $id, $document_id, $product_id ){
        $receipts = $this->ProductReceipt->getData('list', array(
            'conditions' => array(
                'ProductReceipt.document_id' => $document_id,
            ),
            'fields' => array(
                'ProductReceipt.id',
            ),
        ));

        $this->virtualFields['total_receipt'] = 'SUM(ProductReceiptDetail.qty)';
        $receipt = $this->getData('first', array(
            'conditions' => array(
                'ProductReceiptDetail.product_receipt_id' => $receipts,
                'ProductReceiptDetail.product_receipt_id <>' => $id,
                'ProductReceiptDetail.product_id' => $product_id,
            ),
        ));
        return $this->filterEmptyField($receipt, 'ProductReceiptDetail', 'total_receipt', 0);
    }
}
?>