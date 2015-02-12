<?php
class TtujTipeMotor extends AppModel {
	var $name = 'TtujTipeMotor';
	var $validate = array(
		'ttuj_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih.'
			),
		),
        'city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tujuan harap dipilih.'
            ),
        ),
        'tipe_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe motor harap dipilih.'
            ),
        ),
        'color_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Warna motor harap dipilih.'
            ),
        ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah unit harap dipilih.'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'jumlah unit harus berupa angka'
            ),
        ),
	);
    
    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'TipeMotor' => array(
            'className' => 'TipeMotor',
            'foreignKey' => 'tipe_motor_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'ColorMotor' => array(
            'className' => 'ColorMotor',
            'foreignKey' => 'color_motor_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

    var $hasMany = array(
        'TtujTipeMotorUse' => array(
            'className' => 'TtujTipeMotorUse',
            'foreignKey' => 'ttuj_tipe_motor_id',
        ),
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'TtujTipeMotor.status' => 1,
            ),
            'contain' => array(),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = array_merge($default_options['fields'], $options['fields']);
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
}
?>