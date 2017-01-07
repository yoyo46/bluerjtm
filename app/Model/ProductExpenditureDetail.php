<?php
class ProductExpenditureDetail extends AppModel {
	var $name = 'ProductExpenditureDetail';

    var $belongsTo = array(
        'ProductExpenditure' => array(
            'className' => 'ProductExpenditure',
            'foreignKey' => 'product_expenditure_id',
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'SpkProduct' => array(
            'className' => 'SpkProduct',
            'foreignKey' => 'spk_product_id',
        ),
    );

    var $hasOne = array(
        'ProductHistory' => array(
            'className' => 'ProductHistory',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => 'product_expenditures',
            ),
        ),
    );

    var $hasMany = array(
        'ProductExpenditureDetailSerialNumber' => array(
            'className' => 'ProductExpenditureDetailSerialNumber',
            'foreignKey' => 'product_expenditure_detail_id',
            'dependent' => true,
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
        'qty_over' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'qty_over'),
                'message' => 'Tdk boleh lebih dari Qty Out'
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
        'out_stock' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'out_stock'),
                'message' => 'Jml qty melebihi stok barang'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductExpenditureDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductExpenditureDetail.status'] = 1;
                break;
            case 'unexit':
                $default_options['conditions']['ProductExpenditureDetail.status'] = 1;
                $default_options['conditions']['ProductExpenditureDetail.document_status <>'] = 'full';
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
                'ProductExpenditureDetail.product_expenditure_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            if( !empty($values) ) {
                foreach ($values as $key => $value) {
                    $product_id = $this->filterEmptyField($value, 'ProductExpenditureDetail', 'product_id');
                    $product_expenditure_id = $this->filterEmptyField($value, 'ProductExpenditureDetail', 'product_expenditure_id');
                    
                    $value = $this->Product->getMerge($value, $product_id);
                    $values[$key] = $value;
                }
            }

            $data['ProductExpenditureDetail'] = $values;
        }

        return $data;
    }

    function doSave( $datas, $product_expenditure_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menyimpan penerimaan barang');

        if( !empty($product_expenditure_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'ProductExpenditureDetail.product_expenditure_id' => $product_expenditure_id,
            ));
        }

        if ( !empty($datas['ProductExpenditureDetail']) ) {
            foreach ($datas['ProductExpenditureDetail'] as $key => $data) {
                $this->create();

                if( !empty($product_Expenditure_id) ) {
                    $data['ProductExpenditureDetail']['product_expenditure_id'] = $product_expenditure_id;
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

    function getTotalExpenditure( $id, $document_id, $product_id ){
        $values = $this->ProductExpenditure->getData('list', array(
            'conditions' => array(
                'ProductExpenditure.document_id' => $document_id,
            ),
            'fields' => array(
                'ProductExpenditure.id',
            ),
        ));

        $this->virtualFields['total'] = 'SUM(ProductExpenditureDetail.qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductExpenditureDetail.product_expenditure_id' => $values,
                'ProductExpenditureDetail.product_expenditure_id <>' => $id,
                'ProductExpenditureDetail.product_id' => $product_id,
            ),
        ));
        return $this->filterEmptyField($value, 'ProductExpenditureDetail', 'total', 0);
    }
}
?>