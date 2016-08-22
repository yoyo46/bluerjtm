<?php
class SpkProduct extends AppModel {
	var $name = 'SpkProduct';

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
        'price_service' => array(
            'eksternalValidate' => array(
                'rule' => array('eksternalValidate'),
                'message' => 'Harga jasa harap diisi'
            ),
        ),
        'price' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga barang harap diisi'
            ),
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga barang harap diisi'
            ),
        ),
	);

    function eksternalValidate () {
        $dataSpk = $this->Spk->data;
        $data = $this->data;
        $price_service = $this->filterEmptyField($data, 'SpkProduct', 'price_service');

        if( $this->callDisplayToggle('eksternal', $dataSpk) && empty($price_service) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkProduct.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

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
                'SpkProduct.spk_id' => $id
            ),
        ));

        if(!empty($values)){
            $data['SpkProduct'] = $values;
        }

        return $data;
    }
}
?>