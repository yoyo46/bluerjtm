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

    var $belongsTo = array(
        'GroupMotor' => array(
            'className' => 'GroupMotor',
            'foreignKey' => 'group_motor_id',
        ),
    );

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(
                'UangKawalGroupMotor.status' => 1,
            ),
            'order'=> array(
                'UangKawalGroupMotor.id' => 'ASC'
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

            if( !empty($with_count) ) {
                $cnt = $this->find('count', $default_options);
                $data['UangKawalGroupMotorCnt'] = $cnt;
                
                $default_options['contain'][] = 'GroupMotor';
            }

            $uangKawalGroupMotor = $this->find('all', $default_options);
            $data['UangKawalGroupMotor'] = $uangKawalGroupMotor;
        }

        return $data;
    }
}
?>