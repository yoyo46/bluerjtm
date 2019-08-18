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

    function getMerge( $data, $id ){
        if( empty($data['BonBiruDetail']) ) {
            $values = $this->getData('all', array(
                'conditions' => array(
                    'BonBiruDetail.bon_biru_id' => $id,
                ),
            ));

            if( !empty($values) ) {
                $data['BonBiruDetail'] = $values;
            }
        }

        return $data;
    }

    function _callTotalTtujDiterima ( $id = false ) {
        $this->virtualFields['cnt_ttuj'] = 'COUNT(BonBiruDetail.ttuj_id)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'BonBiruDetail.bon_biru_id' => $id,
            ),
        ));

        return !empty($value['BonBiruDetail']['cnt_ttuj'])?$value['BonBiruDetail']['cnt_ttuj']:0;
    }
}
?>