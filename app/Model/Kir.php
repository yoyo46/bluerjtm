<?php
class Kir extends AppModel {
	var $name = 'Kir';
	var $validate = array(
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'no_pol' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'from_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir KIR harap diisi pada data Truk'
            ),
        ),
        'to_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir KIR harap diisi pada data Truk'
            ),
        ),
        'tgl_kir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal KIR harap diisi'
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Biaya KIR harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
		'Truck' => array(
			'className' => 'Truck',
			'foreignKey' => 'truck_id',
		)
	);

	function getData($find, $options = false, $is_merge = false){
        $default_options = array(
            'conditions'=> array(
                'Kir.status' => 1,
            ),
            'order'=> array(
                'Kir.status' => 'DESC',
                'Kir.rejected' => 'ASC',
                'Kir.paid' => 'ASC',
                'Kir.tgl_kir' => 'DESC',
            ),
            'contain' => array(
                'Truck'
            ),
            'fields' => array(),
        );

        if(!empty($options) && $is_merge){
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
        }else{
            $default_options = $options;
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