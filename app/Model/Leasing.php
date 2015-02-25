<?php
class Leasing extends AppModel {
	var $name = 'Leasing';
	var $validate = array(
        'installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cicilan perbulan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Cicilan perbulan harap diisi dengan angka',
            ),
        ),
        'no_contract' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Kontral harap diisi'
            ),
        ),
        'leasing_company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Perusahaan leasing harap dipilih'
            ),
        ),
        'paid_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'fine' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Denda harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Denda harap diisi dengan angka',
            ),
        ),
        'date_first_installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal angsuran pertama harap diisi'
            ),
        ),
        'date_last_installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal angsuran terakhir harap diisi'
            ),
        ),
        'leasing_month' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bulan angsuran harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Bulan angsuran harus berupa angka'
            ),
        ),
        'down_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'DP harap diisi'
            ),
        ),
        'installment_rate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bunga harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'LeasingCompany' => array(
            'className' => 'LeasingCompany',
            'foreignKey' => 'leasing_company_id',
        ),
    );

    var $hasMany = array(
        'LeasingDetail' => array(
            'className' => 'LeasingDetail',
            'foreignKey' => 'leasing_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Leasing.status' => 'DESC'
            ),
            'contain' => array(
                'LeasingCompany'
            ),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>