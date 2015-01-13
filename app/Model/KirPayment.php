<?php
class KirPayment extends AppModel {
	var $name = 'KirPayment';
	var $validate = array(
        'kir_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Pol Truk harap dipilih'
            ),
        ),
        'user_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Anda tidak memiliki otoritas pada halaman ini'
            ),
        ),
        'kir_payment_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl dibayar harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Kir' => array(
			'className' => 'Kir',
			'foreignKey' => 'kir_id',
		)
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'KirPayment.status' => 1,
            ),
            'order'=> array(
                'KirPayment.created' => 'DESC',
                'KirPayment.id' => 'DESC',
            ),
            'contain' => array(
                'Kir'
            ),
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