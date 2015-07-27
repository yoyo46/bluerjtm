<?php
class CashBank extends AppModel {
	var $name = 'CashBank';
	var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
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
		)
	);

    var $hasMany = array(
        'CashBankDetail' => array(
            'className' => 'CashBankDetail',
            'foreignKey' => 'cash_bank_id',
        ),
        'CashBankAuth' => array(
            'className' => 'CashBankAuth',
            'foreignKey' => 'cash_bank_id',
        )
    );

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'CashBank.status' => 1,
            ),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
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
            $data_merge = $this->find('first', array(
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

    function getDocumentCashBank ( $group_branch_id, $prepayment_out_id = false, $document_type = 'prepayment_out' ) {
        $result = array(
            'docs' => array(),
            'docs_type' => false,
        );
        $options = array(
            'conditions' => array(
                'CashBank.status' => 1,
                'CashBank.is_rejected' => 0,
                'CashBank.receiving_cash_type' => $document_type,
                'CashBank.branch_id' => $group_branch_id
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

        $docTmps = $this->getData('all', $options, false);

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
            'CashBank.status' => 1,
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
        ), false);

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

    function beforeFind($data){
        
    }
}
?>