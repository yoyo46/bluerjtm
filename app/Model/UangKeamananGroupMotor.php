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

    function getMerge ( $data = false, $uang_jalan_id = false ) {
        if( empty($data['UangKeamananGroupMotor']) ) {
            $default_options = array(
                'conditions' => array(
                    'UangKeamananGroupMotor.uang_jalan_id'=> $uang_jalan_id,
                    'UangKeamananGroupMotor.status'=> 1,
                ),
                'order' => array(
                    'UangKeamananGroupMotor.id' => 'ASC',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $uangKeamananGroupMotor = $this->find('all', $default_options);
            $data['UangKeamananGroupMotor'] = $uangKeamananGroupMotor;
        }

        return $data;
    }
}
?>