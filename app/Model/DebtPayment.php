<?php
class DebtPayment extends AppModel {
	var $name = 'DebtPayment';
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
        'DebtPaymentDetail' => array(
            'foreignKey' => 'debt_payment_id',
            'conditions' => array(
                'DebtPaymentDetail.status' => 1,
            ),
        ),
    );

    var $belongsTo = array(
        'Coa' => array(
            'foreignKey' => 'coa_id',
        )
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'DebtPayment.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['DebtPayment.status'] = 1;
                break;

            case 'non-active':
                $default_options['conditions']['DebtPayment.status'] = 0;
                break;
        }

        if( !empty($branch) ) {
                $default_options['conditions']['DebtPayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        return $this->full_merge_options($default_options, $options, $find);
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
                $default_options['conditions']['DATE_FORMAT(DebtPayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(DebtPayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['DebtPayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(DebtPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['DebtPayment.description LIKE'] = '%'.$note.'%';
        }

        return $default_options;
    }
}
?>