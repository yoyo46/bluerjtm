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
        'tipe_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe motor harap dipilih.'
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
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'TtujTipeMotor.status' => 1,
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
}
?>