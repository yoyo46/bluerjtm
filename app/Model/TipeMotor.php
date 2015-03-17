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
        'code_motor' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode motor harap diisi'
            ),
        ),
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup motor harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'GroupMotor' => array(
            'className' => 'GroupMotor',
            'foreignKey' => 'group_motor_id',
        ),
	);

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'TipeMotor.status' => 1,
            ),
            'order'=> array(
                'TipeMotor.name' => 'ASC'
            ),
            'contain' => array(
                'GroupMotor'
            ),
            'fields' => array()
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
        if(empty($data['TipeMotor'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'TipeMotor.id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    // function __construct($id = false, $table = null, $ds = null) {
    //     parent::__construct($id, $table, $ds);
    //     $this->virtualFields['tipe_motor_color'] = sprintf('CONCAT(%s.name, " - ", ColorMotor.name)', $this->alias);
    // }
}
?>