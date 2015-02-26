<?php
class AsdpGroupMotor extends AppModel {
	var $name = 'AsdpGroupMotor';
	var $validate = array(
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group motor harap diisi'
            ),
        ),
        'asdp' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Uang Penyebrangan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Uang Penyebrangan harus berupa angka',
            ),
        ),
	);
}
?>