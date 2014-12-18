<?php
class TipeMotor extends AppModel {
	var $name = 'TipeMotor';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe motor harap diisi'
            ),
        ),
        'color_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Warna motor harap diisi'
            ),
        )
	);

	var $belongsTo = array(
		'ColorMotor' => array(
			'className' => 'ColorMotor',
			'foreignKey' => 'color_motor_id',
		)
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'TipeMotor.status' => 1,
            ),
            'order'=> array(
                'TipeMotor.name' => 'ASC'
            ),
            'contain' => array(
                'ColorMotor'
            ),
            'fields' => array()
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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
        if(empty($data['TipeMotor'])){
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

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['tipe_motor_color'] = sprintf('CONCAT(%s.name, " - ", ColorMotor.name)', $this->alias);
    }
}
?>