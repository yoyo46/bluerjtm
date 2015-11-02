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
        'available_from' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl berlaku quotation harap dipilih'
            ),
        ),
        'available_to' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl berlaku quotation harap dipilih'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl quotation harap dipilih'
            ),
        ),
        'available_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl berlaku quotation harap dipilih'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SupplierQuotation.created' => 'DESC',
                'SupplierQuotation.id' => 'DESC',
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['SupplierQuotation.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['SupplierQuotation.branch_id'] = Configure::read('__Site.config_branch_id');
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
                'SupplierQuotation.id' => $id
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
        $defaul_msg = __('supplier quotation');

        if ( !empty($data) ) {
            $nodoc = !empty($data['SupplierQuotation']['nodoc'])?$data['SupplierQuotation']['nodoc']:false;
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

            $this->set($data);
            $validates = $this->validates();

            $detailValidates = $this->SupplierQuotationDetail->doSave($data, false, true);

            if( $validates && $detailValidates ) {
                if( $this->save($data) ) {
                    $id = $this->id;
                    
                    $this->SupplierQuotationDetail->doSave($data, $id);
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

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
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
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
}
?>