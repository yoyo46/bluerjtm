<?php
class DocumentAuth extends AppModel {
	var $name = 'DocumentAuth';
	var $validate = array(
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
        ),
        'approval_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Anda tidak mempunyai hak untuk mengakses kontent tersebut.'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Anda tidak mempunyai hak untuk mengakses kontent tersebut.'
            ),
        ),
        'approval_detail_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Anda tidak mempunyai hak untuk mengakses kontent tersebut.'
            ),
        ),
        'approval_detail_position_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Anda tidak mempunyai hak untuk mengakses kontent tersebut.'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Anda tidak mempunyai hak untuk mengakses kontent tersebut.'
            ),
        ),
        'status_document' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih status dokumen'
            ),
        ),
	);

	var $belongsTo = array(
        // 'DocumentAuthMaster' => array(
        //     'className' => 'DocumentAuthMaster',
        //     'foreignKey' => 'document_auth_master_id',
        // ),
        'Approval' => array(
            'className' => 'Approval',
            'foreignKey' => 'approval_id',
        ),
        'ApprovalDetail' => array(
            'className' => 'ApprovalDetail',
            'foreignKey' => 'approval_detail_id',
        ),
        'ApprovalDetailPosition' => array(
            'className' => 'ApprovalDetailPosition',
            'foreignKey' => 'approval_detail_position_id',
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

    function getMerge( $data, $id, $module = false ){
        $approval_module = $this->Approval->ApprovalModule->getMerge(array(), $module, 'ApprovalModule.slug');
        $module_id = $this->filterEmptyField($approval_module, 'ApprovalModule', 'id');

        $data_merge = $this->getData('first', array(
            'conditions' => array(
                'DocumentAuth.document_id' => $id,
                'Approval.approval_module_id' => $module_id,
            ),
            'contain' => array(
                'Approval',
            ),
        ));

        if(!empty($data_merge['DocumentAuth'])){
            $data['DocumentAuthCurrent'] = $data_merge['DocumentAuth'];
        }

        return $data;
    }
}
?>