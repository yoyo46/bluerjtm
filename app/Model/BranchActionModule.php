<?php
class BranchActionModule extends AppModel {
	var $name = 'BranchActionModule';
	var $validate = array();

    var $belongsTo = array(
        'BranchModule' => array(
            'className' => 'BranchModule',
            'foreignKey' => 'branch_module_id'
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'BranchActionModule.id' => 'ASC'
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


    function getDataBranch($group_branch_id, $full_result = false, $branch_module_id = false){
        $default_conditions = array(
            'BranchActionModule.group_branch_id' => $group_branch_id
        );

        if(!empty($branch_module_id)){
            $default_conditions['BranchActionModule.branch_module_id'] = $branch_module_id;
        }

        $data_auth = $this->find('all', array(
            'conditions' => $default_conditions,
            'fields' => array(
                'BranchActionModule.id', 'BranchActionModule.branch_module_id', 'BranchActionModule.is_allow'
            )
        ));

        $data_result_auth = array();
        if($full_result){
            if(!empty($data_auth)){
                foreach ($data_auth as $key => $value) {
                    $data_result_auth[$value['BranchActionModule']['branch_module_id']] = $value['BranchActionModule']['is_allow'];
                }
                $data_auth = $data_result_auth;
            }
        }else{
            foreach ($data_auth as $key => $value) {
                $data_result_auth[$value['BranchActionModule']['branch_module_id']] = $value['BranchActionModule']['id'];
            }

            $data_auth = $data_result_auth;
        }

        return $data_auth;
    }

    function getRuleByModule($id = '', $group_branch_id = ''){
        if(!empty($id) && !empty($group_branch_id)){
            $result = array();

            $branch_module_id = $this->BranchModule->find('all', array(
                'conditions' => array(
                    'BranchModule.parent_id' => $id
                )
            ));
            
            if(!empty($branch_module_id)){
                $branch_module_id = Set::extract('/BranchModule/id', $branch_module_id);

                $_branch_action_module = $this->getData('all', array(
                    'conditions' => array(
                        'BranchActionModule.branch_module_id' => $branch_module_id,
                        'BranchActionModule.group_branch_id' => $group_branch_id
                    ),
                    'fields' => array(
                        'BranchActionModule.is_allow'
                    ),
                    'contain' => array(
                        'BranchModule' => array(
                            'fields' => array(
                                'BranchModule.controller', 'BranchModule.action', 'BranchModule.extend_action', 'BranchModule.type'
                            )
                        )
                    )
                ));

                if(!empty($_branch_action_module)){
                    foreach ($_branch_action_module as $key => $value) {
                        $result[$value['BranchModule']['type']] = $value['BranchActionModule']['is_allow'];
                    }
                }
            }

            return $result;
        }
    }
}
?>