<?php
class ProductReceipt extends AppModel {
	var $name = 'ProductReceipt';

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
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'ProductReceipt.document_type' => 'po',
            ),
        ),
    );

    var $hasMany = array(
        'ProductReceiptDetail' => array(
            'className' => 'ProductReceiptDetail',
            'foreignKey' => 'product_receipt_id',
        ),
        'DocumentAuth' => array(
            'className' => 'DocumentAuth',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'DocumentAuth.document_type' => 'product_receipt',
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
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Karyawan penerimaan harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Karyawan penerimaan harap dipilih'
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
                'message' => 'Tgl penerimaan harap dipilih'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductReceipt.status' => 'DESC',
                'ProductReceipt.created' => 'DESC',
                'ProductReceipt.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductReceipt.status'] = 1;
                break;
            case 'pending':
                $default_options['conditions']['ProductReceipt.transaction_status'] = array( 'unposting', 'revised' );
                $default_options['conditions']['ProductReceipt.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['ProductReceipt.status'] = 0;
                break;
            default:
                $default_options['conditions']['ProductReceipt.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductReceipt.branch_id'] = Configure::read('__Site.config_branch_id');
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

    function getMerge( $data, $id, $status = 'active' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                'ProductReceipt.id' => $id
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
        $defaul_msg = __('penerimaan barang');

        if ( !empty($data) ) {
            $nodoc = $this->filterEmptyField($data, 'ProductReceipt', 'nodoc');
            $transaction_status = $this->filterEmptyField($data, 'ProductReceipt', 'transaction_status');
            $grandtotal = $this->filterEmptyField($data, 'ProductReceipt', 'grandtotal');

            $data['ProductReceipt']['branch_id'] = Configure::read('__Site.config_branch_id');

            if( !empty($nodoc) ) {
                $defaul_msg = sprintf(__('%s #%s'), $defaul_msg, $nodoc);
            }

            if( empty($id) ) {
                $this->create();
                $defaul_msg = sprintf(__('menyimpan %s'), $defaul_msg);
            } else {
                $this->id = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            if( $transaction_status == 'posting' ) {
                $allowApprovals = $this->User->Employe->EmployePosition->Approval->_callNeedApproval('product_receipt', $grandtotal);

                if( empty($allowApprovals) ) {
                    $data['ProductReceipt']['transaction_status'] = 'approved';
                }
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( !empty($flag) ) {
                if( !empty($id) ) {
                    $this->ProductReceiptDetail->deleteAll(array(
                        'ProductReceiptDetail.product_receipt_id' => $id,
                    ));
                }

                $flag = $this->saveAll($data, array(
                    'deep' => true,
                ));

                if( !empty($flag) ) {
                    $id = $this->id;
                    $this->DocumentAuth->deleteAll(array(
                        'DocumentAuth.document_id' => $id,
                        'DocumentAuth.document_type' => 'product_receipt',
                    ));
                    
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

                    if( $transaction_status == 'posting' ) {
                        if( !empty($allowApprovals) ) {
                            $result['Notification'] = array(
                                'user_id' => $allowApprovals,
                                'name' => sprintf(__('Penerimaan barang dengan No Dokumen %s memerlukan ijin Approval'), $nodoc),
                                'link' => array(
                                    'controller' => 'products',
                                    'action' => 'product_receipt_detail',
                                    $id,
                                    'admin' => false,
                                ),
                                'type_notif' => 'warning',
                                'type' => 'warning',
                            );
                        }
                    }
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
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ProductReceipt.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ProductReceipt.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['ProductReceipt.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['ProductReceipt.vendor_id'] = $vendor_id;
        }
        
        return $default_options;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductReceipt.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $sq_id = !empty($value['ProductReceipt']['document_id'])?$value['ProductReceipt']['document_id']:false;
            $nodoc = !empty($value['ProductReceipt']['nodoc'])?$value['ProductReceipt']['nodoc']:false;
            $default_msg = sprintf(__('menghapus penerimaan barang #%s'), $nodoc);

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');

            if( $this->save() ) {
                $this->ProductReceipt->id = $sq_id;
                $this->ProductReceipt->set('receipt_status', 'none');
                $this->ProductReceipt->save();

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
                'ProductReceipt.nodoc' => 'DESC'
            ),
            'fields' => array(
                'ProductReceipt.nodoc'
            ),
            'conditions' => array(
                'ProductReceipt.nodoc LIKE' => '%'.$format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['ProductReceipt']['nodoc'])){
            $str_arr = explode('-', $last_data['ProductReceipt']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }
}
?>