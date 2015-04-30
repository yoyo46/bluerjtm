<?php
class Perlengkapan extends AppModel {
	var $name = 'Perlengkapan';

	var $belongsTo = array(
		'TruckPerlengkapan' => array(
			'className' => 'TruckPerlengkapan',
			'foreignKey' => 'truck_id',
		),
        'JenisPerlengkapan' => array(
            'className' => 'JenisPerlengkapan',
            'foreignKey' => 'jenis_perlengkapan_id',
        )
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Perlengkapan.status' => 1,
            ),
            'order'=> array(
                'Perlengkapan.name' => 'ASC'
            ),
            'contain' => array(
                'JenisPerlengkapan'
            ),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getListPerlengkapan($type_perlengkapan){
        return $this->find('list', array(
            'conditions' => array(
                'Perlengkapan.status' => 1,
                'Perlengkapan.jenis_perlengkapan_id' => $type_perlengkapan
            ),
            'fields' => array(
                'Perlengkapan.id', 'Perlengkapan.name'
            )
        ));
    }
}
?>