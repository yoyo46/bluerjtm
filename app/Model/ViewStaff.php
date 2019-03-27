<?php
class ViewStaff extends AppModel {
    function getData($find, $options = false, $elements = array()){
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(
            ),
            'order'=> array(
                'ViewStaff.name' => 'ASC',
                'ViewStaff.id' => 'ASC',
            ),
            'contain' => array(
            ),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['ViewStaff.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        return $this->full_merge_options($default_options, $options, $find);
    }

    function getMerge( $data, $id, $type ){
        if(empty($data['ViewStaff'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'ViewStaff.id' => $id,
                    'ViewStaff.type' => $type,
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $default_options = $this->defaultOptionParams($data, $default_options, array(
            'name' => array(
                'field' => 'ViewStaff.name',
                'type' => 'like',
            ),
            'type' => array(
                'field' => 'ViewStaff.type',
            ),
        ));
        
        return $default_options;
    }
}
?>