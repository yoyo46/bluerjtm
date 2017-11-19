<?php
class ApprovalModule extends AppModel {
	var $name = 'ApprovalModule';

    var $hasMany = array(
        'Approval' => array(
            'className' => 'Approval',
            'foreignKey' => 'approval_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ApprovalModule.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['ApprovalModule.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['ApprovalModule.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['ApprovalModule.status'] = 1;
                break;
        }

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

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge ( $data = false, $id = false, $fieldName = 'ApprovalModule.id' ) {
        if( empty($data['Approval']) ) {
            $default_options = array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            );

            $value = $this->getData('first', $default_options);
            $data = array_merge($data, $value);
        }

        return $data;
    }
}
?>