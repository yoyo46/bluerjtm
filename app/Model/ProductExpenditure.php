<?php
class ProductExpenditure extends AppModel {
	var $name = 'ProductExpenditure';

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Staff' => array(
            'className' => 'Employe',
            'foreignKey' => 'staff_id',
        ),
        'Spk' => array(
            'className' => 'Spk',
            'foreignKey' => 'document_id',
        ),
    );

    var $hasMany = array(
        'ProductExpenditureDetail' => array(
            'className' => 'ProductExpenditureDetail',
            'foreignKey' => 'product_expenditure_id',
        ),
        'DocumentAuth' => array(
            'className' => 'DocumentAuth',
            'foreignKey' => 'document_id',
            'conditions' => array(
                'DocumentAuth.document_type' => array( 'product_expenditure', 'product_expenditure_void' ),
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
        'staff_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih karyawan yg mengeluarkan barang'
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
                'message' => 'No. SPK harap dipilih'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl keluar harap dipilih'
            ),
            'validateDate' => array(
                'rule' => array('validateDate'),
                'message' => 'Tgl pengeluaran tidak bole lebih kecil dari tgl SPK'
            ),
        ),
	);

    function validateDate () {
        $transaction_date = Common::hashEmptyField($this->data, 'ProductExpenditure.transaction_date');
        $spk_date = Common::hashEmptyField($this->data, 'ProductExpenditure.spk_date');
        
        if( !empty($spk_date) ) {
            if( $transaction_date < $spk_date ) {
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
        $special_id = isset($elements['special_id'])?$elements['special_id']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductExpenditure.status' => 'DESC',
                'ProductExpenditure.created' => 'DESC',
                'ProductExpenditure.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductExpenditure.status'] = 1;
                break;
            case 'pending':
                $default_options['conditions']['ProductExpenditure.transaction_status'] = array( 'unposting', 'revised' );
                $default_options['conditions']['ProductExpenditure.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['ProductExpenditure.status'] = 0;
                break;
            case 'commit-void':
                $default_options['conditions']['OR'] = array(
                    array(
                        'ProductExpenditure.transaction_status' => 'posting',
                        'ProductExpenditure.status' => 1,
                    ),
                    array(
                        'ProductExpenditure.transaction_status' => 'void',
                        'ProductExpenditure.status' => 0,
                    ),
                );
                break;
            case 'unreceipt_draft':
                $default_options['conditions']['ProductExpenditure.status'] = 1;

                if( !empty($special_id) ) {
                    $default_options['conditions']['OR']['ProductExpenditure.id'] = $special_id;
                    $default_options['conditions']['OR']['ProductExpenditure.draft_receipt_status'] = array( 'none', 'half' );
                } else {
                    $default_options['conditions']['ProductExpenditure.draft_receipt_status'] = array( 'none', 'half' );
                }
                break;
            case 'untransfer_draft':
                $default_options['conditions']['ProductExpenditure.status'] = 1;
                $default_options['conditions']['ProductExpenditure.document_type'] = array( 'wht' );
                $default_options['conditions']['ProductExpenditure.transaction_status'] = 'posting';
                $default_options['conditions']['Spk.to_branch_id'] = Configure::read('__Site.config_branch_id');
                $default_options['conditions']['Spk.document_type'] = 'wht';

                if( !empty($special_id) ) {
                    $default_options['conditions']['OR']['ProductExpenditure.id'] = $special_id;
                    $default_options['conditions']['OR']['ProductExpenditure.draft_receipt_status'] = array( 'none', 'half' );
                } else {
                    $default_options['conditions']['ProductExpenditure.draft_receipt_status'] = array( 'none', 'half' );
                }
                
                $default_options['contain'][] = 'Spk';
                break;
            case 'unproduction_draft':
                $default_options['conditions']['ProductExpenditure.status'] = 1;
                $default_options['conditions']['ProductExpenditure.document_type'] = array( 'production' );
                $default_options['conditions']['ProductExpenditure.transaction_status'] = 'posting';
                $default_options['conditions']['Spk.document_type'] = 'production';

                if( !empty($special_id) ) {
                    $default_options['conditions']['OR']['ProductExpenditure.id'] = $special_id;
                    $default_options['conditions']['OR']['ProductExpenditure.draft_receipt_status'] = array( 'none', 'half' );
                } else {
                    $default_options['conditions']['Spk.draft_receipt_status'] = array( 'none', 'half' );
                }
                
                $default_options['contain'][] = 'Spk';
                break;
            case 'confirm':
                $default_options['conditions']['ProductExpenditure.transaction_status'] = array( 'approved', 'paid', 'half_paid', 'posting' );
                $default_options['conditions']['ProductExpenditure.status'] = 1;
                break;
            default:
                $default_options['conditions']['ProductExpenditure.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductExpenditure.branch_id'] = Configure::read('__Site.config_branch_id');
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

    function getMerge( $data, $id, $fieldName = 'ProductExpenditure.id' ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                $fieldName => $id
            ),
        ), array(
            'branch' => false,
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    function _callExpenditureDocument( $data ) {
        $document_id = $this->filterEmptyField($data, 'ProductExpenditure', 'document_id');
        $transaction_status = $this->filterEmptyField($data, 'ProductExpenditure', 'transaction_status');
        $document_type = $this->filterEmptyField($data, 'ProductExpenditure', 'document_type');
        $transaction_date = $this->filterEmptyField($data, 'ProductExpenditure', 'transaction_date');

        $detail = $this->Spk->SpkProduct->getData('first', array(
            'conditions' => array(
                'SpkProduct.spk_id' => $document_id,
            ),
        ), array(
            'status' => 'unexit',
        ));

        if( !empty($detail) ) {
            $status = 'half';
        } else {
            $status = 'full';
        }

        $settings = $this->callSettingGeneral('spk_internal_status');
        $spk_internal_status = $this->filterEmptyField($settings, 'spk_internal_status');

        $this->Spk->id = $document_id;
        $this->Spk->set('draft_document_status', $status);

        if( $transaction_status == 'posting' && $status == 'full' ) {
            switch ($document_type) {
                case 'wht':
                    $this->Spk->set('transaction_status', 'out');
                    break;
                
                default:
                    // if( $spk_internal_status == 'closed_expenditured' ) {
                        $this->Spk->set('transaction_status', 'closed');
                        // $this->Spk->set('complete_date', $transaction_date);
                    // } else {
                        // $this->Spk->set('transaction_status', 'finish');
                    // }
                    break;
            }
        }

        $this->Spk->save();
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('pengeluaran barang');

        if ( !empty($data) ) {
            // $document_id = Common::hashEmptyField($data, 'ProductExpenditure.document_id');
            // $spk_product_id = Set::extract('/ProductExpenditureDetail/ProductExpenditureDetail/SpkProduct/id', $data);
            $flag = $this->saveAll($data, array(
                'deep' => true,
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                $this->ProductExpenditureDetail->deleteAll(array(
                    'ProductExpenditureDetail.product_expenditure_id' => $id,
                ));
                // $this->ProductExpenditureDetail->SpkProduct->updateAll(array(
                //     'SpkProduct.draft_document_status' => "'none'",
                // ), array(
                //     'SpkProduct.spk_id' => $document_id,
                //     'SpkProduct.id NOT' => $spk_product_id,
                // ));

                $flag = $this->saveAll($data, array(
                    'deep' => true,
                ));
                
                if( !empty($flag) ) {
                    $id = $this->id;

                    $this->_callExpenditureDocument( $data );
                    
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
        $nospk = !empty($data['named']['nospk'])?$data['named']['nospk']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['ProductExpenditure.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($nospk) ) {
            $default_options['conditions']['Spk.nodoc LIKE'] = '%'.$nospk.'%';
            $default_options['contain'][] = 'Spk';
        }
        if( !empty($status) ) {
            $default_options['conditions']['ProductExpenditure.transaction_status'] = $status;
        }
        if( !empty($nopol) ) {
            $default_options['conditions']['Spk.nopol LIKE'] = '%'.$nopol.'%';
            $default_options['contain'][] = 'Spk';
        }
        
        return $default_options;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductExpenditure.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $value = $this->getMergeList($value, array(
                'contain' => array(
                    'ProductExpenditureDetail',
                ),
            ));
            $spk_product_id = Set::extract('/ProductExpenditureDetail/ProductExpenditureDetail/spk_product_id', $value);

            $document_id = $this->filterEmptyField($value, 'ProductExpenditure', 'document_id');
            $nodoc = $this->filterEmptyField($value, 'ProductExpenditure', 'nodoc');
            $document_type = $this->filterEmptyField($value, 'ProductExpenditure', 'document_type');
            $default_msg = sprintf(__('membatalkan pengeluaran barang #%s'), $id);

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');

            $value = $this->getMergeList($value, array(
                'contain' => array(
                    'ProductExpenditureDetail' => array(
                        'contain' => array(
                            'ProductExpenditureDetailSerialNumber',
                            'ProductHistory' => array(
                                'conditions' => array(
                                    'ProductHistory.transaction_type' => 'product_expenditure',
                                ),
                            ),
                        ),
                    ),
                ),
            ));

            $branch_id = Common::hashEmptyField($value, 'ProductExpenditure.branch_id');
            $transaction_date = Common::hashEmptyField($value, 'ProductExpenditure.transaction_date');
            $product_histories = Set::extract('/ProductExpenditureDetail/ProductHistory', $value);
            $product_history_id = Set::extract('/ProductExpenditureDetail/ProductHistory/ProductHistory/id', $value);
            $product_serial_numbers = Set::extract('/ProductExpenditureDetail/ProductExpenditureDetailSerialNumber', $value);

            if( $this->save() ) {
                if( $document_type == 'po' ) {
                    $this->PurchaseOrder->id = $document_id;
                    $this->PurchaseOrder->set('transaction_status', 'none');
                    $this->PurchaseOrder->save();
                } else {
                    $this->Spk->id = $document_id;
                    $this->Spk->set('transaction_status', 'open');
                    $this->Spk->set('draft_document_status', 'none');
                    $this->Spk->save();

                    $this->Spk->SpkProduct->updateAll(array(
                        'SpkProduct.draft_document_status' => "'none'",
                    ), array(
                        'SpkProduct.id' => $spk_product_id,
                    ));
                }

                $dataHistory = array();
                $dataStock = array();

                if( !empty($product_serial_numbers) ) {
                    foreach ($product_serial_numbers as $key => $detail) {
                        $arrDetail = Common::hashEmptyField($detail, 'ProductExpenditureDetailSerialNumber');
                        $product_id = Common::hashEmptyField($arrDetail, 'ProductExpenditureDetailSerialNumber.product_id');
                        $qty = Common::hashEmptyField($arrDetail, 'ProductExpenditureDetailSerialNumber.qty');
                        $price = Common::hashEmptyField($arrDetail, 'ProductExpenditureDetailSerialNumber.price');
                        $current_serial_number = Common::hashEmptyField($arrDetail, 'ProductExpenditureDetailSerialNumber.serial_number');

                        $checkStock = $this->ProductExpenditureDetail->ProductHistory->ProductStock->getData('first', array(
                            'conditions' => array(
                                'ProductStock.product_id' => $product_id,
                                'ProductStock.serial_number' => $current_serial_number,
                                'ProductStock.price' => $price,
                            ),
                        ), array(
                            'status' => false,
                            'branch' => false,
                        ));
                        $checkStock = $this->ProductExpenditureDetail->ProductHistory->ProductStock->getMergeList($checkStock, array(
                            'contain' => array(
                                'ProductHistory' => array(
                                    'primaryKey' => 'id',
                                    'foreignKey' => 'product_history_id',
                                    'elements' => array(
                                        'status' => false,
                                        'branch' => false,
                                    ),
                                ),
                            ),
                        ));
                        $transaction_type = Common::hashEmptyField($checkStock, 'ProductHistory.transaction_type');

                        $productHistory = array(
                            'branch_id' => $branch_id,
                            'product_id' => $product_id,
                            'transaction_id' => Common::hashEmptyField($arrDetail, 'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id'),
                            'transaction_type' => 'product_expenditure_void',
                            'transaction_date' => $transaction_date,
                            'qty' => $qty,
                            'price' => $price,
                            'type' => 'in',
                        );

                        if ($transaction_type == 'product_adjustment_plus') {
                            $arrHistory['ProductHistory'] = $productHistory;
                            $arrStock['ProductStock'] = array(
                                'id' => Common::hashEmptyField($checkStock, 'ProductStock.id'),
                                'product_history_id' => Common::hashEmptyField($checkStock, 'ProductStock.product_history_id'),
                                'product_id' => $product_id,
                                'qty_use' => Common::hashEmptyField($checkStock, 'ProductStock.qty_use', 0) - $qty,
                                'status' => true,
                            );

                            $dataStock[] = $arrStock;
                        } else {
                            $productHistory['ProductStock'] = array(
                                array(
                                    'product_id' => $product_id,
                                    'branch_id' => $branch_id,
                                    'transaction_date' => $transaction_date,
                                    'qty' => $qty,
                                    'price' => $price,
                                    'serial_number' => Common::hashEmptyField($arrDetail, 'ProductExpenditureDetailSerialNumber.serial_number'),
                                ),
                            );
                            $arrHistory['ProductHistory'] = $productHistory;
                        }

                        $dataHistory[] = $arrHistory;
                    }
                } else if( !empty($product_histories) ) {
                    foreach ($product_histories as $key => $history) {
                        $arrHistory = Common::hashEmptyField($history, 'ProductHistory');
                        $product_id = Common::hashEmptyField($arrHistory, 'ProductHistory.product_id');
                        $history_id = Common::hashEmptyField($arrHistory, 'ProductHistory.ProductHistory.id');

                        $arrHistory = Common::_callUnset($arrHistory, array(
                            'ProductHistory' => array(
                                'id',
                                'created',
                                'modified',
                                'status',
                            ),
                        ));
                        $arrHistory['ProductHistory']['transaction_type'] = 'product_expenditure_void';
                        $arrHistory['ProductHistory']['type'] = 'in';
                        $arrHistory['ProductHistory']['ProductStock'] = array(
                            'product_id' => $product_id,
                            'product_history_id' => $history_id,
                            'branch_id' => Common::hashEmptyField($arrHistory, 'ProductHistory.branch_id'),
                            'transaction_date' => Common::hashEmptyField($arrHistory, 'ProductHistory.transaction_date'),
                            'qty' => Common::hashEmptyField($arrHistory, 'ProductHistory.qty'),
                            'price' => Common::hashEmptyField($arrHistory, 'ProductHistory.price'),
                            'serial_number' => sprintf('%s-%s', Common::getNoRef($product_id), date('ymdHis')),
                        );

                        $dataHistory[] = $arrHistory;
                    }
                }

                if( !empty($dataHistory) ) {
                    $this->ProductExpenditureDetail->ProductHistory->saveAll($dataHistory, array(
                        'deep' => true,
                    ));
                }
                if( !empty($dataStock) ) {
                    $this->ProductExpenditureDetail->ProductHistory->ProductStock->saveAll($dataStock, array(
                        'deep' => true,
                    ));
                }
                if( !empty($product_history_id) ) {
                    $this->ProductExpenditureDetail->ProductHistory->updateAll(array(
                        'ProductHistory.status' => 0,
                    ), array(
                        'ProductHistory.id' => $product_history_id,
                    ));
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
                'msg' => __('Gagal menghapus pengeluaran barang. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('SN-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'ProductExpenditure.nodoc' => 'DESC'
            ),
            'fields' => array(
                'ProductExpenditure.nodoc'
            ),
            'conditions' => array(
                'ProductExpenditure.nodoc LIKE' => '%'.$format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['ProductExpenditure']['nodoc'])){
            $str_arr = explode('-', $last_data['ProductExpenditure']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function _callVendors ( $status = 'unpaid', $id = false ) {
        $this->bindModel(array(
            'belongsTo' => array(
                'Vendor' => array(
                    'className' => 'Vendor',
                    'foreignKey' => false,
                    'conditions' => array(
                        'Vendor.id = Spk.vendor_id',
                    ),
                ),
            )
        ), false);

        return $this->getData('list', array(
                'contain' => array(
                    'Spk',
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
                'branch' => false,
            ));
    }
}
?>