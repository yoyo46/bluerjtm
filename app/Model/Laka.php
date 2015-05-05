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
        // 'ttuj_id' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'TTUJ harap dipilih'
        //     ),
        // ),
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
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
                'message' => 'Kota dari harap diisi'
            ),
        ),
        'to_city_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota ke harap diisi'
            ),
        ),
        'status_muatan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Status muatan harap dipilih'
            ),
        ),
        'driver_condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kondisi supir harap diisi'
            ),
        ),
        'truck_condition' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kondisi armada dan muatan harap diisi'
            ),
        ),
        'description_laka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Deskripsi LAKA harap diisi'
            ),
        ),
        'complete_desc' => array(
            'completeValidate' => array(
                'rule' => array('completeValidate'),
                'message' => 'Keterangan selesai LAKA harap diisi'
            ),
        ),
        'completed_date' => array(
            'completeDateValidate' => array(
                'rule' => array('completeDateValidate'),
                'message' => 'Tgl selesai LAKA harap diisi'
            ),
        )
	);

    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
    );

    var $hasOne = array(
        'LakaDetail' => array(
            'className' => 'LakaDetail',
            'foreignKey' => 'laka_id',
        ),
    );

    var $hasMany = array(
        'LakaMedias' => array(
            'className' => 'LakaMedias',
            'foreignKey' => 'laka_id',
        ),
    );

    function completeValidate($data){
        if(!empty($this->data['Laka']['completed'])){
            if( !empty($this->data['Laka']['complete_desc']) ){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    function completeDateValidate($data){
        if(!empty($this->data['Laka']['completed'])){
            if( !empty($this->data['Laka']['completed_date']) ){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

	function getData($find, $options = false, $is_merge = true){
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

        if( !empty($options) && !empty($is_merge) ){
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
        } else if( !empty($options) ){
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $truck_id, $data ){
        if( empty($data['Laka'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Ttuj.truck_id' => $truck_id,
                    'Laka.status' => 1,
                    'Laka.completed' => 0,
                ),
                'contain' => array(
                    'Ttuj'
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeTtuj( $ttuj_id, $data, $conditions = false ){
        if( empty($data['Laka'])){
            $condition_default = array(
                'Laka.ttuj_id' => $ttuj_id,
                'Laka.status' => 1,
            );

            if( !empty($conditions) ) {
                $condition_default = array_merge($condition_default, $conditions);
            }

            $data_merge = $this->getData('first', array(
                'conditions' => $condition_default,
            ), false);

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>