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
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Vendor harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Vendor harap dipilih'
            ),
        ),
        'to_branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Gudang Penerima harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Gudang Penerima harap dipilih'
            ),
        ),
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kepala mekanik harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
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
    );

    function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

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
        }

        if( !empty($branch) ) {
            $default_options['conditions']['Spk.branch_id'] = Configure::read('__Site.config_branch_id');
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

    function getMerge( $data, $id, $fieldName = 'Spk.id' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id
            ),
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    function _callReceiptDocument( $data ) {
        $document_id = $this->filterEmptyField($data, 'Spk', 'document_id');
        $document_type = $this->filterEmptyField($data, 'Spk', 'document_type');
        $transaction_status = $this->filterEmptyField($data, 'Spk', 'transaction_status');

        switch ($document_type) {
            case 'po':
                $purchaseOrderDetail = $this->PurchaseOrder->PurchaseOrderDetail->getData('first', array(
                    'conditions' => array(
                        'purchaseOrderDetail.purchase_order_id' => $document_id,
                    ),
                ), array(
                    'status' => 'unreceipt',
                ));

                if( !empty($purchaseOrderDetail) ) {
                    $receipt_status = 'half';
                } else {
                    $receipt_status = 'full';
                }

                $this->PurchaseOrder->id = $document_id;
                $this->PurchaseOrder->set('draft_receipt_status', $receipt_status);

                if( $transaction_status == 'posting' ) {
                    $this->PurchaseOrder->set('receipt_status', $receipt_status);
                }

                $this->PurchaseOrder->save();
                break;
        }
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('menyimpan SPK');

        if ( !empty($data) ) {
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
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;

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
        
        return $default_options;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'Spk.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $document_id = $this->filterEmptyField($value, 'Spk', 'document_id');
            $nodoc = $this->filterEmptyField($value, 'Spk', 'nodoc');
            $document_type = $this->filterEmptyField($value, 'Spk', 'document_type');
            $default_msg = sprintf(__('menghapus penerimaan barang #%s'), $id);

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');

            if( $this->save() ) {

                switch ($document_type) {
                    case 'po':
                        $this->PurchaseOrder->id = $document_id;
                        $this->PurchaseOrder->set('receipt_status', 'none');
                        $this->PurchaseOrder->save();
                        break;
                }

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
                'msg' => __('Gagal menghapus penerimaan barang. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
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
}
?>