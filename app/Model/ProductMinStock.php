<?php
class ProductMinStock extends AppModel {
	var $name = 'ProductMinStock';

    var $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );

    var $validate = array(
        'min_stock' => array(
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Minimum stok harap diisi'
            // ),
            'callNumber' => array(
                'rule' => array('callNumber', 'min_stock'),
                'message' => 'Minimum stok diisi angka'
            ),
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductMinStock.id' => 'ASC'
            ),
            'fields' => array(),
            'group' => array(),
            'contain' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['ProductMinStock.branch_id'] = Configure::read('__Site.config_branch_id');
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
        if( isset($options['contain']) && empty($options['contain']) ) {
            $default_options['contain'] = false;
        } else if(!empty($options['contain'])){
            $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
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
}
?>