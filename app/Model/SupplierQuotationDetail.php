<?php
class SupplierQuotationDetail extends AppModel {
	var $name = 'SupplierQuotationDetail';

    var $belongsTo = array(
        'SupplierQuotation' => array(
            'className' => 'SupplierQuotation',
            'foreignKey' => 'supplier_quotation_id',
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        )
    );

	var $validate = array(
        'product_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Barang harap dipilih'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga barang harus berupa angka'
            ),
        ),
        'available_from' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl berlaku quotation harap dipilih'
            ),
        ),
        'disc' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'disc harga barang harus berupa angka'
            ),
        ),
        'ppn' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'PPN harga barang harus berupa angka'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SupplierQuotationDetail.id' => 'ASC',
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['SupplierQuotationDetail.status'] = 1;
                break;
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
        $data_merge = $this->getData('all', array(
            'conditions' => array(
                'SupplierQuotationDetail.id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($data_merge)){
            $data['SupplierQuotationDetail'] = $data_merge;
        }

        return $data;
    }

    function doSave( $datas, $value = false, $id = false, $quotation_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menambahkan quotation');

        if( !empty($quotation_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'SupplierQuotationDetail.supplier_quotation_id' => $quotation_id,
            ));
        }

        if ( !empty($datas) ) {
            foreach ($datas as $key => $data) {
                $this->create();

                if( !empty($quotation_id) ) {
                    $data['SupplierQuotationDetail']['supplier_quotation_id'] = $quotation_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( !$flagSave ) {
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
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $value,
                            'error' => 1,
                        ),
                    );
                }
            }

            if( empty($result) ) {
                $msg = __('Berhasil menambahkan quotation');
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'document_id' => $quotation_id,
                    ),
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>