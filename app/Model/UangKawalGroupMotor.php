<?php
class UangKawalGroupMotor extends AppModel {
	var $name = 'UangKawalGroupMotor';
	var $validate = array(
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group motor harap diisi'
            ),
        ),
        'uang_kawal' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang Kawal harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang Kawal harus berupa angka',
            ),
        ),
	);
}
?>