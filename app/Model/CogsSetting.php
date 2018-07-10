<?php
class CogsSetting extends AppModel {
	var $name = 'CogsSetting';

    var $belongsTo = array(
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        )
    );

    function beforeSave( $options = array() ) {
        $this->data = Hash::insert($this->data, 'CogsSetting.branch_id', Configure::read('__Site.config_branch_id'));
    }

	function getData($find, $options = false, $elements = array()){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        
        $default_options = array(
            'conditions'=> array(
                'CogsSetting.status' => 1,
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['CogsSetting.branch_id'] = Configure::read('__Site.config_branch_id');
        }

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

    function getMerge($data, $id, $fieldName = 'CogsSetting.id'){
        if(empty($data['CogsSetting'])){
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