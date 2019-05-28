<?php
class DebtPaymentDetail extends AppModel {
	var $validate = array(
        'debt_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Hutang Karyawan harap dipilih'
            ),
        ),
        'debt_payment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pembayaran tidak ditemukan'
            ),
        ),
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Karyawan tidak ditemukan'
            ),
        ),
        'amount' => array(
            'amountValidate' => array(
                'rule' => array('amountValidate'),
                'message' => 'Total bayar harus diisi dan berupa angka',
            ),
            'outOfTotal' => array(
                'rule' => array('outOfTotal'),
                'message' => 'Total bayar tidak bole lebih besar dari hutang karyawan',
            ),
        ),
	);

	var $belongsTo = array(
        'DebtPayment' => array(
            'foreignKey' => 'debt_payment_id',
        ),
        'Debt' => array(
            'foreignKey' => 'debt_id',
        ),
        'DebtDetail' => array(
            'foreignKey' => 'debt_detail_id',
        ),
        'TtujPaymentDetail' => array(
            'foreignKey' => 'ttuj_payment_detail_id',
        ),
	);

    function getTotalPayment( $document_id = NULL, $payment_id = NULL, $employe_id = NULL ){
        $this->virtualFields['amount'] = 'SUM(DebtPaymentDetail.amount)';
        $options = array(
            'conditions'=> array(
                'DebtPaymentDetail.status' => 1,
                'DebtPayment.is_canceled' => 0,
                'DebtPayment.status' => 1,
            ),
            'contain' => array(
                'DebtPayment'
            ),
        );

        if( !empty($document_id) ) {
            $options['conditions']['DebtPaymentDetail.debt_detail_id'] = $document_id;
        }
        if( !empty($payment_id) ) {
            $options['conditions']['DebtPaymentDetail.debt_payment_id <>'] = $payment_id;
        }
        if( !empty($employe_id) ) {
            $options['conditions']['DebtPaymentDetail.employe_id'] = $employe_id;
        }

        $options = $this->DebtPayment->getData('paginate', $options);
        $result = $this->find('first', $options);

        return !empty($result['DebtPaymentDetail']['amount'])?$result['DebtPaymentDetail']['amount']:0;
    }

    function amountValidate () {
        if( empty($this->data['DebtPaymentDetail']['amount']) ) {
            return false;
        } else if( !is_numeric($this->data['DebtPaymentDetail']['amount']) ) {
            return false;
        } else {
            return true;
        }
    }

    function outOfTotal () {
        if( isset($this->data['DebtPaymentDetail']['out_of_total']) ) {
            if( empty($this->data['DebtPaymentDetail']['out_of_total']) ) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        return $this->full_merge_options($default_options, $options, $find);
    }

    function getMerge( $data, $id ){
        if( empty($data['DebtPaymentDetail']) ) {
            $values = $this->find('all', array(
                'conditions' => array(
                    'DebtPaymentDetail.debt_payment_id' => $id,
                    'DebtPaymentDetail.status' => 1,
                ),
            ));

            if( !empty($values) ) {
                $data['DebtPaymentDetail'] = $values;
            }
        }

        return $data;
    }
}
?>