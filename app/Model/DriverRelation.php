<?php
class DriverRelation extends AppModel {
	var $name = 'DriverRelation';

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