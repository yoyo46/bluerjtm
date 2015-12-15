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
                'message' => 'Vendor leasing harap dipilih'
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Leasing.paid_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Leasing.paid_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['Leasing.no_contract LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['Leasing.vendor_id'] = $vendor_id;
        }
        
        return $default_options;
    }
}
?>