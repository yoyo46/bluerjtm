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

    var $hasOne = array(
        'ProductHistory' => array(
            'className' => 'ProductHistory',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => 'product_receipts',
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
            'validateSn' => array(
                'rule' => array('validateSn'),
                'message' => 'Mohon pilih no. seri'
            ),
        ),
        'over_receipt' => array(
            'validateOverReceipt' => array(
                'rule' => array('validateOverReceipt'),
                'message' => 'Penerimaan Qty melebihi quota.'
            ),
        ),
	);

    function validateSn () {
        $data = $this->ProductReceipt->data;
        $document_type = Common::hashEmptyField($data, 'ProductReceipt.document_type');
        $serial_number = Common::hashEmptyField($this->data, 'ProductReceiptDetail.serial_number');
        $is_serial_number = Common::hashEmptyField($this->data, 'ProductReceiptDetail.is_serial_number');

        if( !in_array($document_type, array( 'production' )) && !empty($is_serial_number) && empty($serial_number) ) {
            return false;
        } else {
            return true;
        }
    }

    function validateOverReceipt() {
        $over_receipt = $this->filterEmptyField($this->data, 'ProductReceiptDetail', 'over_receipt');

        if( !empty($over_receipt) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false, $elements = false ){
        $header = isset($elements['header'])?$elements['header']:false;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductReceiptDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
            'contain' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductReceiptDetail.status'] = 1;
                break;
        }

        if( !empty($header) ) {
            // $default_options['conditions']['ProductReceipt.status'] = 1;
            $default_options['contain'][] = 'ProductReceipt';
        }

        $default_options = $this->merge_options($default_options, $options);

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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $code = $this->filterEmptyField($data, 'named', 'code');
        $name = $this->filterEmptyField($data, 'named', 'name');
        $group = $this->filterEmptyField($data, 'named', 'group');
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));

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

        if( !empty($sort) ) {
            $sortBranch = strpos($sort, 'Branch.');
            $sortProduct = strpos($sort, 'Product.');

            if( is_numeric($sortBranch) ) {
                $this->bindModel(array(
                    'hasOne' => array(
                        'Branch' => array(
                            'className' => 'Branch',
                            'foreignKey' => false,
                            'conditions' => array(
                                'ProductReceipt.branch_id = Branch.id'
                            ),
                        ),
                    )
                ), false);

                $default_options['contain'][] = 'ProductReceipt';
                $default_options['contain'][] = 'Branch';
            }
            if( is_numeric($sortProduct) ) {
                $default_options['contain'][] = 'Product';
            }
        }
        
        return $default_options;
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

    function getTotalReceipt( $id, $document_id, $document_type, $product_id ){
        $values = $this->ProductReceipt->getData('list', array(
            'conditions' => array(
                'ProductReceipt.document_id' => $document_id,
                'ProductReceipt.document_type' => $document_type,
            ),
            'fields' => array(
                'ProductReceipt.id',
            ),
        ));

        $this->virtualFields['total'] = 'SUM(ProductReceiptDetail.qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductReceiptDetail.product_receipt_id' => $values,
                'ProductReceiptDetail.product_receipt_id <>' => $id,
                'ProductReceiptDetail.product_id' => $product_id,
            ),
        ));
        return $this->filterEmptyField($value, 'ProductReceiptDetail', 'total', 0);
    }
}
?>