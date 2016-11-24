<?php
class GeneralLedger extends AppModel {
	var $name = 'GeneralLedger';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
    );

    var $hasMany = array(
        'GeneralLedgerDetail' => array(
            'className' => 'GeneralLedgerDetail',
            'foreignKey' => 'general_ledger_id',
        ),
        'DocumentAuth' => array(
            'className' => 'DocumentAuth',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'DocumentAuth.document_type' => 'gl',
            ),
        ),
    );

	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No dokumen harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'No dokumen sudah terdaftar, mohon masukkan no dokumen lain.'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl transaksi harap dipilih'
            ),
        ),
	);

    public function __construct($id = false, $table = NULL, $ds = NULL){
        parent::__construct($id, $table, $ds);

        $alias = $this->alias;
        $this->virtualFields['order'] = __('CASE %s.transaction_status WHEN \'unposting\' THEN 2 WHEN \'posting\' THEN 1 ELSE 0 END', $alias, $alias);
    }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(
                'GeneralLedger.status' => 1,
            ),
            'order'=> array(
                'GeneralLedger.order' => 'DESC',
                'GeneralLedger.created' => 'DESC',
                'GeneralLedger.id' => 'DESC',
            ),
            'fields' => array(),
        );

        if( !empty($status) ) {
            $default_options['conditions']['GeneralLedger.transaction_status'] = $status;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['GeneralLedger.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = array_merge($default_options['order'], $options['order']);
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $fieldName = 'GeneralLedger.id' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id,
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    function doSave( $data, $value = false ) {
        $result = false;
        $defaul_msg = __('menyimpan jurnal umum');

        if ( !empty($data) ) {
            $id = $this->filterEmptyField($data, 'GeneralLedger', 'id');
            $nodoc = $this->filterEmptyField($data, 'GeneralLedger', 'nodoc');
            $debit_total = $this->filterEmptyField($data, 'GeneralLedger', 'debit_total');
            $credit_total = $this->filterEmptyField($data, 'GeneralLedger', 'credit_total');

            if( !empty($nodoc) ) {
                $defaul_msg = sprintf(__('%s #%s'), $defaul_msg, $nodoc);
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                if( $credit_total == $debit_total ) {
                    if( !empty($id) ) {
                        $nodoc = $this->filterEmptyField($value, 'GeneralLedger', 'nodoc');
                        $this->GeneralLedgerDetail->deleteAll(array(
                            'GeneralLedgerDetail.general_ledger_id' => $id,
                        ));
                        $data = $this->callUnset($data, array(
                            'GeneralLedger' => array(
                                'nodoc',
                            ),
                        ));
                    } else {
                        $nodoc = $this->generateNoDoc();
                        $data['GeneralLedger']['nodoc'] = $nodoc;
                    }

                    if( $this->saveAll($data) ) {
                        $id = $this->id;
                        $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

                        $this->GeneralLedgerDetail->setJournal($id, $data, $nodoc);

                        $result = array(
                            'id' => $id,
                            'msg' => $defaul_msg,
                            'status' => 'success',
                            'Log' => array(
                                'activity' => $defaul_msg,
                                'old_data' => $value,
                                'document_id' => $id,
                            ),
                        );
                    } else {
                        $defaul_msg = sprintf(__('Gagal %s. Silahkan melengkapi field dibawah ini.'), $defaul_msg);
                        $result = array(
                            'msg' => $defaul_msg,
                            'status' => 'error',
                            'Log' => array(
                                'activity' => $defaul_msg,
                                'old_data' => $value,
                                'error' => 1,
                            ),
                            'data' => $data,
                        );
                    }
                } else {
                    $defaul_msg = sprintf(__('Gagal %s. Total transaksi tidak balance.'), $defaul_msg);
                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'error',
                        'data' => $data,
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s. Silahkan melengkapi field dibawah ini.'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
                    'data' => $data,
                );
            }
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $status = $this->filterEmptyField($data, 'named', 'status');
        $nodoc = $this->filterEmptyField($data, 'named', 'nodoc');
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(GeneralLedger.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(GeneralLedger.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['GeneralLedger.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($status) ) {
            $default_options['conditions']['GeneralLedger.transaction_status'] = $status;
        }
        
        return $default_options;
    }

    function doDelete( $id, $value ) {
        $result = false;

        if ( !empty($value) ) {
            $nodoc = $this->filterEmptyField($value, 'GeneralLedger', 'nodoc');
            $default_msg = sprintf(__('menghapus jurnal umum #%s'), $nodoc);

            $this->id = $id;
            $this->set('transaction_status', 'void');

            if( $this->save() ) {
                $this->User->Journal->deleteJournal($id, array(
                    'general_ledger',
                ));
                    
                $msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => __('Gagal menghapus jurnal umum. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function generateNoDoc(){
        $default_id = 1;
        $format_id = sprintf('GJ%s', date('Y'));

        $last_data = $this->getData('first', array(
            'conditions' => array(
                'GeneralLedger.nodoc LIKE' => ''.$format_id.'%',
            ),
            'order' => array(
                'GeneralLedger.nodoc' => 'DESC'
            ),
            'fields' => array(
                'GeneralLedger.nodoc'
            )
        ), array(
            'branch' => false,
        ));
        $nodoc = $this->filterEmptyField($last_data, 'GeneralLedger', 'nodoc');

        if(!empty($nodoc)){
            $str_arr = explode($format_id, $nodoc);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }

        $id = str_pad($default_id, 5,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }
}
?>