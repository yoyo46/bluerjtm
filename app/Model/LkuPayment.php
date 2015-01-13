<?php
class LkuPayment extends AppModel {
	var $name = 'LkuPayment';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No Dokumen telah terdaftar',
            ),
        ),
        'tgl_bayar' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'grandtotal' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
	);

    var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
    );

    var $hasMany = array(
        'LkuPaymentDetail' => array(
            'className' => 'LkuPaymentDetail',
            'foreignKey' => 'lku_payment_id',
        )
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'LkuPayment.status' => 1,
            ),
            'order'=> array(
                'LkuPayment.created' => 'DESC',
                'LkuPayment.id' => 'DESC',
            ),
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