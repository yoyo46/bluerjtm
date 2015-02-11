<?php
class LkuPartDetail extends AppModel {
	var $name = 'LkuPartDetail';
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
        'LkuPart' => array(
            'className' => 'LkuPart',
            'foreignKey' => 'lku_part_id',
            'conditions' => array(
                'LkuPartDetail.status' => 1,
            ),
        ),
        'PartMotor' => array(
            'className' => 'PartMotor',
            'foreignKey' => 'part_motor_id',
            'conditions' => array(
                'PartMotor.status' => 1,
            ),
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'LkuPartDetail.status' => 1,
            ),
            'order'=> array(
                'LkuPartDetail.created' => 'DESC',
                'LkuPartDetail.id' => 'DESC',
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