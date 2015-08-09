<?php
class BranchModule extends AppModel {
	var $name = 'BranchModule';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama module harap diisi'
            ),
        ),
        'controller' => array(
            'validateAction' => array(
                'rule' => array('validateAction'),
                'message' => 'Controller harap diisi'
            ),
        ),
        'action' => array(
            'validateAction' => array(
                'rule' => array('validateAction'),
                'message' => 'Action harap diisi'
            ),
        ),
        'type' => array(
            'validateAction' => array(
                'rule' => array('validateAction'),
                'message' => 'Tipe modul harap diisi'
            ),
        ),
        'branch_parent_module_id' => array(
            'validateParentGroup' => array(
                'rule' => array('validateParentGroup'),
                'message' => 'Parent modul group harap diisi'
            ),
        ),
        'order' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Order harap diisi'
            ),
        ),
	);

    var $hasMany = array(
        'BranchChild' => array(
            'className' => 'BranchModule',
            'foreignKey' => 'parent_id',
        ),
    );

    function validateParentGroup($data){
        $result = true;
        if(!empty($this->data['BranchModule']['is_parent']) && empty($data['branch_parent_module_id'])){
            $result = false;
        }

        return $result;
    }

    function validateAction($data){
        $key = key($data);

        if( empty($this->data['BranchModule']['is_parent']) ) {
            if( empty($data[$key]) && !empty($this->data['BranchModule']['parent_id']) ){
                return false;
            }else{
                if(empty($this->data['BranchModule']['parent_id'])){
                    return true;
                }else{
                    if(!empty($data[$key])){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        } else {
            return true;
        }
    }

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'BranchModule.status' => 1,
            ),
            'order'=> array(
                'BranchModule.name' => 'ASC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
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

    function getParentModule(){
        return $this->find('list', array(
            'conditions' => array(
                'BranchModule.status' => 1,
                'BranchModule.parent_id' => 0,
            ),
            'order'=> array(
                'BranchModule.name' => 'ASC'
            ),
            'fields' => array(
                'BranchModule.id', 'BranchModule.name'
            )
        ));
    }
}
?>