<?php
class SpkPaymentDetail extends AppModel {
	var $name = 'SpkPaymentDetail';

    var $belongsTo = array(
        'Spk' => array(
            'className' => 'Spk',
            'foreignKey' => 'spk_id',
        ),
        'SpkPayment' => array(
            'className' => 'SpkPayment',
            'foreignKey' => 'spk_payment_id',
        ),
    );

	var $validate = array(
        'spk_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Spk harap dipilih'
            ),
        ),
        'price' => array(
            'emptyFill' => array(
                'rule' => array('emptyFill', 'price'),
                'message' => 'Total dibayar harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Total dibayar harus berupa angka'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'SpkPaymentDetail.status' => 1,
            ),
            'order'=> array(
                'SpkPaymentDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['SpkPaymentDetail.status'] = 1;
                break;
            case 'paid':
                $default_options['conditions']['SpkPaymentDetail.status'] = 1;
                $default_options['conditions']['SpkPayment.transaction_status'] = array( 'posting', 'unposting' );
                $default_options['conditions']['SpkPayment.status'] = 1;
                $default_options['contain'][] = 'SpkPayment';
                break;
            case 'paid-posting':
                $default_options['conditions']['SpkPaymentDetail.status'] = 1;
                $default_options['conditions']['SpkPayment.transaction_status'] = array( 'posting' );
                $default_options['conditions']['SpkPayment.status'] = 1;
                $default_options['contain'][] = 'SpkPayment';
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
                'SpkPaymentDetail.spk_payment_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $spk_id = $this->filterEmptyField($value, 'SpkPaymentDetail', 'spk_id');
                $price = $this->filterEmptyField($value, 'SpkPaymentDetail', 'price');

                $value = $this->Spk->getMerge($value, $spk_id);

                $grandtotal = $this->Spk->SpkProduct->_callGrandtotal($spk_id);

                $paid = $this->_callPaidSpk($spk_id, $id);
                $value['Spk']['total_remain'] = $grandtotal - $paid;
                $value['Spk']['total_paid'] = $paid;
                $value['Spk']['grandtotal'] = $grandtotal;
                $values[$key] = $value;
            }
            $data['SpkPaymentDetail'] = $values;
        }

        return $data;
    }

    function _callPaidSpk( $id = false, $payment_id = false, $status = 'paid' ){
        $this->virtualFields['total_paid'] = 'SUM(SpkPaymentDetail.price)';
        $result = $this->getData('first', array(
            'conditions'=> array(
                'SpkPaymentDetail.spk_id' => $id,
                'SpkPaymentDetail.spk_payment_id <>' => $payment_id,
            ),
        ), array(
            'status' => $status,
        ));
        $result = $this->Spk->getMerge($result, $id, 'Spk.id', 'unpaid');

        $total_paid = $this->filterEmptyField($result, 'SpkPaymentDetail', 'total_paid', 0);
        $total_spk = $this->filterEmptyField($result, 'Spk', 'grandtotal', 0);

        return $total_paid;
    }
}
?>