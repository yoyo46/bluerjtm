<?php
class TtujPayment extends AppModel {
	var $name = 'TtujPayment';
	var $validate = array(
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
        'receiver_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Penerima (dibayar kepada) harap dipilih'
            ),
        ),
        'receiver_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Penerima (dibayar kepada) harap dipilih'
            ),
        ),
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Dokumen harap diisi'
            ),
        ),
        'total_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Total pembayaran harap diisi'
            ),
        ),
        'date_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal pembayaran harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'receiver_id',
            'conditions' => array(
                'TtujPayment.receiver_type' => 'Driver',
            )
        ),
        'CustomerNoType' => array(
            'className' => 'CustomerNoType',
            'foreignKey' => 'receiver_id',
            'conditions' => array(
                'TtujPayment.receiver_type' => 'Customer',
            )
        ),
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'receiver_id',
            'conditions' => array(
                'TtujPayment.receiver_type' => 'Vendor',
            )
        ),
        'Employe' => array(
            'className' => 'Employe',
            'foreignKey' => 'receiver_id',
            'conditions' => array(
                'TtujPayment.receiver_type' => 'Employe',
            )
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'TtujPayment.status' => 1,
            ),
            'order'=> array(
                'TtujPayment.id' => 'DESC'
            ),
            'contain' => array(
                'Ttuj',
                'Driver',
                'Vendor',
                'CustomerNoType',
                'Employe'
            ),
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
}
?>