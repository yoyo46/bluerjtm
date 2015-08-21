<?php
class GroupBranch extends AppModel {
	var $name = 'GroupBranch';
	var $validate = array();

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
    );

    var $hasMany = array(
        'BranchActionModule' => array(
            'className' => 'BranchActionModule',
            'foreignKey' => 'group_branch_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'GroupBranch.id' => 'ASC',
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

    public function _callAllowListBranch( $conditions ) {
        return $this->getData('all', array(
            'conditions' => $conditions,
            'contain' => array(
                'Branch',
            ),
            'order' => array(
                'Branch.name' => 'ASC',
            ),
        ));
    }
}
?>