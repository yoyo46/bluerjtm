<?php
class LakaInsurance extends AppModel {
	var $name = 'LakaInsurance';

    function getMerge ( $data = false, $id = false ) {
        if( empty($data['LakaInsurance']) ) {
            $value = $this->find('list', array(
                'conditions' => array(
                    'LakaInsurance.id'=> $id,
                ),
            ));

            if( !empty($value) ) {
            	$data['LakaInsurance']['name'] = implode('<br>', $value);
            }
        }

        return $data;
    }
}
?>