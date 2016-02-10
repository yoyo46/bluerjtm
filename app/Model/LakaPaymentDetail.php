<?php
class LakaPaymentDetail extends AppModel {
	var $name = 'LakaPaymentDetail';
	var $validate = array(
        'laka_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Dokumen LAKA harap dipilih'
            ),
        ),
        'laka_payment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pembayaran tidak ditemukan'
            ),
        ),
        'amount' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Total sisa pembayaran harap diisi'
            ),
            'amountValidate' => array(
                'rule' => array('amountValidate'),
                'message' => 'Total sisa pembayaran harus diisi dan berupa angka',
            ),
        ),
	);

	var $belongsTo = array(
        'LakaPayment' => array(
            'className' => 'LakaPayment',
            'foreignKey' => 'laka_payment_id',
        ),
        'Laka' => array(
            'className' => 'Laka',
            'foreignKey' => 'laka_id',
        ),
	);

    function getTotalPayment( $laka_id, $payment_id = false ){
        $this->virtualFields['amount'] = 'SUM(LakaPaymentDetail.amount)';
        $result = $this->find('first', array(
            'conditions'=> array(
                'LakaPaymentDetail.laka_id' => $laka_id,
                'LakaPaymentDetail.status' => 1,
                'LakaPayment.is_canceled' => 0,
                'LakaPayment.status' => 1,
                'LakaPaymentDetail.laka_payment_id <>' => $payment_id,
            ),
            'contain' => array(
                'LakaPayment'
            ),
        ));

        return !empty($result['LakaPaymentDetail']['amount'])?$result['LakaPaymentDetail']['amount']:0;
    }

    function amountValidate () {
        if( empty($this->data['LakaPaymentDetail']['amount']) ) {
            return false;
        } else if( !is_numeric($this->data['LakaPaymentDetail']['amount']) ) {
            return false;
        } else {
            return true;
        }
    }

    function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        if( empty($data['LakaPaymentDetail']) ) {
            $values = $this->find('all', array(
                'conditions' => array(
                    'LakaPaymentDetail.laka_payment_id' => $id,
                    'LakaPaymentDetail.status' => 1,
                ),
            ));

            if( !empty($values) ) {
                $data['LakaPaymentDetail'] = $values;
            }
        }

        return $data;
    }
}
?>