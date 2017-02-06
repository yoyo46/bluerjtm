<?php
class Spk extends AppModel {
    var $name = 'Spk';

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        ),
        'Employe' => array(
            'className' => 'Employe',
            'foreignKey' => 'employe_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

    var $hasMany = array(
        'SpkProduct' => array(
            'className' => 'SpkProduct',
            'foreignKey' => 'spk_id',
        ),
        'SpkMechanic' => array(
            'className' => 'SpkMechanic',
            'foreignKey' => 'spk_id',
        ),
        'SpkProduction' => array(
            'className' => 'SpkProduction',
            'foreignKey' => 'spk_id',
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
        'vendor_id' => array(
            'eksValidate' => array(
                'rule' => array('eksValidate'),
                'message' => 'Vendor harap dipilih'
            ),
        ),
        'to_branch_id' => array(
            'branchValidate' => array(
                'rule' => array('branchValidate'),
                'message' => 'Gudang Penerima harap dipilih'
            ),
        ),
        'employe_id' => array(
            'mechanicValidate' => array(
                'rule' => array('mechanicValidate'),
                'message' => 'Kepala mekanik harap dipilih'
            ),
        ),
        'document_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'No Dokumen harap dipilih'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl SPK harap dipilih'
            ),
        ),
        'document_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis SPK harap dipilih'
            ),
        ),
        'document_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis SPK harap dipilih'
            ),
        ),
        'start_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl mulai harap dipilih'
            ),
        ),
        'start_time' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jam mulai harap dipilih'
            ),
        ),
        'estimation_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl estimasi penyelesaian harap dipilih'
            ),
        ),
        'estimation_time' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jam estimasi penyelesaian harap dipilih'
            ),
        ),
        'complete_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl selesai harap dipilih'
            ),
        ),
        'complete_time' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jam selesai harap dipilih'
            ),
        ),
        'transaction_status' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Status harap dipilih'
            ),
        ),
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Pol harap dipilih'
            ),
        ),
        'mechanic' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mekanik harap dipilih'
            ),
        ),
        'product' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih barang terlebih dahulu'
            ),
        ),
        'production' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih barang yg dihasilkan'
            ),
        ),
    );

    function mechanicValidate () {
        $data = $this->data;
        $employe_id = $this->filterEmptyField($data, 'Spk', 'employe_id');
        
        if( $this->callDisplayToggle('mechanic', $data) && empty($employe_id) ) {
            return false;
        } else {
            return true;
        }
    }
    function eksValidate () {
        $data = $this->data;
        $vendor_id = $this->filterEmptyField($data, 'Spk', 'vendor_id');
        
        if( $this->callDisplayToggle('eksternal', $data) && empty($vendor_id) ) {
            return false;
        } else {
            return true;
        }
    }
    function branchValidate () {
        $data = $this->data;
        $to_branch_id = $this->filterEmptyField($data, 'Spk', 'to_branch_id');

        if( $this->callDisplayToggle('wht', $data) && empty($to_branch_id) ) {
            return false;
        } else {
            return true;
        }
    }

    function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';
        $role = isset($elements['role'])?$elements['role']:false;
        $special_id = isset($elements['special_id'])?$elements['special_id']:false;
        $type = isset($elements['type'])?$elements['type']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Spk.status' => 'DESC',
                'Spk.created' => 'DESC',
                'Spk.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['Spk.status'] = 1;
                break;
            case 'pending':
                $default_options['conditions']['Spk.transaction_status'] = array( 'unposting', 'revised' );
                $default_options['conditions']['Spk.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['Spk.status'] = 0;
                break;
            default:
                $default_options['conditions']['Spk.status'] = array( 0, 1 );
                break;
            case 'pending-out':
                $default_options['conditions']['Spk.status'] = 1;
                $default_options['conditions']['Spk.document_type'] = array( 'internal', 'wht', 'production' );
                $default_options['conditions']['Spk.transaction_status'] = array( 'open' );
                break;
            case 'unreceipt':
                $default_options['conditions']['Spk.status'] = 1;
                $default_options['conditions']['Spk.document_type'] = array( 'internal' );
                $default_options['conditions']['Spk.receipt_status'] = array( 'none', 'half' );
                break;
            case 'unreceipt_draft':
                $default_options['conditions']['Spk.status'] = 1;
                $default_options['conditions']['Spk.document_type'] = array( 'internal' );

                if( !empty($special_id) ) {
                    $default_options['conditions']['OR']['Spk.id'] = $special_id;
                    $default_options['conditions']['OR']['Spk.draft_receipt_status'] = array( 'none', 'half' );
                } else {
                    $default_options['conditions']['Spk.draft_receipt_status'] = array( 'none', 'half' );
                }
                break;
            case 'untransfer_draft':
                $default_options['conditions']['Spk.status'] = 1;
                $default_options['conditions']['Spk.document_type'] = array( 'wht' );

                if( !empty($special_id) ) {
                    $default_options['conditions']['OR']['Spk.id'] = $special_id;
                    $default_options['conditions']['OR']['Spk.transaction_status'] = 'out';
                } else {
                    $default_options['conditions']['Spk.transaction_status'] = 'out';
                }
                break;
        }

        switch ($type) {
            case 'production':
                $default_options['conditions']['Spk.document_type'] = 'production';
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['Spk.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($role) ) {
            $default_options['conditions']['Spk.transaction_status'] = $role;
        }

        if( !empty($options) ) {
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $fieldName = 'Spk.id', $status = false ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id
            ),
        ), array(
            'status' => $status,
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('menyimpan SPK');

        if ( !empty($data) ) {
            $start_date = Common::hashEmptyField($data, 'Spk.start_date');
            $start_time = Common::hashEmptyField($data, 'Spk.start_time');
            $estimation_date = Common::hashEmptyField($data, 'Spk.estimation_date');
            $estimation_time = Common::hashEmptyField($data, 'Spk.estimation_time');

            if( !empty($start_time) ) {
                $data['Spk']['start_date'] = __('%s %s', $start_date, $start_time);
            }
            if( !empty($estimation_time) ) {
                $data['Spk']['estimation_date'] = __('%s %s', $estimation_date, $estimation_time);
            }

            if( !empty($id) ) {
                $data['Spk']['id'] = $id;
            }

            $flag = $this->saveAll($data, array(
                'deep' => true,
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                $this->SpkProduct->deleteAll(array(
                    'SpkProduct.spk_id' => $id,
                ));
                $this->SpkMechanic->deleteAll(array(
                    'SpkMechanic.spk_id' => $id,
                ));
                $this->SpkProduction->deleteAll(array(
                    'SpkProduction.spk_id' => $id,
                ));

                $flag = $this->saveAll($data, array(
                    'deep' => true,
                ));
                
                if( !empty($flag) ) {
                    $id = $this->id;
                    
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);
                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                        ),
                        'data' => $data,
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
                        'data' => $data,
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
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
        $noref = $this->filterEmptyField($data, 'named', 'noref');
        $document_type = $this->filterEmptyField($data, 'named', 'document_type');
        $nodoc = $this->filterEmptyField($data, 'named', 'nodoc');
        $dateFrom = $this->filterEmptyField($data, 'named', 'DateFrom');
        $dateTo = $this->filterEmptyField($data, 'named', 'DateTo');
        $vendor_id = $this->filterEmptyField($data, 'named', 'vendor_id');

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Spk.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Spk.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['Spk.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['Spk.vendor_id'] = $vendor_id;
        }
        if( !empty($document_type) ) {
            $default_options['conditions']['Spk.document_type'] = $document_type;
        }
        
        return $default_options;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('RR-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'Spk.nodoc' => 'DESC'
            ),
            'fields' => array(
                'Spk.nodoc'
            ),
            'conditions' => array(
                'Spk.nodoc LIKE' => '%'.$format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['Spk']['nodoc'])){
            $str_arr = explode('-', $last_data['Spk']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $default_msg = sprintf(__('menghapus SPK #%s'), $id);

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');

            if( $this->save() ) {
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
                'msg' => __('Gagal menghapus SPK. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function _callVendors ( $status = 'unpaid', $id = false, $document_type = false ) {
        return $this->getData('list', array(
                'contain' => array(
                    'Vendor',
                ),
                'fields' => array(
                    'Vendor.id', 'Vendor.name',
                ),
                'group' => array(
                    'Spk.vendor_id',
                ),
            ), array(
                'status' => $status,
                'special_id' => $id,
                'type' => $document_type,
            ));
    }
}
?>