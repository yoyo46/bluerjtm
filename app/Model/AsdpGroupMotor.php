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

    var $belongsTo = array(
        'GroupMotor' => array(
            'className' => 'GroupMotor',
            'foreignKey' => 'group_motor_id',
        ),
    );

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(
                'AsdpGroupMotor.status' => 1,
            ),
            'order'=> array(
                'AsdpGroupMotor.id' => 'ASC'
            ),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
        );

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge ( $data = false, $uang_jalan_id = false, $with_count = false ) {
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

            if( !empty($with_count) ) {
                $cnt = $this->find('count', $default_options);
                $data['AsdpGroupMotorCnt'] = $cnt;
                
                $default_options['contain'][] = 'GroupMotor';
            }

            $asdpGroupMotor = $this->find('all', $default_options);
            $data['AsdpGroupMotor'] = $asdpGroupMotor;
        }

        return $data;
    }
}
?>