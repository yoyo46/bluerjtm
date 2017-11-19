<?php
class CoaClosing extends AppModel {
	var $name = 'CoaClosing';

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(
                'CoaClosing.status' => 1,
            ),
            'order'=> array(
                'CoaClosing.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

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
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>