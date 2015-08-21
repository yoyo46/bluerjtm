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

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'TtujTipeMotor.status' => 1,
            ),
            'contain' => array(
                'ColorMotor',
                'TipeMotor',
            ),
            'fields' => array(),
            'group' => array(),
            'order' => array(),
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
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

    function getMergeTtujTipeMotor ( $data = false, $ttuj_id = false, $list = 'all', $conditions = false ) {
        if( empty($data['TtujTipeMotor']) ) {
            $default_options = array(
                'conditions' => array(
                    'TtujTipeMotor.ttuj_id'=> $ttuj_id,
                    'TtujTipeMotor.status'=> 1,
                ),
                'group' => array(
                    'TipeMotor.group_motor_id',
                    'TtujTipeMotor.city_id',
                ),
                'order' => array(
                    'TtujTipeMotor.id' => 'ASC',
                ),
                'contain' => array(
                    'City',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $ttujTipeMotor = $this->getData($list, $default_options);

            if( !empty($ttujTipeMotor) && $list != 'first' ) {
                $data['TtujTipeMotor'] = $ttujTipeMotor;
            } else {
                $data['TtujTipeMotor'] = $ttujTipeMotor;
            }
        }

        return $data;
    }

    function getTotalMuatan ( $ttuj_id ) {
        $kembali = 0;
        $ttujTipeMotor = $this->getData('first', array(
            'conditions' => array(
                'TtujTipeMotor.status' => 1,
                'TtujTipeMotor.ttuj_id' => $ttuj_id,
            ),
            'fields' => array(
                'SUM(qty) muatan'
            ),
        ));

        if( !empty($ttujTipeMotor[0]['muatan']) ) {
            $kembali = $ttujTipeMotor[0]['muatan'];
        }

        return $kembali;
    }
}
?>