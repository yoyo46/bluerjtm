<?php
class ProductCategory extends AppModel {
    public $actsAs = array('Tree');

	var $name = 'ProductCategory';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis Barang harap diisi'
            ),
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'ProductCategory.status' => 1,
            ),
            'order'=> array(
                'ProductCategory.name' => 'ASC'
            ),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
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
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $fieldName = 'ProductCategory', $field = 'ProductCategory.id' ){
        $data_merge = $this->find('first', array(
            'conditions' => array(
                $field => $id
            ),
        ));

        if(!empty($data_merge['ProductCategory'])){
            $data[$fieldName] = $data_merge['ProductCategory'];
        }

        return $data;
    }

    function getListParent ( $id = false, $categories = false, $idx = 0 ) {
        $result = array();
        $separator = str_pad('', $idx, '-', STR_PAD_LEFT);

        if( empty($categories) ) {
            $categories = $this->getData('threaded');
        }

        if( !empty($categories) ) {
            foreach ($categories as $key => $value) {
                $cat_id = !empty($value['ProductCategory']['id'])?$value['ProductCategory']['id']:false;

                if( $id != $cat_id ) {
                    $i = $idx;
                    $name = !empty($value['ProductCategory']['name'])?$value['ProductCategory']['name']:false;
                    $child = !empty($value['children'])?$value['children']:false;

                    $result[$cat_id] = trim(sprintf('%s %s', $separator, $name));

                    if( !empty($child) ) {
                        $i += 2;
                        $result = $result + $this->getListParent($id, $child, $i);
                    }
                }
            }
        }

        return $result;
    }
}
?>