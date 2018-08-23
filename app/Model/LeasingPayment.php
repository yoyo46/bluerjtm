<?php
class LeasingPayment extends AppModel {
	var $name = 'LeasingPayment';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            // 'isUnique' => array(
            //     'rule' => array('isUnique'),
            //     'message' => 'No Dokumen telah terdaftar',
            // ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
        'payment_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'vendor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Supplier harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        ),
    );

    var $hasMany = array(
        'LeasingPaymentDetail' => array(
            'className' => 'LeasingPaymentDetail',
            'foreignKey' => 'leasing_payment_id',
        )
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'LeasingPayment.rejected' => 'ASC',
                'LeasingPayment.created' => 'DESC',
                'LeasingPayment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['LeasingPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['LeasingPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['LeasingPayment.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['LeasingPayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options) && $is_merge){
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }else{
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $default_msg = __('melakukan pembayaran leasing');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $data['LeasingPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['LeasingPayment']['user_id'] = Configure::read('__Site.config_user_id');
            $resultDetail = $this->LeasingPaymentDetail->getDataModel($data, $id);

            $data['LeasingPayment']['total_installment'] = !empty($resultDetail['total_installment'])?$resultDetail['total_installment']:0;
            $data['LeasingPayment']['total_installment_rate'] = !empty($resultDetail['total_installment_rate'])?$resultDetail['total_installment_rate']:0;
            $data['LeasingPayment']['total_denda'] = !empty($resultDetail['total_denda'])?$resultDetail['total_denda']:0;
            $data['LeasingPayment']['grandtotal'] = !empty($resultDetail['grandtotal'])?$resultDetail['grandtotal']:0;
            $dataDetail = !empty($resultDetail['data'])?$resultDetail['data']:false;

            $data['LeasingPaymentDetail'] = $dataDetail;

            $this->set($data);
            $mainValidate = $this->validates();

            $detailValidates = $this->LeasingPaymentDetail->doSave($dataDetail, $data, false, $id, true);
            $statusDetail = !empty($detailValidates['status'])?$detailValidates['status']:false;

            if( $mainValidate && $statusDetail == 'success' ) {
                $flagSave = $this->save();
                $id = $this->id;

                if( !empty($flagSave) ) {
                    $this->LeasingPaymentDetail->doSave($dataDetail, $data, false, $id);
                }

                if( !empty($flagSave) ) {
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    $result = array(
                        'msg' => sprintf(__('Berhasil %s #%s'), $default_msg, $noref),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                        'data' => $data,
                    );
                }
            } else {
                if( !empty($mainValidate) ) {
                    $msg = __('Mohon lengkapi detail pembayaran leasing');
                } else {
                    $msg = sprintf(__('Gagal %s'), $default_msg);
                }

                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'data' => $data,
                    'validationErrors' => !empty($detailValidates['validationErrors'])?$detailValidates['validationErrors']:false,
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function doSaveDP( $data, $value = false, $id = false ) {
        $result = false;
        $default_msg = __('melakukan pembayaran DP');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $no_doc = Common::hashEmptyField($data, 'LeasingPayment.no_doc');
            $coa_id = Common::hashEmptyField($data, 'LeasingPayment.coa_id');
            $cogs_id = Common::hashEmptyField($data, 'LeasingPayment.cogs_id');
            $payment_date = Common::hashEmptyField($data, 'LeasingPayment.payment_date');
            $detail = Common::hashEmptyField($data, 'LeasingPaymentDetail.leasing_id');
            
            $vendor_id = Common::hashEmptyField($data, 'LeasingPayment.vendor_id');
            $vendor = $this->Vendor->getMerge(array(), $vendor_id);
            $vendor_name = Common::hashEmptyField($vendor, 'Vendor.name');

            $data['LeasingPayment']['id'] = $id;
            $data['LeasingPayment']['type'] = 'dp';
            $data['LeasingPayment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['LeasingPayment']['user_id'] = Configure::read('__Site.config_user_id');

            if( !empty($detail) ) {
                $result = array();
                $total_installment = 0;
                $total_denda = 0;
                $grandtotal = 0;

                foreach ($detail as $leasing_id => $value) {
                    $dp = Common::hashEmptyField($data, 'LeasingPaymentDetail.installment.'.$leasing_id, 0, array(
                        'type' => 'unprice',
                    ));
                    $denda = Common::hashEmptyField($data, 'LeasingPaymentDetail.denda.'.$leasing_id, 0, array(
                        'type' => 'unprice',
                    ));
                    $total = $dp+$denda;
                    
                    $paid = $this->LeasingPaymentDetail->Leasing->_callLastPaidDP(array(), $leasing_id, $id);
                    $paid_dp = Common::hashEmptyField($paid, 'Leasing.paid_dp', 0);
                    $total_paid = $dp + $paid_dp;

                    $leasing = $this->LeasingPaymentDetail->Leasing->getMerge(array(), $leasing_id);
                    $total_dp = Common::hashEmptyField($leasing, 'Leasing.down_payment', 0);
                    $no_contract = Common::hashEmptyField($leasing, 'Leasing.no_contract');

                    if( $total_paid >= $total_dp ) {
                        $status = 'paid';
                    } else {
                        $status = 'half_paid';
                    }

                    $result[] = array(
                        'LeasingPaymentDetail' => array(
                            'leasing_id' => $leasing_id,
                            'installment' => $dp,
                            'denda' => $denda,
                            'total' => $total,
                        ),
                        'Leasing' => array(
                            'id' => $leasing_id,
                            'no_contract' => $no_contract,
                            'dp_payment_status' => $status,
                        ),
                    );

                    $total_installment += $dp;
                    $total_denda += $denda;
                    $grandtotal += $total;
                }

                $data['LeasingPaymentDetail'] = $result;
                $data = hash::insert($data, 'LeasingPayment.total_installment', $total_installment);
                $data = hash::insert($data, 'LeasingPayment.total_denda', $total_denda);
                $data = hash::insert($data, 'LeasingPayment.grandtotal', $grandtotal);
            }

            if($this->saveAll($data, array(
                'deep' => true,
            ))) {
                $id = $this->id;
                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $title = __('Pembayaran DP #%s kepada supplier %s', $no_doc, $vendor_name);

                if( !empty($total_installment) ) {
                    $coaLeasing = $this->Coa->CoaSettingDetail->getMerge(array(), 'LeasingDPDebit', 'CoaSettingDetail.label');
                    $leasing_coa_debit_id = Common::hashEmptyField($coaLeasing, 'CoaSettingDetail.coa_id');

                    $this->Coa->Journal->setJournal($total_installment, array(
                        'credit' => $coa_id,
                        'debit' => $leasing_coa_debit_id,
                    ), array(
                        'cogs_id' => $cogs_id,
                        'date' => $payment_date,
                        'document_id' => $id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'leasing_payment',
                    ));
                }
                if( !empty($total_denda) ) {
                    $this->Coa->Journal->setJournal($total_denda, array(
                        'credit' => $coa_id,
                        'debit' => 'leasing_denda_coa_id',
                    ), array(
                        'cogs_id' => $cogs_id,
                        'date' => $payment_date,
                        'document_id' => $id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'leasing_payment',
                    ));
                }

                $result = array(
                    'msg' => sprintf(__('Berhasil %s #%s'), $default_msg, $noref),
                    'status' => 'success',
                );
            } else {
                $result = array(
                    'msg' => sprintf(__('Gagal %s'), $default_msg),
                    'status' => 'error',
                    'data' => $data,
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function _callLastPayment ($leasing_id) {
        $value = $this->getData('first', array(
            
        ));
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(LeasingPayment.payment_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(LeasingPayment.payment_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['LeasingPayment.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['LeasingPayment.vendor_id'] = $vendor_id;
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(LeasingPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if( !empty($type) ) {
            $default_options['conditions']['LeasingPayment.type'] = $type;
        }
        
        return $default_options;
    }

    function getPayment ( $data, $id, $type = 'installment' ) {
        $default_options = array(
            'conditions' => array(
                'LeasingPaymentDetail.leasing_id' => $id,
                'LeasingPayment.type' => $type,
                'LeasingPayment.status' => 1,
                'LeasingPayment.rejected' => 0,
            ),
            'contain' => array(
                'LeasingPayment',
            ),
        );

        $this->virtualFields['grandtotal_installment'] = 'SUM(installment)';
        $this->virtualFields['grandtotal_installment_rate'] = 'SUM(installment_rate)';
        $value = $this->LeasingPaymentDetail->getData('first', $default_options);

        if( !empty($value) ) {
            $data = array_merge($data, $value);
        }

        return $data;
    }
}
?>