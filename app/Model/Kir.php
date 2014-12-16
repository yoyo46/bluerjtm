<?php
class Kir extends AppModel {
	var $name = 'Kir';
	var $validate = array(
        'tgl_kir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal KIR harap diisi'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'biaya KIR harap diisi'
            ),
        ),
        'tgl_next_kir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal KIR selanjutnya harap diisi'
            ),
        ),
        'price_next_estimate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'biaya KIR lanjutan harap diisi'
            ),
        )
	);

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'truck_brand_id',
		)
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'status' => 'DESC'
            ),
            'contain' => array(),
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