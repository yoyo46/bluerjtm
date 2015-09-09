<?php
class LeasingPaymentDetail extends AppModel {
	var $name = 'LeasingPaymentDetail';
	var $validate = array(
        'leasing_payment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pembayaran leasing tidak ditemukan'
            ),
        ),
        'leasing_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No kontrak harap dipilih'
            ),
        ),
        'expired_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl jatuh tempo harap dipilih'
            ),
        ),
        'total' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No kontrak harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
        'LeasingPayment' => array(
            'className' => 'LeasingPayment',
            'foreignKey' => 'leasing_payment_id'
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'fields' => array(),
            'contain' => array()
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>