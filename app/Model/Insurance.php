<?php
class Insurance extends AppModel {
	var $name = 'Insurance';
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
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. polis harap diisi'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama asuransi harap diisi'
            ),
        ),
        'date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl asuransi harap diisi'
            ),
        ),
        'to_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama tertanggung harap diisi'
            ),
        ),
        'to_address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat tertanggung harap diisi'
            ),
        ),
        'start_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl mulai asuransi harap dipilih'
            ),
        ),
        'end_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl berakhir asuransi harap dipilih'
            ),
        ),
        'item' => array(
            'checkDetail' => array(
                'rule' => array('checkDetail'),
                'message' => 'Mohon pilih truk terlebih dahulu'
            ),
        ),
	);

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );

    var $hasMany = array(
        'InsuranceDetail' => array(
            'className' => 'InsuranceDetail',
            'foreignKey' => 'insurance_id',
        ),
        'InsurancePayment' => array(
            'className' => 'InsurancePayment',
            'foreignKey' => 'insurance_id',
        ),
        'InsurancePaymentDetail' => array(
            'className' => 'InsurancePaymentDetail',
            'foreignKey' => 'insurance_id',
        ),
    );

    function checkDetail () {
        $data = $this->data;
        $details = Common::hashEmptyField($data, 'Insurance.item');

        if( !empty($details) ) {
            return true;
        } else {
            return false;
        }
    }
    
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['status'] = 'CASE WHEN Insurance.end_date < DATE_FORMAT(NOW(), \'%Y-%m-%d\') THEN -1 ELSE Insurance.status END';
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;
        
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Insurance.status' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'non-active':
                $default_options['conditions']['Insurance.status'] = 0;
                break;

            case 'active':
                $default_options['conditions']['Insurance.status'] = 1;
                break;

            case 'unpaid':
                $default_options['conditions']['Insurance.transaction_status'] = array( 'unpaid', 'half_paid' );
                break;

            case 'publish':
                $default_options['conditions']['Insurance.status'] = 1;
                $defaultOptions['conditions'][] = array(
                    'Insurance.start_date'.' <=' => date('Y-m-d'),
                    'Insurance.end_date'.' >=' => date('Y-m-d'),
                );
                break;
        }

        // Custom Otorisasi
        if( !empty($branch) ) {
            $default_options['conditions']['Insurance.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false, $modelName = 'Insurance' ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $to_name = !empty($data['named']['to_name'])?$data['named']['to_name']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Insurance.start_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Insurance.end_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['Insurance.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($name) ) {
            $default_options['conditions']['Insurance.name LIKE'] = '%'.$name.'%';
        }
        if( !empty($to_name) ) {
            $default_options['conditions']['Insurance.to_name LIKE'] = '%'.$to_name.'%';
        }
        if( !empty($status) ) {
            $nowDate = date('Y-m-d');

            switch ($status) {
                case 'unpaid':
                    $default_options['conditions']['Insurance.transaction_status'] = array( 'unpaid', 'half_paid' );
                    break;
                
                case 'paid':
                    $default_options['conditions']['Insurance.transaction_status'] = 'paid';
                    break;
                
                case 'inactive':
                    $default_options['conditions']['Insurance.status'] = 0;
                    break;
                
                case 'active':
                    $default_options['conditions']['DATE_FORMAT(Insurance.end_date, \'%Y-%m-%d\') >='] = date('Y-m-d');
                    break;
                
                case 'expired':
                    $default_options['conditions']['DATE_FORMAT(Insurance.end_date, \'%Y-%m-%d\') >='] = date('Y-m-d');
                    break;
            }
        }

        return $default_options;
    }

    function beforeSave( $options = array() ) {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Insurance.id', $id);

        if( empty($id) ) {
            $this->data = Hash::insert($this->data, 'Insurance.branch_id', Configure::read('__Site.config_branch_id'));
        }
    }

    function _callLastPaidInstallment( $value, $id, $insurance_payment_id = false ) {
        $grandtotal = Common::hashEmptyField($value, 'Insurance.grandtotal', 0);
        $conditions = array(
            'InsurancePaymentDetail.insurance_id' => $id,
            'InsurancePayment.status' => 1,
            'InsurancePayment.rejected' => 0,
        );

        if( !empty($insurance_payment_id) ) {
            $conditions['InsurancePayment.id <>'] = $insurance_payment_id;
        }

        $hasPaid = $this->InsurancePaymentDetail->getData('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'InsurancePayment',
            ),
            'fields' => array(
                'SUM(InsurancePaymentDetail.total) AS total',
            ),
        ));

        if( !empty($hasPaid) ) {
            $installmentPaid = !empty($hasPaid[0]['total'])?$hasPaid[0]['total']:0;
            $value['Insurance']['grandtotal'] = $grandtotal - $installmentPaid;
        }

        return $value;
    }
}
?>