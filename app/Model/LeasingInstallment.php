<?php
class LeasingInstallment extends AppModel {
	var $name = 'LeasingInstallment';
	var $validate = array(
        'leasing_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Leasing tidak ditemukan'
            ),
        ),
        'paid_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal pembayaran harap diisi'
            ),
        ),
        'installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cicilan perbulan harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
    );

    var $hasMany = array(
        'LeasingPaymentDetail' => array(
            'className' => 'LeasingPaymentDetail',
            'foreignKey' => 'leasing_installment_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';
        
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'LeasingInstallment.paid_date' => 'ASC',
                'LeasingInstallment.id' => 'ASC'
            ),
            'contain' => array(
                'Leasing'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'unpaid':
                $default_options['conditions']['LeasingInstallment.payment_status'] = array( 'unpaid', 'half_paid' );
                $default_options['conditions']['LeasingInstallment.status'] = 1;
                break;
            case 'paid':
                $default_options['conditions']['LeasingInstallment.payment_status'] = 'paid';
                $default_options['conditions']['LeasingInstallment.status'] = 1;
                break;
            
            default:
                $default_options['conditions']['LeasingInstallment.status'] = 1;
                break;
        }

        // Custom Otorisasi
        if( !empty($branch) ) {
            $default_options['conditions']['Leasing.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options)){
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function doSave( $leasing_id, $data ) {
        $leasing_month = !empty($data['Leasing']['leasing_month'])?$data['Leasing']['leasing_month']:false;
        $date_first_installment = !empty($data['Leasing']['date_first_installment'])?$data['Leasing']['date_first_installment']:false;
        $installment = !empty($data['Leasing']['installment'])?$data['Leasing']['installment']:0;

        $this->deleteAll(array( 
            'LeasingInstallment.leasing_id' => $leasing_id,
        ));

        if ( !empty($leasing_month) ) {
            for ($i=0; $i < $leasing_month; $i++) { 
                $paid_date = date ("Y-m-d", strtotime("+$i month", strtotime($date_first_installment)));
                $dataSave = array(
                    'LeasingInstallment' => array(
                        'leasing_id' => $leasing_id,
                        'paid_date' => $paid_date,
                        'installment' => $installment,
                    ),
                );
                $this->create();
                $this->set($dataSave);
                $this->save();
            }
        }
    }

    function _callLastPaidInstallment( $value, $id, $leasing_payment_id = false ) {
        $installment = !empty($value['LeasingInstallment']['installment'])?$value['LeasingInstallment']['installment']:0;
        $installment_rate = !empty($value['Leasing']['installment_rate'])?$value['Leasing']['installment_rate']:0;
        $conditions = array(
            'LeasingPaymentDetail.leasing_installment_id' => $id,
            'LeasingPayment.status' => 1,
            'LeasingPayment.rejected' => 0,
        );

        if( !empty($leasing_payment_id) ) {
            $conditions['LeasingPayment.id <>'] = $leasing_payment_id;
        }

        $hasPaid = $this->LeasingPaymentDetail->getData('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'LeasingPayment',
            ),
            'fields' => array(
                'SUM(LeasingPaymentDetail.installment) AS installment',
                'SUM(LeasingPaymentDetail.installment_rate) AS installment_rate',
            ),
        ));

        if( !empty($hasPaid) ) {
            $installmentPaid = !empty($hasPaid[0]['installment'])?$hasPaid[0]['installment']:0;
            $installmentRatePaid = !empty($hasPaid[0]['installment_rate'])?$hasPaid[0]['installment_rate']:0;
            $value['LeasingInstallment']['installment'] = $installment - $installmentPaid;
            $value['Leasing']['installment_rate'] = $installment_rate - $installmentRatePaid;
        }

        return $value;
    }

    function _callLastPayment ($data, $leasing_id, $leasing_payment_id = false, $leasing_installment_id = false) {
        $conditions = array(
            'LeasingInstallment.leasing_id' => $leasing_id,
        );

        if( !empty($leasing_installment_id) ) {
            $conditions['LeasingInstallment.id'] = $leasing_installment_id;
        } else {
            $conditions['LeasingInstallment.payment_status'] = array(
                'unpaid',
                'half_paid',
            );
        }

        $value = $this->getData('first', array(
            'conditions' => $conditions,
        ));

        if( !empty($value) ) {
            $id = !empty($value['LeasingInstallment']['id'])?$value['LeasingInstallment']['id']:false;

            $value = $this->_callLastPaidInstallment($value, $id, $leasing_payment_id);
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function getMerge ($data, $id, $leasing_payment_id = false) {
        $value = $this->getData('first', array(
            'conditions' => array(
                'LeasingInstallment.id' => $id,
            ),
        ));

        if( !empty($value) ) {
            $id = !empty($value['LeasingInstallment']['id'])?$value['LeasingInstallment']['id']:false;

            $value = $this->_callLastPaidInstallment($value, $id, $leasing_payment_id);
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function getCountInstallment ( $data, $id ) {
        $default_options = array(
            'conditions' => array(
                'LeasingInstallment.leasing_id' => $id,
            ),
            'contain' => false,
        );

        $this->virtualFields['count_installment'] = 'COUNT(LeasingInstallment.id)';
        $value = $this->getData('first', $default_options, array(
            'branch' => false,
            'status' => 'paid',
        ));

        if( !empty($value) ) {
            $data = array_merge($data, $value);
        }

        return $data;
    }
}
?>