<?php
class ProductAdjustment extends AppModel {
	var $name = 'ProductAdjustment';

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
    );

    var $hasMany = array(
        'ProductAdjustmentDetail' => array(
            'className' => 'ProductAdjustmentDetail',
            'foreignKey' => 'product_adjustment_id',
        ),
    );

	var $validate = array(
        'nodoc' => array(
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'No. Dokumen telah terdaftar',
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl harap dipilih'
            ),
        ),
	);

    function beforeSave( $options = array() ) {
        $generate_nodoc = Common::hashEmptyField($this->data, 'ProductAdjustment.generate_nodoc');

        if( !empty($generate_nodoc) ) {
            $this->data = Hash::insert($this->data, 'ProductAdjustment.nodoc', $this->generateNoDoc());
        }
    }

    function generateNoDoc(){
        $default_id = 1;
        $format_id = sprintf('PA-%s-%s-', date('Y'), date('m'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'ProductAdjustment.nodoc' => 'DESC'
            ),
            'fields' => array(
                'ProductAdjustment.nodoc'
            )
        ), array(
            'status' => 'all',
        ));

        if(!empty($last_data['ProductAdjustment']['nodoc'])){
            $str_arr = explode('-', $last_data['ProductAdjustment']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }

        $id = str_pad($default_id, 4,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductAdjustment.status' => 'DESC',
                'ProductAdjustment.created' => 'DESC',
                'ProductAdjustment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductAdjustment.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['ProductAdjustment.status'] = 0;
                break;
            default:
                $default_options['conditions']['ProductAdjustment.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductAdjustment.branch_id'] = Configure::read('__Site.config_branch_id');
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ProductAdjustment.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ProductAdjustment.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['ProductAdjustment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($note) ) {
            $default_options['conditions']['ProductAdjustment.note LIKE'] = '%'.$note.'%';
        }
        if( !empty($status) ) {
            switch ($status) {
                case 'unposting':
                    $default_options['conditions']['ProductAdjustment.status'] = 1;
                    $default_options['conditions']['ProductAdjustment.transaction_status'] = 'unposting';
                    break;
                case 'posting':
                    $default_options['conditions']['ProductAdjustment.status'] = 1;
                    $default_options['conditions']['ProductAdjustment.transaction_status'] = 'posting';
                    break;
                case 'void':
                    $default_options['conditions']['ProductAdjustment.transaction_status'] = 'void';
                    break;
            }
        }
        
        return $default_options;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('Penyesuaian Qty');

        if ( !empty($data) ) {
            $nodoc = $this->filterEmptyField($data, 'ProductAdjustment', 'nodoc');
            $transaction_status = $this->filterEmptyField($data, 'ProductAdjustment', 'transaction_status');

            $data['ProductAdjustment']['branch_id'] = Configure::read('__Site.config_branch_id');
            $data['ProductAdjustment']['generate_nodoc'] = true;

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

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));
                // debug($data);
                // debug($this->validationErrors);die();

            if( !empty($flag) ) {
                $session_id = $this->filterEmptyField($data, 'ProductAdjustment', 'session_id');
                $transaction_status = $this->filterEmptyField($data, 'ProductAdjustment', 'transaction_status');

                if( $transaction_status == 'posting' ) {
                    $this->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->deleteAll(array(
                        'ProductAdjustmentDetailSerialNumber.session_id' => $session_id,
                    ));
                }

                if( !empty($id) ) {
                    $this->ProductAdjustmentDetail->deleteAll(array(
                        'ProductAdjustmentDetail.product_adjustment_id' => $id,
                    ));
                }
                
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

    function doDelete( $id, $type = null ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductAdjustment.id' => $id,
            ),
        ));
        $tmp = $this->getMergeList($value, array(
            'contain' => array(
                'ProductAdjustmentDetail' => array(
                    'contain' => array(
                        'ProductHistory' => array(
                            'contain' => array(
                                'ProductStock',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $disabled_void = Set::extract('/ProductAdjustmentDetail/ProductHistory/ProductStock/ProductStock/qty_use', $tmp);
        $disabled_void = array_filter($disabled_void);

        if ( !empty($value) && empty($disabled_void) ) {
            $default_msg = sprintf(__('membatalkan adjusment barang #%s'), $id);

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');
            
            $value = $this->getMergeList($value, array(
                'contain' => array(
                    'ProductAdjustmentDetail' => array(
                        'contain' => array(
                            'ProductAdjustmentDetailSerialNumber',
                            // 'ProductHistory' => array(
                            //     'conditions' => array(
                            //         'ProductHistory.transaction_type' => 'product_adjustment_'.$type,
                            //     ),
                            //     'contain' => array(
                            //         'ProductStock',
                            //     ),
                            // ),
                        ),
                    ),
                ),
            ));
            $details = $this->filterEmptyField($value, 'ProductAdjustmentDetail');

            if( !empty($details) ) {
                foreach ($details as $key => $detail) {
                    $type = $this->filterEmptyField($detail, 'ProductAdjustmentDetail', 'type');
                    $detail = $this->ProductAdjustmentDetail->getMergeList($detail, array(
                        'contain' => array(
                            'ProductHistory' => array(
                                'conditions' => array(
                                    'ProductHistory.transaction_type' => 'product_adjustment_'.$type,
                                ),
                                'contain' => array(
                                    'ProductStock',
                                ),
                            ),
                        ),
                    ));

                    switch ($type) {
                        case 'min':
                            $branch_id = Common::hashEmptyField($value, 'ProductAdjustment.branch_id');
                            $transaction_date = Common::hashEmptyField($value, 'ProductAdjustment.transaction_date');
                            $product_history_id = Set::extract('/ProductHistory/ProductHistory/id', $detail);
                            $product_serial_numbers = Common::hashEmptyField($detail, 'ProductAdjustmentDetailSerialNumber');
                            $product_histories = Common::hashEmptyField($detail, 'ProductHistory');

                            if( $this->save() ) {
                                $dataHistory = array();

                                if( !empty($product_serial_numbers) ) {
                                    foreach ($product_serial_numbers as $key => $val) {
                                        $product_id = Common::hashEmptyField($val, 'ProductAdjustmentDetailSerialNumber.product_id');
                                        $qty = Common::hashEmptyField($val, 'ProductAdjustmentDetailSerialNumber.qty');
                                        $price = Common::hashEmptyField($val, 'ProductAdjustmentDetailSerialNumber.price');

                                        $arrHistory['ProductHistory'] = array(
                                            'branch_id' => $branch_id,
                                            'product_id' => $product_id,
                                            'transaction_id' => Common::hashEmptyField($val, 'ProductAdjustmentDetailSerialNumber.product_adjustment_detail_id'),
                                            'transaction_type' => 'product_adjustment_min_void',
                                            'transaction_date' => $transaction_date,
                                            'qty' => $qty,
                                            'price' => $price,
                                            'type' => 'in',
                                            'ProductStock' => array(
                                                array(
                                                    'product_id' => $product_id,
                                                    'branch_id' => $branch_id,
                                                    'transaction_date' => $transaction_date,
                                                    'qty' => $qty,
                                                    'price' => $price,
                                                    'serial_number' => Common::hashEmptyField($val, 'ProductAdjustmentDetailSerialNumber.serial_number'),
                                                ),
                                            ),
                                        );
                                        $dataHistory[] = $arrHistory;
                                    }
                                } else if( !empty($product_histories) ) {
                                    foreach ($product_histories as $key => $history) {
                                        $product_id = Common::hashEmptyField($history, 'ProductHistory.product_id');
                                        $history_id = Common::hashEmptyField($history, 'ProductHistory.ProductHistory.id');

                                        $history = Common::_callUnset($history, array(
                                            'ProductHistory' => array(
                                                'id',
                                                'created',
                                                'modified',
                                                'status',
                                            ),
                                        ));
                                        $history['ProductHistory']['transaction_type'] = 'product_adjustment_min_void';
                                        $history['ProductHistory']['type'] = 'in';
                                        $history['ProductHistory']['ProductStock'] = array(
                                            'product_id' => $product_id,
                                            'product_history_id' => $history_id,
                                            'branch_id' => Common::hashEmptyField($history, 'ProductHistory.branch_id'),
                                            'transaction_date' => Common::hashEmptyField($history, 'ProductHistory.transaction_date'),
                                            'qty' => Common::hashEmptyField($history, 'ProductHistory.qty'),
                                            'price' => Common::hashEmptyField($history, 'ProductHistory.price'),
                                            'serial_number' => sprintf('%s-%s', Common::getNoRef($product_id), date('ymdHis')),
                                        );

                                        $dataHistory[] = $history;
                                    }
                                }

                                if( !empty($dataHistory) ) {
                                    $this->ProductAdjustmentDetail->ProductHistory->saveAll($dataHistory, array(
                                        'deep' => true,
                                    ));
                                }
                                if( !empty($product_history_id) ) {
                                    $this->ProductAdjustmentDetail->ProductHistory->updateAll(array(
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
                            break;
                        
                        default:
                            $product_history_id = Set::extract('/ProductHistory/ProductHistory/id', $detail);
                            $product_stock_id = Set::extract('/ProductHistory/ProductStock/ProductStock/id', $detail);
                            $qty_use = Set::extract('/ProductHistory/ProductStock/ProductStock/qty_use', $detail);
                            $qty_use = array_filter($qty_use);

                            if( $this->save() ) {
                                if( !empty($product_history_id) ) {
                                    $this->ProductAdjustmentDetail->ProductHistory->updateAll(array(
                                        'ProductHistory.status' => false,
                                    ), array(
                                        'ProductHistory.id' => $product_history_id,
                                    ));
                                }
                                if( !empty($product_stock_id) ) {
                                    $this->ProductAdjustmentDetail->Product->ProductStock->updateAll(array(
                                        'status' => false,
                                    ), array(
                                        'ProductStock.id' => $product_stock_id,
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
                            break;
                    }
                }
            }
        } else {
            $result = array(
                'msg' => __('Gagal menghapus penerimaan barang. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }
}
?>