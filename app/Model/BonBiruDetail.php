<?php
class BonBiruDetail extends AppModel {
	var $validate = array(
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
        'BonBiru' => array(
            'foreignKey' => 'bon_biru_id',
        ),
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
	);

    function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(
                'BonBiruDetail.status' => 1,
            ),
            'order'=> array(
                'BonBiruDetail.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        return $this->full_merge_options($default_options, $options, $find);
    }
}
?>