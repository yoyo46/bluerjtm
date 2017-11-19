<?php
class DriverRelation extends AppModel {
	var $name = 'DriverRelation';

    function getData($find = 'all', $options = array(), $elements = array()) {
        $default_options = array(
            'conditions' => array(
                'DriverRelation.status' => 1,
            ),
            'contain' => array(),
            'order' => array(
                'DriverRelation.id' => 'ASC',
                'DriverRelation.created' => 'ASC',
            ),
        );

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['DriverRelation'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }
}
?>