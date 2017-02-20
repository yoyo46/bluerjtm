<?php
class SupplierQuotation extends AppModel {
	var $name = 'SupplierQuotation';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        )
    );

    var $hasMany = array(
        'SupplierQuotationDetail' => array(
            'className' => 'SupplierQuotationDetail',
            'foreignKey' => 'supplier_quotation_id',
        ),
        'DocumentAuth' => array(
            'className' => 'DocumentAuth',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'DocumentAuth.document_type' => 'sq',
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
        // 'available_from' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl berlaku quotation harap dipilih'
        //     ),
        // ),
        // 'available_to' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl berlaku quotation harap dipilih'
        //     ),
        // ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl quotation harap dipilih'
            ),
        ),
        // 'available_date' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'Tgl berlaku quotation harap dipilih'
        //     ),
        // ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $vendor = isset($elements['vendor'])?$elements['vendor']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SupplierQuotation.status' => 'DESC',
                'SupplierQuotation.created' => 'DESC',
                'SupplierQuotation.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['SupplierQuotation.status'] = 1;
                break;
            case 'available':
                $default_options['conditions']['SupplierQuotation.transaction_status'] = 'approved';
                $default_options['conditions']['SupplierQuotation.status'] = 1;
                $default_options['conditions'][]['OR'] = array(
                    array(
                        'SupplierQuotation.available_from' => NULL,
                        'SupplierQuotation.available_to' => NULL,
                    ),
                    array(
                        'SupplierQuotation.available_from' => '0000-00-00',
                        'SupplierQuotation.available_to' => '0000-00-00',
                    ),
                    array(
                        'SupplierQuotation.available_from <=' => date('Y-m-d'),
                        'SupplierQuotation.available_to' => '0000-00-00',
                    ),
                    array(
                        'SupplierQuotation.available_from' => '0000-00-00',
                        'SupplierQuotation.available_to >=' => date('Y-m-d'),
                    ),
                    array(
                        'SupplierQuotation.available_from <=' => date('Y-m-d'),
                        'SupplierQuotation.available_to' => NULL,
                    ),
                    array(
                        'SupplierQuotation.available_from' => NULL,
                        'SupplierQuotation.available_to >=' => date('Y-m-d'),
                    ),
                    array(
                        'SupplierQuotation.available_from <=' => date('Y-m-d'),
                        'SupplierQuotation.available_to >=' => date('Y-m-d'),
                    ),
                );
                break;
            // case 'po':
            //     $default_options['conditions']['SupplierQuotation.status'] = 1;
            //     $default_options['conditions']['SupplierQuotation.transaction_status'] = 'po';
            //     break;
            case 'pending-po':
                $default_options['conditions']['SupplierQuotation.status'] = 1;
                $default_options['conditions']['SupplierQuotation.transaction_status'] = array( 'approved' );
                break;
            case 'pending':
                $default_options['conditions']['SupplierQuotation.transaction_status'] = array( 'unposting', 'revised' );
                $default_options['conditions']['SupplierQuotation.status'] = 1;
                break;
        }

        if( !empty($vendor) ) {
            $default_options['conditions']['SupplierQuotation.vendor_id'] = $vendor;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['SupplierQuotation.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $fieldName = 'SupplierQuotation.id' ){
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

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('Penawaran Supplier');

        if ( !empty($data) ) {
            $nodoc = $this->filterEmptyField($data, 'SupplierQuotation', 'nodoc');
            $transaction_status = $this->filterEmptyField($data, 'SupplierQuotation', 'transaction_status');
            $grandtotal = $this->filterEmptyField($data, 'SupplierQuotation', 'grandtotal');
            $data['SupplierQuotation']['branch_id'] = Configure::read('__Site.config_branch_id');

            if( !empty($nodoc) ) {
                $defaul_msg = sprintf(__('%s #%s'), $defaul_msg, $nodoc);
            }

            if( empty($id) ) {
                $this->create();
                $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);
            } else {
                $this->id = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            if( $transaction_status == 'posting' ) {
                $allowApprovals = $this->User->Employe->EmployePosition->Approval->_callNeedApproval('sq', $grandtotal);

                if( empty($allowApprovals) ) {
                    $data['SupplierQuotation']['transaction_status'] = 'approved';
                }
            }

            $this->set($data);
            $validates = $this->validates();

            $detailValidates = $this->SupplierQuotationDetail->doSave($data, false, true);

            if( $validates && $detailValidates ) {
                if( $this->save($data) ) {
                    $id = $this->id;
                    $this->DocumentAuth->deleteAll(array(
                        'DocumentAuth.document_id' => $id,
                        'DocumentAuth.document_type' => 'sq',
                    ));
                    
                    $this->SupplierQuotationDetail->doSave($data, $id);
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

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

                    if( $transaction_status == 'posting' ) {
                        if( !empty($allowApprovals) ) {
                            $result['Notification'] = array(
                                'user_id' => $allowApprovals,
                                'name' => sprintf(__('Penawaran Supplier dengan No Dokumen %s memerlukan ijin Approval'), $nodoc),
                                'link' => array(
                                    'controller' => 'purchases',
                                    'action' => 'supplier_quotation_detail',
                                    $id,
                                    'admin' => false,
                                ),
                                'type_notif' => 'approval',
                                'type' => 'warning',
                            );
                        }
                    }
                } else {
                    $defaul_msg = sprintf(__('Gagal %s. Silahkan melengkapi field dibawah ini.'), $defaul_msg);
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
                $defaul_msg = sprintf(__('Gagal %s. Silahkan melengkapi field dibawah ini.'), $defaul_msg);
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
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(SupplierQuotation.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(SupplierQuotation.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['SupplierQuotation.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['SupplierQuotation.vendor_id'] = $vendor_id;
        }

        if( !empty($status) ) {
            switch ($status) {
                case 'expired':
                    $default_options['conditions']['SupplierQuotation.transaction_status'] = 'approved';
                    $default_options['conditions']['SupplierQuotation.available_to <'] = date('Y-m-d');
                    break;
                case 'approved':
                    $default_options['conditions']['SupplierQuotation.transaction_status'] = 'approved';
                    $default_options['conditions']['SupplierQuotation.available_to >='] = date('Y-m-d');
                    break;
                default:
                    $default_options['conditions']['SupplierQuotation.transaction_status'] = $status;
                    break;
            }
        }
        
        return $default_options;
    }

    function _callRatePrice ( $product_id = false, $quotation_id = false, $empty = 0 ) {
        $value = $this->SupplierQuotationDetail->getData('first', array(
            'conditions' => array(
                'SupplierQuotationDetail.product_id' => $product_id,
                'SupplierQuotationDetail.supplier_quotation_id <>' => $quotation_id,
            ),
            'order' => array(
                'SupplierQuotationDetail.price' => 'ASC',
            ),
        ));

        return !empty($value['SupplierQuotationDetail']['price'])?$value['SupplierQuotationDetail']['price']:$empty;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'SupplierQuotation.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $nodoc = !empty($value['SupplierQuotation']['nodoc'])?$value['SupplierQuotation']['nodoc']:false;
            $default_msg = sprintf(__('menghapus quotation #%s'), $nodoc);

            $this->id = $id;
            $this->set('status', 0);

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
                'msg' => __('Gagal menghapus quotation. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function getDataCustom ( $fieldName, $id, $resultFieldName = false ) {
        $value = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id,
            ),
        ));

        if( !empty($resultFieldName) ) {
            return !empty($value['SupplierQuotation'][$resultFieldName])?$value['SupplierQuotation'][$resultFieldName]:false;
        } else {
            return $value;
        }
    }
}
?>