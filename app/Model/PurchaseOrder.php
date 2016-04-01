<?php
class PurchaseOrder extends AppModel {
	var $name = 'PurchaseOrder';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        ),
        'SupplierQuotation' => array(
            'className' => 'SupplierQuotation',
            'foreignKey' => 'supplier_quotation_id',
        ),
    );

    var $hasMany = array(
        'PurchaseOrderDetail' => array(
            'className' => 'PurchaseOrderDetail',
            'foreignKey' => 'purchase_order_id',
        ),
        'PurchaseOrderAsset' => array(
            'className' => 'PurchaseOrderAsset',
            'foreignKey' => 'purchase_order_id',
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
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl PO harap dipilih'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'PurchaseOrder.status' => 'DESC',
                'PurchaseOrder.created' => 'DESC',
                'PurchaseOrder.id' => 'DESC',
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['PurchaseOrder.status'] = 1;
                break;

            case 'non-active':
                $default_options['conditions']['PurchaseOrder.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['PurchaseOrder.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['PurchaseOrder.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = array_merge($default_options['order'], $options['order']);
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
                'PurchaseOrder.id' => $id
            ),
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('PO');

        if ( !empty($data) ) {
            $nodoc = !empty($data['PurchaseOrder']['nodoc'])?$data['PurchaseOrder']['nodoc']:false;
            $no_sq = !empty($data['PurchaseOrder']['no_sq'])?$data['PurchaseOrder']['no_sq']:false;

            $data['PurchaseOrder']['supplier_quotation_id'] = $this->SupplierQuotation->getDataCustom('SupplierQuotation.nodoc', $no_sq, 'id');
            $data['PurchaseOrder']['branch_id'] = Configure::read('__Site.config_branch_id');

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

            $this->set($data);
            $validates = $this->validates();

            $detailValidates = $this->PurchaseOrderDetail->doSave($data, false, true);

            if( $validates && $detailValidates ) {
                if( $this->save($data) ) {
                    $id = $this->id;
                    
                    $this->PurchaseOrderDetail->doSave($data, $id);
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

                    $sq_id = $data['PurchaseOrder']['supplier_quotation_id'];

                    $this->SupplierQuotation->id = $sq_id;
                    $this->SupplierQuotation->set('is_po', 1);
                    $this->SupplierQuotation->save();

                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                        ),
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
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(PurchaseOrder.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(PurchaseOrder.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['PurchaseOrder.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if( !empty($vendor_id) ) {
            $default_options['conditions']['PurchaseOrder.vendor_id'] = $vendor_id;
        }
        
        return $default_options;
    }

    function _callRatePrice ( $product_id = false, $purchase_order_id = false, $empty = 0 ) {
        $value = $this->PurchaseOrderDetail->getData('first', array(
            'conditions' => array(
                'PurchaseOrderDetail.product_id' => $product_id,
                'PurchaseOrderDetail.purchase_order_id <>' => $purchase_order_id,
            ),
            'order' => array(
                'PurchaseOrderDetail.price' => 'ASC',
            ),
        ));

        return !empty($value['PurchaseOrderDetail']['price'])?$value['PurchaseOrderDetail']['price']:$empty;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'PurchaseOrder.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $sq_id = !empty($value['PurchaseOrder']['supplier_quotation_id'])?$value['PurchaseOrder']['supplier_quotation_id']:false;
            $nodoc = !empty($value['PurchaseOrder']['nodoc'])?$value['PurchaseOrder']['nodoc']:false;
            $default_msg = sprintf(__('menghapus PO #%s'), $nodoc);

            $this->id = $id;
            $this->set('status', 0);

            if( $this->save() ) {
                $this->SupplierQuotation->id = $sq_id;
                $this->SupplierQuotation->set('is_po', 0);
                $this->SupplierQuotation->save();

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
                'msg' => __('Gagal menghapus PO. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function generateNoId(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('PO-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'PurchaseOrder.nodoc' => 'DESC'
            ),
            'fields' => array(
                'PurchaseOrder.nodoc'
            ),
            'conditions' => array(
                'PurchaseOrder.nodoc LIKE' => '%'.$format_id.'%',
            ),
        ), array(
            'status' => 'all',
            'branch' => false,
        ));

        if(!empty($last_data['PurchaseOrder']['nodoc'])){
            $str_arr = explode('-', $last_data['PurchaseOrder']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }
        $id = str_pad($default_id, 6,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function doSaveAsset( $data, $value = false, $id = false ) {
        $msg = __('Gagal menyimpan PO');

        if( !empty($data) ) {            
            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( !empty($flag) ) {
                if( empty($id) ){
                    $data['PurchaseOrder']['nodoc'] = $this->generateNoId();
                }

                $flag = $this->PurchaseOrderAsset->updateAll(array(
                    'PurchaseOrderAsset.status' => 0,
                ), array(
                    'PurchaseOrderAsset.purchase_order_id' => $id,
                ));

                if( !empty($flag) ) {
                    $msg = __('Berhasil menyimpan PO');
                    $this->saveAll($data, array(
                        'deep' => true,
                    ));
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
                    'data' => $data,
                );
            }
        } else {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>