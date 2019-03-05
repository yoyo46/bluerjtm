<?php
class Direction extends AppModel {
	var $name = 'Direction';
	var $validate = array(
        'from_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'dari kota harap diisi'
            ),
        ),
        'to_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'ke kota harap diisi'
            ),
        ),
        'distance' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'jumlah jarak tempuh harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'jumlah jarak tempuh harus berupa angka'
            ),
        ),
        'gas' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'jumlah bahan bakar harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'jumlah bahan bakar harus berupa angka'
            ),
        ),
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Direction.status' => 1,
            ),
            'order'=> array(
                'Direction.id' => 'DESC'
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
}
?>