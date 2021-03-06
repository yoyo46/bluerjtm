<?php
class Leasing extends AppModel {
	var $name = 'Leasing';
	var $validate = array(
        'installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cicilan perbulan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Cicilan perbulan harap diisi dengan angka',
            ),
        ),
        'no_contract' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Kontral harap diisi'
            ),
        ),
        'vendor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Supplier leasing harap dipilih'
            ),
        ),
        'paid_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'fine' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Denda harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Denda harap diisi dengan angka',
            ),
        ),
        'date_first_installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal angsuran pertama harap diisi'
            ),
        ),
        'date_last_installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal angsuran terakhir harap diisi'
            ),
        ),
        'total_leasing' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Total leasing harap diisi'
            ),
        ),
        'leasing_month' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bulan angsuran harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Bulan angsuran harus berupa angka'
            ),
        ),
        'down_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'DP harap diisi'
            ),
        ),
        'installment_rate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bunga harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        ),
    );

    var $hasMany = array(
        'LeasingDetail' => array(
            'className' => 'LeasingDetail',
            'foreignKey' => 'leasing_id',
        ),
        'LeasingPayment' => array(
            'className' => 'LeasingPayment',
            'foreignKey' => 'leasing_id',
        ),
        'LeasingPaymentDetail' => array(
            'className' => 'LeasingPaymentDetail',
            'foreignKey' => 'leasing_id',
        ),
        'LeasingInstallment' => array(
            'className' => 'LeasingInstallment',
            'foreignKey' => 'leasing_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Leasing.status' => 'DESC'
            ),
            'contain' => array(
                'Vendor'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Leasing.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Leasing.status'] = 0;
                break;

            case 'unpaid':
                $default_options['conditions']['Leasing.payment_status'] = array( 'unpaid', 'half_paid' );
                break;

            case 'dp_unpaid':
                $default_options['conditions']['Leasing.dp_payment_status'] = array( 'unpaid', 'half_paid' );
                break;
            
            default:
                $default_options['conditions']['Leasing.status'] = 1;
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

    function getMerge( $data, $id ){
        if( empty($data['Leasing']) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'Leasing.id' => $id,
                ),
                'contain' => false,
            ));

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false, $modelName = 'Leasing', $leasing_installment_id = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;
        $payment_status = !empty($data['named']['payment_status'])?$data['named']['payment_status']:false;
        $dp_payment_status = !empty($data['named']['dp_payment_status'])?$data['named']['dp_payment_status']:false;

        $MonthFrom = !empty($data['named']['MonthFrom'])?$data['named']['MonthFrom']:false;
        $MonthTo = !empty($data['named']['MonthTo'])?$data['named']['MonthTo']:false;

        if( !empty($dateFrom) || !empty($dateTo) || !empty($MonthFrom) || !empty($MonthTo) ) {
            switch ($modelName) {
                case 'LeasingInstallment':
                    $fieldName = 'MIN(LeasingInstallment.paid_date)';
                    $default_options['contain'][] = 'LeasingInstallment';
                    $conditionsInstallment = array(
                        'LeasingInstallment.status' => 1,
                    );

                    if( !empty($leasing_installment_id) ) {
                        $conditionsInstallment['LeasingInstallment.id'] = $leasing_installment_id;
                    } else {
                        $conditionsInstallment['LeasingInstallment.payment_status'] = array(
                            'unpaid',
                            'half_paid',
                        );
                    }

                    $this->unBindModel(array(
                        'hasMany' => array(
                            'LeasingInstallment'
                        )
                    ));

                    $this->bindModel(array(
                        'hasOne' => array(
                            'LeasingInstallment' => array(
                                'className' => 'LeasingInstallment',
                                'foreignKey' => 'leasing_id',
                                'conditions' => $conditionsInstallment,
                                'order' => array(
                                    'LeasingInstallment.paid_date' => 'ASC',
                                    'LeasingInstallment.id' => 'ASC',
                                ),
                            ),
                        )
                    ), false);

                    if( !empty($default_options['group']) ) {
                        unset($default_options['group']);
                    }

                    if( !empty($MonthFrom) || !empty($MonthTo) ) {
                        $default_options['group'][] = 'LeasingInstallment.leasing_id HAVING MIN(DATE_FORMAT(LeasingInstallment.paid_date, \'%Y-%m\')) >= \''.$MonthFrom.'\' AND MIN(DATE_FORMAT(LeasingInstallment.paid_date, \'%Y-%m\')) <= \''.$MonthTo.'\'';
                    } else {
                        $default_options['group'][] = 'LeasingInstallment.leasing_id HAVING MIN(LeasingInstallment.paid_date) >= \''.$dateFrom.'\' AND MIN(LeasingInstallment.paid_date) <= \''.$dateTo.'\'';
                    }
                    break;
                
                default:
                    if( !empty($dateFrom) ) {
                        $default_options['conditions']['DATE_FORMAT(Leasing.paid_date, \'%Y-%m-%d\') >='] = $dateFrom;
                    } else if( !empty($MonthFrom) ) {
                        $default_options['conditions']['DATE_FORMAT(Leasing.paid_date, \'%Y-%m\') >='] = $MonthFrom;
                    }

                    if( !empty($dateTo) ) {
                        $default_options['conditions']['DATE_FORMAT(Leasing.paid_date, \'%Y-%m-%d\') <='] = $dateTo;
                    } else if( !empty($MonthTo) ) {
                        $default_options['conditions']['DATE_FORMAT(Leasing.paid_date, \'%Y-%m\') <='] = $MonthTo;
                    }
                    break;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['Leasing.no_contract LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['Leasing.vendor_id'] = $vendor_id;
        }
        if( !empty($status) ) {
            switch ($status) {
                case 'unpaid':
                    $default_options['conditions']['Leasing.payment_status'] = array( 'unpaid', 'half_paid' );
                    break;
                
                case 'paid':
                    $default_options['conditions']['Leasing.payment_status'] = 'paid';
                    break;
            }
        }
        if( !empty($payment_status) ) {
            $payment_status = explode(',', $payment_status);
            $default_options['conditions']['Leasing.payment_status'] = $payment_status;
        }
        if( !empty($dp_payment_status) ) {
            $dp_payment_status = explode(',', $dp_payment_status);
            $default_options['conditions']['Leasing.dp_payment_status'] = $dp_payment_status;
        }
        
        return $default_options;
    }

    function _callLastPaidDP( $data, $id, $leasing_payment_id = false ) {
        $conditions = array(
            'LeasingPaymentDetail.leasing_id' => $id,
            'LeasingPayment.type' => 'dp',
            'LeasingPayment.status' => 1,
            'LeasingPayment.rejected' => 0,
        );

        if( !empty($leasing_payment_id) ) {
            $conditions['LeasingPayment.id <>'] = $leasing_payment_id;
        }

        $this->LeasingPaymentDetail->virtualFields['paid_dp'] = 'SUM(LeasingPaymentDetail.installment)';
        $paid = $this->LeasingPaymentDetail->getData('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'LeasingPayment',
            ),
        ));

        if( !empty($paid) ) {
            $total_paid = Common::hashEmptyField($paid, 'LeasingPaymentDetail.paid_dp');
            $dp = Common::hashEmptyField($data, 'Leasing.down_payment');

            $data['Leasing']['down_payment'] = $dp - $total_paid;
            $data['Leasing']['paid_dp'] = $total_paid;
        }

        return $data;
    }

    function _callLastDpPayment ( $data, $leasing_id, $leasing_payment_id = false ) {
        $data = $this->_callLastPaidDP($data, $leasing_id, $leasing_payment_id);

        return $data;
    }
}
?>