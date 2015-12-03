<?php
class Journal extends AppModel {
	var $name = 'Journal';
	var $validate = array(
		'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
		),
        'debit' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debit harus berupa angka',
            ),
        ),
        'credit' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Kredit harus berupa angka',
            ),
        )
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
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

    function _callCalcSaldo( $coaType, $type, $biaya, $saldo_awal ) {
        if( $coaType == $type ) {
            $saldo_awal += $biaya;
        } else {
            $saldo_awal -= $biaya;
        }

        return $saldo_awal;
    }

    function setJournal ( $total, $coas, $valueSet = array() ) {
        if( !empty($coas) && is_array($coas) ) {
            foreach ($coas as $type => $coa_name) {
                $coaSetting = $this->User->CoaSetting->getData('first', array(
                    'conditions' => array(
                        'CoaSetting.status' => 1
                    ),
                ));

                if( !empty($coaSetting['CoaSetting'][$coa_name]) ) {
                    $coa_id = $coaSetting['CoaSetting'][$coa_name];
                } else {
                    $coa_id = $coa_name;
                }

                if( !empty($coa_id) ) {
                    $user_id = Configure::read('__Site.config_user_id');
                    $coa = $this->Coa->getData('first', array(
                        'conditions' => array(
                            'Coa.id' => $coa_id,
                        ),
                    ));
                    $coaType = !empty($coa['Coa']['type'])?$coa['Coa']['type']:false;
                    $saldo_awal = $balance = !empty($coa['Coa']['balance'])?$coa['Coa']['balance']:false;

                    if( in_array($type, array( 'debit', 'credit' )) ) {
                        $balance = $this->_callCalcSaldo($coaType, $type, $total, $saldo_awal);

                        $this->Coa->id = $coa_id;
                        $this->Coa->set('balance', $balance);
                        $this->Coa->save();
                    }

                    $data['Journal'] = array(
                        'branch_id' => Configure::read('__Site.config_branch_id'),
                        'user_id' => $user_id,
                        'coa_id' => $coa_id,
                        'saldo_awal' => $saldo_awal,
                    );
                    $data['Journal'][$type] = $total;
                    $data['Journal'] = array_merge($data['Journal'], $valueSet);

                    $this->create();
                    $this->set($data);

                    $this->save($data);
                }
            }
        } else {
            return false;
        }
    }

    function deleteJournal ( $document_id = false, $type = false ) {
        if( !empty($type) ) {
            foreach ($type as $key => $value) {
                $journal = $this->find('first', array(
                    'conditions' => array(
                        'Journal.document_id' => $document_id,
                        'Journal.type' => $value,
                        'Journal.status' => 1,
                    ),
                ));

                if( !empty($journal) ) {
                    $coa_id = !empty($journal['Journal']['coa_id'])?$journal['Journal']['coa_id']:0;
                    $credit = !empty($journal['Journal']['credit'])?$journal['Journal']['credit']:0;
                    $debit = !empty($journal['Journal']['debit'])?$journal['Journal']['debit']:0;

                    $journal = $this->Coa->getMerge($journal, $coa_id);
                    $saldo_awal = $balance = !empty($journal['Coa']['balance'])?$journal['Coa']['balance']:0;

                    if( !empty($credit) ) {
                        $balance += $credit;
                    } else {
                        $balance -= $credit;
                    }

                    if( !empty($coa_id) ) {
                        $this->Coa->id = $coa_id;
                        $this->Coa->set('balance', $balance);
                        $this->Coa->save();
                    }

                    $this->updateAll(
                        array(
                            'Journal.status' => 0
                        ),
                        array(
                            'Journal.document_id' => $document_id,
                            'Journal.type' => $value,
                        )
                    );
                }
            }
        }
    }

    function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Journal.status' => 1,
            ),
            'order'=> array(
                'Journal.date' => 'DESC',
                'Journal.document_id' => 'ASC',
                'Journal.type' => 'ASC',
                'Journal.id' => 'DESC',
            ),
            'fields' => array(),
            'group' => array(),
            'contain' => array(
                'Coa'
            ),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $coa = !empty($data['named']['coa'])?$data['named']['coa']:false;
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Journal.date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Journal.date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($coa) ) {
            $allChildren = $this->Coa->children($coa);
            $tmpId = Set::extract('/Coa/id', $allChildren);
            $tmpId[] = $coa;
            $default_options['conditions']['Journal.coa_id'] = $tmpId;
        }
        if( !empty($sort) ) {
            switch ($sort) {
                case 'by-date-desc':
                    $default_options['order'] = array(
                        'Journal.date' => 'DESC',
                    );
                    break;
                case 'by-date-asc':
                    $default_options['order'] = array(
                        'Journal.date' => 'ASC',
                    );
                    break;
                case 'by-date-by-nodoc-desc':
                    $default_options['order'] = array(
                        'Journal.date' => 'DESC',
                        'Journal.document_no' => 'DESC',
                    );
                    break;
                case 'by-date-by-nodoc-asc':
                    $default_options['order'] = array(
                        'Journal.date' => 'ASC',
                        'Journal.document_no' => 'ASC',
                    );
                    break;
            }
        }
        
        return $default_options;
    }
}
?>