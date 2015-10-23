<?php
class EmployePosition extends AppModel {
	var $name = 'EmployePosition';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Nama posisi harap diisi'
			),
		),
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode posisi harap diisi'
            ),
        ),
	);

    var $hasMany = array(
        'Approval' => array(
            'className' => 'Approval',
            'foreignKey' => 'employe_position_id',
        ),
        'Employe' => array(
            'className' => 'Employe',
            'foreignKey' => 'employe_position_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'EmployePosition.status' => 1,
            ),
            'order'=> array(
                'EmployePosition.name' => 'ASC'
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
        if(empty($data['EmployePosition'])){
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