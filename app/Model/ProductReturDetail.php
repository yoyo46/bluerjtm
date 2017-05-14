<?php
class ProductReturDetail extends AppModel {
	var $name = 'ProductReturDetail';

    var $belongsTo = array(
        'ProductRetur' => array(
            'className' => 'ProductRetur',
            'foreignKey' => 'product_retur_id',
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
                'ProductHistory.transaction_type' => 'product_returs',
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
        'over_retur' => array(
            'validateOverRetur' => array(
                'rule' => array('validateOverRetur'),
                'message' => 'Qty melebihi quota.'
            ),
        ),
	);

    function validateOverRetur() {
        $over_retur = $this->filterEmptyField($this->data, 'ProductReturDetail', 'over_retur');

        if( !empty($over_retur) ) {
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
                'ProductReturDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductReturDetail.status'] = 1;
                break;
        }

        if( !empty($header) ) {
            // $default_options['conditions']['ProductRetur.status'] = 1;
            $default_options['contain'][] = 'ProductRetur';
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
                'ProductReturDetail.product_retur_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            if( !empty($values) ) {
                foreach ($values as $key => $value) {
                    $product_id = $this->filterEmptyField($value, 'ProductReturDetail', 'product_id');
                    $product_retur_id = $this->filterEmptyField($value, 'ProductReturDetail', 'product_retur_id');
                    
                    $value = $this->Product->getMerge($value, $product_id);
                    $values[$key] = $value;
                }
            }

            $data['ProductReturDetail'] = $values;
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
                                'ProductRetur.branch_id = Branch.id'
                            ),
                        ),
                    )
                ), false);

                $default_options['contain'][] = 'ProductRetur';
                $default_options['contain'][] = 'Branch';
            }
            if( is_numeric($sortProduct) ) {
                $default_options['contain'][] = 'Product';
            }
        }
        
        return $default_options;
    }

    function doSave( $datas, $product_retur_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menyimpan retur barang');

        if( !empty($product_retur_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'ProductReturDetail.product_retur_id' => $product_retur_id,
            ));
        }

        if ( !empty($datas['ProductReturDetail']) ) {
            foreach ($datas['ProductReturDetail'] as $key => $data) {
                $this->create();

                if( !empty($product_retur_id) ) {
                    $data['ProductReturDetail']['product_retur_id'] = $product_retur_id;
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
                $msg = __('Berhasil menyimpan retur barang');
                $result = true;
            }
        }

        return $result;
    }

    function getTotalRetur( $id, $document_id, $document_type, $product_id = null ){
        $values = $this->ProductRetur->getData('list', array(
            'conditions' => array(
                'ProductRetur.document_id' => $document_id,
                'ProductRetur.document_type' => $document_type,
            ),
            'fields' => array(
                'ProductRetur.id',
            ),
        ));

        $this->virtualFields['total'] = 'SUM(ProductReturDetail.qty)';
        $conditions = array(
            'ProductReturDetail.product_retur_id' => $values,
            'ProductReturDetail.product_retur_id <>' => $id,
        );

        if( !empty($product_id) ) {
            $conditions['ProductReturDetail.product_id'] = $product_id;
        }

        $value = $this->getData('first', array(
            'conditions' => $conditions,
        ));
        return $this->filterEmptyField($value, 'ProductReturDetail', 'total', 0);
    }
}
?>