<?php
class CoaSetting extends AppModel {
	var $name = 'CoaSetting';
	var $validate = array(
		'cashbank_out_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit Kas/Bank Keluar harap dipilih'
            ),
		),
        'cashbank_out_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit Kas/Bank Keluar harap dipilih'
            ),
        ),
        'cashbank_in_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit Kas/Bank Masuk harap dipilih'
            ),
        ),
        'cashbank_in_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit Kas/Bank Masuk harap dipilih'
            ),
        ),
        'ttuj_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit TTUJ harap dipilih'
            ),
        ),
        'ttuj_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit TTUJ harap dipilih'
            ),
        ),
        'invoice_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit Invoice harap dipilih'
            ),
        ),
        'invoice_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit Invoice harap dipilih'
            ),
        ),
        'lku_ksu_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit LKU/KSU harap dipilih'
            ),
        ),
        'lku_ksu_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit LKU/KSU harap dipilih'
            ),
        ),
        'kir_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit KIR harap dipilih'
            ),
        ),
        'kir_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit KIR harap dipilih'
            ),
        ),
        'siup_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit Ijin Usaha harap dipilih'
            ),
        ),
        'siup_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit Ijin Usaha harap dipilih'
            ),
        ),
        'laka_coa_debit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Debit Laka harap dipilih'
            ),
        ),
        'laka_coa_credit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA Kredit Laka harap dipilih'
            ),
        ),
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'CoaSetting.status' => 1,
            ),
            'contain' => array(),
            'fields' => array(),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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