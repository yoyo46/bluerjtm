<?php
class UangKeamananGroupMotor extends AppModel {
	var $name = 'UangKeamananGroupMotor';
	var $validate = array(
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group motor harap diisi'
            ),
        ),
        'uang_keamanan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang Keamanan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang Keamanan harus berupa angka',
            ),
        ),
	);
}
?>