<?php
class LkuDetail extends AppModel {
	var $name = 'LkuDetail';
	var $validate = array(
        'tipe_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'no_rangka' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
	);

    var $belongsTo = array(
        'Lku' => array(
            'className' => 'Lku',
            'foreignKey' => 'lku_id',
            'conditions' => array(
                'LkuDetail.status' => 1,
            ),
        ),
        'TipeMotor' => array(
            'className' => 'TipeMotor',
            'foreignKey' => 'tipe_motor_id',
            'conditions' => array(
                'TipeMotor.status' => 1,
            ),
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'LkuDetail.status' => 1,
            ),
            'order'=> array(
                'LkuDetail.created' => 'DESC',
                'LkuDetail.id' => 'DESC',
            ),
            'fields' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>