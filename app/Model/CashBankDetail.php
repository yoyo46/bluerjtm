<?php
class CashBankDetail extends AppModel {
	var $name = 'CashBankDetail';
	var $validate = array(
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Coa' => array(
			'className' => 'Coa',
			'foreignKey' => 'coa_id',
		),
        'CashBank' => array(
            'className' => 'CashBank',
            'foreignKey' => 'cash_bank_id',
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function totalPrepaymentDibayarPerCoa ( $prepayment_id, $coa_id, $cash_bank_id = false ) {
        $conditions = array(
            'CashBank.document_id' => $prepayment_id,
            'CashBankDetail.coa_id' => $coa_id,
            'CashBank.status' => 1,
            'CashBank.prepayment_status <>' => 'full_paid',
            'CashBank.is_rejected' => 0,
            'CashBank.receiving_cash_type' => 'prepayment_in',
        );

        if( !empty($cash_bank_id) ) {
            $options['conditions']['CashBank.id <>'] = $cash_bank_id;
        }

        $docPaid = $this->getData('first', array(
            'conditions' => $conditions,
            'contain' => array(
                'CashBank',
            ),
            'fields' => array(
                'SUM(CashBankDetail.total) AS total'
            ),
        ), false);

        return !empty($docPaid[0]['total'])?$docPaid[0]['total']:0;
    }

    function getMerge ( $data = false, $id = false, $options = false ) {
        if( empty($data['CashBankDetail']) ) {
            $default_options = array(
                'conditions' => array(
                    'CashBankDetail.cash_bank_id'=> $id,
                ),
                'order' => array(
                    'CashBankDetail.id' => 'ASC',
                ),
            );

            if( !empty($options) ) {
                $default_options = array_merge($default_options, $options);
            }

            $value = $this->getData('all', $default_options);
            $data['CashBankDetail'] = $value;
        }

        return $data;
    }
}
?>