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
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
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

    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        )
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
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

        if( !empty($branch) ) {
                $default_options['conditions']['TtujPayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

        $dateFromTtuj = !empty($data['named']['DateFromTtuj'])?$data['named']['DateFromTtuj']:false;
        $dateToTtuj = !empty($data['named']['DateToTtuj'])?$data['named']['DateToTtuj']:false;

        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $nottuj = !empty($data['named']['nottuj'])?$data['named']['nottuj']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;

        $uj1 = !empty($data['named']['uj1'])?$data['named']['uj1']:false;
        $uj2 = !empty($data['named']['uj2'])?$data['named']['uj2']:false;
        $uje = !empty($data['named']['uje'])?$data['named']['uje']:false;
        $com = !empty($data['named']['com'])?$data['named']['com']:false;
        $come = !empty($data['named']['come'])?$data['named']['come']:false;
        $kuli_muat = !empty($data['named']['kuli_muat'])?$data['named']['kuli_muat']:false;
        $kuli_bongkar = !empty($data['named']['kuli_bongkar'])?$data['named']['kuli_bongkar']:false;
        $asdp = !empty($data['named']['asdp'])?$data['named']['asdp']:false;
        $uang_kawal = !empty($data['named']['uang_kawal'])?$data['named']['uang_kawal']:false;
        $uang_keamanan = !empty($data['named']['uang_keamanan'])?$data['named']['uang_keamanan']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;

        $fromcity = !empty($data['named']['fromcity'])?$data['named']['fromcity']:false;
        $tocity = !empty($data['named']['tocity'])?$data['named']['tocity']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        $driver_type = !empty($data['named']['driver_type'])?$data['named']['driver_type']:1;
        $driver_value = !empty($data['named']['driver_value'])?$data['named']['driver_value']:false;
        $paid_type = !empty($data['named']['paid_type'])?$data['named']['paid_type']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(TtujPayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(TtujPayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($dateFromTtuj) || !empty($dateToTtuj) ) {
            if( !empty($dateFromTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') >='] = $dateFromTtuj;
            }

            if( !empty($dateToTtuj) ) {
                $default_options['conditions']['DATE_FORMAT(Ttuj.ttuj_date, \'%Y-%m-%d\') <='] = $dateToTtuj;
            }
                
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($nodoc)){
            $default_options['conditions']['TtujPayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($nottuj)){
            $default_options['conditions']['Ttuj.no_ttuj LIKE'] = '%'.$nottuj.'%';
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($name)){
            $default_options['conditions']['TtujPayment.receiver_name LIKE'] = '%'.$name.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(TtujPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['Ttuj.note LIKE'] = '%'.$note.'%';
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($nopol)){
            $default_options['conditions']['Ttuj.nopol LIKE'] = '%'.$nopol.'%';
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($nopol)){
            $default_options['conditions']['Ttuj.nopol LIKE'] = '%'.$nopol.'%';
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($nopol)){
            $default_options['conditions']['Ttuj.nopol LIKE'] = '%'.$nopol.'%';
            $default_options['contain'][] = 'Ttuj';
        }

        if(!empty($uj1)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $uj1;
        }
        if(!empty($uj2)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $uj2;
        }
        if(!empty($uje)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $uje;
        }
        if(!empty($com)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $com;
        }
        if(!empty($come)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $come;
        }
        if(!empty($kuli_muat)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $kuli_muat;
        }
        if(!empty($kuli_bongkar)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $kuli_bongkar;
        }
        if(!empty($asdp)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $asdp;
        }
        if(!empty($uang_kawal)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $uang_kawal;
        }
        if(!empty($uang_keamanan)){
            $default_options['conditions']['TtujPaymentDetail.type'][] = $uang_keamanan;
        }

        if(!empty($fromcity)){
            $default_options['conditions']['Ttuj.from_city_id'] = $fromcity;
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($tocity)){
            $default_options['conditions']['Ttuj.to_city_id'] = $tocity;
            $default_options['contain'][] = 'Ttuj';
        }
        if(!empty($status)){
            switch ($status) {
                case 'paid':
                        $default_options['conditions']['OR'] = array(
                            array(
                                'TtujPaymentDetail.type' => 'uang_jalan',
                                'Ttuj.paid_uang_jalan' => 'full',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'uang_jalan_2',
                                'Ttuj.paid_uang_jalan_2' => 'full',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'uang_jalan_extra',
                                'Ttuj.paid_uang_jalan_extra' => 'full',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'commission',
                                'Ttuj.paid_commission' => 'full',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'commission_extra',
                                'Ttuj.paid_commission_extra' => 'full',
                            ),
                        );
                    break;
                case 'unpaid':
                        $default_options['conditions']['OR'] = array(
                            array(
                                'TtujPaymentDetail.type' => 'uang_jalan',
                                'Ttuj.paid_uang_jalan' => 'none',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'uang_jalan_2',
                                'Ttuj.paid_uang_jalan_2' => 'none',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'uang_jalan_extra',
                                'Ttuj.paid_uang_jalan_extra' => 'none',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'commission',
                                'Ttuj.paid_commission' => 'none',
                            ),
                            array(
                                'TtujPaymentDetail.type' => 'commission_extra',
                                'Ttuj.paid_commission_extra' => 'none',
                            ),
                        );
                    break;
            }
            
            $default_options['contain'][] = 'Ttuj';
        }
        if( !empty($transaction_status) ) {
            switch ($transaction_status) {
                case 'draft':
                    $default_options['conditions']['TtujPayment.transaction_status'] = 'unposting';
                    $default_options['conditions']['TtujPayment.is_canceled'] = 0;
                    break;
                case 'commit':
                    $default_options['conditions']['TtujPayment.transaction_status'] = 'posting';
                    $default_options['conditions']['TtujPayment.is_canceled'] = 0;
                    break;
                case 'void':
                    $default_options['conditions']['TtujPayment.is_canceled'] = 1;
                    break;
            }
        }
        if(!empty($driver_value)){
            if( $driver_type == 2 ) {
                $default_options['conditions']['IFNULL(Ttuj.driver_pengganti_id, Ttuj.driver_id)'] = $driver_value;
            } else {
                $default_options['conditions']['Ttuj.driver_name LIKE'] = '%'.$driver_value.'%';
            }

            $default_options['contain'][] = 'Ttuj';
        }
        if( !empty($paid_type) ) {
            switch ($paid_type) {
                case 'laka':
                    $default_options['conditions']['TtujPaymentDetail.laka <>'] = 0;
                    $default_options['conditions']['TtujPaymentDetail.laka NOT'] = NULL;
                    break;
            }
        }

        return $default_options;
    }

    function _callTtujPaid ( $data, $ttuj_id, $type = false, $options = array() ) {
        $default_options = array(
            'conditions' => array(
                'TtujPayment.is_canceled' => 0,
                'TtujPaymentDetail.status' => 1,
                'TtujPaymentDetail.ttuj_id' => $ttuj_id,
                'TtujPayment.transaction_status' => 'posting',
            ),
            'contain' => array(
                'TtujPayment',
            ),
            'group' => array(
                'TtujPaymentDetail.ttuj_id',
                'TtujPaymentDetail.type',
            ),
            'order' => false,
        );

        if( !empty($type) ) {
            $default_options['conditions']['TtujPaymentDetail.type'] = $type;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }

        $this->virtualFields['grandtotal'] = 'SUM(amount)';
        $options =  $this->getData('paginate', $default_options, true, array(
            'branch' => false,
        ));

        $value =  $this->TtujPaymentDetail->find('first', $options);
        $paid = !empty($value['TtujPayment']['grandtotal'])?$value['TtujPayment']['grandtotal']:0;
        $data['TtujPayment']['paid'] = $paid;

        return $data;
    }
}
?>