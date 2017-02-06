<?php
class SpkProduction extends AppModel {
	var $name = 'SpkProduction';

    var $belongsTo = array(
        'Spk' => array(
            'foreignKey' => 'spk_id',
        ),
        'Product' => array(
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
        'qty' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Qty harap diisi'
            ),
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Qty harap diisi'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkProduction.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'unreceipt':
                $default_options['conditions']['SpkProduction.status'] = 1;
                $default_options['conditions']['SpkProduction.receipt_status <>'] = 'full';
                break;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['contain'])){
            $default_options['contain'] = $options['contain'];
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
                'SpkProduction.spk_id' => $id
            ),
        ));

        if(!empty($values)){
            $data['SpkProduction'] = $values;
        }

        return $data;
    }

    function getMergeProduct( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'SpkProduction.spk_id' => $id,
                'SpkProduction.product_id' => $product_id,
            ),
        ));

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $code = $this->filterEmptyField($data, 'named', 'code');
        $name = $this->filterEmptyField($data, 'named', 'name');
        $group = $this->filterEmptyField($data, 'named', 'group');

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
        
        return $default_options;
    }

    function getMergeData( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'SpkProduction.spk_id' => $id,
                'SpkProduction.product_id' => $product_id,
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }
}
?>