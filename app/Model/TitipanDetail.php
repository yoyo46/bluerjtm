<?php
class TitipanDetail extends AppModel {
    var $validate = array(
        'driver_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Supir harap dipilih'
            ),
        ),
        'total' => array(
            'validateTotal' => array(
                'rule' => array('validateTotal'),
                'message' => 'Jml titipan harap diisi'
            ),
        ),
    );

	var $belongsTo = array(
		'TtujPaymentDetail' => array(
			'foreignKey' => 'ttuj_payment_detail_id',
		),
        'Driver' => array(
            'foreignKey' => 'driver_id',
        ),
        'Titipan' => array(
            'foreignKey' => 'titipan_id',
        ),
	);

    function validateTotal () {
        $total = intval(Common::hashEmptyField($this->data, 'TitipanDetail.total', 0));

        if( !empty($total) ) {
            return true;
        } else {
            return false;
        }
    }

	function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        return $this->full_merge_options($default_options, $options, $find);
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $default_options = $this->defaultOptionParams($data, $default_options, array(
            'DateFrom' => array(
                'field' => 'Titipan.transaction_date >=',
            ),
            'DateTo' => array(
                'field' => 'Titipan.transaction_date <=',
            ),
            'name' => array(
                'field' => 'Driver.driver_name',
                'type' => 'like',
                'contain' => array(
                    'Driver',
                ),
            ),
            'type' => array(
                'field' => 'TitipanDetail.type',
            ),
            'phone' => array(
                'field' => 'Driver.phone',
                'type' => 'like',
                'contain' => array(
                    'Driver',
                ),
            ),
        ));
        
        return $default_options;
    }
}
?>