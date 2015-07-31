<?php
class ApprovalDetailPosition extends AppModel {
	var $name = 'ApprovalDetailPosition';
	var $validate = array(
        'group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Karyawan harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'ApprovalDetail' => array(
			'className' => 'ApprovalDetail',
			'foreignKey' => 'approval_detail_id',
		),
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
        ),
	);

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ApprovalDetailPosition.id' => 'ASC',
            ),
            'contain' => array(
                'Group',
            ),
            'fields' => array(),
            'group' => array(
                'ApprovalDetailPosition.group_id',
            ),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['ApprovalDetailPosition.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['ApprovalDetailPosition.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['ApprovalDetailPosition.status'] = 1;
                break;
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
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

    function getMerge ( $data = false, $id = false ) {
        if( empty($data['ApprovalDetailPosition']) ) {
            $default_options = array(
                'conditions' => array(
                    'ApprovalDetailPosition.approval_detail_id'=> $id,
                    'ApprovalDetailPosition.status'=> 1,
                ),
                'order' => array(
                    'ApprovalDetailPosition.id' => 'ASC',
                ),
            );

            $approvalDetailPositions = $this->getData('all', $default_options);
            $data['ApprovalDetailPosition'] = $approvalDetailPositions;
        }

        return $data;
    }
}
?>