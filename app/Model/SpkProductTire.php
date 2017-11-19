<?php
class SpkProductTire extends AppModel {
	var $name = 'SpkProductTire';

    var $belongsTo = array(
        'SpkProduct' => array(
            'foreignKey' => 'spk_product_id',
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkProductTire.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['contain'])){
            $default_options['contain'] = $options['contain'];
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
}
?>