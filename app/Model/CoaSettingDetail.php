<?php
class CoaSettingDetail extends AppModel {
	var $name = 'CoaSettingDetail';

    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        )
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'CoaSettingDetail.status' => 1,
            ),
            'contain' => array(),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id, $fieldName = 'CoaSettingDetail.id'){
        if(empty($data['CoaSettingDetail'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id
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