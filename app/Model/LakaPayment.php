<?php
class LakaPayment extends AppModel {
	var $name = 'LakaPayment';
	var $validate = array(
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
        'LakaPaymentDetail' => array(
            'className' => 'LakaPaymentDetail',
            'foreignKey' => 'laka_payment_id',
            'conditions' => array(
                'LakaPaymentDetail.status' => 1,
            ),
        ),
    );

    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        )
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'LakaPayment.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['LakaPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['LakaPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['LakaPayment.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
                $default_options['conditions']['LakaPayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) ){
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        
        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;

        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(LakaPayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(LakaPayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['LakaPayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(LakaPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['LakaPayment.description LIKE'] = '%'.$note.'%';
        }

        return $default_options;
    }
}
?>