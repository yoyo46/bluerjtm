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
        'Spk' => array(
            'className' => 'Spk',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'ProductReceipt.document_type' => 'spk',
            ),
        ),
        'ProductExpenditure' => array(
            'className' => 'ProductExpenditure',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'ProductReceipt.document_type' => 'wht',
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
        'ProductReceiptDetailSerialNumber' => array(
            'className' => 'ProductReceiptDetailSerialNumber',
            'foreignKey' => 'product_receipt_id',
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
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Supplier harap dipilih'
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
            'checkDocDate' => array(
                'rule' => array('checkDocDate'),
            ),
            'validateDate' => array(
                'rule' => array('validateDate'),
            ),
        ),
	);

    function checkDocDate () {
        $invalid_date = Common::hashEmptyField($this->data, 'ProductReceipt.invalid_date', null, array(
            'isset' => true,
        ));

        if( !empty($invalid_date) ) {
            return false;
        } else {
            return true;
        }
    }

    function validateDate () {
        $transaction_date = Common::hashEmptyField($this->data, 'ProductReceipt.transaction_date');
        $reference_date = Common::hashEmptyField($this->data, 'ProductReceipt.reference_date');
        
        if( !empty($reference_date) ) {
            if( $transaction_date < $reference_date ) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductReceipt.status' => 'DESC',
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
            case 'posting':
                $default_options['conditions']['ProductReceipt.transaction_status NOT'] = array( 'unposting', 'revised', 'void' );
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

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $fieldName = 'ProductReceipt.id' ){
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
        $document_id = $this->filterEmptyField($data, 'ProductReceipt', 'document_id');
        $document_type = $this->filterEmptyField($data, 'ProductReceipt', 'document_type');
        $transaction_status = $this->filterEmptyField($data, 'ProductReceipt', 'transaction_status');
        $transaction_date = $this->filterEmptyField($data, 'ProductReceipt', 'transaction_date');

        switch ($document_type) {
            case 'spk':
                $dataDetail = $this->Spk->SpkProduct->getData('first', array(
                    'conditions' => array(
                        'SpkProduct.spk_id' => $document_id,
                    ),
                ), array(
                    'status' => 'unreceipt',
                ));

                if( !empty($dataDetail) ) {
                    $receipt_status = 'half';
                } else {
                    $receipt_status = 'full';
                }

                $settings = $this->callSettingGeneral('spk_internal_status');
                $spk_internal_status = $this->filterEmptyField($settings, 'spk_internal_status');

                $this->Spk->id = $document_id;
                $this->Spk->set('draft_receipt_status', $receipt_status);

                if( $transaction_status == 'posting' ) {
                    $this->Spk->set('receipt_status', $receipt_status);

                    if( $receipt_status == 'full' ) {
                        $this->Spk->set('transaction_status', 'closed');
                        // $this->Spk->set('complete_date', $transaction_date);
                    }
                }

                $this->Spk->save();
                break;
            case 'wht':
                $dataDetail = $this->ProductExpenditure->ProductExpenditureDetail->getData('first', array(
                    'conditions' => array(
                        'ProductExpenditureDetail.product_expenditure_id' => $document_id,
                    ),
                ), array(
                    'status' => 'unreceipt',
                ));

                $expenditure = $this->ProductExpenditure->getData('first', array(
                    'conditions' => array(
                        'ProductExpenditure.id' => $document_id,
                    ),
                ), array(
                    'branch' => false,
                ));
                $expenditure = $this->ProductExpenditure->getMergeList($expenditure, array(
                    'contain' => array(
                        'Spk' => array(
                            'elements' => array(
                                'branch' => false,
                            ),
                        ),
                    ),
                ));
                $spk_id = $this->filterEmptyField($expenditure, 'ProductExpenditure', 'document_id');
                $spk_status = $this->filterEmptyField($expenditure, 'Spk', 'transaction_status');

                if( !empty($dataDetail) ) {
                    $receipt_status = 'half';
                } else {
                    $receipt_status = 'full';
                }

                $dataSave = array(
                    'ProductExpenditure' => array(
                        'id' => $document_id,
                        'draft_receipt_status' => $receipt_status,
                    ),
                );

                if( $transaction_status == 'posting' ) {
                    $dataSave['ProductExpenditure']['receipt_status'] = $receipt_status;

                    if( $receipt_status == 'full' && $spk_status == 'out' ) {
                        $dataSave['Spk']['id'] = $spk_id;
                        $dataSave['Spk']['transaction_status'] = 'closed';
                        // $dataSave['Spk']['complete_date'] = $transaction_date;
                    }
                }

                $this->ProductExpenditure->saveAll($dataSave);
                break;
            case 'production':
                $dataDetail = $this->Spk->SpkProduction->getData('first', array(
                    'conditions' => array(
                        'SpkProduction.spk_id' => $document_id,
                    ),
                ), array(
                    'status' => 'unreceipt',
                ));

                if( !empty($dataDetail) ) {
                    $receipt_status = 'half';
                } else {
                    $receipt_status = 'full';
                }

                $this->Spk->id = $document_id;
                $this->Spk->set('draft_receipt_status', $receipt_status);

                if( $transaction_status == 'posting' ) {
                    $this->Spk->set('receipt_status', $receipt_status);

                    if( $receipt_status == 'full' ) {
                        $this->Spk->set('transaction_status', 'closed');
                        // $this->Spk->set('complete_date', $transaction_date);
                    }
                }

                $this->Spk->save();
                break;
            default:
                $purchaseOrderDetail = $this->PurchaseOrder->PurchaseOrderDetail->getData('first', array(
                    'conditions' => array(
                        'PurchaseOrderDetail.purchase_order_id' => $document_id,
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
        $defaul_msg = __('penerimaan barang');

        if ( !empty($data) ) {
            $flag = $this->saveAll($data, array(
                'deep' => true,
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                if( empty($id) ){
                    $data['ProductReceipt']['nodoc'] = $this->generateNoId();
                }

                $this->ProductReceiptDetail->deleteAll(array(
                    'ProductReceiptDetail.product_receipt_id' => $id,
                ));

                $flag = $this->saveAll($data, array(
                    'deep' => true,
                ));
                
                if( !empty($flag) ) {
                    $id = $this->id;
                    
                    $session_id = $this->filterEmptyField($data, 'ProductReceipt', 'session_id');
                    $this->ProductReceiptDetailSerialNumber->updateAll(array(
                        'ProductReceiptDetailSerialNumber.product_receipt_id' => $id,
                        'ProductReceiptDetailSerialNumber.active' => 1,
                    ),array(
                        'ProductReceiptDetailSerialNumber.session_id' => $session_id,
                        'ProductReceiptDetailSerialNumber.status' => 1,
                    ));

                    $this->_callReceiptDocument( $data );
                    
                    $defaul_msg = sprintf(__('Berhasil melakukan %s'), $defaul_msg);
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
                    $defaul_msg = sprintf(__('Gagal melakukan %s'), $defaul_msg);
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
                $defaul_msg = sprintf(__('Gagal melakukan %s'), $defaul_msg);
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
        $nodocref = !empty($data['named']['nodocref'])?$data['named']['nodocref']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

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
        if( !empty($nodocref) ) {
            $default_options['conditions']['ProductReceipt.document_number LIKE'] = '%'.$nodocref.'%';
        }
        if( !empty($status) ) {
            $default_options['conditions']['ProductReceipt.transaction_status'] = $status;
        }
        
        return $default_options;
    }

    function doDelete( $id, $type = null ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductReceipt.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $document_id = $this->filterEmptyField($value, 'ProductReceipt', 'document_id');
            $nodoc = $this->filterEmptyField($value, 'ProductReceipt', 'nodoc');
            $document_type = $this->filterEmptyField($value, 'ProductReceipt', 'document_type');

            switch ($type) {
                case 'void':
                    $default_msg = sprintf(__('membatalkan penerimaan barang #%s'), $id);
                    break;
                
                default:
                    $default_msg = sprintf(__('menghapus penerimaan barang #%s'), $id);
                    break;
            }

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');
            
            $value = $this->getMergeList($value, array(
                'contain' => array(
                    'ProductReceiptDetail' => array(
                        'contain' => array(
                            'ProductHistory' => array(
                                'conditions' => array(
                                    'ProductHistory.transaction_type' => 'product_receipt',
                                ),
                                'contain' => array(
                                    'ProductStock',
                                ),
                            ),
                        ),
                    ),
                ),
            ));

            $product_history_id = Set::extract('/ProductReceiptDetail/ProductHistory/id', $value);
            $product_stock_id = Set::extract('/ProductReceiptDetail/ProductHistory/ProductStock/ProductStock/id', $value);
            $spk_product_id = Set::extract('/ProductReceiptDetail/ProductReceiptDetail/document_detail_id', $value);
            $qty_use = Set::extract('/ProductReceiptDetail/ProductHistory/ProductStock/ProductStock/qty_use', $value);
            $qty_use = array_filter($qty_use);

            $details = Common::hashEmptyField($value, 'ProductReceiptDetail');

            if( $this->save() ) {
                $defaultOptions = array(
                    'ProductReceipt.id <>' => $id,
                    'ProductReceipt.document_id' => $document_id,
                    'ProductReceipt.document_type' => $document_type,
                );
                $receiptProduct = $this->getData('first', array(
                    'conditions' => $defaultOptions,
                ), array(
                    'status' => 'posting',
                ));
                $draftReceiptProduct = $this->getData('first', array(
                    'conditions' => $defaultOptions,
                ), array(
                    'status' => 'pending',
                ));

                if( !empty($receiptProduct['ProductReceipt']['id']) ) {
                    $receipt_status = 'half';
                } else {
                    $receipt_status = 'none';
                }
                if( !empty($draftReceiptProduct['ProductReceipt']['id']) ) {
                    $draft_receipt_status = 'half';
                } else {
                    $draft_receipt_status = $receipt_status;
                }

                switch ($document_type) {
                    case 'spk':
                        $this->Spk->id = $document_id;
                        $this->Spk->set('receipt_status', $receipt_status);
                        $this->Spk->set('draft_receipt_status', $draft_receipt_status);
                        $this->Spk->set('transaction_status', 'open');
                        $this->Spk->set('draft_document_status', 'none');
                        $this->Spk->save();

                        $modelDetail = $this->Spk->SpkProduct;

                        // $this->Spk->SpkProduct->updateAll(array(
                        //     'SpkProduct.document_status' => "'none'",
                        //     'SpkProduct.receipt_status' => "'none'",
                        //     'SpkProduct.draft_document_status' => "'none'",
                        // ), array(
                        //     'SpkProduct.id' => $spk_product_id,
                        // ));
                        break;
                    case 'wht':
                        $value = $this->ProductExpenditure->getMerge($value, $document_id);
                        $value = $this->ProductExpenditure->getMergeList($value, array(
                            'contain' => array(
                                'Spk',
                            ),
                        ));
                        $spk_id = $this->filterEmptyField($value, 'ProductExpenditure', 'document_id');
                        $spk_status = $this->filterEmptyField($value, 'Spk', 'transaction_status');

                        if( $spk_status == 'closed' ) {
                            $transaction_status = 'out';
                        } else {
                            $transaction_status = 'open';
                        }

                        $dataSave = array(
                            'ProductExpenditure' => array(
                                'id' => $document_id,
                                'receipt_status' => $receipt_status,
                                'draft_receipt_status' => $draft_receipt_status,
                            ),
                            'Spk' => array(
                                'Spk.id' => $spk_id,
                                'Spk.transaction_status' => $transaction_status,
                            ),
                        );

                        $this->ProductExpenditure->saveAll($dataSave);

                        $modelDetail = $this->ProductExpenditure->ProductExpenditureDetail;
                        break;
                    case 'production':
                        $this->Spk->id = $document_id;
                        $this->Spk->set('receipt_status', $receipt_status);
                        $this->Spk->set('draft_receipt_status', $draft_receipt_status);
                        $this->Spk->save();
                        
                        $modelDetail = $this->Spk->SpkProduction;
                        break;
                    default:
                        $this->PurchaseOrder->id = $document_id;
                        $this->PurchaseOrder->set('receipt_status', $receipt_status);
                        $this->PurchaseOrder->set('draft_receipt_status', $draft_receipt_status);
                        $this->PurchaseOrder->save();
                        
                        $modelDetail = $this->PurchaseOrder->PurchaseOrderDetail;
                        break;
                }

                if( !empty($details) ) {
                    foreach ($details as $key => $val) {
                        $product_id = Common::hashEmptyField($val, 'ProductReceiptDetail.product_id');
                        $document_detail_id = Common::hashEmptyField($val, 'ProductReceiptDetail.document_detail_id');

                        $optionsDetail = $this->getData('paginate', array(
                            'conditions' => array(
                                'ProductReceiptDetail.product_receipt_id <>' => $id,
                                'ProductReceiptDetail.document_detail_id' => $document_detail_id,
                                'ProductReceiptDetail.product_id' => $product_id,
                            ),
                            'contain' => array(
                                'ProductReceipt',
                            ),
                        ));
                        $dataDetail = $this->ProductReceiptDetail->find('first', $optionsDetail);

                        if( !empty($dataDetail['ProductReceiptDetail']['id']) ) {
                            $receipt_product_status = 'half';
                        } else {
                            $receipt_product_status = 'none';
                        }

                        $modelDetail->id = $document_detail_id;
                        $modelDetail->set('receipt_status', $receipt_product_status);

                        switch ($document_type) {
                            case 'spk':
                                $modelDetail->set('draft_receipt_status', $draft_receipt_status);
                                $modelDetail->set('transaction_status', 'open');
                                $modelDetail->set('draft_document_status', 'open');
                                break;
                        }

                        $modelDetail->save();
                    }
                }

                if( empty($qty_use) ) {
                    if( !empty($product_history_id) ) {
                        $this->ProductReceiptDetail->ProductHistory->updateAll(array(
                            'ProductHistory.status' => false,
                        ), array(
                            'ProductHistory.id' => $product_history_id,
                        ));
                    }
                    if( !empty($product_stock_id) ) {
                        $this->ProductReceiptDetail->Product->ProductStock->updateAll(array(
                            'status' => false,
                        ), array(
                            'ProductStock.id' => $product_stock_id,
                        ));
                    }
                }

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