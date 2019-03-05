<?php
class SpkPayment extends AppModel {
	var $name = 'SpkPayment';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        ),
    );

    var $hasMany = array(
        'SpkPaymentDetail' => array(
            'className' => 'SpkPaymentDetail',
            'foreignKey' => 'spk_payment_id',
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
                'message' => 'Supplier harap dipilih'
            ),
        ),
        'canceled_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl pembatalan harap dipilih'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kas/Bank harap dipilih'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl pembayaran harap dipilih'
            ),
        ),
	);

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['order'] = 'CASE WHEN SpkPayment.transaction_status = \'void\' THEN 2 WHEN SpkPayment.transaction_status = \'posting\' THEN 1 ELSE 0 END';
    }

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkPayment.order' => 'ASC',
                'SpkPayment.status' => 'DESC',
                'SpkPayment.id' => 'DESC',
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['SpkPayment.status'] = 1;
                $default_options['conditions']['SpkPayment.transaction_status <>'] = 'void';
                break;
            case 'unposting':
                $default_options['conditions']['SpkPayment.status'] = 1;
                $default_options['conditions']['SpkPayment.transaction_status'] = 'unposting';
                break;
            case 'void-active':
                $default_options['conditions']['SpkPayment.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['SpkPayment.status'] = 0;
                break;
            default:
                $default_options['conditions']['SpkPayment.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['SpkPayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
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

    function getMerge( $data, $id ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                'SpkPayment.id' => $id
            ),
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(SpkPayment.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(SpkPayment.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['SpkPayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['SpkPayment.vendor_id'] = $vendor_id;
        }
        
        return $default_options;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('SPKPAID-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'SpkPayment.nodoc' => 'DESC'
            ),
            'fields' => array(
                'SpkPayment.nodoc'
            ),
            'conditions' => array(
                'SpkPayment.nodoc LIKE' => $format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['SpkPayment']['nodoc'])){
            $str_arr = explode('-', $last_data['SpkPayment']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function doSave( $data, $value = false, $id = false ) {
        $msg = __('Gagal melakukan pembayaran SPK');

        if( !empty($data) ) {   
            if( empty($id) ){
                $data['SpkPayment']['nodoc'] = $this->generateNoId();
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( !empty($flag) ) {
                $flag = $this->SpkPaymentDetail->updateAll(array(
                    'SpkPaymentDetail.status' => 0,
                ), array(
                    'SpkPaymentDetail.spk_payment_id' => $id,
                ));

                if( !empty($flag) ) {
                    $msg = __('Berhasil melakukan pembayaran SPK');
                    $this->saveAll($data, array(
                        'deep' => true,
                    ));
                    $this->_callSetJournalPayment($id, $data);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'document_id' => $id,
                            'activity' => $msg,
                            'old_data' => $value,
                        ),
                        'data' => $data,
                    );
                } else {
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'document_id' => $id,
                            'activity' => $msg,
                            'old_data' => $value,
                            'error' => 1,
                        ),
                        'data' => $data,
                    );
                }
            } else {
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'document_id' => $id,
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                    'data' => $data,
                );
            }
        } else {
            $result['data'] = $value;
        }

        return $result;
    }

    function _callSetJournalPayment ( $id, $data ) {
        $transaction_status = $this->filterEmptyField($data, 'SpkPayment', 'transaction_status');

        if( $transaction_status == 'posting' ) {
            $vendor_id = $this->filterEmptyField($data, 'SpkPayment', 'vendor_id');
            $grandtotal = $this->filterEmptyField($data, 'SpkPayment', 'grandtotal');
            $transaction_date = $this->filterEmptyField($data, 'SpkPayment', 'transaction_date');
            $nodoc = $this->filterEmptyField($data, 'SpkPayment', 'nodoc');
            $coa_id = $this->filterEmptyField($data, 'SpkPayment', 'coa_id');
            $cogs_id = $this->filterEmptyField($data, 'SpkPayment', 'cogs_id');

            $vendor = $this->Vendor->getMerge(array(), $vendor_id);
            $vendor_name = $this->filterEmptyField($vendor, 'Vendor', 'name');

            $coaHutangUsaha = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'HutangProduct', 'CoaSettingDetail.label');
            $hutang_usaha_coa_id = !empty($coaHutangUsaha['CoaSettingDetail']['coa_id'])?$coaHutangUsaha['CoaSettingDetail']['coa_id']:false;

            $this->User->Journal->deleteJournal($id, array(
                'spk_payment',
            ));

            $titleJournal = sprintf(__('Pembayaran SPK kepada supplier %s '), $vendor_name);
            $titleJournal = $this->filterEmptyField($data, 'SpkPayment', 'note', $titleJournal);

            $this->User->Journal->setJournal($grandtotal, array(
                'credit' => $coa_id,
                'debit' => $hutang_usaha_coa_id,
            ), array(
                'cogs_id' => $cogs_id,
                'date' => $transaction_date,
                'document_id' => $id,
                'title' => $titleJournal,
                'document_no' => $nodoc,
                'type' => 'spk_payment',
            ));
        }
    }

    function doDelete( $id, $value, $data ) {
        $result = false;

        if ( !empty($value) ) {
            $nodoc = $this->filterEmptyField($value, 'SpkPayment', 'nodoc');
            $grandtotal = $this->filterEmptyField($value, 'SpkPayment', 'grandtotal');
            $transaction_status = $this->filterEmptyField($value, 'SpkPayment', 'transaction_status');

            $data['SpkPayment']['id'] = $id;
            $data['SpkPayment']['transaction_status'] = 'void';
            
            $value = $this->SpkPaymentDetail->getMerge($value, $id);
            $details = $this->filterEmptyField($value, 'SpkPaymentDetail');

            if( !empty($details) ) {
                foreach ($details as $key => $detail) {
                    $detail_id = $this->filterEmptyField($detail, 'SpkPaymentDetail', 'id');
                    $spk_id = $this->filterEmptyField($detail, 'SpkPaymentDetail', 'spk_id');
                    $draft_paid = $this->SpkPaymentDetail->_callPaidSpk($spk_id, $id);

                    if( empty($draft_paid) ) {
                        $draft_status = 'none';
                    } else {
                        $draft_status = 'half_paid';
                    }

                    $data['SpkPaymentDetail'][$key] = array(
                        'SpkPaymentDetail' => array(
                            'id' => $detail_id,
                        ),
                        'Spk' => array(
                            'id' => $spk_id,
                            'draft_payment_status' => $draft_status,
                        ),
                    );

                    if( $transaction_status == 'posting' ) {
                        $paid = $this->SpkPaymentDetail->_callPaidSpk($spk_id, $id, 'paid-posting');

                        if( empty($paid) ) {
                            $status = 'none';
                        } else {
                            $status = 'half_paid';
                        }

                        $data['SpkPaymentDetail'][$key]['Spk']['payment_status'] = $status;
                    }
                }
            }

            $default_msg = sprintf(__('menghapus pembayaran SPK #%s'), $nodoc);

            if($this->saveAll($data, array(
                'deep' => true,
            ))) {
                $msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'document_id' => $id,
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
                        'document_id' => $id,
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => __('Gagal menghapus pembayaran SPK. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }
}
?>