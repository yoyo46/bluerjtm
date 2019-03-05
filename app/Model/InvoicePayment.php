<?php
class InvoicePayment extends AppModel {
	var $name = 'InvoicePayment';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Dokumen harap diisi'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Invoice harap dipilih'
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
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'CashBank' => array(
            'className' => 'CashBank',
            'foreignKey' => 'document_id',
        ),
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        ),
	);

    var $hasMany = array(
        'InvoicePaymentDetail' => array(
            'className' => 'InvoicePaymentDetail',
            'foreignKey' => 'invoice_payment_id',
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'InvoicePayment.status' => 'DESC',
                'InvoicePayment.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['InvoicePayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['InvoicePayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['InvoicePayment.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['InvoicePayment.branch_id'] = Configure::read('__Site.config_branch_id');
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

    function getMerge($data, $id){
        if(empty($data['InvoicePayment'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'InvoicePayment.id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(InvoicePayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['InvoicePayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(InvoicePayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if( !empty($transaction_status) ) {
            switch ($transaction_status) {
                case 'draft':
                    $default_options['conditions']['InvoicePayment.transaction_status'] = 'unposting';
                    $default_options['conditions']['InvoicePayment.is_canceled'] = 0;
                    break;
                case 'commit':
                    $default_options['conditions']['InvoicePayment.transaction_status'] = 'posting';
                    $default_options['conditions']['InvoicePayment.is_canceled'] = 0;
                    break;
                case 'void':
                    $default_options['conditions']['InvoicePayment.is_canceled'] = 1;
                    break;
            }
        }
        
        return $default_options;
    }
}
?>