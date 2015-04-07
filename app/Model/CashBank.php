<?php
class CashBank extends AppModel {
	var $name = 'CashBank';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
            ),
        ),
        'receiving_cash_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Kas Bank harap dipilih'
            ),
        ),
        'tgl_cash_bank' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal Kas Bank harap diisi'
            ),
        ),
        'receiver' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'User penerima atau di bayar kepada harap dipilih'
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kas Bank harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Coa' => array(
			'className' => 'Coa',
			'foreignKey' => 'coa_id',
		)
	);

    var $hasMany = array(
        'CashBankDetail' => array(
            'className' => 'CashBankDetail',
            'foreignKey' => 'cash_bank_id',
        ),
        'CashBankAuth' => array(
            'className' => 'CashBankAuth',
            'foreignKey' => 'cash_bank_id',
        )
    );

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'CashBank.status' => 1,
            ),
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

    function getMerge($data, $id){
        if(empty($data['CashBank'])){
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