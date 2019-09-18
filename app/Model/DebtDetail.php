<?php
class DebtDetail extends AppModel {
	var $validate = array(
        'employe_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Karyawan harap dipilih'
            ),
        ),
        'total' => array(
            'validateTotal' => array(
                'rule' => array('validateTotal'),
                'message' => 'Jml hutang harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
		'ViewStaff' => array(
			'foreignKey' => 'employe_id',
            'conditions' => array(
                'ViewStaff.type = DebtDetail.type',
            ),
		),
        'Debt' => array(
            'foreignKey' => 'debt_id',
        ),
	);

    var $hasMany = array(
        'DebtPaymentDetail' => array(
            'foreignKey' => 'debt_detail_id',
        ),
    );

    function validateTotal () {
        $total = intval(Common::hashEmptyField($this->data, 'DebtDetail.total', 0));

        if( !empty($total) ) {
            return true;
        } else {
            return false;
        }
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($status) ) {
            switch ($status) {
                case 'paid':
                    $default_options['conditions']['DebtDetail.paid_status'] = 'full';
                    break;

                case 'unpaid':
                    $default_options['conditions']['DebtDetail.paid_status <>'] = 'full';
                    break;
            }
        }

        return $this->full_merge_options($default_options, $options, $find);
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $default_options = $this->defaultOptionParams($data, $default_options, array(
            'DateFrom' => array(
                'field' => 'Debt.transaction_date >=',
            ),
            'DateTo' => array(
                'field' => 'Debt.transaction_date <=',
            ),
            'name' => array(
                'field' => 'ViewStaff.full_name',
                'type' => 'like',
                'contain' => array(
                    'ViewStaff',
                ),
            ),
            'type' => array(
                'field' => 'DebtDetail.type',
            ),
            'phone' => array(
                'field' => 'ViewStaff.phone',
                'type' => 'like',
                'contain' => array(
                    'ViewStaff',
                ),
            ),
            'staff_id' => array(
                'field' => 'CONCAT(\'#\', ViewStaff.no_id)',
                'type' => 'like',
                'contain' => array(
                    'ViewStaff',
                ),
            ),
            'nodoc' => array(
                'field' => 'Debt.nodoc',
                'type' => 'like',
            ),
        ));
        
        return $default_options;
    }
}
?>