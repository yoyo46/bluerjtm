<?php
class TtujPaymentDetail extends AppModel {
	var $name = 'TtujPaymentDetail';
	var $validate = array(
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
        'ttuj_payment_id' => array(
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
        'type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe pembayaran harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'TtujPayment' => array(
            'className' => 'TtujPayment',
            'foreignKey' => 'ttuj_payment_id',
        ),
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
	);

    function getTotalPayment( $ttuj_id, $data_type, $payment_id = false, $options = array() ){
        $default_options = array(
            'conditions'=> array(
                'TtujPaymentDetail.ttuj_id' => $ttuj_id,
                'TtujPaymentDetail.type' => $data_type,
                'TtujPaymentDetail.status' => 1,
                'TtujPayment.is_canceled' => 0,
                'TtujPayment.status' => 1,
                'TtujPaymentDetail.ttuj_payment_id <>' => $payment_id,
            ),
            'fields'=> array(
                'SUM(TtujPaymentDetail.amount) AS amount'
            ),
            'contain' => array(
                'TtujPayment'
            ),
        );

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }

        $result = $this->find('first', $default_options);
        
        return !empty($result[0]['amount'])?$result[0]['amount']:0;
    }

    function amountValidate () {
        if( empty($this->data['TtujPaymentDetail']['amount']) ) {
            return false;
        } else if( !is_numeric($this->data['TtujPaymentDetail']['amount']) ) {
            return false;
        } else {
            return true;
        }
    }
}
?>