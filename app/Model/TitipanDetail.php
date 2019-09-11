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
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $truck_type = !empty($data['named']['truck_type'])?$data['named']['truck_type']:false;

        $default_options = $this->defaultOptionParams($data, $default_options, array(
            'DateFrom' => array(
                'field' => 'Titipan.transaction_date >=',
            ),
            'DateTo' => array(
                'field' => 'Titipan.transaction_date <=',
            ),
            'name' => array(
                'field' => 'CASE WHEN Driver.alias = \'\' THEN Driver.name ELSE CONCAT(Driver.name, \' ( \', Driver.alias, \' )\') END',
                'type' => 'like',
                'contain' => array(
                    'Driver',
                ),
            ),
            'type' => array(
                'field' => 'TitipanDetail.type',
            ),
            'phone' => array(
                'field' => '(CASE WHEN Driver.no_hp = \'\' OR Driver.no_hp IS NOT NULL THEN Driver.no_hp ELSE Driver.phone END)',
                'type' => 'like',
                'contain' => array(
                    'Driver',
                ),
            ),
            'staff_id' => array(
                'field' => 'CONCAT(\'#\', Driver.no_id)',
                'type' => 'like',
                'contain' => array(
                    'Driver',
                ),
            ),
        ));

        if(!empty($nopol)){
            if( $truck_type == 2 ) {
                $conditionsNopol = array(
                    'Truck.id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'Truck.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $truckSearch = $this->Driver->Truck->getData('list', array(
                'conditions' => $conditionsNopol,
                'fields' => array(
                    'Truck.id', 'Truck.driver_id',
                ),
            ), true, array(
                'branch' => false,
            ));
            $default_options['conditions']['TitipanDetail.driver_id'] = $truckSearch;
        }
        
        return $default_options;
    }
}
?>