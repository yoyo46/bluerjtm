<?php
class ProductUnit extends AppModel {
	var $name = 'ProductUnit';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Satuan barang harap diisi'
            ),
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'ProductUnit.status' => 1,
            ),
            'order'=> array(
                'ProductUnit.name' => 'ASC'
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

    function getMerge( $data, $id, $field = 'ProductUnit.id' ){
        if(empty($data['ProductUnit'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    $field => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        
        if( !empty($name) ) {
            $default_options['conditions']['ProductUnit.name LIKE'] = '%'.$name.'%';
        }
        
        return $default_options;
    }
}
?>