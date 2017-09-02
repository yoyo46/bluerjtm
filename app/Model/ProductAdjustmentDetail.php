<?php
class ProductAdjustmentDetail extends AppModel {
	var $name = 'ProductAdjustmentDetail';

    var $belongsTo = array(
        'ProductAdjustment' => array(
            'className' => 'ProductAdjustment',
            'foreignKey' => 'product_adjustment_id',
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
    );

    var $hasMany = array(
        'ProductAdjustmentDetailSerialNumber' => array(
            'className' => 'ProductAdjustmentDetailSerialNumber',
            'foreignKey' => 'product_adjustment_detail_id',
            'dependent' => true,
        ),
        'ProductHistory' => array(
            'className' => 'ProductHistory',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => array( 'product_adjustment_plus', 'product_adjustment_min' ),
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
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Qty harap diisi'
            ),
            'validateDifference' => array(
                'rule' => array('validateDifference'),
                'message' => 'Qty tidak boleh sama dengan stok. Masukkan jumlah lain.'
            ),
        ),
        'price' => array(
            'validatePrice' => array(
                'rule' => array('validatePrice'),
                'message' => 'Harga barang harap diisi'
            ),
        ),
        'serial_number' => array(
            'validateSn' => array(
                'rule' => array('validateSn'),
                'message' => 'Mohon pilih no. seri'
            ),
        ),
        'sn_match' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'sn_match'),
                'message' => 'Serial number keluar tdk sesuai dgn Qty'
            ),
        ),
        'sn_empty' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'sn_empty'),
                'message' => 'Mohon pilih serial number sesuai Qty'
            ),
        ),
	);

    function validatePrice() {
        $data = $this->data;
        $price = Common::hashEmptyField($data, 'ProductAdjustmentDetail.price');
        $qty_difference = Common::hashEmptyField($data, 'ProductAdjustmentDetail.qty_difference');

        if( $qty_difference > 0 && empty($price) ) {
            return false;
        } else {
            return true;
        }
    }

    function validateDifference() {
        $data = $this->data;
        $flag_qty_difference = Common::hashEmptyField($data, 'ProductAdjustmentDetail.flag_qty_difference');

        return $flag_qty_difference;
    }

    function validateSn () {
        $serial_number = Common::hashEmptyField($this->data, 'ProductAdjustmentDetail.serial_number');
        $is_serial_number = Common::hashEmptyField($this->data, 'ProductAdjustmentDetail.is_serial_number');

        if( !empty($is_serial_number) && empty($serial_number) ) {
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
                'ProductAdjustmentDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductAdjustmentDetail.status'] = 1;
                break;
        }

        if( !empty($header) ) {
            $default_options['contain'][] = 'ProductAdjustment';
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
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
                                'ProductAdjustment.branch_id = Branch.id'
                            ),
                        ),
                    )
                ), false);

                $default_options['contain'][] = 'ProductAdjustment';
                $default_options['contain'][] = 'Branch';
            }
            if( is_numeric($sortProduct) ) {
                $default_options['contain'][] = 'Product';
            }
        }
        
        return $default_options;
    }

    function doSave( $datas, $product_adjustment_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menyimpan qty adjustment');

        if( !empty($product_adjustment_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'ProductAdjustmentDetail.product_adjustment_id' => $product_adjustment_id,
            ));
        }

        if ( !empty($datas['ProductAdjustmentDetail']) ) {
            foreach ($datas['ProductAdjustmentDetail'] as $key => $data) {
                $this->create();

                if( !empty($product_adjustment_id) ) {
                    $data['ProductAdjustmentDetail']['product_adjustment_id'] = $product_adjustment_id;
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

    function getTotalAdjustment( $id, $document_id, $document_type, $product_id ){
        $values = $this->ProductAdjustment->getData('list', array(
            'conditions' => array(
                'ProductAdjustment.document_id' => $document_id,
                'ProductAdjustment.document_type' => $document_type,
            ),
            'fields' => array(
                'ProductAdjustment.id',
            ),
        ));

        $this->virtualFields['total'] = 'SUM(ProductAdjustmentDetail.qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductAdjustmentDetail.product_adjustment_id' => $values,
                'ProductAdjustmentDetail.product_adjustment_id <>' => $id,
                'ProductAdjustmentDetail.product_id' => $product_id,
            ),
        ));
        return $this->filterEmptyField($value, 'ProductAdjustmentDetail', 'total', 0);
    }
}
?>