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

    function getMerge ( $data = false, $uang_jalan_id = false ) {
        if( empty($data['AsdpGroupMotor']) ) {
            $default_options = array(
                'conditions' => array(
                    'AsdpGroupMotor.uang_jalan_id'=> $uang_jalan_id,
                    'AsdpGroupMotor.status'=> 1,
                ),
                'group' => array(
                    'AsdpGroupMotor.group_motor_id',
                ),
                'order' => array(
                    'AsdpGroupMotor.id' => 'ASC',
                ),
            );

            if( !empty($conditions) ) {
                $default_options['conditions'] = $conditions;
            }

            $asdpGroupMotor = $this->find('all', $default_options);
            $data['AsdpGroupMotor'] = $asdpGroupMotor;
        }

        return $data;
    }
}
?>