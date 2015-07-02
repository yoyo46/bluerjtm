<?php
class Notification extends AppModel {
	var $name = 'Notification';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Notification name harap diisi'
            ),
        ),
        'branch' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap diisi'
            ),
        ),
        'account_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Rek harap diisi'
            ),
        ),
        'account_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Atas Nama harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Notification.id' => 'DESC'
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

    function saveData($user_id, $data = array()){
        if(!empty($user_id)){
            if(is_array($user_id)){
                foreach ($user_id as $key => $value) {
                    $this->create();
                    $data['user_id'] = $value;
                    $this->set($data);
                    $this->save();
                }
            }else{
                $this->create();
                $this->set($data);
                if($this->save()){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }
}
?>