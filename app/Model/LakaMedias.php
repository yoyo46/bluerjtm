<?php
class LakaMedias extends AppModel {
	var $name = 'LakaMedias';

    var $belongsTo = array(
        'Laka' => array(
            'className' => 'Laka',
            'foreignKey' => 'laka_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $truck_id, $data ){
        if( empty($data['LakaMedias'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(),
                'contain' => array(),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>