<?php
class CashBank extends AppModel {
	var $name = 'CashBank';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
            ),
            // 'isUnique' => array(
            //     'rule' => array('isUnique'),
            //     'message' => 'No Dokumen telah terdaftar',
            // ),
        ),
        'receiving_cash_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis Kas/Bank harap dipilih'
            ),
        ),
        'tgl_cash_bank' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal Transaksi harap diisi'
            ),
        ),
        'receiver' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'User penerima atau di bayar kepada harap dipilih'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
        'document_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Dokumen harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Coa' => array(
			'className' => 'Coa',
			'foreignKey' => 'coa_id',
		),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'receiver_id',
        ),
        'Employe' => array(
            'className' => 'Employe',
            'foreignKey' => 'receiver_id',
        ),
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'receiver_id',
        ),
        'Driver' => array(
            'className' => 'Driver',
            'foreignKey' => 'receiver_id',
        ),
	);

    var $hasMany = array(
        'CashBankDetail' => array(
            'className' => 'CashBankDetail',
            'foreignKey' => 'cash_bank_id',
        ),
        'DocumentAuth' => array(
            'className' => 'DocumentAuth',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'DocumentAuth.document_type' => 'cash_bank',
            ),
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['grand_total'] = 'debit_total+credit_total';
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'CashBank.created' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['CashBank.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        switch ($status) {
            case 'all':
                $default_options['conditions']['CashBank.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['CashBank.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['CashBank.status'] = 1;
                break;
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
        if(empty($data['CashBank'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getDocumentCashBank ( $prepayment_out_id = false, $document_type = 'prepayment_out' ) {
        $result = array(
            'docs' => array(),
            'docs_type' => false,
        );
        $options = array(
            'conditions' => array(
                'CashBank.is_rejected' => 0,
                'CashBank.completed',
                'CashBank.receiving_cash_type' => $document_type,
            ),
        );

        switch ($document_type) {
            case 'prepayment_in':
                $options['conditions']['document_id'] = $prepayment_out_id;
                break;
            
            default:
                if( !empty($prepayment_out_id) ) {
                    $options['conditions']['OR'] = array(
                        'CashBank.prepayment_status <>' => 'full_paid',
                        'CashBank.id' => $prepayment_out_id,
                    );
                } else {
                    $options['conditions']['CashBank.prepayment_status <>'] = 'full_paid';
                }
                break;
        }

        $docTmps = $this->getData('all', $options);

        if( $document_type == 'prepayment_in' ) {
            return $docTmps;
        } else {
            $docs = array();
            
            if( !empty($docTmps) ) {
                foreach ($docTmps as $key => $docTmp) {
                    $id = $docTmp['CashBank']['id'];
                    $docs[$id] = $docTmp['CashBank']['nodoc'];
                }
            }

            $result = array(
                'docs' => $docs,
                'docs_type' => 'prepayment',
            );

            return $result;
        }
    }

    function totalPrepaymentDibayar ( $prepayment_id ) {
        $conditions = array(
            'CashBank.document_id' => $prepayment_id,
            'CashBank.is_rejected' => 0,
            'CashBank.receiving_cash_type' => 'prepayment_in',
        );

        if( !empty($prepayment_id) ) {
            $conditions['OR'] = array(
                'CashBank.prepayment_status <>' => 'full_paid',
                'CashBank.id' => $prepayment_id,
            );
        } else {
            $conditions['CashBank.prepayment_status <>'] = 'full_paid';
        }

        $docPaid = $this->getData('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(CashBank.debit_total+CashBank.credit_total) AS total'
            ),
        ));

        return !empty($docPaid[0]['total'])?$docPaid[0]['total']:0;
    }

    function getStatusPrepayment ( $prepayment_id ) {
        $totalDibayar = $this->totalPrepaymentDibayar($prepayment_id);

        if( $totalDibayar > 0 ) {
            return 'half_paid';
        } else {
            return 'none';
        }
    }

    function getDataCashBank ( $data, $id ) {
        $dataCashbank = $this->getData('first', array(
            'conditions' => array(
                'CashBank.id' => $id,
            ),
        ));

        if( !empty($dataCashbank) ) {
            $data['PrepaymentOut'] = $dataCashbank['CashBank'];
        }

        return $data;
    }

    function getReceiver ( $receiver_type, $receiver = false, $data_type = 'default' ) {
        $model = $receiver_type;
        // Load Model
        $this->{$model} = ClassRegistry::init($model);
        // Default conditions
        $conditions = array(
            $model.'.status' => 1
        );

        switch ($data_type) {
            case 'search':
                // If call by id
                if( !empty($receiver) ) {
                    $conditions[$model.'.name LIKE'] = '%'.$receiver.'%';
                }

                $result = $this->{$model}->getData('list', array(
                    'conditions' => $conditions,
                    'fields' => array(
                        $model.'.id', $model.'.id',
                    ),
                ), true, array(
                    'branch' => false,
                ));

                return $result;
                break;
        }
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $total = !empty($data['named']['total'])?$data['named']['total']:false;
        $description = !empty($data['named']['description'])?$data['named']['description']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;
        $coa = !empty($data['named']['coa'])?urldecode($data['named']['coa']):false;
        
        $documenttype = !empty($data['named']['documenttype'])?urldecode($data['named']['documenttype']):false;
        $documenttype = !empty($data['CashBank']['documenttype'])?urldecode($data['CashBank']['documenttype']):$documenttype;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['CashBank.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($type)){
            $default_options['conditions']['CashBank.receiving_cash_type LIKE'] = '%'.$type.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['CashBank.description LIKE'] = '%'.$note.'%';
        }
        if(!empty($name)){
            $vendors = $this->Vendor->getData('list', array(
                'conditions' => array(
                    'Vendor.name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Vendor.id', 'Vendor.id',
                ),
                'limit' => 100,
            ), array(
                'branch' => false,
            ));
            $employes = $this->Employe->getData('list', array(
                'conditions' => array(
                    'Employe.name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Employe.id', 'Employe.id',
                ),
                'limit' => 100,
            ));
            $customers = $this->Customer->getData('list', array(
                'conditions' => array(
                    'Customer.customer_name_code LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Customer.id', 'Customer.id',
                ),
                'limit' => 100,
            ), true, array(
                'branch' => false,
            ));
            $drivers = $this->Driver->getData('list', array(
                'conditions' => array(
                    'Driver.driver_name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Driver.id', 'Driver.id',
                ),
                'limit' => 100,
            ), array(
                'branch' => false,
            ));

            $default_options['conditions']['OR'] = array(
                array(
                    'CashBank.receiver_type' => 'Supplier',
                    'CashBank.receiver_id' => $vendors,
                ),
                array(
                    'CashBank.receiver_type' => 'Employe',
                    'CashBank.receiver_id' => $employes,
                ),
                array(
                    'CashBank.receiver_type' => 'Customer',
                    'CashBank.receiver_id' => $customers,
                ),
                array(
                    'CashBank.receiver_type' => 'Driver',
                    'CashBank.receiver_id' => $drivers,
                ),
            );
        }
        if(!empty($total)){
            $default_options['conditions']['CashBank.debit_total LIKE'] = '%'.$total.'%';
        }
        if(!empty($documenttype)){
            switch ($documenttype) {
                case 'outstanding':
                    $default_options['conditions']['CashBank.prepayment_status <>'] = 'full_paid';
                    break;
            }
        }
        if(!empty($description)){
            $default_options['conditions']['CashBank.description LIKE'] = '%'.$description.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(CashBank.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if( !empty($transaction_status) ) {
            switch ($transaction_status) {
                case 'draft':
                    $default_options['conditions']['CashBank.transaction_status'] = 'unposting';
                    $default_options['conditions']['CashBank.is_rejected'] = 0;
                    break;
                case 'commit':
                    $default_options['conditions']['CashBank.transaction_status'] = 'posting';
                    $default_options['conditions']['CashBank.is_rejected'] = 0;
                    $default_options['conditions']['CashBank.completed'] = 0;
                    $default_options['conditions']['CashBank.is_revised'] = 0;
                    break;
                case 'approved':
                    $default_options['conditions']['CashBank.completed'] = 1;
                    $default_options['conditions']['CashBank.is_rejected'] = 0;
                    break;
                case 'revised':
                    $default_options['conditions']['CashBank.is_revised'] = 1;
                    $default_options['conditions']['CashBank.is_rejected'] = 0;
                    break;
                case 'void':
                    $default_options['conditions']['CashBank.is_rejected'] = 1;
                    break;
            }
        }
        if( !empty($coa) ) {
            $default_options['conditions']['CashBank.coa_id'] = $coa;
        }
        
        return $default_options;
    }

    public function _callDataParams( $data = '', $options = false ) {
        $nodoc = !empty($data['CashBank']['nodoc'])?urldecode($data['CashBank']['nodoc']):false;
        $dateFrom = !empty($data['CashBank']['dateFrom'])?urldecode($data['CashBank']['dateFrom']):false;
        $dateTo = !empty($data['CashBank']['dateTo'])?urldecode($data['CashBank']['dateTo']):false;
        $name = !empty($data['CashBank']['name'])?urldecode($data['CashBank']['name']):false;
        $description = !empty($data['CashBank']['description'])?urldecode($data['CashBank']['description']):false;

        if(!empty($nodoc)){
            $options['conditions']['CashBank.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($dateFrom) && !empty($dateTo)){
            $options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') >='] = $dateFrom;
            $options['conditions']['DATE_FORMAT(CashBank.tgl_cash_bank, \'%Y-%m-%d\') <='] = $dateTo;
        }
        if(!empty($name)){
            $vendors = $this->Vendor->getData('list', array(
                'conditions' => array(
                    'Vendor.name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Vendor.id', 'Vendor.id',
                ),
                'limit' => 100,
            ), array(
                'branch' => false,
            ));
            $employes = $this->Employe->getData('list', array(
                'conditions' => array(
                    'Employe.name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Employe.id', 'Employe.id',
                ),
                'limit' => 100,
            ));
            $customers = $this->Customer->getData('list', array(
                'conditions' => array(
                    'Customer.name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Customer.id', 'Customer.id',
                ),
                'limit' => 100,
            ), true, array(
                'branch' => false,
            ));
            $drivers = $this->Driver->getData('list', array(
                'conditions' => array(
                    'Driver.driver_name LIKE' => '%'.$name.'%',
                ),
                'fields' => array(
                    'Driver.id', 'Driver.id',
                ),
                'limit' => 100,
            ), array(
                'branch' => false,
            ));

            $options['conditions']['OR'] = array(
                array(
                    'CashBank.receiver_type' => 'Supplier',
                    'CashBank.receiver_id' => $vendors,
                ),
                array(
                    'CashBank.receiver_type' => 'Employe',
                    'CashBank.receiver_id' => $employes,
                ),
                array(
                    'CashBank.receiver_type' => 'Customer',
                    'CashBank.receiver_id' => $customers,
                ),
                array(
                    'CashBank.receiver_type' => 'Driver',
                    'CashBank.receiver_id' => $drivers,
                ),
            );
        }
        if(!empty($description)){
            $options['conditions']['CashBank.description LIKE'] = '%'.$description.'%';
        }
        
        return $options;
    }
}
?>