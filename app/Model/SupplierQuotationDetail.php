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
            'emptyFill' => array(
                'rule' => array('emptyFill', 'price'),
                'message' => 'Harga barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga barang harus berupa angka'
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
            'group' => array(),
            'contain' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['SupplierQuotationDetail.status'] = 1;
                break;
            case 'available':
                $default_options['conditions']['SupplierQuotation.transaction_status'] = 'approved';
                $default_options['conditions']['SupplierQuotation.status'] = 1;
                $default_options['conditions'][]['OR'] = array(
                    array(
                        'SupplierQuotation.available_from' => NULL,
                        'SupplierQuotation.available_to' => NULL,
                    ),
                    array(
                        'SupplierQuotation.available_from' => '0000-00-00',
                        'SupplierQuotation.available_to' => '0000-00-00',
                    ),
                    array(
                        'SupplierQuotation.available_from <=' => date('Y-m-d'),
                        'SupplierQuotation.available_to' => '0000-00-00',
                    ),
                    array(
                        'SupplierQuotation.available_from' => '0000-00-00',
                        'SupplierQuotation.available_to >=' => date('Y-m-d'),
                    ),
                    array(
                        'SupplierQuotation.available_from <=' => date('Y-m-d'),
                        'SupplierQuotation.available_to' => NULL,
                    ),
                    array(
                        'SupplierQuotation.available_from' => NULL,
                        'SupplierQuotation.available_to >=' => date('Y-m-d'),
                    ),
                    array(
                        'SupplierQuotation.available_from <=' => date('Y-m-d'),
                        'SupplierQuotation.available_to >=' => date('Y-m-d'),
                    ),
                );
                break;
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'SupplierQuotationDetail.supplier_quotation_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            $values = $this->Product->getMerge($values, false, 'SupplierQuotationDetail', $id);
            $data['SupplierQuotationDetail'] = $values;
        }

        return $data;
    }

    function doSave( $datas, $quotation_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menambahkan quotation');

        if( !empty($quotation_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'SupplierQuotationDetail.supplier_quotation_id' => $quotation_id,
            ));
        }

        if ( !empty($datas['SupplierQuotationDetail']) ) {
            foreach ($datas['SupplierQuotationDetail'] as $key => $data) {
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
                        return false;
                    }
                } else {
                    return false;
                }
            }

            if( empty($result) ) {
                $msg = __('Berhasil menambahkan quotation');
                $result = true;
            }
        }

        return $result;
    }
}
?>