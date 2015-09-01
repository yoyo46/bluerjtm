<?php
class LkuDetail extends AppModel {
	var $name = 'LkuDetail';
	var $validate = array(
        'tipe_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Motor harap diisi',
            ),
        ),
        // 'no_rangka' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No. Rangka harap diisi',
        //     ),
        // ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah Motor harap diisi',
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga Klaim harap diisi',
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
        'PartsMotor' => array(
            'className' => 'PartsMotor',
            'foreignKey' => 'part_motor_id',
            'conditions' => array(
                'PartsMotor.status' => 1,
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
            'contain' => array(),
            'group' => array(),
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge ( $data = false, $lku_id = false ) {
        if( empty($data['LkuDetail']) ) {
            $default_options = array(
                'conditions' => array(
                    'LkuDetail.lku_id'=> $lku_id,
                ),
                'order' => array(
                    'LkuDetail.id' => 'ASC',
                ),
            );

            $lkuDetails = $this->getData('all', $default_options);
            $data['LkuDetail'] = $lkuDetails;
        }

        return $data;
    }

    function getGroupMerge ( $data = false, $lku_id = false ) {
        if( empty($data['LkuDetail']) ) {
            $default_options = array(
                'conditions' => array(
                    'LkuDetail.lku_id'=> $lku_id,
                ),
                'contain' => array(
                    'PartsMotor',
                ),
                'order' => array(
                    'LkuDetail.id' => 'ASC',
                ),
                'fields' => array(
                    'SUM(LkuDetail.qty) AS qty',
                    'SUM(LkuDetail.price) AS price',
                    'PartsMotor.name',
                ),
                'group' => array(
                    'LkuDetail.part_motor_id',
                    'LkuDetail.price',
                ),
            );

            $lkuDetails = $this->getData('all', $default_options);
            $data['LkuDetail'] = $lkuDetails;
        }

        return $data;
    }
}
?>