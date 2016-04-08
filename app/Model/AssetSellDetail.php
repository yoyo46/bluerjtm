<?php
class AssetSellDetail extends AppModel {
    var $name = 'AssetSellDetail';

    var $belongsTo = array(
        'AssetSell' => array(
            'className' => 'AssetSell',
            'foreignKey' => 'asset_sell_id',
        ),
        'Asset' => array(
            'className' => 'Asset',
            'foreignKey' => 'asset_id',
        ),
    );

    var $validate = array(
        'asset_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Asset harap dipilih'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga jual harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga jual harus berupa angka'
            ),
        ),
    );

    function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'AssetSellDetail.status' => 1,
            ),
            'order'=> array(
                'AssetSellDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['AssetSellDetail.status'] = 1;
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
                'AssetSellDetail.asset_sell_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            $values = $this->Asset->getMergeAll($values, 'AssetSellDetail');
            $data['AssetSellDetail'] = $values;
        }

        return $data;
    }
}
?>