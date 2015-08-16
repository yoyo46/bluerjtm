<?php
class City extends AppModel {
	var $name = 'City';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'City name harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Kota sudah terdaftar, mohon masukkan kota lain.'
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

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $plant = isset($elements['plant'])?$elements['plant']:false;
        $branch = isset($elements['branch'])?$elements['branch']:false;
        // $pool = isset($elements['pool'])?$elements['pool']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'City.name' => 'ASC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['City.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['City.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['City.status'] = 1;
                break;
        }

        if( !empty($plant) ) {
            $default_options['conditions']['City.is_plant'] = 1;
        }
        // if( !empty($pool) ) {
        //     $default_options['conditions']['City.is_pool'] = 1;
        // }
        if( !empty($branch) ) {
            $default_options['conditions']['City.is_branch'] = 1;
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
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

    // function poolCities(){
    //     return $this->getData('list', array(
    //         'conditions' => array(
    //             'City.status' => 1,
    //             'City.is_pool' => 1,
    //         ),
    //     ));
    // }
    
    function branchCities($id = false){
        return $this->getData('list', array(
            'conditions' => array(
                'City.status' => 1,
                'City.is_branch' => 1,
            ),
        ));
    }
    
    function getListCities($id = false){
        $default_conditions = array(
            'City.status' => 1,
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

    function getMerge ( $data = false, $city_id = false, $ModelName = 'City' ) {
        if( empty($data[$ModelName]) ) {
            $default_options = array(
                'conditions' => array(
                    'City.id'=> $city_id,
                ),
                'order' => array(
                    'City.name' => 'ASC',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $city = $this->getData('first', $default_options, true, array(
                'status' => 'all',
            ));

            if( !empty($city) ) {
                $data[$ModelName] = $city['City'];
            }
        }

        return $data;
    }

    function getCityIdPlants ( $conditions = false, $fieldName = 'Truck.branch_id' ) {
        $is_plant = Configure::read('__Site.config_branch_plant');
        $result = false;
        $plantCityId = false;

        if( !empty($is_plant) ) {
            $cityPlants = $this->getData('list', false, true, array(
                'plant' => true,
            ));
            $result = $plantCityId = array_keys($cityPlants);
        }

        if( !empty($conditions) && !empty($result) ) {
            $result = $conditions;
            $result[$fieldName] = $plantCityId;
        }

        return $result;
    }
}
?>