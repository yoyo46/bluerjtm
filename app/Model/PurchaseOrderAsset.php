<?php
class PurchaseOrderAsset extends AppModel {
	var $name = 'PurchaseOrderAsset';

    var $belongsTo = array(
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'purchase_order_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'AssetGroup' => array(
            'className' => 'AssetGroup',
            'foreignKey' => 'asset_group_id',
        ),
        'Asset' => array(
            'className' => 'Asset',
            'foreignKey' => 'asset_id',
        ),
    );

	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama asset harap diisi'
            ),
            'validateName' => array(
                'rule' => array('validateName', 'name'),
                'message' => 'Nopol truk telah terdaftar.'
            ),
        ),
        'asset_group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group asset harap dipilih'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga pembelian harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga pembelian harus berupa angka'
            ),
        ),
	);

    function validateName($data, $field){
        $truck_id = !empty($this->data['Truck']['id'])?$this->data['Truck']['id']:false;
        $name = !empty($data[$field])?$data[$field]:false;
        
        $checkExists = $this->Truck->getData('first', array(
            'conditions' => array(
                'Truck.id <>' => $truck_id,
                'Truck.nopol' => $name,
            ),
        ));

        if( !empty($checkExists) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'PurchaseOrderAsset.status' => 1,
            ),
            'order'=> array(
                'PurchaseOrderAsset.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['PurchaseOrderAsset.status'] = 1;
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
                'PurchaseOrderAsset.purchase_order_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            $values = $this->Truck->getMergeAll($values, 'PurchaseOrderAsset');
            $data['PurchaseOrderAsset'] = $values;
        }

        return $data;
    }
}
?>