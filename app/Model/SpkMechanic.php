<?php
class SpkMechanic extends AppModel {
	var $name = 'SpkMechanic';

    var $belongsTo = array(
        'Spk' => array(
            'foreignKey' => 'spk_id',
        ),
        'Employe' => array(
            'foreignKey' => 'employe_id',
        ),
    );

	var $validate = array(
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mekanik harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Mekanik harap dipilih'
            ),
        ),
	);

	function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'SpkMechanic.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'SpkMechanic.spk_id' => $id
            ),
        ));

        if(!empty($values)){
            $data['SpkMechanic'] = $values;
        }

        return $data;
    }
}
?>