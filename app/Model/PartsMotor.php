<?php
class PartsMotor extends AppModel {
	var $name = 'PartsMotor';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama part motor harap diisi'
            ),
        ),
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Part motor harap diisi'
            ),
        ),
        // 'biaya_claim' => array(
        //     'numeric' => array(
        //         'rule' => array('numeric'),
        //         'allowEmpty' => true,
        //         'message' => 'Biaya harus berupa angka'
        //     ),
        // ),
        'biaya_claim_unit' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Biaya per unit harus berupa angka'
            ),
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'PartsMotor.status' => 1,
            ),
            'order'=> array(
                'PartsMotor.name' => 'ASC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['PartsMotor'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>