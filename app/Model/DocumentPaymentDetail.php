<?php
class DocumentPaymentDetail extends AppModel {
	var $name = 'DocumentPaymentDetail';
	var $validate = array(
        'document_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Dokumen truk harap dipilih'
            ),
        ),
        'document_payment_id' => array(
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
        'document_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis surat harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'DocumentPayment' => array(
            'className' => 'DocumentPayment',
            'foreignKey' => 'document_payment_id',
        ),
        'Stnk' => array(
            'className' => 'Stnk',
            'foreignKey' => 'document_id',
        ),
        'Siup' => array(
            'className' => 'Siup',
            'foreignKey' => 'document_id',
        ),
        'Kir' => array(
            'className' => 'Kir',
            'foreignKey' => 'document_id',
        ),
	);

    function getTotalPayment( $document_id, $data_type, $payment_id = false ){
        $result = $this->find('first', array(
            'conditions'=> array(
                'DocumentPaymentDetail.document_id' => $document_id,
                'DocumentPaymentDetail.document_type' => $data_type,
                'DocumentPaymentDetail.status' => 1,
                'DocumentPayment.is_canceled' => 0,
                'DocumentPayment.status' => 1,
                'DocumentPaymentDetail.document_payment_id <>' => $payment_id,
            ),
            'fields'=> array(
                'SUM(DocumentPaymentDetail.amount) AS amount'
            ),
            'contain' => array(
                'DocumentPayment'
            ),
        ));

        return !empty($result[0]['amount'])?$result[0]['amount']:0;
    }

    function amountValidate () {
        if( empty($this->data['DocumentPaymentDetail']['amount']) ) {
            return false;
        } else if( !is_numeric($this->data['DocumentPaymentDetail']['amount']) ) {
            return false;
        } else {
            return true;
        }
    }

    function getMerge( $data, $id ){
        if( empty($data['DocumentPaymentDetail']) ) {
            $values = $this->find('all', array(
                'conditions' => array(
                    'DocumentPaymentDetail.document_payment_id' => $id,
                    'DocumentPaymentDetail.status' => 1,
                ),
            ));

            if( !empty($values) ) {
                $data['DocumentPaymentDetail'] = $values;
            }
        }

        return $data;
    }
}
?>