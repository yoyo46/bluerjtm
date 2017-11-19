<?php
class DocumentAuthMaster extends AppModel {
	var $name = 'DocumentAuthMaster';
	var $validate = array(
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
        'DocumentAuth' => array(
            'className' => 'DocumentAuth',
            'foreignKey' => 'document_auth_master_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'employe_id',
        )
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
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

    function getUserApproval ( $id = false ) {
        $document_auth_master = $this->find('all', array(
            'contain' => array(
                'User' => array(
                    'Group'
                )
            )
        ));

        if(!empty($document_auth_master)){
            foreach ($document_auth_master as $key => $value) {
                $conditions = array(
                    'DocumentAuth.document_auth_master_id' => $value['DocumentAuthMaster']['id'],
                );

                if( !empty($id) ) {
                    $conditions['DocumentAuth.document_id'] = $id;
                }

                $document_auth = $this->DocumentAuth->getData('first', array(
                    'conditions' => $conditions,
                ));

                if(!empty($document_auth)){
                    $document_auth_master[$key] = array_merge($value, $document_auth);
                }
            }
        }

        return $document_auth_master;
    }
}
?>