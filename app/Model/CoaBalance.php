<?php
class CoaBalance extends AppModel {
	var $name = 'CoaBalance';
	var $validate = array(
		'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
		),
        'user_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'User pembuat harap dipilih'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
        ),
        'type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe balance harap diisi'
            ),
        ),
        'saldo' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Saldo harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Saldo harus berupa angka',
            ),
        ),
        'note' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Keterangan harap diisi'
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
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(
                'CoaBalance.status' => 1,
            ),
            'order'=> array(
                'CoaBalance.created' => 'DESC',
                'CoaBalance.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

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
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
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

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(CoaBalance.date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(CoaBalance.date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($coa) ) {
            $allChildren = $this->Coa->children($coa);
            $tmpId = Set::extract('/Coa/id', $allChildren);
            $tmpId[] = $coa;
            $default_options['conditions']['CoaBalance.coa_id'] = $tmpId;
        }
        
        return $default_options;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('%s-COA-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'CoaBalance.nodoc' => 'DESC'
            ),
            'fields' => array(
                'CoaBalance.nodoc'
            ),
            'conditions' => array(
                'CoaBalance.nodoc LIKE' => '%'.$format_id.'%',
            )
        ));

        if(!empty($last_data['CoaBalance']['nodoc'])){
            $str_arr = explode('-', $last_data['CoaBalance']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('menambahkan balance terhadap COA');

        if ( !empty($data) ) {
            $data['CoaBalance']['nodoc'] = $nodoc = $this->generateNoId();

            $this->create();

            $this->set($data);
            $flagValidates = $this->validates();

            $coa_name = !empty($data['Coa']['coa_name'])?$data['Coa']['coa_name']:false;
            $note = !empty($data['CoaBalance']['note'])?$data['CoaBalance']['note']:false;

            if( !empty($coa_name) ) {
                $defaul_msg = sprintf(__('%s %s'), $defaul_msg, $coa_name);
            }

            if( $flagValidates ) {
                debug($data);die();
                if( $this->save($data) ) {
                    $id = $this->id;
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

                    $this->User->Journal->setJournal($total, array(
                        'credit' => 'commission_coa_credit_id',
                        'debit' => 'commission_coa_debit_id',
                    ), array(
                        'document_id' => $id,
                        'title' => $note,
                        'document_no' => $nodoc,
                        'type' => 'coa_balance',
                    ));

                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                        ),
                    );
                } else {
                    $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        } else {
            $result['data'] = array(
                'CoaBalance' => array(
                    'date' => date('d/m/Y'),
                ),
            );
        }

        return $result;
    }
}
?>