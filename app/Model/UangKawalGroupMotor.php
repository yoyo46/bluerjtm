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

    function getMerge ( $data = false, $uang_jalan_id = false ) {
        if( empty($data['UangKawalGroupMotor']) ) {
            $default_options = array(
                'conditions' => array(
                    'UangKawalGroupMotor.uang_jalan_id'=> $uang_jalan_id,
                    'UangKawalGroupMotor.status'=> 1,
                ),
                'order' => array(
                    'UangKawalGroupMotor.id' => 'ASC',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $uangKawalGroupMotor = $this->find('all', $default_options);
            $data['UangKawalGroupMotor'] = $uangKawalGroupMotor;
        }

        return $data;
    }
}
?>