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
        // 'debit' => array(
        //     'numeric' => array(
        //         'rule' => array('numeric'),
        //         'message' => 'Debit harus berupa angka',
        //     ),
        // ),
        // 'credit' => array(
        //     'numeric' => array(
        //         'rule' => array('numeric'),
        //         'message' => 'Kredit harus berupa angka',
        //     ),
        // )
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
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        ),
        'Branch' => array(
            'foreignKey' => 'branch_id',
        ),
    );

    var $hasMany = array(
        'JournalSummary' => array(
            'className' => 'JournalSummary',
            'foreignKey' => 'coa_id',
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
            $date = Common::hashEmptyField($valueSet, 'date');
            $type = $tmp_type = Common::hashEmptyField($valueSet, 'type');
            $document_id = Common::hashEmptyField($valueSet, 'document_id');

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
                        'fields' => array(
                            'Coa.id',
                            'Coa.type',
                            'Coa.balance',
                        ),
                    ));
                    $coaType = !empty($coa['Coa']['type'])?$coa['Coa']['type']:false;
                    $saldo_awal = $balance = !empty($coa['Coa']['balance'])?$coa['Coa']['balance']:false;

                    $data['Journal'] = array(
                        'branch_id' => Configure::read('__Site.config_branch_id'),
                        'user_id' => $user_id,
                        'coa_id' => $coa_id,
                        'saldo_awal' => $saldo_awal,
                    );
                    $data['Journal'][$type] = $total;
                    $data['Journal'] = array_merge($data['Journal'], $valueSet);

                    $this->saveAll($data);
                }
            }

            if( strpos($tmp_type, 'void') !== false && !empty($document_id) ){
                $tmp_type = str_replace(array( '_void', 'void_' ), array( '', '' ), $tmp_type);
                $this->updateAll(
                    array(
                        'Journal.is_void' => 1,
                    ),
                    array(
                        'Journal.document_id' => $document_id,
                        'Journal.type' => $tmp_type,
                    )
                );
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

                    // if( !empty($coa_id) ) {
                    //     $this->Coa->id = $coa_id;
                    //     $this->Coa->set('balance', $balance);
                    //     $this->Coa->save();
                    // }

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

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $type = isset($elements['type'])?$elements['type']:false;

        $default_options = array(
            'conditions'=> array(
                'Journal.status' => 1,
            ),
            'order'=> array(
                'Journal.date' => 'DESC',
                'Journal.document_id' => 'DESC',
                'Journal.type' => 'ASC',
                'Journal.id' => 'DESC',
            ),
            'fields' => array(),
            'group' => array(),
            'contain' => array(
                'Coa'
            ),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['Journal.status'] = 1;
                break;

            case 'without-void':
                $default_options['conditions']['Journal.status'] = 1;
                $default_options['conditions']['Journal.type NOT like'] = '%void%';
                $default_options['conditions']['Journal.is_void'] = 0;
                break;
        }

        switch ($type) {
            case 'active':
                $default_options['conditions']['Journal.type NOT LIKE'] = '%void%';
                $default_options['conditions']['Journal.is_void'] = 0;
                // $default_options['conditions']['JournalVoid.id'] = NULL;
                // $default_options['contain'][] = 'JournalVoid';

                // $this->bindModel(array(
                //     'belongsTo' => array(
                //         'JournalVoid' => array(
                //             'className' => 'Journal',
                //             'foreignKey' => false,
                //             'conditions' => array(
                //                 'JournalVoid.status' => 1,
                //                 'Journal.document_id = JournalVoid.document_id',
                //                 'Journal.document_no = JournalVoid.document_no',
                //                 'OR' => array(
                //                     'JournalVoid.type = CONCAT(Journal.type, \'_void\')',
                //                     'JournalVoid.type = CONCAT(\'void_\', Journal.type)',
                //                 ),
                //             ),
                //         ),
                //     )
                // ), false);
                break;
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
        $title = !empty($data['named']['title'])?$data['named']['title']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $coa = !empty($data['named']['coa'])?$data['named']['coa']:false;
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $journalcoa = !empty($data['named']['journalcoa'])?$data['named']['journalcoa']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['Journal.date >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['Journal.date <='] = $dateTo;
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
                        'Journal.document_no' => 'ASC',
                    );
                    break;
                case 'by-date-asc':
                    $default_options['order'] = array(
                        'Journal.date' => 'ASC',
                        'Journal.document_no' => 'ASC',
                    );
                    break;
                case 'by-nodoc-desc':
                    $default_options['order'] = array(
                        'Journal.document_no' => 'DESC',
                        'Journal.date' => 'DESC',
                    );
                    break;
                case 'by-nodoc-asc':
                    $default_options['order'] = array(
                        'Journal.document_no' => 'ASC',
                        'Journal.date' => 'DESC',
                    );
                    break;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['Journal.document_no LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($title) ) {
            $default_options['conditions']['Journal.title LIKE'] = '%'.$title.'%';
        }
        if( !empty($journalcoa) && $journalcoa != 'all' ) {
            $default_options['conditions']['Journal.coa_id'] = $journalcoa;
        }
        if( $status == 'active' ) {
            $default_options['conditions']['Journal.type NOT LIKE'] = '%void%';
            $default_options['conditions']['Journal.is_void'] = 0;
            // $default_options['conditions']['JournalVoid.id'] = NULL;
            // $default_options['contain'][] = 'JournalVoid';

            // $this->bindModel(array(
            //     'belongsTo' => array(
            //         'JournalVoid' => array(
            //             'className' => 'Journal',
            //             'foreignKey' => false,
            //             'conditions' => array(
            //                 'Journal.document_id = JournalVoid.document_id',
            //                 'Journal.document_no = JournalVoid.document_no',
            //                 'OR' => array(
            //                     'JournalVoid.type = CONCAT(Journal.type, \'_void\')',
            //                     'JournalVoid.type = CONCAT(\'void_\', Journal.type)',
            //                 ),
            //             ),
            //         ),
            //     )
            // ), false);
        }
        
        return $default_options;
    }

    function _callCashFlow ( $data, $value, $options = array() ) {
        $conditions = $this->filterEmptyField($options, 'conditions', false, array());
        $cashflow = $this->filterEmptyField($options, 'cashflow');
        $total_field = $this->filterEmptyField($options, 'total_field');

        $document_id = $this->filterEmptyField($value, 'Journal', 'document_id');
        $journal_type = $this->filterEmptyField($value, 'Journal', 'type');
        $curr_coa_id = $this->filterEmptyField($value, 'Journal', 'coa_id');

        $this->User->Journal->virtualFields['total_debit'] = 'SUM(Journal.debit)';
        $this->User->Journal->virtualFields['total_credit'] = 'SUM(Journal.credit)';
        $options = array(
            'conditions' => array_merge($conditions, array(
                'Journal.coa_id <>' => $curr_coa_id,
                'Journal.document_id' => $document_id,
                'Journal.type' => $journal_type,
            )),
            'group' => array(
                'Journal.coa_id',
                'Journal.document_id',
                'Journal.type',
            ),
            'order' => false,
            'contain' => false,
        );

        $journals = $this->User->Journal->getData('all', $options, true, array(
            'status' => 'without-void',
        ));
        // debug($journals);die();

        if( !empty($journals) ) {
            foreach ($journals as $key => $journal) {
                $tmp_cashflow = $cashflow;
                $total = $this->filterEmptyField($journal, 'Journal', $total_field, false, 0);

                if( empty($total) ) {
                    switch ($total_field) {
                        case 'total_debit':
                            $total = $this->filterEmptyField($journal, 'Journal', 'total_credit', false, 0);
                            $tmp_cashflow = 'in';
                            break;
                        
                        default:
                            $total = $this->filterEmptyField($journal, 'Journal', 'total_debit', false, 0);
                            $tmp_cashflow = 'out';
                            break;
                    }
                }

                $grandtotal = $this->filterEmptyField($data, 'Grandtotal', $tmp_cashflow, 0);
                $journal_coa_id = $this->filterEmptyField($journal, 'Journal', 'coa_id');
                
                $journal = $this->Coa->getMerge($journal, $journal_coa_id);
                $coa_name = $this->filterEmptyField($journal, 'Coa', 'coa_name');

                $totalCashflow = $this->filterEmptyField($data, 'CashFlow', $tmp_cashflow);
                $total_cashflow = $this->filterEmptyField($totalCashflow, $journal_coa_id, false, 0);
                
                $data['CashFlow'][$tmp_cashflow][$journal_coa_id] = $total_cashflow + $total;
                $data['Grandtotal'][$tmp_cashflow] = $grandtotal + $total;
                $data['Coas'][$tmp_cashflow][$journal_coa_id] = $coa_name;
            }
        }

        return $data;
    }

    function _callCoaSetTotal ( $total, $coaName, $field, $options ) {
        if( !empty($total) ) {
            $coa = $this->User->Coa->CoaSettingDetail->getMerge(array(), $coaName, 'CoaSettingDetail.label');
            $coa_id = !empty($coa['CoaSettingDetail']['coa_id'])?$coa['CoaSettingDetail']['coa_id']:false;

            $this->setJournal($total, array(
                $field => $coa_id,
            ), $options);
        }
    }
}
?>