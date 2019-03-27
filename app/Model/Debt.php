<?php
class Debt extends AppModel {
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal Transaksi harap diisi'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Coa' => array(
			'foreignKey' => 'coa_id',
		),
        'User' => array(
            'foreignKey' => 'user_id',
        ),
        'Cogs' => array(
            'foreignKey' => 'cogs_id',
        ),
	);

    var $hasMany = array(
        'DebtDetail' => array(
            'foreignKey' => 'debt_id',
        ),
        'DebtPaymentDetail' => array(
            'foreignKey' => 'debt_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $transaction_status = isset($elements['transaction_status'])?$elements['transaction_status']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Debt.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['Debt.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($status) ) {
            switch ($status) {
                case 'active':
                    $default_options['conditions']['Debt.status'] = 1;
                    break;

                case 'non-active':
                    $default_options['conditions']['Debt.status'] = 0;
                    break;
            }
        }

        if( !empty($transaction_status) ) {
            $default_options['conditions']['Debt.transaction_status'] = $transaction_status;
        }

        return $this->full_merge_options($default_options, $options, $find);
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $total = !empty($data['named']['total'])?$data['named']['total']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;
        $coa = !empty($data['named']['coa'])?urldecode($data['named']['coa']):false;
        
        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Debt.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Debt.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Debt.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['Debt.note LIKE'] = '%'.$note.'%';
        }
        if(!empty($total)){
            $default_options['conditions']['Debt.total LIKE'] = '%'.$total.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(Debt.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if( !empty($transaction_status) ) {
            $default_options['conditions']['Debt.transaction_status'] = $transaction_status;
        }
        if( !empty($coa) ) {
            $default_options['conditions']['Debt.coa_id'] = $coa;
        }
        
        return $default_options;
    }
}
?>