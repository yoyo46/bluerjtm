<?php
class ApprovalDetail extends AppModel {
	var $name = 'ApprovalDetail';
	var $validate = array(
        'min_amount' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Jumlah minimal harap diisi'
            ),
        ),
        'max_amount' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Jumlah maksimal harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
		'Approval' => array(
			'className' => 'Approval',
			'foreignKey' => 'approval_id',
		),
	);

    var $hasMany = array(
        'ApprovalDetailPosition' => array(
            'className' => 'ApprovalDetailPosition',
            'foreignKey' => 'approval_detail_id',
        ),
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ApprovalDetail.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['ApprovalDetail.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['ApprovalDetail.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['ApprovalDetail.status'] = 1;
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
        if( empty($data['ApprovalDetail']) ) {
            $default_options = array(
                'conditions' => array(
                    'ApprovalDetail.approval_id'=> $id,
                    'ApprovalDetail.status'=> 1,
                ),
                'order' => array(
                    'ApprovalDetail.id' => 'ASC',
                ),
            );

            $approvalDetails = $this->find('all', $default_options);
            $data['ApprovalDetail'] = $approvalDetails;
        }

        return $data;
    }
}
?>