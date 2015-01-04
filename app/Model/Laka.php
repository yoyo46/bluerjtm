<?php
class Laka extends AppModel {
	var $name = 'Laka';
	var $validate = array(
        'nopol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nopol harap diisi'
            ),
        ),
        'tgl_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl LAKA harap dipilih'
            ),
        ),
        'driver_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama supir harap dipilih'
            ),
        ),
        'lokasi_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Lokasi LAKA harap diisi'
            ),
        ),
        'from_city_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota dari harap dipilih'
            ),
        ),
        'to_city_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota ke harap dipilih'
            ),
        ),
        'status_muatan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'status muatan harap dipilih'
            ),
        ),
        'driver_condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'kondisi supir harap diisi'
            ),
        ),
        'truck_condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'kondisi armada dan muatan harap diisi'
            ),
        ),
        'description_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'deskripsi LAKA harap diisi'
            ),
        ),
	);

    var $hasOne = array(
        'LakaDetail' => array(
            'className' => 'LakaDetail',
            'foreignKey' => 'laka_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Laka.status' => 1,
            ),
            'order'=> array(
                'Laka.created' => 'DESC',
                'Laka.id' => 'DESC',
            ),
            'contain' => array(
                'LakaDetail'
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
}
?>