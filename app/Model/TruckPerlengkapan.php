<?php
class TruckPerlengkapan extends AppModel {
	var $name = 'TruckPerlengkapan';
	var $validate = array(
		'truck_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Truk harap dipilih.'
			),
		),
        'perlengkapan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Perlengkapan harap dipilih.'
            ),
        ),
        'qty' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah Perlengkapan harap dipilih.'
            ),
        ),
	);
    
    var $belongsTo = array(
        'Perlengkapan' => array(
            'className' => 'Perlengkapan',
            'foreignKey' => 'perlengkapan_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'TruckPerlengkapan.status' => 1,
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