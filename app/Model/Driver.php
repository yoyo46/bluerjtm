<?php
class Driver extends AppModel {
	var $name = 'Driver';

	var $validate = array(
        'photo' => array(
            'notempty' => array(
                'on' => 'create',
                'rule' => array('notempty'),
                'message' => 'Foto harap diisi'
            ),
        ),
        'no_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. ID harap diisi'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama lengkap harap diisi'
            ),
        ),
        'identity_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. KTP harap diisi'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat Rumah harap diisi'
            ),
        ),
        'city' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat Kota harap diisi'
            ),
        ),
        'provinsi' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat Provinsi harap diisi'
            ),
        ),
        'no_hp' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. HP harap diisi'
            ),
        ),
        'birth_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Lahir harap dipilih'
            ),
            'date' => array(
                'rule' => array('date'),
                'message' => 'Tgl Lahir tidak benar'
            ),
        ),
        'tempat_lahir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tempat Lahir harap diisi'
            ),
        ),
        'jenis_sim_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis SIM harap dipilih'
            ),
        ),
        'no_sim' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. SIM harap diisi'
            ),
        ),
        'expired_date_sim' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir SIM harap dipilih'
            ),
            'date' => array(
                'rule' => array('date'),
                'message' => 'Tgl Berakhir SIM tidak benar'
            ),
        ),
        'kontak_darurat_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama lengkap harap diisi'
            ),
        ),
        'kontak_darurat_no_hp' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. HP harap diisi'
            ),
        ),
        'driver_relation_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Hubungan kerabat harap dipilih'
            ),
        ),
        'join_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Penerimaan harap dipilih'
            ),
            'date' => array(
                'rule' => array('date'),
                'message' => 'Tgl Penerimaan tidak benar'
            ),
        ),
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang Penerimaan harap dipilih'
            ),
        ),
	);

	var $hasOne = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'driver_id',
		),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'DriverRelation' => array(
            'className' => 'DriverRelation',
            'foreignKey' => 'driver_relation_id',
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Driver.status' => 1,
            ),
            'order'=> array(
                'Driver.name' => 'ASC'
            ),
            'contain' => array(),
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
        if(empty($data['Driver'])){
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

    function getGenerateDate ( $data ) {
        if( !empty($data['Driver']['expired_date_sim']) && $data['Driver']['expired_date_sim'] != '0000-00-00' ) {
            $mkDate = strtotime($data['Driver']['expired_date_sim']);
            $data['Driver']['tgl_expire_sim']['day'] = date('d', $mkDate);
            $data['Driver']['tgl_expire_sim']['month'] = date('m', $mkDate);
            $data['Driver']['tgl_expire_sim']['year'] = date('Y', $mkDate);
        }
        if( !empty($data['Driver']['birth_date']) && $data['Driver']['birth_date'] != '0000-00-00' ) {
            $mkDate = strtotime($data['Driver']['birth_date']);
            $data['Driver']['tgl_lahir']['day'] = date('d', $mkDate);
            $data['Driver']['tgl_lahir']['month'] = date('m', $mkDate);
            $data['Driver']['tgl_lahir']['year'] = date('Y', $mkDate);
        }
        if( !empty($data['Driver']['join_date']) && $data['Driver']['join_date'] != '0000-00-00' ) {
            $mkDate = strtotime($data['Driver']['join_date']);
            $data['Driver']['tgl_penerimaan']['day'] = date('d', $mkDate);
            $data['Driver']['tgl_penerimaan']['month'] = date('m', $mkDate);
            $data['Driver']['tgl_penerimaan']['year'] = date('Y', $mkDate);
        }

        return $data;
    }
}
?>