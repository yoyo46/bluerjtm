<?php
class TruckBrand extends AppModel {
	var $name = 'TruckBrand';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Brand name harap diisi'
			),
		)
	);

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'truck_brand_id',
		),
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'TruckBrand.status' => 1,
            ),
            'order'=> array(
                'TruckBrand.name' => 'ASC'
            ),
            'contain' => array(),
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['TruckBrand'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

}
?>