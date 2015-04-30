<?php
class KsuPaymentDetail extends AppModel {
	var $name = 'KsuPaymentDetail';
	var $validate = array(
        'ksu_payment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'ksu_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'total_klaim' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'total_biaya_klaim' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
	);

    var $belongsTo = array(
        'Ksu' => array(
            'className' => 'Ksu',
            'foreignKey' => 'ksu_id',
        ),
        'KsuPayment' => array(
            'className' => 'KsuPayment',
            'foreignKey' => 'ksu_payment_id'
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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