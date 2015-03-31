<?php
class Employe extends AppModel {
	var $name = 'Employe';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Nama karyawan harap diisi'
			),
		),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Alamat harap diisi'
            ),
        ),
        'phone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Telepon harap diisi'
            ),
        ),
        'employe_position_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Posisi Karyawan harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'EmployePosition' => array(
            'className' => 'EmployePosition',
            'foreignKey' => 'employe_position_id',
        ),
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'Employe.status' => 1,
            ),
            'order'=> array(
                'Employe.name' => 'ASC'
            ),
            'contain' => array(
                'EmployePosition'
            ),
        );

        if(!empty($options) && $is_merge){
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
        } else if( !$is_merge ) {
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