<?php
class CustomerTargetUnitDetail extends AppModel {
	var $name = 'CustomerTargetUnitDetail';
	var $validate = array(
        'customer_target_unit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'month' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bulan harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Bulan harap dipilih'
            ),
        ),
        'unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Target Unit harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Target Unit dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'CustomerTargetUnit' => array(
            'className' => 'CustomerTargetUnit',
            'foreignKey' => 'customer_target_unit_id',
        ),
    );
}
?>