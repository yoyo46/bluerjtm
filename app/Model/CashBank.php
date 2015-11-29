<?php
class CashBank extends AppModel {
	var $name = 'CashBank';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No Dokumen telah terdaftar',
            ),
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
	);

    var $hasMany = array(
        'CashBankDetail' => array(
            'className' => 'CashBankDetail',
            'foreignKey' => 'cash_bank_id',
        ),
        'CashBankAuth' => array(
            'className' => 'CashBankAuth',
            'foreignKey' => 'cash_bank_id',
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
                ));

                return $result;
                break;
            
            default:
                // If call by id
                if( !empty($receiver) ) {
                    $conditions[$model.'.id'] = $receiver;
                }

                $result = $this->{$model}->getData('first', array(
                    'conditions' => $conditions,
                ));

                return !empty($result[$model]['name'])?$result[$model]['name']:false;
                break;
        }
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

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
        
        return $default_options;
    }
}
?>