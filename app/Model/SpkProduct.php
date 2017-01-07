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
    var $hasMany = array(
        'ProductExpenditureDetail' => array(
            'className' => 'ProductExpenditureDetail',
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
                'rule' => array('eksternalValidate', 'price_service'),
                'message' => 'Harga jasa harap diisi'
            ),
        ),
        'price' => array(
            'checkPrice' => array(
                'rule' => array('eksternalValidate', 'price'),
                'message' => 'Harga barang harap diisi'
            ),
        ),
	);

    function eksternalValidate ( $data, $field = false ) {
        $dataSpk = $this->Spk->data;
        $data = $this->data;
        $price_service = $this->filterEmptyField($data, 'SpkProduct', $field);

        if( $this->callDisplayToggle('eksternal', $dataSpk) && empty($price_service) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkProduct.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'pending-out':
                $default_options['conditions']['Spk.status'] = 1;
                $default_options['conditions']['Spk.document_type'] = array( 'internal' );
                $default_options['conditions']['Spk.transaction_status'] = array( 'open' );
                $default_options['contain'][] = 'Spk';
                break;
            case 'unexit':
                $default_options['conditions']['SpkProduct.document_status <>'] = 'full';
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
                'SpkProduct.spk_id' => $id
            ),
        ));

        if(!empty($values)){
            $data['SpkProduct'] = $values;
        }

        return $data;
    }

    function getMergeProduct( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'SpkProduct.spk_id' => $id,
                'SpkProduct.product_id' => $product_id,
            ),
        ));

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        // $noref = $this->filterEmptyField($data, 'named', 'noref');
        // $document_type = $this->filterEmptyField($data, 'named', 'document_type');
        // $nodoc = $this->filterEmptyField($data, 'named', 'nodoc');
        // $dateFrom = $this->filterEmptyField($data, 'named', 'DateFrom');
        // $dateTo = $this->filterEmptyField($data, 'named', 'DateTo');
        // $vendor_id = $this->filterEmptyField($data, 'named', 'vendor_id');

        // if( !empty($dateFrom) || !empty($dateTo) ) {
        //     if( !empty($dateFrom) ) {
        //         $default_options['conditions']['DATE_FORMAT(Spk.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
        //     }

        //     if( !empty($dateTo) ) {
        //         $default_options['conditions']['DATE_FORMAT(Spk.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
        //     }
        // }
        // if( !empty($nodoc) ) {
        //     $default_options['conditions']['Spk.nodoc LIKE'] = '%'.$nodoc.'%';
        // }
        // if( !empty($vendor_id) ) {
        //     $default_options['conditions']['Spk.vendor_id'] = $vendor_id;
        // }
        // if( !empty($document_type) ) {
        //     $default_options['conditions']['Spk.document_type'] = $document_type;
        // }
        
        return $default_options;
    }
}
?>