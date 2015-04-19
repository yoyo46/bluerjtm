<?php
class Spk extends AppModel {
	var $name = 'Spk';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No Dokumen telah terdaftar',
            ),
        ),
        'date_spk' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl SPK harap dipilih'
            ),
        ),
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
            ),
        ),
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kepala mekanik harap dipilih'
            ),
        ),
        'date_target_from' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal mulai harap dipilih'
            ),
        ),
        'date_target_to' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal selesai harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'Employe' => array(
            'className' => 'Employe',
            'foreignKey' => 'employe_id',
        ),
    );

    var $hasMany = array(
        'SpkMechanic' => array(
            'className' => 'SpkMechanic',
            'foreignKey' => 'spk_id',
        ),
        'SpkProduct' => array(
            'className' => 'SpkProduct',
            'foreignKey' => 'spk_id',
        ),
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'Spk.status' => 1,
            ),
            'order'=> array(
                'Spk.created' => 'DESC',
                'Spk.id' => 'DESC',
            ),
            'contain' => array(
                'Truck',
                'Employe',
                'SpkProduct',
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
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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
}
?>