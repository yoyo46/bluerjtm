<?php
class UangKuliGroupMotor extends AppModel {
	var $name = 'UangKuliGroupMotor';
	var $validate = array(
        'uang_kuli_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang Kuli harap diisi'
            ),
        ),
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group Motor harap dipilih'
            ),
        ),
        'uang_kuli' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang Kuli harap diisi'
            ),
            'numeric' => array(
                'allowEmpty'=> true,
                'rule' => array('numeric'),
                'message' => 'Uang Kuli harus berupa angka',
            ),
        ),
	);

    var $belongsTo = array(
        'UangKuli' => array(
            'className' => 'UangKuli',
            'foreignKey' => 'uang_kuli_id',
        ),
    );
}
?>