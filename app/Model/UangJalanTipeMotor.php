<?php
class UangJalanTipeMotor extends AppModel {
	var $name = 'UangJalanTipeMotor';
	var $validate = array(
        'tipe_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe motor harap dipilih'
            ),
        ),
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group motor harap dipilih'
            ),
        ),
        'uang_jalan_1' => array(
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang Jalan Pertama harus berupa angka',
            ),
        ),
        'uang_jalan_2' => array(
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang Jalan Kedua harus berupa angka',
            ),
        ),
        'uang_kuli_muat' => array(
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang kuli muat harus berupa angka',
            ),
        ),
        'uang_kuli_bongkar' => array(
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang kuli bongkar harus berupa angka',
            ),
        ),
	);

    var $belongsTo = array(
        'UangJalan' => array(
            'className' => 'UangJalan',
            'foreignKey' => 'uang_jalan_id',
        ),
    );

    function getMerge ( $data = false, $uang_jalan_id = false ) {
        if( empty($data['UangJalanTipeMotor']) ) {
            $default_options = array(
                'conditions' => array(
                    'UangJalanTipeMotor.uang_jalan_id'=> $uang_jalan_id,
                    'UangJalanTipeMotor.status'=> 1,
                ),
                'order' => array(
                    'UangJalanTipeMotor.id' => 'ASC',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $uangJalanTipeMotor = $this->find('all', $default_options);
            $data['UangJalanTipeMotor'] = $uangJalanTipeMotor;
        }

        return $data;
    }
}
?>