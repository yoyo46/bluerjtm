<?php
class PurchaseOrderPayment extends AppModel {
	var $name = 'PurchaseOrderPayment';

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
        'PurchaseOrderPaymentDetail' => array(
            'className' => 'PurchaseOrderPaymentDetail',
            'foreignKey' => 'purchase_order_payment_id',
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
        $this->virtualFields['order'] = 'CASE WHEN PurchaseOrderPayment.transaction_status = \'void\' THEN 2 WHEN PurchaseOrderPayment.transaction_status = \'posting\' THEN 1 ELSE 0 END';
    }

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'PurchaseOrderPayment.order' => 'ASC',
                'PurchaseOrderPayment.status' => 'DESC',
                'PurchaseOrderPayment.created' => 'DESC',
                'PurchaseOrderPayment.id' => 'DESC',
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['PurchaseOrderPayment.status'] = 1;
                $default_options['conditions']['PurchaseOrderPayment.transaction_status <>'] = 'void';
                break;
            case 'unposting':
                $default_options['conditions']['PurchaseOrderPayment.status'] = 1;
                $default_options['conditions']['PurchaseOrderPayment.transaction_status'] = 'unposting';
                break;
            case 'void-active':
                $default_options['conditions']['PurchaseOrderPayment.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['PurchaseOrderPayment.status'] = 0;
                break;
            default:
                $default_options['conditions']['PurchaseOrderPayment.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['PurchaseOrderPayment.branch_id'] = Configure::read('__Site.config_branch_id');
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
                'PurchaseOrderPayment.id' => $id
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
                $default_options['conditions']['DATE_FORMAT(PurchaseOrderPayment.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(PurchaseOrderPayment.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['PurchaseOrderPayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['PurchaseOrderPayment.vendor_id'] = $vendor_id;
        }
        
        return $default_options;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('POPAID-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'PurchaseOrderPayment.nodoc' => 'DESC'
            ),
            'fields' => array(
                'PurchaseOrderPayment.nodoc'
            ),
            'conditions' => array(
                'PurchaseOrderPayment.nodoc LIKE' => $format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['PurchaseOrderPayment']['nodoc'])){
            $str_arr = explode('-', $last_data['PurchaseOrderPayment']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function doSave( $data, $value = false, $id = false ) {
        $msg = __('Gagal melakukan pembayaran PO');

        if( !empty($data) ) {   
            if( empty($id) ){
                $data['PurchaseOrderPayment']['nodoc'] = $this->generateNoId();
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( !empty($flag) ) {
                $flag = $this->PurchaseOrderPaymentDetail->updateAll(array(
                    'PurchaseOrderPaymentDetail.status' => 0,
                ), array(
                    'PurchaseOrderPaymentDetail.purchase_order_payment_id' => $id,
                ));

                if( !empty($flag) ) {
                    $msg = __('Berhasil melakukan pembayaran PO');
                    $this->saveAll($data, array(
                        'deep' => true,
                    ));
                    $this->_callSetJournalPayment($id, $data);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
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
        $transaction_status = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'transaction_status');

        if( $transaction_status == 'posting' ) {
            $vendor_id = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'vendor_id');
            $grandtotal = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'grandtotal');
            $transaction_date = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'transaction_date');
            $nodoc = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'nodoc');
            $coa_id = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'coa_id');

            $vendor = $this->Vendor->getMerge(array(), $vendor_id);
            $vendor_name = $this->filterEmptyField($vendor, 'Vendor', 'name');

            $coaHutangUsaha = $this->User->Coa->CoaSettingDetail->getMerge(array(), 'HutangUsaha', 'CoaSettingDetail.label');
            $hutang_usaha_coa_id = !empty($coaHutangUsaha['CoaSettingDetail']['coa_id'])?$coaHutangUsaha['CoaSettingDetail']['coa_id']:false;

            $this->User->Journal->deleteJournal($id, array(
                'po_payment',
            ));

            $titleJournal = sprintf(__('Pembayaran PO kepada supplier %s '), $vendor_name);
            $titleJournal = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'note', $titleJournal);

            $this->User->Journal->setJournal($grandtotal, array(
                'credit' => $coa_id,
                'debit' => $hutang_usaha_coa_id,
            ), array(
                'date' => $transaction_date,
                'document_id' => $id,
                'title' => $titleJournal,
                'document_no' => $nodoc,
                'type' => 'po_payment',
            ));
        }
    }

    function doDelete( $id, $value, $data ) {
        $result = false;

        if ( !empty($value) ) {
            $nodoc = $this->filterEmptyField($value, 'PurchaseOrderPayment', 'nodoc');
            $grandtotal = $this->filterEmptyField($value, 'PurchaseOrderPayment', 'grandtotal');

            $data['PurchaseOrderPayment']['id'] = $id;
            $data['PurchaseOrderPayment']['transaction_status'] = 'void';
            
            $value = $this->PurchaseOrderPaymentDetail->getMerge($value, $id);
            $details = $this->filterEmptyField($value, 'PurchaseOrderPaymentDetail');

            if( !empty($details) ) {
                foreach ($details as $key => $detail) {
                    $detail_id = $this->filterEmptyField($detail, 'PurchaseOrderPaymentDetail', 'id');
                    $purchase_order_id = $this->filterEmptyField($detail, 'PurchaseOrderPaymentDetail', 'purchase_order_id');
                    $paid = $this->PurchaseOrderPaymentDetail->_callPaidPO($purchase_order_id, $id);

                    if( empty($paid) ) {
                        $status = 'approved';
                    } else {
                        $status = 'half_paid';
                    }

                    $data['PurchaseOrderPaymentDetail'][$key] = array(
                        'PurchaseOrderPaymentDetail' => array(
                            'id' => $detail_id,
                        ),
                        'PurchaseOrder' => array(
                            'id' => $purchase_order_id,
                            'transaction_status' => $status,
                        ),
                    );
                }
            }

            $default_msg = sprintf(__('menghapus pembayaran PO #%s'), $nodoc);

            if($this->saveAll($data, array(
                'deep' => true,
            ))) {
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
                'msg' => __('Gagal menghapus pembayaran PO. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }
}
?>