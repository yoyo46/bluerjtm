<?php
class PurchaseOrderDetail extends AppModel {
	var $name = 'PurchaseOrderDetail';

    var $belongsTo = array(
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'purchase_order_id',
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
                'PurchaseOrderDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['PurchaseOrderDetail.status'] = 1;
                break;
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
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

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
                'PurchaseOrderDetail.purchase_order_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            $values = $this->Product->getMerge($values, false, 'PurchaseOrderDetail', $id);
            $data['PurchaseOrderDetail'] = $values;
        }

        return $data;
    }

    function doSave( $datas, $purchase_order_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menambahkan PO');

        if( !empty($purchase_order_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'PurchaseOrderDetail.purchase_order_id' => $purchase_order_id,
            ));
        }

        if ( !empty($datas['PurchaseOrderDetail']) ) {
            foreach ($datas['PurchaseOrderDetail'] as $key => $data) {
                $this->create();

                if( !empty($purchase_order_id) ) {
                    $data['PurchaseOrderDetail']['purchase_order_id'] = $purchase_order_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( !$flagSave ) {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }

            if( empty($result) ) {
                $msg = __('Berhasil menambahkan PO');
                $result = true;
            }
        }

        return $result;
    }
}
?>