<?php
class TtujPayment extends AppModel {
	var $name = 'TtujPayment';
	var $validate = array(
        'receiver_name' => array(
            'validateReceiver' => array(
                'rule' => array('validateReceiver'),
                'message' => 'Penerima (dibayar kepada) harap dipilih'
            ),
        ),
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Dokumen harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No. Dokumen telah terdaftar',
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

    var $hasMany = array(
        'TtujPaymentDetail' => array(
            'className' => 'TtujPaymentDetail',
            'foreignKey' => 'ttuj_payment_id',
            'conditions' => array(
                'TtujPaymentDetail.status' => 1,
            ),
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'TtujPayment.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'TtujPayment.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['TtujPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['TtujPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['TtujPayment.status'] = 1;
                break;
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
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

    function validateReceiver () {
        if( !empty($this->data['TtujPayment']['type']) && $this->data['TtujPayment']['type'] == 'biaya_ttuj' ) {
            if( !empty($this->data['TtujPayment']['receiver_id']) && !empty($this->data['TtujPayment']['receiver_name']) && !empty($this->data['TtujPayment']['receiver_type']) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
?>