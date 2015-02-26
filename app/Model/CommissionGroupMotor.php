<?php
class CommissionGroupMotor extends AppModel {
	var $name = 'CommissionGroupMotor';
	var $validate = array(
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group motor harap diisi'
            ),
        ),
        'commission' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Komisi harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Komisi harus berupa angka',
            ),
        ),
	);
}
?>