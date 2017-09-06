<?php
class ProductRetur extends AppModel {
	var $name = 'ProductRetur';

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
                'ProductRetur.document_type' => 'po',
            ),
        ),
    );

    var $hasMany = array(
        'ProductReturDetail' => array(
            'className' => 'ProductReturDetail',
            'foreignKey' => 'product_retur_id',
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
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Karyawan harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Karyawan harap dipilih'
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
                'message' => 'Tgl Retur harap dipilih'
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
        $invalid_date = Common::hashEmptyField($this->data, 'ProductRetur.invalid_date', null, array(
            'isset' => true,
        ));

        if( !empty($invalid_date) ) {
            return false;
        } else {
            return true;
        }
    }

    function validateDate () {
        $transaction_date = Common::hashEmptyField($this->data, 'ProductRetur.transaction_date');
        $reference_date = Common::hashEmptyField($this->data, 'ProductRetur.reference_date');
        
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
                'ProductRetur.status' => 'DESC',
                'ProductRetur.created' => 'DESC',
                'ProductRetur.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductRetur.status'] = 1;
                break;
            case 'pending':
                $default_options['conditions']['ProductRetur.transaction_status'] = array( 'unposting', 'revised' );
                $default_options['conditions']['ProductRetur.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['ProductRetur.status'] = 0;
                break;
            default:
                $default_options['conditions']['ProductRetur.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductRetur.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $fieldName = 'ProductRetur.id' ){
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

    function _callReturDocument( $data ) {
        $document_id = $this->filterEmptyField($data, 'ProductRetur', 'document_id');
        $document_type = $this->filterEmptyField($data, 'ProductRetur', 'document_type');
        $transaction_status = $this->filterEmptyField($data, 'ProductRetur', 'transaction_status');
        $transaction_date = $this->filterEmptyField($data, 'ProductRetur', 'transaction_date');

        $purchaseOrderDetail = $this->PurchaseOrder->PurchaseOrderDetail->getData('first', array(
            'conditions' => array(
                'PurchaseOrderDetail.purchase_order_id' => $document_id,
            ),
        ), array(
            'status' => 'unretur',
        ));

        if( !empty($purchaseOrderDetail) ) {
            $status = 'half';
        } else {
            $status = 'full';
        }

        $this->PurchaseOrder->id = $document_id;
        $this->PurchaseOrder->set('draft_retur_status', $status);

        if( $transaction_status == 'posting' ) {
            $this->PurchaseOrder->set('retur_status', $status);
        }

        $this->PurchaseOrder->save();
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('retur barang');

        if ( !empty($data) ) {
            $flag = $this->saveAll($data, array(
                'deep' => true,
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                if( empty($id) ){
                    $data['ProductRetur']['nodoc'] = $this->generateNoId();
                }

                $this->ProductReturDetail->deleteAll(array(
                    'ProductReturDetail.product_retur_id' => $id,
                ));

                $flag = $this->saveAll($data, array(
                    'deep' => true,
                ));
                
                if( !empty($flag) ) {
                    $id = $this->id;

                    $this->_callReturDocument( $data );
                    
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

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(ProductRetur.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(ProductRetur.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['ProductRetur.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['ProductRetur.vendor_id'] = $vendor_id;
        }
        if( !empty($nodocref) ) {
            $default_options['conditions']['ProductRetur.document_number LIKE'] = '%'.$nodocref.'%';
        }
        
        return $default_options;
    }

    function doDelete( $id, $type = null ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductRetur.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $document_id = $this->filterEmptyField($value, 'ProductRetur', 'document_id');
            $nodoc = $this->filterEmptyField($value, 'ProductRetur', 'nodoc');
            $document_type = $this->filterEmptyField($value, 'ProductRetur', 'document_type');

            switch ($type) {
                case 'void':
                    $default_msg = sprintf(__('membatalkan retur barang #%s'), $id);
                    break;
                
                default:
                    $default_msg = sprintf(__('menghapus retur barang #%s'), $id);
                    break;
            }

            $this->id = $id;
            $this->set('status', 0);
            $this->set('transaction_status', 'void');
            
            $value = $this->getMergeList($value, array(
                'contain' => array(
                    'ProductReturDetail' => array(
                        'contain' => array(
                            'ProductHistory' => array(
                                'conditions' => array(
                                    'ProductHistory.transaction_type' => 'product_returs',
                                ),
                                'contain' => array(
                                    'ProductStock',
                                ),
                            ),
                        ),
                    ),
                ),
            ));
            $product_history_id = Set::extract('/ProductReturDetail/ProductHistory/id', $value);
            $product_stock_id = Set::extract('/ProductReturDetail/ProductHistory/ProductStock/ProductStock/id', $value);
            $qty_use = Set::extract('/ProductReturDetail/ProductHistory/ProductStock/ProductStock/qty_use', $value);
            $qty_use = array_filter($qty_use);

            if( $this->save() ) {
                $this->PurchaseOrder->id = $document_id;
                $this->PurchaseOrder->set('retur_status', 'none');
                $this->PurchaseOrder->set('draft_retur_status', 'none');
                $this->PurchaseOrder->save();

                if( empty($qty_use) ) {
                    if( !empty($product_history_id) ) {
                        $this->ProductReturDetail->ProductHistory->updateAll(array(
                            'ProductHistory.status' => false,
                        ), array(
                            'ProductHistory.id' => $product_history_id,
                        ));
                    }
                    if( !empty($product_stock_id) ) {
                        $this->ProductReturDetail->Product->ProductStock->updateAll(array(
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
                'msg' => __('Gagal menghapus retur barang. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('RT-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'ProductRetur.nodoc' => 'DESC'
            ),
            'fields' => array(
                'ProductRetur.nodoc'
            ),
            'conditions' => array(
                'ProductRetur.nodoc LIKE' => '%'.$format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['ProductRetur']['nodoc'])){
            $str_arr = explode('-', $last_data['ProductRetur']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }
}
?>