<?php
class City extends AppModel {
	var $name = 'City';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'City name harap diisi'
            ),
        ),
        'region_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Provinsi harap diisi'
            ),
        ),
        'alias' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama Singkatan harap diisi'
            ),
        )
	);

	var $belongsTo = array(
		'TruckAlocation' => array(
			'className' => 'TruckAlocation',
			'foreignKey' => 'city_id',
		)
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'City.status' => 1,
            ),
            'order'=> array(
                'City.name' => 'ASC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
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

    function getMerge($data, $id){
        if(empty($data['City'])){
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

    function getMergeDirection($data){
        $result = array();
        if( !empty($data['Direction']['from_city_id'])){
            $data_merge_from = $this->find('first', array(
                'conditions' => array(
                    'id' => $data['Direction']['from_city_id']
                )
            ));

            if(!empty($data_merge_from)){
                $result['CityFrom'] = $data_merge_from['City'];
            }
        }

        if( !empty($data['Direction']['to_city_id'])){
            $data_merge_to = $this->find('first', array(
                'conditions' => array(
                    'id' => $data['Direction']['to_city_id']
                )
            ));

            if(!empty($data_merge_to)){
                $result['CityTo'] = $data_merge_to['City'];
            }
        }

        if(!empty($result)){
            $data = array_merge($data, $result);
        }

        return $data;
    }

    function fromCities(){
        return $this->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                // 'City.is_asal' => 1,
            ),
        ));
    }
    
    function toCities($id = false){
        $default_conditions = array(
            'City.status' => 1,
            // 'City.is_tujuan' => 1,
        );
        
        if(!empty($id)){
            $default_conditions['City.id'] = $id;
        }

        return $this->getData('list', array(
            'conditions' => $default_conditions,
        ));
    }

    function getCity( $id, $field = false ){
        $city = $this->getData('first', array(
            'conditions' => array(
                'City.id' => $id,
            ),
        ), false);

        if( !empty($city['City'][$field]) ) {
            $city = $city['City'][$field];
        }
        return $city;
    }
}
?>