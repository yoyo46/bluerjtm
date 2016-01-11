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
        $this->virtualFields['amount'] = 'SUM(DocumentPaymentDetail.amount)';
        $result = $this->find('first', array(
            'conditions'=> array(
                'DocumentPaymentDetail.document_id' => $document_id,
                'DocumentPaymentDetail.document_type' => $data_type,
                'DocumentPaymentDetail.status' => 1,
                'DocumentPayment.is_canceled' => 0,
                'DocumentPayment.status' => 1,
                'DocumentPaymentDetail.document_payment_id <>' => $payment_id,
            ),
            'contain' => array(
                'DocumentPayment'
            ),
        ));

        return !empty($result['DocumentPaymentDetail']['amount'])?$result['DocumentPaymentDetail']['amount']:0;
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