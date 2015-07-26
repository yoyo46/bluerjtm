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

    function getMerge ( $data = false, $uang_jalan_id = false ) {
        if( empty($data['CommissionGroupMotor']) ) {
            $default_options = array(
                'conditions' => array(
                    'CommissionGroupMotor.uang_jalan_id'=> $uang_jalan_id,
                    'CommissionGroupMotor.status'=> 1,
                ),
                'order' => array(
                    'CommissionGroupMotor.id' => 'ASC',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $commissionGroupMotor = $this->find('all', $default_options);
            $data['CommissionGroupMotor'] = $commissionGroupMotor;
        }

        return $data;
    }
}
?>